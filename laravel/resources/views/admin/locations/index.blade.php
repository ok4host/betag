@extends('admin.layouts.app')

@section('title', 'إدارة المواقع')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>قائمة المواقع</span>
        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> إضافة موقع
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>الموقع</th>
                        <th>النوع</th>
                        <th>عدد العقارات</th>
                        <th>الحالة</th>
                        <th width="150">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $location)
                    <tr>
                        <td>{{ $location->id }}</td>
                        <td>
                            <strong>{{ $location->name_ar }}</strong>
                            @if($location->name_en)
                                <br><small class="text-muted">{{ $location->name_en }}</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $typeColors = ['city' => 'primary', 'area' => 'info', 'compound' => 'success'];
                                $typeNames = ['city' => 'مدينة', 'area' => 'منطقة', 'compound' => 'كمبوند'];
                            @endphp
                            <span class="badge bg-{{ $typeColors[$location->type] ?? 'secondary' }}">
                                {{ $typeNames[$location->type] ?? $location->type }}
                            </span>
                        </td>
                        <td><span class="badge bg-secondary">{{ $location->properties_count }}</span></td>
                        <td>
                            <span class="badge bg-{{ $location->is_active ? 'success' : 'danger' }}">
                                {{ $location->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
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
                        <td colspan="6" class="text-center py-4 text-muted">لا توجد مواقع</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $locations->links() }}
    </div>
</div>
@endsection
