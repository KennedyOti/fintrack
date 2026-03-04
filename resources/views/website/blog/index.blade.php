@extends('layouts.app')

@section('title', ($search ? 'Search: ' . $search . ' — ' : '') . 'FinTrack Blog — Financial Insights for Freelancers')
@section('meta-description', 'Expert financial tips, freelance business advice, invoicing guides, and tax strategies. Stay ahead with FinTrack\'s blog.')
@section('og-title', 'FinTrack Blog — Financial Insights for Freelancers')
@section('og-description', 'Expert financial tips, freelance business advice, invoicing guides, and tax strategies.')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/blog.css') }}">
<link rel="canonical" href="{{ route('blog.index') }}">
@endsection

@section('content')

{{-- ── Hero ───────────────────────────────────────────────────────────── --}}
<section class="blog-hero">
    <div class="container" style="max-width:1160px;">
        <div class="blog-hero-content">
            <div class="blog-hero-badge">
                <i class="fa-solid fa-rss"></i> FinTrack Blog
            </div>
            <h1 class="blog-hero-title">
                Financial clarity for<br>
                <span class="gradient-text">freelancers who mean business</span>
            </h1>
            <p class="blog-hero-sub">
                Expert guides, money tips & strategies to help you earn more, save smarter, and grow your freelance business.
            </p>

            {{-- Search --}}
            <form action="{{ route('blog.index') }}" method="GET" class="blog-search-form">
                <div class="blog-search-wrap">
                    <i class="fa-solid fa-magnifying-glass blog-search-icon"></i>
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Search articles, guides, tips..."
                           class="blog-search-input">
                    <button type="submit" class="blog-search-btn">Search</button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- ── Featured Post ───────────────────────────────────────────────────── --}}
@if($featured && !$search && !$category && !$tag)
<section class="section-sm" style="padding-top:0;">
    <div class="container" style="max-width:1160px;">
        <div class="blog-featured-card">
            @if($featured->featured_image)
            <div class="blog-featured-img">
                <img src="{{ Storage::url($featured->featured_image) }}" alt="{{ $featured->title }}" loading="lazy">
                <div class="blog-featured-img-overlay"></div>
            </div>
            @else
            <div class="blog-featured-img blog-featured-placeholder">
                <div class="blog-placeholder-icon"><i class="fa-solid fa-newspaper"></i></div>
            </div>
            @endif
            <div class="blog-featured-body">
                <div class="blog-featured-meta">
                    <span class="blog-badge blog-badge-featured"><i class="fa-solid fa-star"></i> Featured</span>
                    @if($featured->category)
                    <a href="{{ route('blog.category', $featured->category->slug) }}" class="blog-cat-pill" style="background:{{ $featured->category->color }}22;color:{{ $featured->category->color }};border-color:{{ $featured->category->color }}44;">
                        <i class="fa-solid {{ $featured->category->icon }}"></i> {{ $featured->category->name }}
                    </a>
                    @endif
                </div>
                <h2 class="blog-featured-title">
                    <a href="{{ route('blog.show', $featured->slug) }}">{{ $featured->title }}</a>
                </h2>
                @if($featured->excerpt)
                <p class="blog-featured-excerpt">{{ $featured->excerpt }}</p>
                @endif
                <div class="blog-featured-footer">
                    <div class="blog-author-row">
                        <div class="blog-author-avatar">{{ strtoupper(substr($featured->author->name, 0, 1)) }}</div>
                        <div>
                            <div class="blog-author-name">{{ $featured->author->name }}</div>
                            <div class="blog-post-meta-row">
                                <span><i class="fa-regular fa-calendar"></i> {{ $featured->published_at->format('M j, Y') }}</span>
                                <span><i class="fa-regular fa-clock"></i> {{ $featured->reading_time }} min read</span>
                                <span><i class="fa-regular fa-eye"></i> {{ number_format($featured->views) }}</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('blog.show', $featured->slug) }}" class="btn-blog-read">
                        Read Article <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── Main Content ─────────────────────────────────────────────────────── --}}
