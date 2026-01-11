@extends('admin.layouts.app')

@section('title', 'إدارة التصنيفات')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>قائمة التصنيفات</span>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> إضافة تصنيف
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>التصنيف</th>
                        <th>الاسم بالإنجليزية</th>
                        <th>عدد العقارات</th>
                        <th>الحالة</th>
                        <th>الترتيب</th>
                        <th width="150">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" width="40" height="40" class="rounded" style="object-fit: cover;">
                                @elseif($category->icon)
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                        <i class="bi bi-{{ $category->icon }}"></i>
                                    </div>
                                @endif
                                <strong>{{ $category->name_ar }}</strong>
                            </div>
                        </td>
                        <td>{{ $category->name_en ?? '-' }}</td>
                        <td><span class="badge bg-secondary">{{ $category->properties_count }}</span></td>
                        <td>
                            <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                                {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td>{{ $category->sort_order ?? 0 }}</td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
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
                        <td colspan="7" class="text-center py-4 text-muted">لا توجد تصنيفات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $categories->links() }}
    </div>
</div>
@endsection
