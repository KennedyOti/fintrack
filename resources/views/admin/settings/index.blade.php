@extends('layouts.portal')

@section('title', 'System Settings — Admin')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">System Settings</h1>
        <p class="page-subtitle">Global platform configuration</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-xs btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Admin Home
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf

    @php
        $groupIcons = [
            'general'      => ['fas fa-sliders',          'var(--ft-teal)',   'rgba(14,116,144,.1)'],
            'security'     => ['fas fa-shield-halved',     'var(--ft-navy)',   'rgba(15,58,102,.1)'],
            'localization' => ['fas fa-earth-africa',      'var(--ft-violet)', 'rgba(139,92,246,.1)'],
        ];
        $groupLabels = [
            'general'      => 'General',
            'security'     => 'Security',
            'localization' => 'Localization',
        ];
    @endphp

    @foreach($settingGroups as $groupKey => $groupSettings)
    @php
        [$gIcon, $gColor, $gBg] = $groupIcons[$groupKey] ?? ['fas fa-gear', 'var(--ft-teal)', 'rgba(14,116,144,.1)'];
        $gLabel = $groupLabels[$groupKey] ?? ucfirst($groupKey);
    @endphp
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-header-title">
                <span class="card-header-icon" style="background:{{ $gBg }};">
                    <i class="{{ $gIcon }}" style="color:{{ $gColor }};"></i>
                </span>
                {{ $gLabel }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($groupSettings as $setting)
                <div class="col-12 {{ $setting['type'] === 'boolean' ? '' : 'col-sm-6' }}">
                    <div class="{{ $setting['type'] === 'boolean' ? 'd-flex align-items-start gap-3' : '' }}">

                        @if($setting['type'] === 'boolean')
                            {{-- Toggle switch --}}
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox"
                                       name="{{ $setting['key'] }}" id="setting_{{ $setting['key'] }}"
                                       {{ $setting['value'] == '1' ? 'checked' : '' }}
                                       style="width:40px;height:22px;">
                            </div>
                            <div>
                                <label class="form-label mb-0" for="setting_{{ $setting['key'] }}"
                                       style="font-weight:600;cursor:pointer;">
                                    {{ $setting['label'] }}
                                </label>
                                @if($setting['description'])
                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
                                    {{ $setting['description'] }}
                                </div>
                                @endif
                            </div>

                        @elseif($setting['type'] === 'integer')
                            <label class="form-label" for="setting_{{ $setting['key'] }}">
                                {{ $setting['label'] }}
                            </label>
                            <input type="number" class="form-control form-control-sm"
                                   id="setting_{{ $setting['key'] }}" name="{{ $setting['key'] }}"
                                   value="{{ old($setting['key'], $setting['value']) }}" min="0">
                            @if($setting['description'])
                            <div class="form-text">{{ $setting['description'] }}</div>
                            @endif

                        @else
                            <label class="form-label" for="setting_{{ $setting['key'] }}">
                                {{ $setting['label'] }}
                            </label>
                            @if(strlen($setting['value'] ?? '') > 80)
                                <textarea class="form-control form-control-sm" rows="2"
                                          id="setting_{{ $setting['key'] }}" name="{{ $setting['key'] }}">{{ old($setting['key'], $setting['value']) }}</textarea>
                            @else
                                <input type="text" class="form-control form-control-sm"
                                       id="setting_{{ $setting['key'] }}" name="{{ $setting['key'] }}"
                                       value="{{ old($setting['key'], $setting['value']) }}">
                            @endif
                            @if($setting['description'])
                            <div class="form-text">{{ $setting['description'] }}</div>
                            @endif
                        @endif

                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    {{-- Sticky Save Bar --}}
    <div style="position:sticky;bottom:0;background:var(--surface);border-top:1px solid var(--border);padding:12px 0;z-index:100;">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Save Settings
            </button>
        </div>
    </div>

</form>

@endsection
