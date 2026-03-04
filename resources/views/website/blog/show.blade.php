@extends('layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' — FinTrack Blog')
@section('meta-description', $post->meta_description ?: $post->excerpt)
@section('og-title', $post->meta_title ?: $post->title)
@section('og-description', $post->meta_description ?: $post->excerpt)

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/blog.css') }}">
@if($post->canonical_url)
<link rel="canonical" href="{{ $post->canonical_url }}">
@else
<link rel="canonical" href="{{ route('blog.show', $post->slug) }}">
@endif
{{-- Open Graph & Twitter Cards --}}
<meta property="og:type" content="article">
<meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
@if($post->og_image)
<meta property="og:image" content="{{ Storage::url($post->og_image) }}">
<meta name="twitter:image" content="{{ Storage::url($post->og_image) }}">
@elseif($post->featured_image)
<meta property="og:image" content="{{ Storage::url($post->featured_image) }}">
<meta name="twitter:image" content="{{ Storage::url($post->featured_image) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $post->meta_title ?: $post->title }}">
<meta name="twitter:description" content="{{ $post->meta_description ?: $post->excerpt }}">
@if($post->published_at)
<meta property="article:published_time" content="{{ $post->published_at->toISOString() }}">
@endif
<meta property="article:author" content="{{ $post->author->name }}">
@foreach($post->tags as $tag)
<meta property="article:tag" content="{{ $tag->name }}">
@endforeach
@endsection

@section('content')

{{-- Schema.org JSON-LD --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ addslashes($post->title) }}",
    "description": "{{ addslashes($post->excerpt ?? Str::limit(strip_tags($post->content), 200)) }}",
    "author": {
        "@type": "Person",
        "name": "{{ addslashes($post->author->name) }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "FinTrack",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('assets/images/logo.png') }}"
        }
    },
    "datePublished": "{{ $post->published_at?->toISOString() }}",
    "dateModified": "{{ $post->updated_at->toISOString() }}",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ route('blog.show', $post->slug) }}"
    }
    @if($post->featured_image)
    ,"image": "{{ Storage::url($post->featured_image) }}"
    @endif
}
</script>

{{-- ── Breadcrumb ──────────────────────────────────────────────────────── --}}
<div style="background:var(--bg-surface);border-bottom:1px solid var(--border-dim);padding:12px 0;">
    <div class="container" style="max-width:1160px;">
        <nav class="blog-breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('blog.index') }}">Blog</a>
            @if($post->category)
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
            @endif
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ Str::limit($post->title, 40) }}</span>
        </nav>
    </div>
</div>

