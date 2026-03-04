@extends('layouts.portal')
@section('title', 'New Blog Post — FinTrack Admin')

@section('styles')
<style>
.seo-score { display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600; }
.seo-good { background:rgba(34,197,94,.15);color:#22C55E; }
.seo-ok   { background:rgba(245,158,11,.15);color:#F59E0B; }
.seo-bad  { background:rgba(244,63,94,.15);color:#F43F5E; }
.tag-pill { display:inline-flex;align-items:center;gap:4px;background:rgba(14,116,144,.2);color:#22D3EE;border:1px solid rgba(14,116,144,.4);padding:3px 10px;border-radius:20px;font-size:12px;cursor:pointer; }
.tag-pill:hover { background:rgba(244,63,94,.15);color:#F43F5E;border-color:rgba(244,63,94,.4); }
.tag-input-wrap { display:flex;flex-wrap:wrap;gap:6px;padding:8px 12px;border:1px solid var(--bs-border-color);border-radius:6px;min-height:42px;cursor:text;transition:border-color .2s; }
.tag-input-wrap:focus-within { border-color:var(--ft-teal); }
.tag-raw-input { border:none;outline:none;background:transparent;color:inherit;font-size:13px;min-width:120px;flex:1; }
.tox-tinymce { border-radius: 8px !important; border-color: var(--bs-border-color) !important; }
.char-count { font-size:11px;color:var(--bs-secondary-color);float:right; }
.char-warn  { color:var(--ft-amber); }
.char-over  { color:var(--ft-rose); }
</style>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-plus-circle me-2" style="color:var(--ft-teal);font-size:20px;"></i>New Blog Post</h1>
        <p class="page-subtitle">Create and publish a new article</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.blog.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
@csrf

<div class="row g-3">

    {{-- ── Left (main content) ── --}}
    <div class="col-xl-8">

        {{-- Title --}}
        <div class="card mb-3">
            <div class="card-body" style="padding:20px;">
                <div class="mb-3">
                    <input type="text" name="title" id="titleInput" value="{{ old('title') }}"
                           class="form-control form-control-lg fw-600" placeholder="Enter post title…"
                           style="font-size:20px;border:none;border-bottom:2px solid var(--bs-border-color);border-radius:0;padding:0 0 10px;background:transparent;"
                           required>
                    <div class="mt-1 d-flex justify-content-between align-items-center">
                        <span style="font-size:12px;color:var(--bs-secondary-color);">
                            Slug: <code id="slugPreview" style="color:var(--ft-teal);"></code>
                        </span>
                        <span class="char-count" id="titleCount">0/60</span>
                    </div>
                </div>

                {{-- Excerpt --}}
                <div>
                    <label class="form-label fw-500" style="font-size:13px;">Excerpt <span style="color:var(--bs-secondary-color);font-weight:400;">(optional — shown in listings)</span></label>
                    <textarea name="excerpt" id="excerptInput" rows="2"
                              class="form-control" placeholder="Brief description of the post…"
                              style="font-size:13px;resize:vertical;">{{ old('excerpt') }}</textarea>
                    <div class="mt-1 d-flex justify-content-end">
                        <span class="char-count" id="excerptCount">0/500</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TinyMCE Content --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;">
                <h6 class="mb-0 fw-600"><i class="fas fa-pen-nib me-2" style="color:var(--ft-teal);"></i>Content</h6>
            </div>
            <div class="card-body" style="padding:20px;">
                <textarea name="content" id="contentEditor">{{ old('content') }}</textarea>
            </div>
        </div>

        {{-- SEO Panel --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#seoPanel">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-600"><i class="fas fa-search-dollar me-2" style="color:var(--ft-emerald);"></i>SEO & Meta</h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="seo-score seo-ok" id="seoScore">Set up SEO</span>
                        <i class="fas fa-chevron-down" style="font-size:12px;color:var(--bs-secondary-color);"></i>
                    </div>
                </div>
            </div>
            <div class="collapse" id="seoPanel">
                <div class="card-body" style="padding:20px;">
                    {{-- SERP Preview --}}
                    <div class="p-3 mb-4 rounded" style="background:var(--bs-tertiary-bg);">
                        <div style="font-size:11px;color:var(--bs-secondary-color);margin-bottom:8px;font-weight:600;">SERP PREVIEW</div>
                        <div id="serpTitle" style="color:#8AB4F8;font-size:17px;font-weight:400;line-height:1.3;">{{ config('app.name') }} Blog — Your Post Title</div>
                        <div id="serpUrl" style="color:#BDC1C6;font-size:13px;margin:2px 0;">{{ url('/blog/') }}/<span id="serpSlug">your-post-slug</span></div>
                        <div id="serpDesc" style="color:#BDC1C6;font-size:13px;line-height:1.5;">Your meta description will appear here. Make it compelling to improve click-through rate.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:13px;">Focus Keyword</label>
                            <input type="text" name="focus_keyword" value="{{ old('focus_keyword') }}" class="form-control" placeholder="e.g. freelance invoice tips" style="font-size:13px;" id="focusKeyword">
                            <div class="form-text">The main keyword you want this post to rank for.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:13px;">
                                SEO Title <span class="char-count" id="metaTitleCount">0/60</span>
                            </label>
                            <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control" id="metaTitleInput"
                                   placeholder="SEO title (leave blank to use post title)" style="font-size:13px;" maxlength="160">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:13px;">
                                Meta Description <span class="char-count" id="metaDescCount">0/160</span>
                            </label>
                            <textarea name="meta_description" id="metaDescInput" rows="2" class="form-control"
                                      placeholder="Meta description for search engines (120–160 chars)" style="font-size:13px;"
                                      maxlength="320">{{ old('meta_description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:13px;">Canonical URL</label>
                            <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" class="form-control" style="font-size:13px;" placeholder="https://…">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- OG Image --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#ogPanel">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-600"><i class="fa-brands fa-facebook me-2" style="color:#1877F2;"></i>Social Preview Image (OG)</h6>
                    <i class="fas fa-chevron-down" style="font-size:12px;color:var(--bs-secondary-color);"></i>
                </div>
            </div>
            <div class="collapse" id="ogPanel">
                <div class="card-body" style="padding:20px;">
                    <label class="form-label" style="font-size:13px;">Open Graph / Social share image <span style="color:var(--bs-secondary-color);">(1200×630 px recommended)</span></label>
                    <input type="file" name="og_image" class="form-control" accept="image/*" style="font-size:13px;">
                    <div class="form-text">If not set, the featured image will be used. Max 3MB.</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Right (sidebar options) ── --}}
    <div class="col-xl-4">

        {{-- Publish Box --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;">
                <h6 class="mb-0 fw-600"><i class="fas fa-paper-plane me-2" style="color:var(--ft-teal);"></i>Publish</h6>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                <div class="mb-3">
                    <label class="form-label fw-500" style="font-size:13px;">Status</label>
                    <select name="status" id="statusSelect" class="form-select" style="font-size:13px;">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    </select>
                </div>
                <div id="scheduledWrap" style="display:none;" class="mb-3">
                    <label class="form-label fw-500" style="font-size:13px;">Schedule Date & Time</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="form-control" style="font-size:13px;">
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="featured" id="featuredCheck" value="1" {{ old('featured') ? 'checked' : '' }}>
                        <label class="form-check-label" for="featuredCheck" style="font-size:13px;">
                            <i class="fas fa-star me-1" style="color:var(--ft-amber);"></i> Mark as Featured
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="allow_comments" id="allowComments" value="1" {{ old('allow_comments', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="allowComments" style="font-size:13px;">
                            <i class="fas fa-comments me-1" style="color:var(--ft-teal);"></i> Allow Comments
                        </label>
                    </div>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-sm" style="background:var(--ft-navy);color:#fff;">
                        <i class="fas fa-save me-1"></i> Save Post
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="saveAsDraft()">
                        <i class="fas fa-file me-1"></i> Save as Draft
                    </button>
                </div>
            </div>
        </div>

        {{-- Category --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;">
                <h6 class="mb-0 fw-600"><i class="fas fa-folder me-2" style="color:var(--ft-teal);"></i>Category</h6>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                <select name="category_id" class="form-select" style="font-size:13px;">
                    <option value="">— Uncategorised —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <div class="mt-2">
                    <a href="{{ route('admin.blog.categories.create') }}" style="font-size:12px;color:var(--ft-teal);">
                        <i class="fas fa-plus me-1"></i> Add New Category
                    </a>
                </div>
            </div>
        </div>

        {{-- Tags --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;">
                <h6 class="mb-0 fw-600"><i class="fas fa-tags me-2" style="color:var(--ft-teal);"></i>Tags</h6>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                <div class="tag-input-wrap" id="tagInputWrap" onclick="document.getElementById('tagRawInput').focus()">
                    <div id="tagPills" class="d-flex flex-wrap gap-1"></div>
                    <input type="text" id="tagRawInput" class="tag-raw-input" placeholder="Add tags, press Enter…">
                </div>
                <input type="hidden" name="tags" id="tagsHidden" value="{{ old('tags') }}">
                <div class="mt-2" style="font-size:12px;color:var(--bs-secondary-color);">
                    Existing: @foreach($tags->take(10) as $t)<a href="#" class="tag-suggest" data-tag="{{ $t->name }}" style="color:var(--ft-teal);">{{ $t->name }}</a>{{ !$loop->last ? ', ' : '' }}@endforeach
                </div>
            </div>
        </div>

        {{-- Featured Image --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;">
                <h6 class="mb-0 fw-600"><i class="fas fa-image me-2" style="color:var(--ft-teal);"></i>Featured Image</h6>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                <div id="imgPreviewWrap" style="display:none;margin-bottom:12px;">
                    <img id="imgPreview" style="width:100%;border-radius:8px;object-fit:cover;max-height:160px;" alt="Preview">
                </div>
                <input type="file" name="featured_image" id="featuredImageInput" class="form-control" accept="image/*" style="font-size:13px;">
                <div class="form-text">Recommended: 1200×630 px. Max 3MB.</div>
            </div>
        </div>

    </div>
</div>

</form>

@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY') }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#contentEditor',
    height: 550,
    plugins: [
        'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image',
        'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks',
        'wordcount', 'fullscreen', 'preview', 'code', 'help'
    ],
    toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | codesample | removeformat | fullscreen preview | code help',
    content_style: 'body { font-family: Inter, sans-serif; font-size: 15px; color: #1e293b; background: #fff; line-height: 1.7; }',
    images_upload_url: '{{ route('admin.blog.upload-image') }}',
    images_upload_handler: function(blobInfo, progress) {
        return new Promise((resolve, reject) => {
            const fd = new FormData();
            fd.append('file', blobInfo.blob(), blobInfo.filename());
            fd.append('_token', '{{ csrf_token() }}');
            fetch('{{ route('admin.blog.upload-image') }}', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => { if (data.location) resolve(data.location); else reject('Upload failed'); })
                .catch(() => reject('Upload failed'));
        });
    },
    automatic_uploads: true,
    file_picker_types: 'image',
    skin: 'oxide',
    promotion: false,
    branding: false,
    resize: true,
});

// ── Slug from title ────────────────────────────────────────────────────────
const titleInput  = document.getElementById('titleInput');
const slugPreview = document.getElementById('slugPreview');
const serpTitle   = document.getElementById('serpTitle');
const serpSlug    = document.getElementById('serpSlug');
const titleCount  = document.getElementById('titleCount');

function slugify(str) {
    return str.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
}

titleInput.addEventListener('input', function() {
    const slug = slugify(this.value);
    slugPreview.textContent = slug || '…';
    serpSlug.textContent = slug || 'your-post-slug';
    serpTitle.textContent = (document.getElementById('metaTitleInput').value || this.value || 'Your Post Title') + ' — FinTrack Blog';
    const l = this.value.length;
    titleCount.textContent = l + '/60';
    titleCount.className = 'char-count' + (l > 60 ? ' char-over' : l > 50 ? ' char-warn' : '');
    updateSeoScore();
});

// ── Meta title / desc char count + SERP update ────────────────────────────
const metaTitleInput = document.getElementById('metaTitleInput');
const metaDescInput  = document.getElementById('metaDescInput');
const metaTitleCount = document.getElementById('metaTitleCount');
const metaDescCount  = document.getElementById('metaDescCount');
const serpDesc       = document.getElementById('serpDesc');

metaTitleInput.addEventListener('input', function() {
    const l = this.value.length;
    metaTitleCount.textContent = l + '/60';
    metaTitleCount.className = 'char-count' + (l > 60 ? ' char-over' : l > 50 ? ' char-warn' : '');
    serpTitle.textContent = (this.value || titleInput.value || 'Your Post Title') + ' — FinTrack Blog';
    updateSeoScore();
});

metaDescInput.addEventListener('input', function() {
    const l = this.value.length;
    metaDescCount.textContent = l + '/160';
    metaDescCount.className = 'char-count' + (l > 160 ? ' char-over' : l > 140 ? ' char-warn' : '');
    serpDesc.textContent = this.value || 'Your meta description will appear here.';
    updateSeoScore();
});

const excerptInput = document.getElementById('excerptInput');
const excerptCount = document.getElementById('excerptCount');
excerptInput.addEventListener('input', function() {
    const l = this.value.length;
    excerptCount.textContent = l + '/500';
    excerptCount.className = 'char-count' + (l > 500 ? ' char-over' : '');
});

// ── SEO Score ─────────────────────────────────────────────────────────────
function updateSeoScore() {
    const score = document.getElementById('seoScore');
    const hasTitle = metaTitleInput.value.length > 10 && metaTitleInput.value.length <= 60;
    const hasDesc  = metaDescInput.value.length >= 120 && metaDescInput.value.length <= 160;
    const hasKeyword = document.getElementById('focusKeyword').value.length > 0;
    const good = [hasTitle, hasDesc, hasKeyword].filter(Boolean).length;
    if (good === 3) { score.className = 'seo-score seo-good'; score.textContent = '✓ SEO Good'; }
    else if (good >= 1) { score.className = 'seo-score seo-ok'; score.textContent = '⚠ Needs improvement'; }
    else { score.className = 'seo-score seo-bad'; score.textContent = '✕ Poor SEO'; }
}
document.getElementById('focusKeyword').addEventListener('input', updateSeoScore);

// ── Status toggle ─────────────────────────────────────────────────────────
const statusSelect   = document.getElementById('statusSelect');
const scheduledWrap  = document.getElementById('scheduledWrap');
statusSelect.addEventListener('change', function() {
    scheduledWrap.style.display = this.value === 'scheduled' ? 'block' : 'none';
});

function saveAsDraft() {
    statusSelect.value = 'draft';
    document.getElementById('postForm').submit();
}

// ── Featured image preview ─────────────────────────────────────────────────
document.getElementById('featuredImageInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('imgPreviewWrap').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// ── Tags ──────────────────────────────────────────────────────────────────
let tags = @json(old('tags') ? explode(',', old('tags')) : []).map(t => t.trim()).filter(Boolean);
const tagsHidden  = document.getElementById('tagsHidden');
const tagPills    = document.getElementById('tagPills');
const tagRawInput = document.getElementById('tagRawInput');

function renderTags() {
    tagPills.innerHTML = '';
    tags.forEach(function(tag, i) {
        const pill = document.createElement('span');
        pill.className = 'tag-pill';
        pill.innerHTML = tag + ' <i class="fas fa-xmark" style="font-size:10px;"></i>';
        pill.querySelector('i').addEventListener('click', () => { tags.splice(i, 1); renderTags(); });
        tagPills.appendChild(pill);
    });
    tagsHidden.value = tags.join(',');
}

tagRawInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const val = this.value.trim().replace(/,$/, '');
        if (val && !tags.includes(val)) { tags.push(val); renderTags(); }
        this.value = '';
    }
    if (e.key === 'Backspace' && this.value === '' && tags.length) {
        tags.pop(); renderTags();
    }
});

document.querySelectorAll('.tag-suggest').forEach(function(a) {
    a.addEventListener('click', function(e) {
        e.preventDefault();
        const tag = this.dataset.tag;
        if (!tags.includes(tag)) { tags.push(tag); renderTags(); }
    });
});

renderTags();
</script>
@endsection
