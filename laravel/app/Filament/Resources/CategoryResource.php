<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'إدارة العقارات';

    protected static ?string $modelLabel = 'تصنيف';

    protected static ?string $pluralModelLabel = 'التصنيفات';

    protected static ?int $navigationSort = 2;

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

                                Forms\Components\TextInput::make('sort_order')
                                    ->label('الترتيب')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف بالعربية')
                            ->rows(3),

                        Forms\Components\Textarea::make('description_en')
                            ->label('الوصف بالإنجليزية')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('الصورة والأيقونة')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('الصورة')
                            ->image()
                            ->directory('categories'),

                        Forms\Components\TextInput::make('icon')
                            ->label('الأيقونة')
                            ->placeholder('fas fa-home'),
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
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
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

                Tables\Columns\TextColumn::make('slug')
                    ->label('الرابط')
                    ->searchable(),

                Tables\Columns\TextColumn::make('properties_count')
                    ->label('عدد العقارات')
                    ->counts('properties')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('نشط'),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
