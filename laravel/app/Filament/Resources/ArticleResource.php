<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?string $modelLabel = 'مقال';

    protected static ?string $pluralModelLabel = 'المقالات';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('البيانات الأساسية')
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

                                Forms\Components\Select::make('category_id')
                                    ->label('التصنيف')
                                    ->relationship('category', 'name_ar')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('author_id')
                                    ->label('الكاتب')
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(auth()->id()),
                            ]),

                        Forms\Components\Textarea::make('excerpt_ar')
                            ->label('المقتطف بالعربية')
                            ->rows(2),

                        Forms\Components\Textarea::make('excerpt_en')
                            ->label('المقتطف بالإنجليزية')
                            ->rows(2),

                        Forms\Components\RichEditor::make('content_ar')
                            ->label('المحتوى بالعربية')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content_en')
                            ->label('المحتوى بالإنجليزية')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('الصورة')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('صورة المقال')
                            ->image()
                            ->directory('articles')
                            ->imageEditor(),
                    ]),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('عنوان SEO')
                            ->maxLength(70),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('وصف SEO')
                            ->maxLength(160),

                        Forms\Components\TagsInput::make('tags')
                            ->label('الوسوم'),
                    ])->collapsible(),

                Forms\Components\Section::make('الحالة والنشر')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('الحالة')
                                    ->options([
                                        'draft' => 'مسودة',
                                        'published' => 'منشور',
                                    ])
                                    ->required()
                                    ->default('draft'),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('تاريخ النشر'),

                                Forms\Components\TextInput::make('views_count')
                                    ->label('المشاهدات')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),
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

                Tables\Columns\TextColumn::make('title_ar')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('category.name_ar')
                    ->label('التصنيف')
                    ->sortable(),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('الكاتب')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'منشور',
                        'draft' => 'مسودة',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('المشاهدات')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                    ]),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name_ar'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('نشر')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'published', 'published_at' => now()]))
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
