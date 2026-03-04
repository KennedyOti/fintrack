<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogLike;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    // ── Blog Index ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $search   = $request->get('q');
        $category = $request->get('category');
        $tag      = $request->get('tag');

        $query = BlogPost::published()
            ->with(['author', 'category', 'tags'])
            ->withCount(['likes', 'approvedComments as comments_count']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $category));
        }

        if ($tag) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $tag));
        }

        $posts      = $query->orderByDesc('published_at')->paginate(9)->withQueryString();
        $featured   = BlogPost::published()->featured()->with(['author', 'category'])->latest('published_at')->first();
        $categories = BlogCategory::where('is_active', true)->withCount(['publishedPosts'])->orderBy('sort_order')->get();
        $popularPosts = BlogPost::published()->orderByDesc('views')->take(5)->get();
        $recentPosts  = BlogPost::published()->latest('published_at')->take(5)->get();
        $allTags      = BlogTag::has('posts')->withCount('posts')->orderByDesc('posts_count')->take(30)->get();

        return view('website.blog.index', compact(
            'posts', 'featured', 'categories', 'popularPosts', 'recentPosts',
            'allTags', 'search', 'category', 'tag'
        ));
    }

    // ── Single Post ───────────────────────────────────────────────────────
    public function show(Request $request, string $slug)
    {
        $post = BlogPost::published()
            ->with(['author', 'category', 'tags'])
            ->withCount('likes')
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views (once per session per post)
        $viewKey = 'blog_viewed_' . $post->id;
        if (!$request->session()->has($viewKey)) {
            $post->incrementViews();
            $request->session()->put($viewKey, true);
        }

        // Check if liked
        $isLiked = $post->isLikedBy(Auth::user(), $request->ip());

        // Comments (approved top-level, with replies)
        $comments = BlogComment::where('blog_post_id', $post->id)
            ->where('status', 'approved')
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        // Related posts
        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($post) {
                if ($post->category_id) {
                    $q->where('category_id', $post->category_id);
                }
            })
            ->withCount('likes')
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($related->count() < 3) {
            $extra = BlogPost::published()
                ->where('id', '!=', $post->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->withCount('likes')
                ->latest('published_at')
                ->take(3 - $related->count())
                ->get();
            $related = $related->merge($extra);
        }

        $categories   = BlogCategory::where('is_active', true)->withCount('publishedPosts')->orderBy('sort_order')->get();
        $popularPosts = BlogPost::published()->where('id', '!=', $post->id)->orderByDesc('views')->take(5)->get();
        $allTags      = BlogTag::has('posts')->withCount('posts')->orderByDesc('posts_count')->take(20)->get();

        return view('website.blog.show', compact(
            'post', 'isLiked', 'comments', 'related', 'categories', 'popularPosts', 'allTags'
        ));
    }

    // ── Category ──────────────────────────────────────────────────────────
    public function category(string $slug)
    {
        $cat   = BlogCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $posts = BlogPost::published()
            ->where('category_id', $cat->id)
            ->with(['author', 'category', 'tags'])
            ->withCount(['likes', 'approvedComments as comments_count'])
            ->orderByDesc('published_at')
            ->paginate(9);

        $categories   = BlogCategory::where('is_active', true)->withCount('publishedPosts')->orderBy('sort_order')->get();
        $popularPosts = BlogPost::published()->orderByDesc('views')->take(5)->get();
        $allTags      = BlogTag::has('posts')->withCount('posts')->orderByDesc('posts_count')->take(20)->get();

        return view('website.blog.category', compact('cat', 'posts', 'categories', 'popularPosts', 'allTags'));
    }

    // ── Tag ───────────────────────────────────────────────────────────────
    public function tag(string $slug)
    {
        $tag   = BlogTag::where('slug', $slug)->firstOrFail();
        $posts = BlogPost::published()
            ->whereHas('tags', fn($q) => $q->where('slug', $slug))
            ->with(['author', 'category', 'tags'])
            ->withCount(['likes', 'approvedComments as comments_count'])
            ->orderByDesc('published_at')
            ->paginate(9);

        $categories   = BlogCategory::where('is_active', true)->withCount('publishedPosts')->orderBy('sort_order')->get();
        $popularPosts = BlogPost::published()->orderByDesc('views')->take(5)->get();
        $allTags      = BlogTag::has('posts')->withCount('posts')->orderByDesc('posts_count')->take(20)->get();

        return view('website.blog.tag', compact('tag', 'posts', 'categories', 'popularPosts', 'allTags'));
    }

    // ── Like / Unlike ─────────────────────────────────────────────────────
    public function like(Request $request, string $slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        $user = Auth::user();
        $ip   = $request->ip();

        if ($user) {
            $existing = BlogLike::where('blog_post_id', $post->id)->where('user_id', $user->id)->first();
        } else {
            $existing = BlogLike::where('blog_post_id', $post->id)->whereNull('user_id')->where('ip_address', $ip)->first();
        }

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            BlogLike::create([
                'blog_post_id' => $post->id,
                'user_id'      => $user?->id,
                'ip_address'   => $ip,
            ]);
            $liked = true;
        }

        $count = $post->likes()->count();

        return response()->json(['liked' => $liked, 'count' => $count]);
    }

    // ── Comment ───────────────────────────────────────────────────────────
    public function comment(Request $request, string $slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->where('allow_comments', true)->firstOrFail();

        $rules = [
            'content'   => 'required|string|min:3|max:2000',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ];

        if (!Auth::check()) {
            $rules['guest_name']  = 'required|string|max:100';
            $rules['guest_email'] = 'required|email|max:150';
        }

        $validated = $request->validate($rules);

        $comment = BlogComment::create([
            'blog_post_id' => $post->id,
            'user_id'      => Auth::id(),
            'parent_id'    => $validated['parent_id'] ?? null,
            'guest_name'   => Auth::check() ? null : $validated['guest_name'],
            'guest_email'  => Auth::check() ? null : ($validated['guest_email'] ?? null),
            'content'      => $validated['content'],
            'status'       => Auth::check() ? 'approved' : 'pending', // auto-approve logged-in users
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);

        if ($request->expectsJson()) {
            $status = $comment->status === 'approved'
                ? 'Your comment has been posted!'
                : 'Thanks! Your comment is awaiting moderation.';
            return response()->json(['success' => true, 'message' => $status, 'status' => $comment->status]);
        }

        $msg = $comment->status === 'approved'
            ? 'Your comment has been posted!'
            : 'Thanks! Your comment is awaiting moderation.';

        return back()->with('success', $msg);
    }
}
