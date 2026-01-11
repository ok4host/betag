@extends('admin.layouts.app')

@section('title', 'تعديل العقار')

@section('content')
<div class="card">
    <div class="card-header">تعديل العقار: {{ $property->title_ar }}</div>
    <div class="card-body">
        <form action="{{ route('admin.properties.update', $property) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">العنوان بالعربية *</label>
                        <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror" value="{{ old('title_ar', $property->title_ar) }}" required>
                        @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان بالإنجليزية</label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $property->title_en) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف بالعربية *</label>
                        <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror" rows="5" required>{{ old('description_ar', $property->description_ar) }}</textarea>
                        @error('description_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف بالإنجليزية</label>
                        <textarea name="description_en" class="form-control" rows="5">{{ old('description_en', $property->description_en) }}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">النوع *</label>
                        <select name="type" class="form-select" required>
                            <option value="sale" {{ old('type', $property->type) == 'sale' ? 'selected' : '' }}>للبيع</option>
                            <option value="rent" {{ old('type', $property->type) == 'rent' ? 'selected' : '' }}>للإيجار</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السعر *</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', $property->price) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المساحة (م²)</label>
                        <input type="number" name="area" class="form-control" value="{{ old('area', $property->area) }}">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">غرف النوم</label>
                            <input type="number" name="bedrooms" class="form-control" value="{{ old('bedrooms', $property->bedrooms) }}" min="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">الحمامات</label>
                            <input type="number" name="bathrooms" class="form-control" value="{{ old('bathrooms', $property->bathrooms) }}" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">التصنيف *</label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $property->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الموقع *</label>
                        <select name="location_id" class="form-select" required>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('location_id', $property->location_id) == $location->id ? 'selected' : '' }}>
                                    {{ $location->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الحالة *</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ old('status', $property->status) == 'pending' ? 'selected' : '' }}>معلق</option>
                            <option value="active" {{ old('status', $property->status) == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="sold" {{ old('status', $property->status) == 'sold' ? 'selected' : '' }}>مباع</option>
                            <option value="rented" {{ old('status', $property->status) == 'rented' ? 'selected' : '' }}>مؤجر</option>
                            <option value="rejected" {{ old('status', $property->status) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>
                    @if($property->featured_image)
                    <div class="mb-3">
                        <label class="form-label">الصورة الحالية</label>
                        <img src="{{ asset('storage/' . $property->featured_image) }}" class="img-thumbnail d-block" style="max-height: 150px;">
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">تغيير الصورة</label>
                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" value="1" {{ $property->is_featured ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">عقار مميز</label>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> حفظ التغييرات
                </button>
                <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
