<?php

namespace App\Services;

use App\Models\FreeDocAnalytic;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FreeDocsAnalyticsService
{
    /**
     * Track a free-docs analytics event.
     * Never throws — analytics must never break the main request flow.
     */
    public static function track(string $eventType, array $data = []): void
    {
        try {
            $request = request();
            $ip      = $request->ip();

            $geo  = self::geoLookup($ip);
            $sess = self::sessionHash();

            FreeDocAnalytic::create([
                'session_hash'   => $sess,
                'ip_hash'        => hash('sha256', $ip . config('app.key')),
                'country_code'   => $geo['country_code'],
                'country_name'   => $geo['country_name'],
                'event_type'     => $eventType,
                'doc_type'       => $data['doc_type']      ?? null,
                'template'       => $data['template']      ?? null,
                'font'           => $data['font']          ?? null,
                'currency'       => $data['currency']      ?? null,
                'features_used'  => $data['features_used'] ?? null,
                'referrer_domain'=> self::referrerDomain($request),
            ]);
        } catch (\Throwable $e) {
            Log::warning('FreeDocsAnalytics track failed: ' . $e->getMessage());
        }
    }

    /**
     * Extract feature flags and doc metadata from the raw doc array
     * (as POSTed by the JS builder).
     */
    public static function extractDocMeta(array $doc, string $type, string $template): array
    {
        $items = $doc['items'] ?? [];

        $features = [
            'tax'          => !empty($doc['tax']['enabled']),
            'discount'     => !empty($doc['discount']['enabled']),
            'shipping'     => !empty($doc['shipping']['enabled']),
            'logo'         => !empty($doc['logo']),
            'notes'        => !empty($doc['notes']['enabled']),
            'terms'        => !empty($doc['terms']['enabled']),
            'payment_info' => !empty($doc['payment_info']['enabled']),
            'item_count'   => count($items),
        ];

        return [
            'doc_type'      => $type,
            'template'      => $template,
            'font'          => $doc['font']                    ?? null,
            'currency'      => $doc['details']['currency']     ?? null,
            'features_used' => $features,
        ];
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private static function geoLookup(string $ip): array
    {
        $empty = ['country_code' => null, 'country_name' => null];

        // Skip private / loopback IPs
        if (in_array($ip, ['127.0.0.1', '::1'], true)) {
            return ['country_code' => 'XX', 'country_name' => 'Local / Dev'];
        }

        $isPublic = (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );

        if (!$isPublic) {
            return ['country_code' => 'XX', 'country_name' => 'Local / Dev'];
        }

        // Cache per hashed IP for 24 hours to respect ip-api.com rate limits
        $cacheKey = 'geo_' . md5($ip);

        return Cache::remember($cacheKey, 86400, function () use ($ip, $empty) {
            try {
                $res = Http::timeout(3)
                    ->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode");

                if ($res->successful() && $res->json('status') === 'success') {
                    return [
                        'country_code' => $res->json('countryCode'),
                        'country_name' => $res->json('country'),
                    ];
                }
            } catch (\Throwable $e) {
                Log::debug('Geo lookup failed for ' . $ip . ': ' . $e->getMessage());
            }

            return $empty;
        });
    }

    private static function sessionHash(): string
    {
        try {
            if (session()->isStarted()) {
                return hash('sha256', session()->getId() . config('app.key'));
            }
        } catch (\Throwable) {}

        // Fallback: daily stable fingerprint (no PII stored)
        return hash('sha256', request()->ip() . request()->userAgent() . date('Y-m-d') . config('app.key'));
    }

    private static function referrerDomain($request): ?string
    {
        $ref = $request->headers->get('referer');
        if (!$ref) {
            return null;
        }
        $host = parse_url($ref, PHP_URL_HOST);
        return $host ?: null;
    }
}
