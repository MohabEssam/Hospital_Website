<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Department Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Contact Email</label>
    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $department->contact_email) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Contact Phone</label>
    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $department->contact_phone) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Status</label>
    <select name="is_active" class="form-select">
      <option value="1" @selected(old('is_active', $department->is_active ?? true))>Active</option>
      <option value="0" @selected(! old('is_active', $department->is_active ?? true))>Inactive</option>
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Icon</label>
    @if($department->icon_url)
      <div class="mb-2">
        <img src="{{ $department->icon_url }}" alt="Icon" class="rounded" style="width:60px;height:60px;object-fit:cover;" onerror="this.style.display='none'">
      </div>
    @endif
    <input type="file" name="icon" class="form-control @error('icon') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
    @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Hero Image</label>
    @if($department->hero_image_url)
      <div class="mb-2">
        <img src="{{ $department->hero_image_url }}" alt="Hero" class="rounded" style="width:100px;height:60px;object-fit:cover;" onerror="this.style.display='none'">
      </div>
    @endif
    <input type="file" name="hero_image" class="form-control @error('hero_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
    @error('hero_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Sidebar Image</label>
    @if($department->sidebar_image_url)
      <div class="mb-2">
        <img src="{{ $department->sidebar_image_url }}" alt="Sidebar" class="rounded" style="width:100px;height:60px;object-fit:cover;" onerror="this.style.display='none'">
      </div>
    @endif
    <input type="file" name="sidebar_image" class="form-control @error('sidebar_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
    @error('sidebar_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="col-12">
    <label class="form-label small fw-semibold">Description</label>
    <textarea name="description" class="form-control" rows="4">{{ old('description', $department->description) }}</textarea>
  </div>
  <div class="col-12">
    <label class="form-label small fw-semibold">Services</label>
    <textarea name="services" class="form-control" rows="6" placeholder="One service per line">{{ old('services', is_array($department->services) ? implode(PHP_EOL, $department->services) : '') }}</textarea>
  </div>
</div>
