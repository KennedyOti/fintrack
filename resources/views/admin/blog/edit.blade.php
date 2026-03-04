@extends('layouts.portal')
@section('title', 'Edit: ' . $blog->title . ' — FinTrack Admin')

@section('styles')
<style>
.tag-pill { display:inline-flex;align-items:center;gap:4px;background:rgba(14,116,144,.2);color:#22D3EE;border:1px solid rgba(14,116,144,.4);padding:3px 10px;border-radius:20px;font-size:12px;cursor:pointer; }
.tag-pill:hover { background:rgba(244,63,94,.15);color:#F43F5E;border-color:rgba(244,63,94,.4); }
.tag-input-wrap { display:flex;flex-wrap:wrap;gap:6px;padding:8px 12px;border:1px solid var(--bs-border-color);border-radius:6px;min-height:42px;cursor:text;transition:border-color .2s; }
.tag-input-wrap:focus-within { border-color:var(--ft-teal); }
.tag-raw-input { border:none;outline:none;background:transparent;color:inherit;font-size:13px;min-width:120px;flex:1; }
.seo-score { display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600; }
.seo-good { background:rgba(34,197,94,.15);color:#22C55E; }
.seo-ok   { background:rgba(245,158,11,.15);color:#F59E0B; }
.seo-bad  { background:rgba(244,63,94,.15);color:#F43F5E; }
.char-count { font-size:11px;color:var(--bs-secondary-color);float:right; }
.char-warn { color:var(--ft-amber); }
.char-over { color:var(--ft-rose); }
</style>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-pen me-2" style="color:var(--ft-teal);font-size:20px;"></i>Edit Post</h1>
        <p class="page-subtitle text-truncate" style="max-width:400px;">{{ $blog->title }}</p>
    </div>
    <div class="page-actions">
        @if($blog->status === 'published')
        <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-external-link-alt me-1"></i> View Post
        </a>
        @endif
        <a href="{{ route('admin.blog.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <strong>Please fix the errors:</strong>
    <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('admin.blog.update', $blog->slug) }}" method="POST" enctype="multipart/form-data" id="postForm">
@csrf @method('PUT')

<div class="row g-3">
    <div class="col-xl-8">

        {{-- Title --}}
        <div class="card mb-3">
            <div class="card-body" style="padding:20px;">
                <input type="text" name="title" value="{{ old('title', $blog->title) }}"
                       class="form-control form-control-lg fw-600" placeholder="Post title…"
                       style="font-size:20px;border:none;border-bottom:2px solid var(--bs-border-color);border-radius:0;padding:0 0 10px;background:transparent;"
                       id="titleInput" required>
                <div class="mt-1 d-flex justify-content-between">
                    <span style="font-size:12px;color:var(--bs-secondary-color);">
                        Slug: <code id="slugPreview" style="color:var(--ft-teal);">{{ $blog->slug }}</code>
                    </span>
                </div>
                <div class="mt-3">
                    <label class="form-label fw-500" style="font-size:13px;">Excerpt</label>
                    <textarea name="excerpt" rows="2" class="form-control" style="font-size:13px;resize:vertical;" id="excerptInput">{{ old('excerpt', $blog->excerpt) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;">
                <h6 class="mb-0 fw-600"><i class="fas fa-pen-nib me-2" style="color:var(--ft-teal);"></i>Content</h6>
            </div>
            <div class="card-body" style="padding:20px;">
                <textarea name="content" id="contentEditor">{{ old('content', $blog->content) }}</textarea>
            </div>
        </div>

        {{-- SEO --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#seoPanel">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-600"><i class="fas fa-search-dollar me-2" style="color:var(--ft-emerald);"></i>SEO & Meta</h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="seo-score seo-ok" id="seoScore">Checking…</span>
                        <i class="fas fa-chevron-down" style="font-size:12px;color:var(--bs-secondary-color);"></i>
                    </div>
                </div>
            </div>
            <div class="collapse show" id="seoPanel">
                <div class="card-body" style="padding:20px;">
                    <div class="p-3 mb-4 rounded" style="background:var(--bs-tertiary-bg);">
                        <div style="font-size:11px;color:var(--bs-secondary-color);margin-bottom:8px;font-weight:600;">SERP PREVIEW</div>
                        <div id="serpTitle" style="color:#8AB4F8;font-size:17px;">{{ $blog->meta_title ?: $blog->title }} — FinTrack Blog</div>
                        <div id="serpUrl" style="color:#BDC1C6;font-size:13px;">{{ url('/blog/' . $blog->slug) }}</div>
                        <div id="serpDesc" style="color:#BDC1C6;font-size:13px;line-height:1.5;">{{ $blog->meta_description ?: $blog->excerpt ?: 'No meta description set.' }}</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:13px;">Focus Keyword</label>
                            <input type="text" name="focus_keyword" value="{{ old('focus_keyword', $blog->focus_keyword) }}" class="form-control" style="font-size:13px;" id="focusKeyword">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:13px;">SEO Title <span class="char-count" id="metaTitleCount">{{ strlen($blog->meta_title ?? '') }}/60</span></label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $blog->meta_title) }}" class="form-control" id="metaTitleInput" style="font-size:13px;" maxlength="160">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:13px;">Meta Description <span class="char-count" id="metaDescCount">{{ strlen($blog->meta_description ?? '') }}/160</span></label>
                            <textarea name="meta_description" id="metaDescInput" rows="2" class="form-control" style="font-size:13px;" maxlength="320">{{ old('meta_description', $blog->meta_description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:13px;">Canonical URL</label>
                            <input type="url" name="canonical_url" value="{{ old('canonical_url', $blog->canonical_url) }}" class="form-control" style="font-size:13px;">
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
                    @if($blog->og_image)
                    <div class="mb-3">
                        <img src="{{ Storage::url($blog->og_image) }}" style="width:100%;max-height:150px;object-fit:cover;border-radius:8px;" alt="">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remove_og_image" id="removeOg" value="1">
                            <label class="form-check-label" for="removeOg" style="font-size:13px;">Remove current OG image</label>
                        </div>
                    </div>
                    @endif
                    <input type="file" name="og_image" class="form-control" accept="image/*" style="font-size:13px;">
                </div>
            </div>
        </div>

    </div>

    <div class="col-xl-4">

        {{-- Publish --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;">
                <h6 class="mb-0 fw-600"><i class="fas fa-paper-plane me-2" style="color:var(--ft-teal);"></i>Publish</h6>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                @if($blog->published_at)
                <div class="mb-3 p-2 rounded" style="background:var(--bs-tertiary-bg);font-size:12px;color:var(--bs-secondary-color);">
                    <i class="fas fa-calendar me-1"></i> Published {{ $blog->published_at->format('M j, Y \a\t H:i') }}
                </div>
                @endif
                <div class="mb-3">
                    <label class="form-label fw-500" style="font-size:13px;">Status</label>
                    <select name="status" id="statusSelect" class="form-select" style="font-size:13px;">
                        <option value="draft" {{ old('status', $blog->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $blog->status) === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="scheduled" {{ old('status', $blog->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    </select>
                </div>
                <div id="scheduledWrap" style="display:{{ old('status', $blog->status) === 'scheduled' ? 'block' : 'none' }};" class="mb-3">
                    <label class="form-label fw-500" style="font-size:13px;">Schedule Date & Time</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $blog->scheduled_at?->format('Y-m-d\TH:i')) }}" class="form-control" style="font-size:13px;">
                </div>
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="featured" id="featuredCheck" value="1" {{ old('featured', $blog->featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="featuredCheck" style="font-size:13px;"><i class="fas fa-star me-1" style="color:var(--ft-amber);"></i> Featured</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="allow_comments" id="allowComments" value="1" {{ old('allow_comments', $blog->allow_comments) ? 'checked' : '' }}>
                        <label class="form-check-label" for="allowComments" style="font-size:13px;"><i class="fas fa-comments me-1" style="color:var(--ft-teal);"></i> Allow Comments</label>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-sm" style="background:var(--ft-navy);color:#fff;">
                        <i class="fas fa-save me-1"></i> Update Post
                    </button>
                </div>

                <div class="mt-3 pt-3" style="border-top:1px solid var(--bs-border-color);">
                    <div class="d-flex justify-content-between" style="font-size:12px;color:var(--bs-secondary-color);">
                        <span><i class="fas fa-eye me-1"></i>{{ number_format($blog->views) }} views</span>
                        <span><i class="fas fa-clock me-1"></i>{{ $blog->reading_time }}m read</span>
                    </div>
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
                    <option value="{{ $cat->id }}" {{ old('category_id', $blog->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
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
                <input type="hidden" name="tags" id="tagsHidden" value="{{ old('tags', $blog->tags->pluck('name')->join(',')) }}">
                <div class="mt-2" style="font-size:12px;color:var(--bs-secondary-color);">
                    @foreach($tags->take(10) as $t)<a href="#" class="tag-suggest" data-tag="{{ $t->name }}" style="color:var(--ft-teal);">{{ $t->name }}</a>{{ !$loop->last ? ', ' : '' }}@endforeach
                </div>
            </div>
        </div>

        {{-- Featured Image --}}
        <div class="card mb-3">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;">
                <h6 class="mb-0 fw-600"><i class="fas fa-image me-2" style="color:var(--ft-teal);"></i>Featured Image</h6>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                @if($blog->featured_image)
                <div class="mb-3">
                    <img id="imgPreview" src="{{ Storage::url($blog->featured_image) }}" style="width:100%;border-radius:8px;object-fit:cover;max-height:160px;" alt="">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remove_featured_image" id="removeFeatured" value="1">
                        <label class="form-check-label" for="removeFeatured" style="font-size:13px;">Remove featured image</label>
                    </div>
                </div>
                @else
                <div id="imgPreviewWrap" style="display:none;margin-bottom:12px;">
                    <img id="imgPreview" style="width:100%;border-radius:8px;object-fit:cover;max-height:160px;" alt="">
                </div>
                @endif
                <input type="file" name="featured_image" id="featuredImageInput" class="form-control" accept="image/*" style="font-size:13px;">
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
    plugins: ['anchor','autolink','charmap','codesample','emoticons','image','link','lists','media','searchreplace','table','visualblocks','wordcount','fullscreen','preview','code','help'],
    toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | codesample | removeformat | fullscreen preview | code help',
    content_style: 'body { font-family: Inter, sans-serif; font-size: 15px; color: #1e293b; background: #fff; line-height: 1.7; }',
    images_upload_url: '{{ route('admin.blog.upload-image') }}',
    images_upload_handler: function(blobInfo, progress) {
        return new Promise((resolve, reject) => {
            const fd = new FormData();
            fd.append('file', blobInfo.blob(), blobInfo.filename());
            fd.append('_token', '{{ csrf_token() }}');
            fetch('{{ route('admin.blog.upload-image') }}', { method: 'POST', body: fd })
                .then(r => r.json()).then(data => { if (data.location) resolve(data.location); else reject('Upload failed'); })
                .catch(() => reject('Upload failed'));
        });
    },
    automatic_uploads: true,
    skin: 'oxide',
    promotion: false,
    branding: false,
    resize: true,
});

document.getElementById('statusSelect').addEventListener('change', function() {
    document.getElementById('scheduledWrap').style.display = this.value === 'scheduled' ? 'block' : 'none';
});

document.getElementById('featuredImageInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('imgPreview');
            if (preview) { preview.src = e.target.result; }
            const wrap = document.getElementById('imgPreviewWrap');
            if (wrap) wrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Tags
let tags = '{{ old('tags', $blog->tags->pluck('name')->join(',')) }}'.split(',').map(t => t.trim()).filter(Boolean);
const tagsHidden = document.getElementById('tagsHidden');
const tagPills   = document.getElementById('tagPills');
const tagRawInput= document.getElementById('tagRawInput');

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
    if (e.key === 'Backspace' && this.value === '' && tags.length) { tags.pop(); renderTags(); }
});
document.querySelectorAll('.tag-suggest').forEach(a => a.addEventListener('click', function(e) {
    e.preventDefault();
    const tag = this.dataset.tag;
    if (!tags.includes(tag)) { tags.push(tag); renderTags(); }
}));
renderTags();

// SEO
const metaTitleInput = document.getElementById('metaTitleInput');
const metaDescInput  = document.getElementById('metaDescInput');
function updateSeoScore() {
    const score = document.getElementById('seoScore');
    const hasTitle = metaTitleInput.value.length > 10 && metaTitleInput.value.length <= 60;
    const hasDesc  = metaDescInput.value.length >= 120 && metaDescInput.value.length <= 160;
    const hasKw    = document.getElementById('focusKeyword').value.length > 0;
    const good = [hasTitle, hasDesc, hasKw].filter(Boolean).length;
    if (good === 3) { score.className = 'seo-score seo-good'; score.textContent = '✓ SEO Good'; }
    else if (good >= 1) { score.className = 'seo-score seo-ok'; score.textContent = '⚠ Needs improvement'; }
    else { score.className = 'seo-score seo-bad'; score.textContent = '✕ Poor SEO'; }
}
metaTitleInput.addEventListener('input', function() {
    const l = this.value.length;
    document.getElementById('metaTitleCount').textContent = l + '/60';
    document.getElementById('serpTitle').textContent = (this.value || '{{ addslashes($blog->title) }}') + ' — FinTrack Blog';
    updateSeoScore();
});
metaDescInput.addEventListener('input', function() {
    const l = this.value.length;
    document.getElementById('metaDescCount').textContent = l + '/160';
    document.getElementById('serpDesc').textContent = this.value || 'No meta description.';
    updateSeoScore();
});
document.getElementById('focusKeyword').addEventListener('input', updateSeoScore);
updateSeoScore();
</script>
@endsection
