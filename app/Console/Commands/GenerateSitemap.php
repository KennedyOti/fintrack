<?php

namespace App\Console\Commands;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature   = 'sitemap:generate';
    protected $description = 'Generate the public XML sitemap for SEO';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        // ── Static public pages ──────────────────────────────────────────
        $sitemap->add(
            Url::create('/')
                ->setPriority(1.0)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setLastModificationDate(Carbon::now())
        );

        foreach ([
            '/about'        => [0.8, Url::CHANGE_FREQUENCY_MONTHLY],
            '/products'     => [0.8, Url::CHANGE_FREQUENCY_MONTHLY],
            '/how-it-works' => [0.8, Url::CHANGE_FREQUENCY_MONTHLY],
            '/free-docs'    => [0.9, Url::CHANGE_FREQUENCY_WEEKLY],
        ] as $path => [$priority, $freq]) {
            $sitemap->add(
                Url::create($path)
                    ->setPriority($priority)
                    ->setChangeFrequency($freq)
                    ->setLastModificationDate(Carbon::now())
            );
        }

        // Free Docs builder pages (no auth required, indexable tools)
        foreach (['invoice', 'quote', 'receipt'] as $type) {
            $sitemap->add(
                Url::create("/free-docs/builder/{$type}")
                    ->setPriority(0.7)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setLastModificationDate(Carbon::now())
            );
        }

        // ── Blog index ───────────────────────────────────────────────────
        $sitemap->add(
            Url::create('/blog')
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setLastModificationDate(Carbon::now())
        );

        // ── Published blog posts ─────────────────────────────────────────
        BlogPost::published()
            ->select('slug', 'updated_at', 'published_at')
            ->orderByDesc('published_at')
            ->each(function (BlogPost $post) use ($sitemap) {
                $sitemap->add(
                    Url::create("/blog/{$post->slug}")
                        ->setPriority(0.8)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setLastModificationDate($post->updated_at ?? $post->published_at)
                );
            });

        // ── Blog categories ──────────────────────────────────────────────
        BlogCategory::query()
            ->select('slug', 'updated_at')
            ->each(function (BlogCategory $category) use ($sitemap) {
                $sitemap->add(
                    Url::create("/blog/category/{$category->slug}")
                        ->setPriority(0.6)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setLastModificationDate($category->updated_at)
                );
            });

        // ── Blog tags ────────────────────────────────────────────────────
        BlogTag::query()
            ->select('slug', 'updated_at')
            ->each(function (BlogTag $tag) use ($sitemap) {
                $sitemap->add(
                    Url::create("/blog/tag/{$tag->slug}")
                        ->setPriority(0.5)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setLastModificationDate($tag->updated_at)
                );
            });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated at public/sitemap.xml');

        return self::SUCCESS;
    }
}
