@extends('admin.layouts.app')

@section('title', 'إضافة عقار جديد')

@section('content')
<div class="card">
    <div class="card-header">إضافة عقار جديد</div>
    <div class="card-body">
        <form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">العنوان بالعربية *</label>
                        <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror" value="{{ old('title_ar') }}" required>
                        @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان بالإنجليزية</label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف بالعربية *</label>
                        <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror" rows="5" required>{{ old('description_ar') }}</textarea>
                        @error('description_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف بالإنجليزية</label>
                        <textarea name="description_en" class="form-control" rows="5">{{ old('description_en') }}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">النوع *</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="sale" {{ old('type') == 'sale' ? 'selected' : '' }}>للبيع</option>
                            <option value="rent" {{ old('type') == 'rent' ? 'selected' : '' }}>للإيجار</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السعر *</label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المساحة (م²)</label>
                        <input type="number" name="area" class="form-control" value="{{ old('area') }}">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">غرف النوم</label>
                            <input type="number" name="bedrooms" class="form-control" value="{{ old('bedrooms') }}" min="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">الحمامات</label>
                            <input type="number" name="bathrooms" class="form-control" value="{{ old('bathrooms') }}" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">التصنيف *</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">اختر التصنيف</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الموقع *</label>
                        <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                            <option value="">اختر الموقع</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الحالة *</label>
                        <select name="status" class="form-select" required>
                            <option value="pending">معلق</option>
                            <option value="active">نشط</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الصورة الرئيسية</label>
                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" value="1">
                            <label class="form-check-label" for="is_featured">عقار مميز</label>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> حفظ العقار
                </button>
                <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
