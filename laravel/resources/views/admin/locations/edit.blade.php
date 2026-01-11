@extends('admin.layouts.app')

@section('title', 'تعديل الموقع')

@section('content')
<div class="card">
    <div class="card-header">تعديل الموقع: {{ $location->name_ar }}</div>
    <div class="card-body">
        <form action="{{ route('admin.locations.update', $location) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">الاسم بالعربية *</label>
                        <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $location->name_ar) }}" required>
                        @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">الاسم بالإنجليزية</label>
                        <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $location->name_en) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">النوع *</label>
                        <select name="type" class="form-select" required>
                            <option value="city" {{ old('type', $location->type) == 'city' ? 'selected' : '' }}>مدينة</option>
                            <option value="area" {{ old('type', $location->type) == 'area' ? 'selected' : '' }}>منطقة</option>
                            <option value="compound" {{ old('type', $location->type) == 'compound' ? 'selected' : '' }}>كمبوند</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">الموقع الأب</label>
                        <select name="parent_id" class="form-select">
                            <option value="">بدون</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('parent_id', $location->parent_id) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name_ar }} (مدينة)
                                </option>
                            @endforeach
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('parent_id', $location->parent_id) == $area->id ? 'selected' : '' }}>
                                    {{ $area->name_ar }} (منطقة)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">الوصف بالعربية</label>
                        <textarea name="description_ar" class="form-control" rows="3">{{ old('description_ar', $location->description_ar) }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">الوصف بالإنجليزية</label>
                        <textarea name="description_en" class="form-control" rows="3">{{ old('description_en', $location->description_en) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">الصورة</label>
                @if($location->image)
                    <img src="{{ asset('storage/' . $location->image) }}" class="img-thumbnail d-block mb-2" style="max-height: 100px;">
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $location->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">موقع نشط</label>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> حفظ التغييرات</button>
                <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
