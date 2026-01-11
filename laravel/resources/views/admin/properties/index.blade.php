@extends('admin.layouts.app')

@section('title', 'إدارة العقارات')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>قائمة العقارات</span>
        <a href="{{ route('admin.properties.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> إضافة عقار
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">كل الأنواع</option>
                    <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>للبيع</option>
                    <option value="rent" {{ request('type') == 'rent' ? 'selected' : '' }}>للإيجار</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>مباع</option>
                    <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>مؤجر</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">كل التصنيفات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name_ar }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-search"></i> بحث
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>العقار</th>
                        <th>النوع</th>
                        <th>السعر</th>
                        <th>التصنيف</th>
                        <th>الموقع</th>
                        <th>الحالة</th>
                        <th>مميز</th>
                        <th width="150">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($properties as $property)
                    <tr>
                        <td>{{ $property->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($property->featured_image)
                                    <img src="{{ asset('storage/' . $property->featured_image) }}" width="50" height="50" class="rounded" style="object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ Str::limit($property->title_ar, 40) }}</strong>
                                    <br><small class="text-muted">{{ $property->created_at->format('Y/m/d') }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $property->type == 'sale' ? 'success' : 'info' }}">
                                {{ $property->type == 'sale' ? 'للبيع' : 'للإيجار' }}
                            </span>
                        </td>
                        <td>{{ number_format($property->price) }} ج.م</td>
                        <td>{{ $property->category->name_ar ?? '-' }}</td>
                        <td>{{ $property->location->name_ar ?? '-' }}</td>
                        <td>
                            @php
                                $statusColors = ['active' => 'success', 'pending' => 'warning', 'sold' => 'info', 'rented' => 'info', 'rejected' => 'danger'];
                                $statusNames = ['active' => 'نشط', 'pending' => 'معلق', 'sold' => 'مباع', 'rented' => 'مؤجر', 'rejected' => 'مرفوض'];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$property->status] ?? 'secondary' }}">
                                {{ $statusNames[$property->status] ?? $property->status }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.properties.toggle-featured', $property) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-{{ $property->is_featured ? 'warning' : 'outline-secondary' }}">
                                    <i class="bi bi-star{{ $property->is_featured ? '-fill' : '' }}"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.properties.edit', $property) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">لا توجد عقارات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $properties->links() }}
    </div>
</div>
@endsection