{{-- ── Article ──────────────────────────────────────────────────────────── --}}
<div class="blog-article-layout">
    <div class="container" style="max-width:1160px;">
        <div class="blog-article-grid">

            {{-- Main Article --}}
            <article class="blog-article" itemscope itemtype="https://schema.org/Article">

                {{-- Article Header --}}
                <header class="blog-article-header">
                    {{-- Badges --}}
                    <div class="blog-article-badges">
                        @if($post->category)
                        <a href="{{ route('blog.category', $post->category->slug) }}" class="blog-cat-pill" style="background:{{ $post->category->color }}22;color:{{ $post->category->color }};border-color:{{ $post->category->color }}44;">
                            <i class="fa-solid {{ $post->category->icon }}"></i> {{ $post->category->name }}
                        </a>
                        @endif
                        @if($post->featured)
                        <span class="blog-badge blog-badge-featured"><i class="fa-solid fa-star"></i> Featured</span>
                        @endif
                    </div>

                    <h1 class="blog-article-title" itemprop="headline">{{ $post->title }}</h1>

                    @if($post->excerpt)
                    <p class="blog-article-excerpt">{{ $post->excerpt }}</p>
                    @endif

                    {{-- Author & Meta --}}
                    <div class="blog-article-meta-bar">
                        <div class="blog-author-row">
                            <div class="blog-author-avatar blog-author-avatar-lg">{{ strtoupper(substr($post->author->name, 0, 1)) }}</div>
                            <div>
                                <div class="blog-author-name" style="font-size:15px;">{{ $post->author->name }}</div>
                                <div class="blog-post-meta-row">
                                    <span><i class="fa-regular fa-calendar"></i> {{ $post->published_at->format('F j, Y') }}</span>
                                    <span><i class="fa-regular fa-clock"></i> {{ $post->reading_time }} min read</span>
                                    <span><i class="fa-regular fa-eye"></i> {{ number_format($post->views) }} views</span>
                                </div>
                            </div>
                        </div>
                        {{-- Social Share --}}
                        <div class="blog-share-btns">
                            <span class="blog-share-label">Share:</span>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="blog-share-btn blog-share-twitter" title="Share on X">
                                <i class="fa-brands fa-x-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" rel="noopener" class="blog-share-btn blog-share-linkedin" title="Share on LinkedIn">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" rel="noopener" class="blog-share-btn blog-share-fb" title="Share on Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . route('blog.show', $post->slug)) }}" target="_blank" rel="noopener" class="blog-share-btn blog-share-wa" title="Share on WhatsApp">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                            <button class="blog-share-btn blog-share-copy" id="copyLinkBtn" title="Copy link" data-url="{{ route('blog.show', $post->slug) }}">
                                <i class="fa-solid fa-link"></i>
                            </button>
                        </div>
                    </div>
                </header>

                {{-- Featured Image --}}
                @if($post->featured_image)
                <div class="blog-article-hero-img">
                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" itemprop="image" loading="eager">
                </div>
                @endif

                {{-- Content --}}
                <div class="blog-article-content prose" itemprop="articleBody" id="articleContent">
                    {!! $post->content !!}
                </div>

                {{-- Tags --}}
                @if($post->tags->count())
                <div class="blog-article-tags">
                    <span class="blog-tags-label"><i class="fa-solid fa-tags"></i> Tags:</span>
                    @foreach($post->tags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}" class="blog-tag-chip">{{ $tag->name }}</a>
                    @endforeach
                </div>
                @endif

                {{-- Like + Share bar --}}
                <div class="blog-article-reaction-bar">
                    <button class="blog-like-btn {{ $isLiked ? 'liked' : '' }}" id="likeBtn"
                            data-url="{{ route('blog.like', $post->slug) }}"
                            data-liked="{{ $isLiked ? 'true' : 'false' }}">
                        <i class="fa-{{ $isLiked ? 'solid' : 'regular' }} fa-heart blog-like-icon"></i>
                        <span class="blog-like-count" id="likeCount">{{ number_format($post->likes_count) }}</span>
                        <span class="blog-like-label">{{ $isLiked ? 'Liked' : 'Like this article' }}</span>
                    </button>
                    <div class="blog-reaction-share">
                        <span style="color:var(--text-muted);font-size:13px;">Found this helpful?</span>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="blog-share-btn blog-share-twitter">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" rel="noopener" class="blog-share-btn blog-share-linkedin">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                {{-- Author Box --}}
                <div class="blog-author-box">
                    <div class="blog-author-box-avatar">{{ strtoupper(substr($post->author->name, 0, 1)) }}</div>
                    <div class="blog-author-box-body">
                        <div class="blog-author-box-label">Written by</div>
                        <div class="blog-author-box-name">{{ $post->author->name }}</div>
                        <div class="blog-author-box-bio">Financial content creator at FinTrack. Helping freelancers achieve financial clarity and build sustainable businesses.</div>
                    </div>
                </div>

                {{-- ── Comments ──────────────────────────────────────────────── --}}
                @if($post->allow_comments)
                <section class="blog-comments-section" id="comments">
                    <h3 class="blog-section-title">
                        <i class="fa-regular fa-comments"></i>
                        {{ $comments->count() }} Comment{{ $comments->count() !== 1 ? 's' : '' }}
                    </h3>

                    {{-- Flash messages --}}
                    @if(session('success'))
                    <div class="blog-alert blog-alert-success">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                    @endif
                    @if($errors->any())
                    <div class="blog-alert blog-alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
                    </div>
                    @endif

                    {{-- Comment Form --}}
                    <div class="blog-comment-form-wrap">
                        <h4 class="blog-comment-form-title">
                            @auth Leave a Comment @else Join the Discussion @endauth
                        </h4>
                        <form action="{{ route('blog.comment', $post->slug) }}" method="POST" class="blog-comment-form" id="commentForm">
                            @csrf
                            @if(!Auth::check())
                            <div class="blog-form-row-2">
                                <div class="blog-form-group">
                                    <label class="blog-form-label">Name <span class="req">*</span></label>
                                    <input type="text" name="guest_name" class="blog-form-input" placeholder="Your name" value="{{ old('guest_name') }}" required>
                                </div>
                                <div class="blog-form-group">
                                    <label class="blog-form-label">Email <span class="req">*</span></label>
                                    <input type="email" name="guest_email" class="blog-form-input" placeholder="your@email.com" value="{{ old('guest_email') }}" required>
                                    <span class="blog-form-hint">Not shown publicly</span>
                                </div>
                            </div>
                            @else
                            <div class="blog-comment-as">
                                <div class="blog-author-avatar blog-author-avatar-sm">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                                <span>Commenting as <strong>{{ Auth::user()->name }}</strong></span>
                            </div>
                            @endif
                            <div class="blog-form-group">
                                <label class="blog-form-label">Comment <span class="req">*</span></label>
                                <textarea name="content" class="blog-form-textarea" rows="4" placeholder="Share your thoughts..." required>{{ old('content') }}</textarea>
                            </div>
                            <button type="submit" class="blog-btn-submit">
                                <i class="fa-regular fa-paper-plane"></i> Post Comment
                            </button>
                        </form>
                    </div>

                    {{-- Comments List --}}
                    @if($comments->count())
                    <div class="blog-comments-list" id="commentsList">
                        @foreach($comments as $comment)
                        <div class="blog-comment" id="comment-{{ $comment->id }}">
                            <div class="blog-comment-header">
                                {!! $comment->author_avatar !!}
                                <div class="blog-comment-info">
                                    <div class="blog-comment-name">{{ $comment->author_name }}</div>
                                    <div class="blog-comment-time">{{ $comment->created_at->diffForHumans() }}</div>
                                </div>
                                <button class="blog-reply-trigger" data-parent="{{ $comment->id }}" data-name="{{ $comment->author_name }}">
                                    <i class="fa-solid fa-reply"></i> Reply
                                </button>
                            </div>
                            <div class="blog-comment-body">{{ $comment->content }}</div>

                            {{-- Reply form (hidden) --}}
                            <div class="blog-reply-form-wrap" id="reply-form-{{ $comment->id }}" style="display:none;">
                                <form action="{{ route('blog.comment', $post->slug) }}" method="POST" class="blog-comment-form blog-reply-form">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    @if(!Auth::check())
                                    <div class="blog-form-row-2">
                                        <div class="blog-form-group">
                                            <input type="text" name="guest_name" class="blog-form-input blog-form-input-sm" placeholder="Your name" required>
                                        </div>
                                        <div class="blog-form-group">
                                            <input type="email" name="guest_email" class="blog-form-input blog-form-input-sm" placeholder="your@email.com" required>
                                        </div>
                                    </div>
                                    @endif
                                    <textarea name="content" class="blog-form-textarea blog-form-textarea-sm" rows="3" placeholder="Replying to {{ $comment->author_name }}..." required></textarea>
                                    <div class="blog-reply-form-actions">
                                        <button type="submit" class="blog-btn-submit blog-btn-submit-sm"><i class="fa-solid fa-reply"></i> Reply</button>
                                        <button type="button" class="blog-btn-cancel blog-reply-cancel" data-parent="{{ $comment->id }}">Cancel</button>
                                    </div>
                                </form>
                            </div>

                            {{-- Replies --}}
                            @if($comment->replies->count())
                            <div class="blog-replies">
                                @foreach($comment->replies as $reply)
                                <div class="blog-comment blog-comment-reply">
                                    <div class="blog-comment-header">
                                        {!! $reply->author_avatar !!}
                                        <div class="blog-comment-info">
                                            <div class="blog-comment-name">{{ $reply->author_name }}</div>
                                            <div class="blog-comment-time">{{ $reply->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <div class="blog-comment-body">{{ $reply->content }}</div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="blog-comments-empty">
                        <i class="fa-regular fa-comments"></i>
                        <p>No comments yet. Be the first to share your thoughts!</p>
                    </div>
                    @endif

                </section>
                @endif
            </article>

            {{-- Sidebar --}}
            <aside class="blog-sidebar blog-article-sidebar">

                {{-- TOC --}}
                <div class="blog-widget blog-toc-widget" id="tocWidget">
                    <h4 class="blog-widget-title"><i class="fa-solid fa-list-ul"></i> Table of Contents</h4>
                    <nav class="blog-toc" id="toc"></nav>
                </div>

                {{-- Popular --}}
                @if($popularPosts->count())
                <div class="blog-widget">
                    <h4 class="blog-widget-title"><i class="fa-solid fa-fire"></i> Popular Articles</h4>
                    <div class="blog-widget-list">
                        @foreach($popularPosts as $i => $p)
                        <a href="{{ route('blog.show', $p->slug) }}" class="blog-widget-post {{ $p->id === $post->id ? 'blog-widget-post-active' : '' }}">
                            <span class="blog-widget-rank">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="blog-widget-post-body">
                                <div class="blog-widget-post-title">{{ $p->title }}</div>
                                <div class="blog-widget-post-meta">
                                    <span><i class="fa-regular fa-eye"></i> {{ number_format($p->views) }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Categories --}}
                @if($categories->count())
                <div class="blog-widget">
                    <h4 class="blog-widget-title"><i class="fa-solid fa-folder-tree"></i> Categories</h4>
                    <div class="blog-cat-list">
                        @foreach($categories as $cat)
                        @if($cat->published_posts_count > 0)
                        <a href="{{ route('blog.category', $cat->slug) }}" class="blog-cat-row">
                            <span class="blog-cat-dot" style="background:{{ $cat->color }};"></span>
                            <span class="blog-cat-name">{{ $cat->name }}</span>
                            <span class="blog-cat-count">{{ $cat->published_posts_count }}</span>
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Tags --}}
                @if($allTags->count())
                <div class="blog-widget">
                    <h4 class="blog-widget-title"><i class="fa-solid fa-tags"></i> Topics</h4>
                    <div class="blog-tag-cloud">
                        @foreach($allTags as $t)
                        <a href="{{ route('blog.tag', $t->slug) }}" class="blog-tag-chip {{ $post->tags->contains('slug', $t->slug) ? 'blog-tag-chip-active' : '' }}">{{ $t->name }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Reading progress --}}
                <div class="blog-widget blog-progress-widget">
                    <h4 class="blog-widget-title"><i class="fa-regular fa-clock"></i> Reading Progress</h4>
                    <div class="blog-progress-bar-wrap">
                        <div class="blog-progress-bar" id="readingProgress"></div>
                    </div>
                    <div class="blog-progress-info">
                        <span id="progressText">0%</span>
                        <span>{{ $post->reading_time }} min read</span>
                    </div>
                </div>

            </aside>

        </div>
    </div>
