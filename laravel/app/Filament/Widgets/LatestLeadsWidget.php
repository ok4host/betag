<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLeadsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'أحدث العملاء المحتملين';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::query()->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->copyable()
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('property.title_ar')
                    ->label('العقار')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->property?->title_ar),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'warning' => 'new',
                        'info' => 'contacted',
                        'primary' => 'interested',
                        'success' => 'converted',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'جديد',
                        'contacted' => 'تم التواصل',
                        'interested' => 'مهتم',
                        'converted' => 'محول',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('call')
                    ->label('اتصال')
                    ->icon('heroicon-m-phone')
                    ->color('success')
                    ->url(fn (Lead $record): string => "tel:{$record->phone}"),

                Tables\Actions\Action::make('whatsapp')
                    ->label('واتساب')
                    ->icon('heroicon-m-chat-bubble-left')
                    ->color('success')
                    ->url(fn (Lead $record): string => "https://wa.me/{$record->phone}"),

                Tables\Actions\Action::make('view')
                    ->label('عرض')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Lead $record): string => route('filament.admin.resources.leads.edit', $record)),
            ])
            ->emptyStateHeading('لا يوجد عملاء محتملين')
            ->emptyStateDescription('سيظهر هنا أحدث العملاء المحتملين عند إضافتهم')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
