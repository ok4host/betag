<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.site-settings';

    protected static ?string $navigationLabel = 'إعدادات الموقع';

    protected static ?string $title = 'إعدادات الموقع';

    protected static ?string $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // Site Info
            'site_logo' => Setting::get('site_logo', ''),
            'site_logo_dark' => Setting::get('site_logo_dark', ''),
            'site_favicon' => Setting::get('site_favicon', ''),

            // Hero Section
            'hero_title_ar' => Setting::get('hero_title_ar', 'ابحث عن عقارك المثالي'),
            'hero_title_en' => Setting::get('hero_title_en', 'Find Your Perfect Property'),
            'hero_subtitle_ar' => Setting::get('hero_subtitle_ar', ''),
            'hero_subtitle_en' => Setting::get('hero_subtitle_en', ''),
            'hero_background' => Setting::get('hero_background', ''),

            // Features
            'show_featured_properties' => Setting::get('show_featured_properties', true),
            'featured_properties_count' => Setting::get('featured_properties_count', 6),
            'show_latest_properties' => Setting::get('show_latest_properties', true),
            'latest_properties_count' => Setting::get('latest_properties_count', 8),
            'show_categories' => Setting::get('show_categories', true),
            'show_locations' => Setting::get('show_locations', true),
            'show_blog' => Setting::get('show_blog', true),
            'blog_posts_count' => Setting::get('blog_posts_count', 3),

            // Footer
            'footer_text_ar' => Setting::get('footer_text_ar', ''),
            'footer_text_en' => Setting::get('footer_text_en', ''),
            'copyright_text_ar' => Setting::get('copyright_text_ar', 'جميع الحقوق محفوظة'),
            'copyright_text_en' => Setting::get('copyright_text_en', 'All Rights Reserved'),

            // Maintenance
            'maintenance_mode' => Setting::get('maintenance_mode', false),
            'maintenance_message_ar' => Setting::get('maintenance_message_ar', 'الموقع تحت الصيانة'),
            'maintenance_message_en' => Setting::get('maintenance_message_en', 'Site Under Maintenance'),

            // Currency & Format
            'currency_ar' => Setting::get('currency_ar', 'جنيه'),
            'currency_en' => Setting::get('currency_en', 'EGP'),
            'currency_symbol' => Setting::get('currency_symbol', 'ج.م'),
            'area_unit_ar' => Setting::get('area_unit_ar', 'متر مربع'),
            'area_unit_en' => Setting::get('area_unit_en', 'sqm'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Site Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('الهوية')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make('شعارات الموقع')
                                    ->columns(3)
                                    ->schema([
                                        Forms\Components\FileUpload::make('site_logo')
                                            ->label('الشعار الرئيسي')
                                            ->image()
                                            ->directory('settings')
                                            ->helperText('الشعار للوضع الفاتح'),

                                        Forms\Components\FileUpload::make('site_logo_dark')
                                            ->label('الشعار (الوضع الداكن)')
                                            ->image()
                                            ->directory('settings')
                                            ->helperText('الشعار للوضع الداكن'),

                                        Forms\Components\FileUpload::make('site_favicon')
                                            ->label('Favicon')
                                            ->image()
                                            ->directory('settings')
                                            ->helperText('أيقونة المتصفح - 32x32 أو 64x64'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('الصفحة الرئيسية')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Section::make('قسم Hero')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('hero_title_ar')
                                            ->label('العنوان (عربي)')
                                            ->required(),

                                        Forms\Components\TextInput::make('hero_title_en')
                                            ->label('العنوان (إنجليزي)')
                                            ->required(),

                                        Forms\Components\Textarea::make('hero_subtitle_ar')
                                            ->label('النص الفرعي (عربي)')
                                            ->rows(2),

                                        Forms\Components\Textarea::make('hero_subtitle_en')
                                            ->label('النص الفرعي (إنجليزي)')
                                            ->rows(2),

                                        Forms\Components\FileUpload::make('hero_background')
                                            ->label('صورة الخلفية')
                                            ->image()
                                            ->directory('settings')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('الأقسام')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('show_featured_properties')
                                            ->label('عرض العقارات المميزة')
                                            ->default(true),

                                        Forms\Components\TextInput::make('featured_properties_count')
                                            ->label('عدد العقارات المميزة')
                                            ->numeric()
                                            ->default(6)
                                            ->minValue(1)
                                            ->maxValue(20),

                                        Forms\Components\Toggle::make('show_latest_properties')
                                            ->label('عرض أحدث العقارات')
                                            ->default(true),

                                        Forms\Components\TextInput::make('latest_properties_count')
                                            ->label('عدد أحدث العقارات')
                                            ->numeric()
                                            ->default(8)
                                            ->minValue(1)
                                            ->maxValue(20),

                                        Forms\Components\Toggle::make('show_categories')
                                            ->label('عرض التصنيفات')
                                            ->default(true),

                                        Forms\Components\Toggle::make('show_locations')
                                            ->label('عرض المواقع')
                                            ->default(true),

                                        Forms\Components\Toggle::make('show_blog')
                                            ->label('عرض المقالات')
                                            ->default(true),

                                        Forms\Components\TextInput::make('blog_posts_count')
                                            ->label('عدد المقالات')
                                            ->numeric()
                                            ->default(3)
                                            ->minValue(1)
                                            ->maxValue(10),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('التذييل')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('نصوص التذييل')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Textarea::make('footer_text_ar')
                                            ->label('نص التذييل (عربي)')
                                            ->rows(3),

                                        Forms\Components\Textarea::make('footer_text_en')
                                            ->label('نص التذييل (إنجليزي)')
                                            ->rows(3),

                                        Forms\Components\TextInput::make('copyright_text_ar')
                                            ->label('نص حقوق النشر (عربي)'),

                                        Forms\Components\TextInput::make('copyright_text_en')
                                            ->label('نص حقوق النشر (إنجليزي)'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('الوحدات')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Section::make('العملة والوحدات')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('currency_ar')
                                            ->label('اسم العملة (عربي)')
                                            ->default('جنيه'),

                                        Forms\Components\TextInput::make('currency_en')
                                            ->label('اسم العملة (إنجليزي)')
                                            ->default('EGP'),

                                        Forms\Components\TextInput::make('currency_symbol')
                                            ->label('رمز العملة')
                                            ->default('ج.م'),

                                        Forms\Components\TextInput::make('area_unit_ar')
                                            ->label('وحدة المساحة (عربي)')
                                            ->default('متر مربع'),

                                        Forms\Components\TextInput::make('area_unit_en')
                                            ->label('وحدة المساحة (إنجليزي)')
                                            ->default('sqm'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('الصيانة')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Forms\Components\Section::make('وضع الصيانة')
                                    ->description('عند تفعيل وضع الصيانة، لن يتمكن الزوار من الوصول للموقع')
                                    ->schema([
                                        Forms\Components\Toggle::make('maintenance_mode')
                                            ->label('تفعيل وضع الصيانة')
                                            ->default(false),

                                        Forms\Components\Textarea::make('maintenance_message_ar')
                                            ->label('رسالة الصيانة (عربي)')
                                            ->rows(3),

                                        Forms\Components\Textarea::make('maintenance_message_en')
                                            ->label('رسالة الصيانة (إنجليزي)')
                                            ->rows(3),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'site');
        }

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('حفظ الإعدادات')
                ->submit('save'),
        ];
    }
}
