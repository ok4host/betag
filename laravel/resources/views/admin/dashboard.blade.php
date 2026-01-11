@extends('admin.layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">إجمالي العقارات</p>
                    <h3 class="mb-0">{{ $stats['properties'] }}</h3>
                    <small class="text-success">{{ $stats['active_properties'] }} نشط</small>
                </div>
                <div class="icon primary">
                    <i class="bi bi-house-door"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">العقارات المميزة</p>
                    <h3 class="mb-0">{{ $stats['featured_properties'] }}</h3>
                </div>
                <div class="icon success">
                    <i class="bi bi-star"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">العملاء المحتملين</p>
                    <h3 class="mb-0">{{ $stats['leads'] }}</h3>
                    <small class="text-warning">{{ $stats['new_leads'] }} جديد</small>
                </div>
                <div class="icon info">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">المستخدمين</p>
                    <h3 class="mb-0">{{ $stats['users'] }}</h3>
                </div>
                <div class="icon danger">
                    <i class="bi bi-person"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Latest Properties -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>أحدث العقارات</span>
                <a href="{{ route('admin.properties.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>العقار</th>
                                <th>النوع</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestProperties as $property)
                            <tr>
                                <td>
                                    <strong>{{ Str::limit($property->title_ar, 30) }}</strong>
                                    <br><small class="text-muted">{{ $property->category->name_ar ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $property->type == 'sale' ? 'success' : 'info' }}">
                                        {{ $property->type == 'sale' ? 'للبيع' : 'للإيجار' }}
                                    </span>
                                </td>
                                <td>{{ number_format($property->price) }} ج.م</td>
                                <td>
                                    @php
                                        $statusColors = ['active' => 'success', 'pending' => 'warning', 'sold' => 'info', 'rented' => 'info', 'rejected' => 'danger'];
                                        $statusNames = ['active' => 'نشط', 'pending' => 'معلق', 'sold' => 'مباع', 'rented' => 'مؤجر', 'rejected' => 'مرفوض'];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$property->status] ?? 'secondary' }}">
                                        {{ $statusNames[$property->status] ?? $property->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">لا توجد عقارات</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Leads -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">أحدث العملاء المحتملين</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($latestLeads as $lead)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $lead->name }}</strong>
                                <br><small class="text-muted">{{ $lead->phone }}</small>
                            </div>
                            <span class="badge bg-{{ $lead->status == 'new' ? 'warning' : 'success' }}">
                                {{ $lead->status == 'new' ? 'جديد' : 'تم التواصل' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted py-4">
                        لا يوجد عملاء محتملين
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