</div>

{{-- ── Related Posts ────────────────────────────────────────────────────── --}}
@if($related->count())
<section class="blog-related-section">
    <div class="container" style="max-width:1160px;">
        <h2 class="blog-section-heading">You Might Also Like</h2>
        <div class="blog-related-grid">
            @foreach($related as $r)
            <article class="blog-card">
                <a href="{{ route('blog.show', $r->slug) }}" class="blog-card-img-link">
                    @if($r->featured_image)
                    <img src="{{ Storage::url($r->featured_image) }}" alt="{{ $r->title }}" loading="lazy" class="blog-card-img">
                    @else
                    <div class="blog-card-img-placeholder"><i class="fa-solid fa-newspaper"></i></div>
                    @endif
                </a>
                <div class="blog-card-body">
                    <div class="blog-card-meta">
                        @if($r->category)
                        <a href="{{ route('blog.category', $r->category->slug) }}" class="blog-cat-pill blog-cat-pill-sm" style="background:{{ $r->category->color }}22;color:{{ $r->category->color }};border-color:{{ $r->category->color }}44;">{{ $r->category->name }}</a>
                        @endif
                        <span class="blog-read-time"><i class="fa-regular fa-clock"></i> {{ $r->reading_time }}m</span>
                    </div>
                    <h3 class="blog-card-title"><a href="{{ route('blog.show', $r->slug) }}">{{ $r->title }}</a></h3>
                    <div class="blog-card-footer">
                        <div class="blog-card-stats">
                            <span><i class="fa-regular fa-heart"></i> {{ number_format($r->likes_count ?? 0) }}</span>
                            <span><i class="fa-regular fa-eye"></i> {{ number_format($r->views) }}</span>
                        </div>
                        <a href="{{ route('blog.show', $r->slug) }}" class="btn-ghost-sm">Read <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Reading progress bar at top --}}
