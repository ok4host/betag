<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'المبيعات';

    protected static ?string $modelLabel = 'طلب';

    protected static ?string $pluralModelLabel = 'طلبات التواصل';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات العميل')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('الاسم')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20),

                                Forms\Components\Select::make('source')
                                    ->label('المصدر')
                                    ->options([
                                        'property_page' => 'صفحة العقار',
                                        'contact_page' => 'صفحة الاتصال',
                                        'compound_page' => 'صفحة الكمبوند',
                                        'whatsapp' => 'واتساب',
                                        'phone' => 'هاتف',
                                        'other' => 'أخرى',
                                    ]),
                            ]),

                        Forms\Components\Textarea::make('message')
                            ->label('الرسالة')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('العقار المرتبط')
                    ->schema([
                        Forms\Components\Select::make('property_id')
                            ->label('العقار')
                            ->relationship('property', 'title_ar')
                            ->searchable()
                            ->preload(),
                    ]),

                Forms\Components\Section::make('المتابعة')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('الحالة')
                                    ->options([
                                        'new' => 'جديد',
                                        'contacted' => 'تم التواصل',
                                        'interested' => 'مهتم',
                                        'not_interested' => 'غير مهتم',
                                        'converted' => 'تم البيع',
                                    ])
                                    ->default('new')
                                    ->required(),

                                Forms\Components\Select::make('assigned_to')
                                    ->label('مسؤول المتابعة')
                                    ->relationship('assignedUser', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات المتابعة')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('follow_up_at')
                            ->label('موعد المتابعة'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ الرقم'),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('property.title_ar')
                    ->label('العقار')
                    ->limit(20)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('source')
                    ->label('المصدر')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'property_page' => 'صفحة العقار',
                        'contact_page' => 'صفحة الاتصال',
                        'compound_page' => 'كمبوند',
                        'whatsapp' => 'واتساب',
                        'phone' => 'هاتف',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'contacted' => 'warning',
                        'interested' => 'success',
                        'not_interested' => 'danger',
                        'converted' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'جديد',
                        'contacted' => 'تم التواصل',
                        'interested' => 'مهتم',
                        'not_interested' => 'غير مهتم',
                        'converted' => 'تم البيع',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('المسؤول')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'new' => 'جديد',
                        'contacted' => 'تم التواصل',
                        'interested' => 'مهتم',
                        'not_interested' => 'غير مهتم',
                        'converted' => 'تم البيع',
                    ]),

                Tables\Filters\SelectFilter::make('source')
                    ->label('المصدر')
                    ->options([
                        'property_page' => 'صفحة العقار',
                        'contact_page' => 'صفحة الاتصال',
                        'compound_page' => 'كمبوند',
                    ]),

                Tables\Filters\Filter::make('has_property')
                    ->label('مرتبط بعقار')
                    ->query(fn ($query) => $query->whereNotNull('property_id')),
            ])
            ->actions([
                Tables\Actions\Action::make('call')
                    ->label('اتصال')
                    ->icon('heroicon-o-phone')
                    ->url(fn ($record) => 'tel:' . $record->phone)
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('whatsapp')
                    ->label('واتساب')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('success')
                    ->url(fn ($record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->phone))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_contacted')
                        ->label('تحديد كـ "تم التواصل"')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['status' => 'contacted']))
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