<section class="section-sm" style="padding-top:0;">
    <div class="container" style="max-width:1160px;">

        {{-- Filter bar --}}
        <div class="blog-filter-bar">
            <div class="blog-filter-scroll">
                <a href="{{ route('blog.index') }}" class="blog-filter-pill {{ !$category && !$tag && !$search ? 'active' : '' }}">
                    All Posts
                </a>
                @foreach($categories as $cat)
                @if($cat->published_posts_count > 0)
                <a href="{{ route('blog.category', $cat->slug) }}"
                   class="blog-filter-pill {{ $category === $cat->slug ? 'active' : '' }}"
                   style="{{ $category === $cat->slug ? 'background:'.$cat->color.'22;color:'.$cat->color.';border-color:'.$cat->color.'66;' : '' }}">
                    <i class="fa-solid {{ $cat->icon }}"></i> {{ $cat->name }}
                    <span class="blog-filter-count">{{ $cat->published_posts_count }}</span>
                </a>
                @endif
                @endforeach
            </div>
        </div>

        <div class="blog-layout">

            {{-- Posts Grid --}}
            <div class="blog-main">
                @if($search)
                <div class="blog-search-result-header">
                    <h2>{{ $posts->total() }} result{{ $posts->total() !== 1 ? 's' : '' }} for "<span style="color:var(--cyan);">{{ $search }}</span>"</h2>
                    <a href="{{ route('blog.index') }}" class="btn-ghost-sm"><i class="fa-solid fa-xmark"></i> Clear</a>
                </div>
                @endif

                @if($posts->count())
                <div class="blog-grid">
                    @foreach($posts as $post)
                    <article class="blog-card">
                        <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-img-link">
                            @if($post->featured_image)
                            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" loading="lazy" class="blog-card-img">
                            @else
                            <div class="blog-card-img-placeholder">
                                <i class="fa-solid fa-newspaper"></i>
                            </div>
                            @endif
                        </a>
                        <div class="blog-card-body">
                            <div class="blog-card-meta">
                                @if($post->category)
                                <a href="{{ route('blog.category', $post->category->slug) }}" class="blog-cat-pill blog-cat-pill-sm" style="background:{{ $post->category->color }}22;color:{{ $post->category->color }};border-color:{{ $post->category->color }}44;">
                                    {{ $post->category->name }}
                                </a>
                                @endif
                                <span class="blog-read-time"><i class="fa-regular fa-clock"></i> {{ $post->reading_time }}m</span>
                            </div>
                            <h3 class="blog-card-title">
                                <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                            </h3>
                            @if($post->excerpt)
                            <p class="blog-card-excerpt">{{ Str::limit($post->excerpt, 120) }}</p>
                            @endif
                            <div class="blog-card-footer">
                                <div class="blog-card-author">
                                    <div class="blog-author-avatar blog-author-avatar-sm">{{ strtoupper(substr($post->author->name, 0, 1)) }}</div>
                                    <span>{{ $post->author->name }}</span>
                                </div>
                                <div class="blog-card-stats">
                                    <span><i class="fa-regular fa-heart"></i> {{ number_format($post->likes_count ?? 0) }}</span>
                                    <span><i class="fa-regular fa-comment"></i> {{ number_format($post->comments_count ?? 0) }}</span>
                                    <span><i class="fa-regular fa-eye"></i> {{ number_format($post->views) }}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($posts->hasPages())
                <div class="blog-pagination">
                    {{ $posts->links('website.blog.pagination') }}
                </div>
                @endif

                @else
                <div class="blog-empty">
                    <div class="blog-empty-icon"><i class="fa-solid fa-face-thinking"></i></div>
                    <h3>No articles found</h3>
                    <p>@if($search) Try different search terms. @elseif($category || $tag) No posts in this filter yet. @else No posts published yet. Check back soon! @endif</p>
                    <a href="{{ route('blog.index') }}" class="btn-blog-read">Browse All Posts</a>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="blog-sidebar">

                {{-- Popular Posts --}}
                @if($popularPosts->count())
                <div class="blog-widget">
                    <h4 class="blog-widget-title"><i class="fa-solid fa-fire"></i> Most Popular</h4>
                    <div class="blog-widget-list">
                        @foreach($popularPosts as $i => $p)
                        <a href="{{ route('blog.show', $p->slug) }}" class="blog-widget-post">
                            <span class="blog-widget-rank">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="blog-widget-post-body">
                                <div class="blog-widget-post-title">{{ $p->title }}</div>
                                <div class="blog-widget-post-meta">
                                    <span><i class="fa-regular fa-eye"></i> {{ number_format($p->views) }}</span>
                                    <span><i class="fa-regular fa-clock"></i> {{ $p->reading_time }}m</span>
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
                        <a href="{{ route('blog.tag', $t->slug) }}" class="blog-tag-chip">{{ $t->name }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Newsletter --}}
                <div class="blog-widget blog-newsletter-widget">
                    <div class="blog-newsletter-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
                    <h4 class="blog-newsletter-title">Stay in the loop</h4>
                    <p class="blog-newsletter-sub">Get the latest financial tips & freelance guides delivered to your inbox.</p>
                    <a href="{{ route('register') }}" class="btn-blog-newsletter">
                        <i class="fa-solid fa-user-plus"></i> Join FinTrack Free
                    </a>
                </div>

            </aside>

        </div>
    </div>
</section>

@endsection
