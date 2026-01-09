<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use App\Models\Category;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'إدارة العقارات';

    protected static ?string $modelLabel = 'عقار';

    protected static ?string $pluralModelLabel = 'العقارات';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Property')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('البيانات الأساسية')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('title_ar')
                                            ->label('العنوان بالعربية')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                                        Forms\Components\TextInput::make('title_en')
                                            ->label('العنوان بالإنجليزية')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('slug')
                                            ->label('الرابط')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),

                                        Forms\Components\Select::make('type')
                                            ->label('النوع')
                                            ->options([
                                                'sale' => 'للبيع',
                                                'rent' => 'للإيجار',
                                            ])
                                            ->required(),

                                        Forms\Components\Select::make('category_id')
                                            ->label('التصنيف')
                                            ->relationship('category', 'name_ar')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        Forms\Components\Select::make('location_id')
                                            ->label('الموقع')
                                            ->relationship('location', 'name_ar')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        Forms\Components\Select::make('user_id')
                                            ->label('المالك/المُعلن')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ]),

                                Forms\Components\RichEditor::make('description_ar')
                                    ->label('الوصف بالعربية')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('description_en')
                                    ->label('الوصف بالإنجليزية')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('التفاصيل والمواصفات')
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('price')
                                            ->label('السعر')
                                            ->numeric()
                                            ->required()
                                            ->prefix('EGP'),

                                        Forms\Components\TextInput::make('area')
                                            ->label('المساحة')
                                            ->numeric()
                                            ->suffix('م²'),

                                        Forms\Components\TextInput::make('bedrooms')
                                            ->label('غرف النوم')
                                            ->numeric()
                                            ->minValue(0),

                                        Forms\Components\TextInput::make('bathrooms')
                                            ->label('الحمامات')
                                            ->numeric()
                                            ->minValue(0),

                                        Forms\Components\TextInput::make('floor')
                                            ->label('الطابق')
                                            ->numeric(),

                                        Forms\Components\TextInput::make('building_year')
                                            ->label('سنة البناء')
                                            ->numeric(),

                                        Forms\Components\Select::make('finishing')
                                            ->label('التشطيب')
                                            ->options([
                                                'finished' => 'تشطيب كامل',
                                                'semi_finished' => 'نصف تشطيب',
                                                'unfinished' => 'بدون تشطيب',
                                            ]),

                                        Forms\Components\Select::make('furnishing')
                                            ->label('الفرش')
                                            ->options([
                                                'furnished' => 'مفروش',
                                                'semi_furnished' => 'نصف مفروش',
                                                'unfurnished' => 'بدون فرش',
                                            ]),
                                    ]),

                                Forms\Components\CheckboxList::make('features')
                                    ->label('المميزات')
                                    ->options([
                                        'parking' => 'موقف سيارات',
                                        'garden' => 'حديقة',
                                        'pool' => 'حمام سباحة',
                                        'gym' => 'جيم',
                                        'security' => 'أمن',
                                        'elevator' => 'مصعد',
                                        'balcony' => 'بلكونة',
                                        'ac' => 'تكييف',
                                        'kitchen' => 'مطبخ',
                                        'internet' => 'إنترنت',
                                    ])
                                    ->columns(5),
                            ]),

                        Forms\Components\Tabs\Tab::make('الصور')
                            ->schema([
                                Forms\Components\FileUpload::make('featured_image')
                                    ->label('الصورة الرئيسية')
                                    ->image()
                                    ->directory('properties')
                                    ->imageEditor(),

                                Forms\Components\FileUpload::make('gallery')
                                    ->label('معرض الصور')
                                    ->multiple()
                                    ->image()
                                    ->directory('properties/gallery')
                                    ->reorderable()
                                    ->maxFiles(10),
                            ]),

                        Forms\Components\Tabs\Tab::make('معلومات التواصل')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('owner_phone')
                                            ->label('رقم الهاتف')
                                            ->tel(),

                                        Forms\Components\TextInput::make('owner_whatsapp')
                                            ->label('واتساب')
                                            ->tel(),

                                        Forms\Components\TextInput::make('address')
                                            ->label('العنوان التفصيلي')
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('latitude')
                                            ->label('خط العرض')
                                            ->numeric(),

                                        Forms\Components\TextInput::make('longitude')
                                            ->label('خط الطول')
                                            ->numeric(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('عنوان SEO')
                                    ->maxLength(70),

                                Forms\Components\Textarea::make('meta_description')
                                    ->label('وصف SEO')
                                    ->maxLength(160),

                                Forms\Components\TagsInput::make('meta_keywords')
                                    ->label('الكلمات المفتاحية'),
                            ]),

                        Forms\Components\Tabs\Tab::make('الحالة')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('الحالة')
                                            ->options([
                                                'pending' => 'في الانتظار',
                                                'active' => 'نشط',
                                                'sold' => 'تم البيع',
                                                'rented' => 'تم التأجير',
                                                'rejected' => 'مرفوض',
                                            ])
                                            ->required()
                                            ->default('pending'),

                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('مميز')
                                            ->default(false),

                                        Forms\Components\TextInput::make('views')
                                            ->label('المشاهدات')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('الصورة')
                    ->circular(),

                Tables\Columns\TextColumn::make('title_ar')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sale' => 'success',
                        'rent' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sale' => 'للبيع',
                        'rent' => 'للإيجار',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name_ar')
                    ->label('التصنيف')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location.name_ar')
                    ->label('الموقع')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'sold' => 'info',
                        'rented' => 'info',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'pending' => 'في الانتظار',
                        'sold' => 'تم البيع',
                        'rented' => 'تم التأجير',
                        'rejected' => 'مرفوض',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('مميز')
                    ->boolean(),

                Tables\Columns\TextColumn::make('views')
                    ->label('المشاهدات')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'sale' => 'للبيع',
                        'rent' => 'للإيجار',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'في الانتظار',
                        'active' => 'نشط',
                        'sold' => 'تم البيع',
                        'rented' => 'تم التأجير',
                        'rejected' => 'مرفوض',
                    ]),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name_ar'),

                Tables\Filters\SelectFilter::make('location_id')
                    ->label('الموقع')
                    ->relationship('location', 'name_ar'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('مميز'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('تفعيل')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'active']))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('feature')
                        ->label('تمييز')
                        ->icon('heroicon-o-star')
                        ->action(fn ($records) => $records->each->update(['is_featured' => true]))
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
