@extends('layouts.app')

@section('title', ($tag->meta_title ?: '#' . $tag->name) . ' — FinTrack Blog')
@section('meta-description', $tag->meta_description ?: 'Browse articles tagged "' . $tag->name . '" on FinTrack Blog.')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/blog.css') }}">
<link rel="canonical" href="{{ route('blog.tag', $tag->slug) }}">
@endsection

@section('content')

<section class="blog-hero" style="padding:60px 0 40px;">
    <div class="container" style="max-width:1160px;">
        <div class="blog-hero-content" style="max-width:600px;margin:0 auto;text-align:center;">
            <div class="blog-hero-badge"><i class="fa-solid fa-tag"></i> Tag</div>
            <h1 class="blog-hero-title" style="font-size:clamp(2rem,5vw,3rem);">#{{ $tag->name }}</h1>
            <p class="blog-hero-sub">{{ $posts->total() }} article{{ $posts->total() !== 1 ? 's' : '' }} tagged with this topic</p>
        </div>
    </div>
</section>

<section class="section-sm" style="padding-top:0;">
    <div class="container" style="max-width:1160px;">
        <nav class="blog-breadcrumb" style="margin-bottom:24px;">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('blog.index') }}">Blog</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>#{{ $tag->name }}</span>
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
                                @if($post->category)
                                <a href="{{ route('blog.category', $post->category->slug) }}" class="blog-cat-pill blog-cat-pill-sm" style="background:{{ $post->category->color }}22;color:{{ $post->category->color }};border-color:{{ $post->category->color }}44;">{{ $post->category->name }}</a>
                                @endif
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
                    <div class="blog-empty-icon"><i class="fa-solid fa-tag"></i></div>
                    <h3>No articles with this tag yet</h3>
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
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($allTags->count())
                <div class="blog-widget">
                    <h4 class="blog-widget-title"><i class="fa-solid fa-tags"></i> All Topics</h4>
                    <div class="blog-tag-cloud">
                        @foreach($allTags as $t)
                        <a href="{{ route('blog.tag', $t->slug) }}" class="blog-tag-chip {{ $t->slug === $tag->slug ? 'blog-tag-chip-active' : '' }}">{{ $t->name }}</a>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </div>
    </div>
</section>
@endsection
