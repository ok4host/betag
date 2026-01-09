<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\User;
use App\Models\Lead;
use App\Models\Article;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalProperties = Property::count();
        $activeProperties = Property::where('status', 'active')->count();
        $featuredProperties = Property::where('is_featured', true)->count();

        $totalLeads = Lead::count();
        $newLeads = Lead::where('status', 'new')->count();
        $convertedLeads = Lead::where('status', 'converted')->count();

        $totalUsers = User::count();
        $agents = User::where('role', 'agent')->count();

        $totalArticles = Article::count();
        $publishedArticles = Article::where('status', 'published')->count();

        // Calculate trends (last 30 days vs previous 30 days)
        $recentLeads = Lead::where('created_at', '>=', now()->subDays(30))->count();
        $previousLeads = Lead::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();
        $leadsTrend = $previousLeads > 0 ? (($recentLeads - $previousLeads) / $previousLeads) * 100 : 0;

        $recentProperties = Property::where('created_at', '>=', now()->subDays(30))->count();
        $previousProperties = Property::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();
        $propertiesTrend = $previousProperties > 0 ? (($recentProperties - $previousProperties) / $previousProperties) * 100 : 0;

        return [
            Stat::make('إجمالي العقارات', $totalProperties)
                ->description($activeProperties . ' عقار نشط')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([7, 3, 4, 5, 6, 3, 5, $recentProperties])
                ->color($propertiesTrend >= 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('العقارات المميزة', $featuredProperties)
                ->description('من إجمالي ' . $totalProperties)
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('العملاء المحتملين', $totalLeads)
                ->description($newLeads . ' عميل جديد')
                ->descriptionIcon('heroicon-m-user-plus')
                ->chart([2, 4, 6, 3, 5, 7, 4, $recentLeads])
                ->color($leadsTrend >= 0 ? 'success' : 'danger'),

            Stat::make('العملاء المحولين', $convertedLeads)
                ->description(($totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100) : 0) . '% نسبة التحويل')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('المستخدمين', $totalUsers)
                ->description($agents . ' وكيل عقاري')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('المقالات', $totalArticles)
                ->description($publishedArticles . ' مقال منشور')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),
        ];
    }
}