<div class="blog-read-progress-line" id="readProgressLine"></div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Reading progress bar ──────────────────────────────────────────────
    const progressLine = document.getElementById('readProgressLine');
    const progressBar  = document.getElementById('readingProgress');
    const progressText = document.getElementById('progressText');
    const article      = document.getElementById('articleContent');

    function updateProgress() {
        if (!article) return;
        const rect    = article.getBoundingClientRect();
        const total   = article.offsetHeight - window.innerHeight;
        const scrolled = Math.max(0, -rect.top);
        const pct = Math.min(100, Math.max(0, (scrolled / total) * 100));
        if (progressLine) progressLine.style.width = pct + '%';
        if (progressBar)  progressBar.style.width  = pct + '%';
        if (progressText) progressText.textContent  = Math.round(pct) + '%';
    }
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();

    // ── Table of Contents ─────────────────────────────────────────────────
    const toc = document.getElementById('toc');
    const tocWidget = document.getElementById('tocWidget');
    if (article && toc) {
        const headings = article.querySelectorAll('h2, h3, h4');
        if (headings.length > 1) {
            headings.forEach(function(h, i) {
                if (!h.id) h.id = 'heading-' + i;
                const a = document.createElement('a');
                a.href = '#' + h.id;
                a.textContent = h.textContent;
                a.className = 'blog-toc-' + h.tagName.toLowerCase();
                toc.appendChild(a);
            });

            // Active heading tracking
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        toc.querySelectorAll('a').forEach(function(a) { a.classList.remove('active'); });
                        const activeLink = toc.querySelector('a[href="#' + entry.target.id + '"]');
                        if (activeLink) activeLink.classList.add('active');
                    }
                });
            }, { rootMargin: '-20% 0px -70% 0px' });
            headings.forEach(function(h) { observer.observe(h); });
        } else {
            if (tocWidget) tocWidget.style.display = 'none';
        }
    }

    // ── Like button ───────────────────────────────────────────────────────
    const likeBtn = document.getElementById('likeBtn');
    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
            const url   = likeBtn.dataset.url;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            })
            .then(r => r.json())
            .then(data => {
                const icon  = likeBtn.querySelector('.blog-like-icon');
                const count = document.getElementById('likeCount');
                const label = likeBtn.querySelector('.blog-like-label');
                if (data.liked) {
                    likeBtn.classList.add('liked');
                    icon.className = 'fa-solid fa-heart blog-like-icon';
                    label.textContent = 'Liked';
                } else {
                    likeBtn.classList.remove('liked');
                    icon.className = 'fa-regular fa-heart blog-like-icon';
                    label.textContent = 'Like this article';
                }
                count.textContent = data.count.toLocaleString();
                // Pulse animation
                likeBtn.classList.add('like-pulse');
                setTimeout(() => likeBtn.classList.remove('like-pulse'), 600);
            });
        });
    }

    // ── Copy link ─────────────────────────────────────────────────────────
    const copyBtn = document.getElementById('copyLinkBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            navigator.clipboard.writeText(copyBtn.dataset.url).then(function() {
                copyBtn.innerHTML = '<i class="fa-solid fa-check"></i>';
                setTimeout(() => { copyBtn.innerHTML = '<i class="fa-solid fa-link"></i>'; }, 2000);
            });
        });
    }

    // ── Reply toggles ─────────────────────────────────────────────────────
    document.querySelectorAll('.blog-reply-trigger').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const parentId = btn.dataset.parent;
            const form = document.getElementById('reply-form-' + parentId);
            if (form) {
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            }
        });
    });
    document.querySelectorAll('.blog-reply-cancel').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const parentId = btn.dataset.parent;
            const form = document.getElementById('reply-form-' + parentId);
            if (form) form.style.display = 'none';
        });
    });

});
</script>
@endsection
