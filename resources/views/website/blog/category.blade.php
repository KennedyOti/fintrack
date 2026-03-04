@extends('layouts.app')

@section('title', ($cat->meta_title ?: $cat->name) . ' — FinTrack Blog')
@section('meta-description', $cat->meta_description ?: 'Browse ' . $cat->name . ' articles on FinTrack Blog.')
@section('og-title', $cat->name . ' — FinTrack Blog')
@section('og-description', $cat->meta_description ?: 'Browse ' . $cat->name . ' articles on FinTrack Blog.')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/blog.css') }}">
<link rel="canonical" href="{{ route('blog.category', $cat->slug) }}">
@endsection

@section('content')

{{-- Category Hero --}}
<section class="blog-cat-hero" style="--cat-color: {{ $cat->color }};">
    <div class="container" style="max-width:1160px;">
        <div class="blog-cat-hero-inner">
            <div class="blog-cat-hero-icon" style="background:{{ $cat->color }}22;border-color:{{ $cat->color }}44;">
                <i class="fa-solid {{ $cat->icon }}" style="color:{{ $cat->color }};"></i>
            </div>
            <h1 class="blog-cat-hero-title">{{ $cat->name }}</h1>
            @if($cat->description)
            <p class="blog-cat-hero-desc">{{ $cat->description }}</p>
            @endif
            <div class="blog-cat-hero-meta">
                <span><i class="fa-regular fa-file-lines"></i> {{ $posts->total() }} article{{ $posts->total() !== 1 ? 's' : '' }}</span>
            </div>
        </div>
    </div>
</section>

<section class="section-sm" style="padding-top:0;">
    <div class="container" style="max-width:1160px;">

        {{-- Breadcrumb --}}
        <nav class="blog-breadcrumb" style="margin-bottom:24px;" aria-label="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('blog.index') }}">Blog</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $cat->name }}</span>
        </nav>

        <div class="blog-layout">
            <div class="blog-main">
                @if($posts->count())
                <div class="blog-grid">
                    @foreach($posts as $post)
                    <article class="blog-card">
                        <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-img-link">
                            @if($post->featured_image)
                            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" loading="lazy" class="blog-card-img">
                            @else
                            <div class="blog-card-img-placeholder"><i class="fa-solid fa-newspaper"></i></div>
                            @endif
                        </a>
                        <div class="blog-card-body">
                            <div class="blog-card-meta">
                                <span class="blog-cat-pill blog-cat-pill-sm" style="background:{{ $cat->color }}22;color:{{ $cat->color }};border-color:{{ $cat->color }}44;">{{ $cat->name }}</span>
                                <span class="blog-read-time"><i class="fa-regular fa-clock"></i> {{ $post->reading_time }}m</span>
                            </div>
                            <h3 class="blog-card-title"><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
                            @if($post->excerpt)
                            <p class="blog-card-excerpt">{{ Str::limit($post->excerpt, 120) }}</p>
                            @endif
                            <div class="blog-card-footer">
                                <div class="blog-card-author">
                                    <div class="blog-author-avatar blog-author-avatar-sm">{{ strtoupper(substr($post->author->name, 0, 1)) }}</div>
                                    <span>{{ $post->author->name }}</span>
                                </div>
                                <div class="blog-card-stats">
                                    <span><i class="fa-regular fa-heart"></i> {{ $post->likes_count ?? 0 }}</span>
                                    <span><i class="fa-regular fa-eye"></i> {{ number_format($post->views) }}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
                @if($posts->hasPages())
                <div class="blog-pagination">{{ $posts->links('website.blog.pagination') }}</div>
                @endif
                @else
                <div class="blog-empty">
                    <div class="blog-empty-icon"><i class="fa-solid fa-folder-open"></i></div>
                    <h3>No articles in this category yet</h3>
                    <a href="{{ route('blog.index') }}" class="btn-blog-read">Browse All Posts</a>
                </div>
                @endif
            </div>

            <aside class="blog-sidebar">
                @if($popularPosts->count())
                <div class="blog-widget">
                    <h4 class="blog-widget-title"><i class="fa-solid fa-fire"></i> Popular Articles</h4>
                    <div class="blog-widget-list">
                        @foreach($popularPosts as $i => $p)
                        <a href="{{ route('blog.show', $p->slug) }}" class="blog-widget-post">
                            <span class="blog-widget-rank">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="blog-widget-post-body">
                                <div class="blog-widget-post-title">{{ $p->title }}</div>
                                <div class="blog-widget-post-meta"><span><i class="fa-regular fa-eye"></i> {{ number_format($p->views) }}</span></div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="blog-widget">
                    <h4 class="blog-widget-title"><i class="fa-solid fa-folder-tree"></i> Categories</h4>
                    <div class="blog-cat-list">
                        @foreach($categories as $c)
                        @if($c->published_posts_count > 0)
                        <a href="{{ route('blog.category', $c->slug) }}" class="blog-cat-row {{ $c->slug === $cat->slug ? 'blog-cat-row-active' : '' }}">
                            <span class="blog-cat-dot" style="background:{{ $c->color }};"></span>
                            <span class="blog-cat-name">{{ $c->name }}</span>
                            <span class="blog-cat-count">{{ $c->published_posts_count }}</span>
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>

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
            </aside>
        </div>
    </div>
</section>
@endsection
