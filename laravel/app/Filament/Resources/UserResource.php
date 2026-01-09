<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'إدارة النظام';

    protected static ?string $modelLabel = 'مستخدم';

    protected static ?string $pluralModelLabel = 'المستخدمين';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('البيانات الأساسية')
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
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->maxLength(20),

                                Forms\Components\Select::make('role')
                                    ->label('الدور')
                                    ->options([
                                        'user' => 'مستخدم عادي',
                                        'agent' => 'وكيل عقاري',
                                        'admin' => 'مدير',
                                    ])
                                    ->default('user')
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('كلمة المرور')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('تأكيد كلمة المرور')
                            ->password()
                            ->same('password')
                            ->dehydrated(false)
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])->columns(2),

                Forms\Components\Section::make('الصورة الشخصية')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('الصورة')
                            ->image()
                            ->directory('avatars')
                            ->avatar(),
                    ]),

                Forms\Components\Section::make('الحالة')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),

                        Forms\Components\Toggle::make('email_verified')
                            ->label('تم التحقق من البريد')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('الصورة')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),

                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('الدور')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'agent' => 'warning',
                        'user' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'مدير',
                        'agent' => 'وكيل',
                        'user' => 'مستخدم',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('properties_count')
                    ->label('عدد العقارات')
                    ->counts('properties')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('الدور')
                    ->options([
                        'user' => 'مستخدم عادي',
                        'agent' => 'وكيل عقاري',
                        'admin' => 'مدير',
                    ]),

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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
