<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'إدارة العقارات';

    protected static ?string $modelLabel = 'موقع';

    protected static ?string $pluralModelLabel = 'المواقع والكمبوندات';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('البيانات الأساسية')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name_ar')
                                    ->label('الاسم بالعربية')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                                Forms\Components\TextInput::make('name_en')
                                    ->label('الاسم بالإنجليزية')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('slug')
                                    ->label('الرابط')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\Select::make('type')
                                    ->label('النوع')
                                    ->options([
                                        'city' => 'مدينة',
                                        'area' => 'منطقة',
                                        'compound' => 'كمبوند',
                                    ])
                                    ->required()
                                    ->reactive(),

                                Forms\Components\Select::make('parent_id')
                                    ->label('الموقع الأب')
                                    ->relationship('parent', 'name_ar')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('اختر الموقع الأب'),

                                Forms\Components\TextInput::make('developer')
                                    ->label('المطور العقاري')
                                    ->visible(fn ($get) => $get('type') === 'compound'),
                            ]),

                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف بالعربية')
                            ->rows(3),

                        Forms\Components\Textarea::make('description_en')
                            ->label('الوصف بالإنجليزية')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('الصورة')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('الصورة')
                            ->image()
                            ->directory('locations'),

                        Forms\Components\FileUpload::make('gallery')
                            ->label('معرض الصور')
                            ->multiple()
                            ->image()
                            ->directory('locations/gallery')
                            ->visible(fn ($get) => $get('type') === 'compound'),
                    ])->columns(2),

                Forms\Components\Section::make('الموقع الجغرافي')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('خط العرض')
                            ->numeric(),

                        Forms\Components\TextInput::make('longitude')
                            ->label('خط الطول')
                            ->numeric(),
                    ])->columns(2),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('عنوان SEO')
                            ->maxLength(70),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('وصف SEO')
                            ->maxLength(160),
                    ])->collapsible(),

                Forms\Components\Section::make('الحالة')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('نشط')
                                    ->default(true),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label('مميز')
                                    ->default(false),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular(),

                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'city' => 'info',
                        'area' => 'success',
                        'compound' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'city' => 'مدينة',
                        'area' => 'منطقة',
                        'compound' => 'كمبوند',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('parent.name_ar')
                    ->label('الموقع الأب')
                    ->sortable(),

                Tables\Columns\TextColumn::make('developer')
                    ->label('المطور')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('properties_count')
                    ->label('عدد العقارات')
                    ->counts('properties')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('مميز')
                    ->boolean(),
            ])
            ->defaultSort('name_ar')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'city' => 'مدينة',
                        'area' => 'منطقة',
                        'compound' => 'كمبوند',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('نشط'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('مميز'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
