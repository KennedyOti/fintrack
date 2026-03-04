<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('q');

        $query = BlogComment::with(['post', 'user', 'parent'])
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        $comments       = $query->paginate(20)->withQueryString();
        $pendingCount   = BlogComment::where('status', 'pending')->count();
        $approvedCount  = BlogComment::where('status', 'approved')->count();
        $spamCount      = BlogComment::where('status', 'spam')->count();

        return view('admin.blog.comments.index', compact(
            'comments', 'status', 'search', 'pendingCount', 'approvedCount', 'spamCount'
        ));
    }

    public function approve(BlogComment $comment)
    {
        $comment->update(['status' => 'approved']);
        return back()->with('success', 'Comment approved.');
    }

    public function spam(BlogComment $comment)
    {
        $comment->update(['status' => 'spam']);
        return back()->with('success', 'Comment marked as spam.');
    }

    public function destroy(BlogComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action'   => 'required|in:approve,spam,delete',
            'ids'      => 'required|array',
            'ids.*'    => 'exists:blog_comments,id',
        ]);

        $comments = BlogComment::whereIn('id', $request->ids);

        match ($request->action) {
            'approve' => $comments->update(['status' => 'approved']),
            'spam'    => $comments->update(['status' => 'spam']),
            'delete'  => $comments->delete(),
        };

        return back()->with('success', 'Bulk action applied to ' . count($request->ids) . ' comment(s).');
    }
}
