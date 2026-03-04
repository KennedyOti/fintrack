<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogLike;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('q');

        $query = BlogPost::withTrashed(false)
            ->with(['author', 'category'])
            ->withCount(['likes', 'comments', 'approvedComments as approved_comments_count'])
            ->withCount('likes as likes_count');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        // Stats
        $totalPosts     = BlogPost::count();
        $publishedCount = BlogPost::where('status', 'published')->count();
        $draftCount     = BlogPost::where('status', 'draft')->count();
        $scheduledCount = BlogPost::where('status', 'scheduled')->count();
        $totalViews     = BlogPost::sum('views');
        $totalLikes     = BlogLike::count();
        $pendingComments= BlogComment::where('status', 'pending')->count();
        $totalComments  = BlogComment::where('status', 'approved')->count();

        // Top performing posts
        $topPosts = BlogPost::where('status', 'published')
            ->withCount('likes as likes_count')
            ->orderByDesc('views')
            ->take(5)
            ->get();

        // Monthly views chart data (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $d->format('M'),
                'posts' => BlogPost::whereYear('created_at', $d->year)->whereMonth('created_at', $d->month)->count(),
            ];
        }

        return view('admin.blog.index', compact(
            'posts', 'status', 'search',
            'totalPosts', 'publishedCount', 'draftCount', 'scheduledCount',
            'totalViews', 'totalLikes', 'pendingComments', 'totalComments',
            'topPosts', 'monthlyData'
        ));
    }

    // ── Create ────────────────────────────────────────────────────────────
    public function create()
    {
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();
        $tags       = BlogTag::orderBy('name')->get();

        return view('admin.blog.create', compact('categories', 'tags'));
    }

    // ── Store ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'excerpt'        => 'nullable|string|max:500',
            'category_id'    => 'nullable|exists:blog_categories,id',
            'status'         => 'required|in:draft,published,scheduled',
            'scheduled_at'   => 'nullable|date|required_if:status,scheduled',
            'featured'       => 'boolean',
            'allow_comments' => 'boolean',
            'meta_title'     => 'nullable|string|max:160',
            'meta_description'=> 'nullable|string|max:320',
            'focus_keyword'  => 'nullable|string|max:100',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'og_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'tags'           => 'nullable|string',
            'canonical_url'  => 'nullable|url|max:500',
        ]);

        $slug = BlogPost::generateSlug($validated['title']);

        // Handle image uploads
        $featuredImagePath = null;
        $ogImagePath       = null;

        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $request->file('featured_image')->store('blog/images', 'public');
        }
        if ($request->hasFile('og_image')) {
            $ogImagePath = $request->file('og_image')->store('blog/og', 'public');
        }

        $publishedAt = null;
        if ($validated['status'] === 'published') {
            $publishedAt = now();
        } elseif ($validated['status'] === 'scheduled') {
            $publishedAt = $validated['scheduled_at'];
        }

        $post = BlogPost::create([
            'user_id'         => Auth::id(),
            'category_id'     => $validated['category_id'] ?? null,
            'title'           => $validated['title'],
            'slug'            => $slug,
            'excerpt'         => $validated['excerpt'] ?? null,
            'content'         => $validated['content'],
            'featured_image'  => $featuredImagePath,
            'og_image'        => $ogImagePath,
            'status'          => $validated['status'],
            'published_at'    => $publishedAt,
            'scheduled_at'    => $validated['scheduled_at'] ?? null,
            'featured'        => $request->boolean('featured'),
            'allow_comments'  => $request->boolean('allow_comments', true),
            'reading_time'    => BlogPost::calculateReadingTime($validated['content']),
            'meta_title'      => $validated['meta_title'] ?? null,
            'meta_description'=> $validated['meta_description'] ?? null,
            'focus_keyword'   => $validated['focus_keyword'] ?? null,
            'canonical_url'   => $validated['canonical_url'] ?? null,
        ]);

        // Sync tags
        if (!empty($validated['tags'])) {
            $tagIds = [];
            foreach (explode(',', $validated['tags']) as $tagName) {
                $tagName = trim($tagName);
                if ($tagName) {
                    $tag     = BlogTag::findOrCreateByName($tagName);
                    $tagIds[] = $tag->id;
                }
            }
            $post->tags()->sync($tagIds);
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post "' . $post->title . '" created successfully!');
    }

    // ── Edit ──────────────────────────────────────────────────────────────
    public function edit(BlogPost $blog)
    {
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();
        $tags       = BlogTag::orderBy('name')->get();
        $blog->load('tags', 'category');

        return view('admin.blog.edit', compact('blog', 'categories', 'tags'));
    }

    // ── Update ────────────────────────────────────────────────────────────
    public function update(Request $request, BlogPost $blog)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'content'         => 'required|string',
            'excerpt'         => 'nullable|string|max:500',
            'category_id'     => 'nullable|exists:blog_categories,id',
            'status'          => 'required|in:draft,published,scheduled',
            'scheduled_at'    => 'nullable|date|required_if:status,scheduled',
            'featured'        => 'boolean',
            'allow_comments'  => 'boolean',
            'meta_title'      => 'nullable|string|max:160',
            'meta_description'=> 'nullable|string|max:320',
            'focus_keyword'   => 'nullable|string|max:100',
            'featured_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'og_image'        => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'tags'            => 'nullable|string',
            'canonical_url'   => 'nullable|url|max:500',
            'remove_featured_image' => 'nullable|boolean',
            'remove_og_image'       => 'nullable|boolean',
        ]);

        // Handle slug — regenerate only if title changed
        if ($blog->title !== $validated['title']) {
            $slug = BlogPost::generateSlug($validated['title'], $blog->id);
        } else {
            $slug = $blog->slug;
        }

        // Images
        $featuredImagePath = $blog->featured_image;
        $ogImagePath       = $blog->og_image;

        if ($request->boolean('remove_featured_image')) {
            if ($blog->featured_image) Storage::disk('public')->delete($blog->featured_image);
            $featuredImagePath = null;
        }
        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image) Storage::disk('public')->delete($blog->featured_image);
            $featuredImagePath = $request->file('featured_image')->store('blog/images', 'public');
        }

        if ($request->boolean('remove_og_image')) {
            if ($blog->og_image) Storage::disk('public')->delete($blog->og_image);
            $ogImagePath = null;
        }
        if ($request->hasFile('og_image')) {
            if ($blog->og_image) Storage::disk('public')->delete($blog->og_image);
            $ogImagePath = $request->file('og_image')->store('blog/og', 'public');
        }

        // Published at logic
        $publishedAt = $blog->published_at;
        if ($validated['status'] === 'published' && !$blog->published_at) {
            $publishedAt = now();
        } elseif ($validated['status'] === 'scheduled') {
            $publishedAt = $validated['scheduled_at'];
        } elseif ($validated['status'] === 'draft') {
            $publishedAt = null;
        }

        $blog->update([
            'category_id'     => $validated['category_id'] ?? null,
            'title'           => $validated['title'],
            'slug'            => $slug,
            'excerpt'         => $validated['excerpt'] ?? null,
            'content'         => $validated['content'],
            'featured_image'  => $featuredImagePath,
            'og_image'        => $ogImagePath,
            'status'          => $validated['status'],
            'published_at'    => $publishedAt,
            'scheduled_at'    => $validated['scheduled_at'] ?? null,
            'featured'        => $request->boolean('featured'),
            'allow_comments'  => $request->boolean('allow_comments', true),
            'reading_time'    => BlogPost::calculateReadingTime($validated['content']),
            'meta_title'      => $validated['meta_title'] ?? null,
            'meta_description'=> $validated['meta_description'] ?? null,
            'focus_keyword'   => $validated['focus_keyword'] ?? null,
            'canonical_url'   => $validated['canonical_url'] ?? null,
        ]);

        // Sync tags
        $tagIds = [];
        if (!empty($validated['tags'])) {
            foreach (explode(',', $validated['tags']) as $tagName) {
                $tagName = trim($tagName);
                if ($tagName) {
                    $tag    = BlogTag::findOrCreateByName($tagName);
                    $tagIds[] = $tag->id;
                }
            }
        }
        $blog->tags()->sync($tagIds);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post updated successfully!');
    }

    // ── Destroy ───────────────────────────────────────────────────────────
    public function destroy(BlogPost $blog)
    {
        $blog->delete();
        return back()->with('success', 'Post moved to trash.');
    }

    // ── Quick status toggle ───────────────────────────────────────────────
    public function toggleStatus(Request $request, BlogPost $blog)
    {
        $newStatus = $blog->status === 'published' ? 'draft' : 'published';
        $blog->update([
            'status'       => $newStatus,
            'published_at' => $newStatus === 'published' ? ($blog->published_at ?? now()) : $blog->published_at,
        ]);

        return response()->json(['status' => $newStatus, 'label' => ucfirst($newStatus)]);
    }

    // ── Upload image (TinyMCE) ────────────────────────────────────────────
    public function uploadImage(Request $request)
    {
        $request->validate(['file' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120']);
        $path = $request->file('file')->store('blog/content', 'public');
        return response()->json(['location' => Storage::disk('public')->url($path)]);
    }
}
