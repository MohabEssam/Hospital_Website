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
    <label class="form-label small fw-semibold">Icon Path</label>
    <input type="text" name="icon_path" class="form-control" value="{{ old('icon_path', $department->icon_path) }}" placeholder="assets/images/Department/card.png">
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Hero Image Path</label>
    <input type="text" name="hero_image_path" class="form-control" value="{{ old('hero_image_path', $department->hero_image_path) }}">
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Sidebar Image Path</label>
    <input type="text" name="sidebar_image_path" class="form-control" value="{{ old('sidebar_image_path', $department->sidebar_image_path) }}">
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
