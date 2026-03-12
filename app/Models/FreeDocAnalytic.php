<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeDocAnalytic extends Model
{
    protected $fillable = [
        'session_hash',
        'ip_hash',
        'country_code',
        'country_name',
        'event_type',
        'doc_type',
        'template',
        'font',
        'currency',
        'features_used',
        'referrer_domain',
    ];

    protected $casts = [
        'features_used' => 'array',
    ];

    // ── Event type constants ──────────────────────────────────────────────────
    const EVENT_PAGE_VIEW      = 'page_view';
    const EVENT_BUILDER_OPEN   = 'builder_open';
    const EVENT_PDF_GENERATED  = 'pdf_generated';
    const EVENT_DOC_SAVED      = 'doc_saved';
    const EVENT_DOC_VIEWED     = 'doc_viewed';
    const EVENT_PDF_FROM_SHARE = 'pdf_from_share';

    public static function eventLabel(string $event): string
    {
        return match ($event) {
            self::EVENT_PAGE_VIEW      => 'Page View',
            self::EVENT_BUILDER_OPEN   => 'Builder Opened',
            self::EVENT_PDF_GENERATED  => 'PDF Downloaded',
            self::EVENT_DOC_SAVED      => 'Doc Saved/Shared',
            self::EVENT_DOC_VIEWED     => 'Shared Doc Viewed',
            self::EVENT_PDF_FROM_SHARE => 'PDF from Share',
            default                    => ucfirst(str_replace('_', ' ', $event)),
        };
    }
}
