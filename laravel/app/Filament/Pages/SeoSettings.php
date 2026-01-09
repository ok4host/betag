<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SeoSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string $view = 'filament.pages.seo-settings';

    protected static ?string $navigationLabel = 'إعدادات SEO';

    protected static ?string $title = 'إعدادات SEO';

    protected static ?string $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // General SEO
            'site_name_ar' => Setting::get('site_name_ar', 'بي تاج'),
            'site_name_en' => Setting::get('site_name_en', 'BeTaj'),
            'site_tagline_ar' => Setting::get('site_tagline_ar', 'منصة العقارات الرائدة'),
            'site_tagline_en' => Setting::get('site_tagline_en', 'Leading Real Estate Platform'),

            // Meta Tags
            'meta_description_ar' => Setting::get('meta_description_ar', ''),
            'meta_description_en' => Setting::get('meta_description_en', ''),
            'meta_keywords_ar' => Setting::get('meta_keywords_ar', ''),
            'meta_keywords_en' => Setting::get('meta_keywords_en', ''),

            // Social Media
            'og_image' => Setting::get('og_image', ''),
            'twitter_handle' => Setting::get('twitter_handle', ''),
            'facebook_url' => Setting::get('facebook_url', ''),
            'instagram_url' => Setting::get('instagram_url', ''),
            'youtube_url' => Setting::get('youtube_url', ''),
            'linkedin_url' => Setting::get('linkedin_url', ''),
            'tiktok_url' => Setting::get('tiktok_url', ''),

            // Contact Info
            'phone' => Setting::get('phone', ''),
            'whatsapp' => Setting::get('whatsapp', ''),
            'email' => Setting::get('email', ''),
            'address_ar' => Setting::get('address_ar', ''),
            'address_en' => Setting::get('address_en', ''),

            // Analytics
            'google_analytics_id' => Setting::get('google_analytics_id', ''),
            'google_tag_manager_id' => Setting::get('google_tag_manager_id', ''),
            'facebook_pixel_id' => Setting::get('facebook_pixel_id', ''),

            // Schema
            'schema_type' => Setting::get('schema_type', 'RealEstateAgent'),
            'schema_name' => Setting::get('schema_name', ''),
            'schema_logo' => Setting::get('schema_logo', ''),

            // Robots
            'robots_txt' => Setting::get('robots_txt', "User-agent: *\nAllow: /\nSitemap: " . url('/sitemap.xml')),

            // Custom Code
            'head_code' => Setting::get('head_code', ''),
            'body_code' => Setting::get('body_code', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('SEO Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('عام')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Section::make('معلومات الموقع')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('site_name_ar')
                                            ->label('اسم الموقع (عربي)')
                                            ->required(),

                                        Forms\Components\TextInput::make('site_name_en')
                                            ->label('اسم الموقع (إنجليزي)')
                                            ->required(),

                                        Forms\Components\TextInput::make('site_tagline_ar')
                                            ->label('شعار الموقع (عربي)')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('site_tagline_en')
                                            ->label('شعار الموقع (إنجليزي)')
                                            ->columnSpan(1),
                                    ]),

                                Forms\Components\Section::make('معلومات الاتصال')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label('رقم الهاتف')
                                            ->tel(),

                                        Forms\Components\TextInput::make('whatsapp')
                                            ->label('واتساب')
                                            ->tel(),

                                        Forms\Components\TextInput::make('email')
                                            ->label('البريد الإلكتروني')
                                            ->email(),

                                        Forms\Components\Textarea::make('address_ar')
                                            ->label('العنوان (عربي)')
                                            ->rows(2),

                                        Forms\Components\Textarea::make('address_en')
                                            ->label('العنوان (إنجليزي)')
                                            ->rows(2),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Meta Tags')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                Forms\Components\Section::make('الوصف التعريفي')
                                    ->columns(1)
                                    ->schema([
                                        Forms\Components\Textarea::make('meta_description_ar')
                                            ->label('وصف الموقع (عربي)')
                                            ->helperText('الوصف الذي يظهر في نتائج البحث - يفضل 150-160 حرف')
                                            ->maxLength(160)
                                            ->rows(3),

                                        Forms\Components\Textarea::make('meta_description_en')
                                            ->label('وصف الموقع (إنجليزي)')
                                            ->helperText('Site description for search results - 150-160 characters recommended')
                                            ->maxLength(160)
                                            ->rows(3),
                                    ]),

                                Forms\Components\Section::make('الكلمات المفتاحية')
                                    ->columns(1)
                                    ->schema([
                                        Forms\Components\TagsInput::make('meta_keywords_ar')
                                            ->label('الكلمات المفتاحية (عربي)')
                                            ->placeholder('أضف كلمة مفتاحية')
                                            ->helperText('الكلمات المفتاحية الرئيسية للموقع'),

                                        Forms\Components\TagsInput::make('meta_keywords_en')
                                            ->label('الكلمات المفتاحية (إنجليزي)')
                                            ->placeholder('Add keyword')
                                            ->helperText('Main keywords for the site'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('السوشيال ميديا')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\Section::make('صورة المشاركة')
                                    ->schema([
                                        Forms\Components\FileUpload::make('og_image')
                                            ->label('صورة المشاركة (OG Image)')
                                            ->image()
                                            ->directory('settings')
                                            ->helperText('الصورة التي تظهر عند مشاركة الموقع - يفضل 1200x630 بكسل'),
                                    ]),

                                Forms\Components\Section::make('روابط السوشيال ميديا')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('facebook_url')
                                            ->label('فيسبوك')
                                            ->url()
                                            ->prefix('https://'),

                                        Forms\Components\TextInput::make('instagram_url')
                                            ->label('انستجرام')
                                            ->url()
                                            ->prefix('https://'),

                                        Forms\Components\TextInput::make('twitter_handle')
                                            ->label('تويتر/X')
                                            ->prefix('@'),

                                        Forms\Components\TextInput::make('youtube_url')
                                            ->label('يوتيوب')
                                            ->url()
                                            ->prefix('https://'),

                                        Forms\Components\TextInput::make('linkedin_url')
                                            ->label('لينكد إن')
                                            ->url()
                                            ->prefix('https://'),

                                        Forms\Components\TextInput::make('tiktok_url')
                                            ->label('تيك توك')
                                            ->url()
                                            ->prefix('https://'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('التحليلات')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Section::make('Google Analytics')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('google_analytics_id')
                                            ->label('Google Analytics ID')
                                            ->placeholder('G-XXXXXXXXXX')
                                            ->helperText('معرف Google Analytics 4'),

                                        Forms\Components\TextInput::make('google_tag_manager_id')
                                            ->label('Google Tag Manager ID')
                                            ->placeholder('GTM-XXXXXXX')
                                            ->helperText('معرف Tag Manager'),
                                    ]),

                                Forms\Components\Section::make('Facebook Pixel')
                                    ->schema([
                                        Forms\Components\TextInput::make('facebook_pixel_id')
                                            ->label('Facebook Pixel ID')
                                            ->placeholder('123456789012345')
                                            ->helperText('معرف Facebook Pixel للتتبع'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Schema')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                Forms\Components\Section::make('Schema.org Markup')
                                    ->description('بيانات Schema المنظمة لتحسين ظهور الموقع في محركات البحث')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Select::make('schema_type')
                                            ->label('نوع Schema')
                                            ->options([
                                                'RealEstateAgent' => 'Real Estate Agent',
                                                'RealEstateCompany' => 'Real Estate Company',
                                                'Organization' => 'Organization',
                                                'LocalBusiness' => 'Local Business',
                                            ])
                                            ->default('RealEstateAgent'),

                                        Forms\Components\TextInput::make('schema_name')
                                            ->label('اسم المنظمة')
                                            ->helperText('الاسم الذي سيظهر في Schema'),

                                        Forms\Components\FileUpload::make('schema_logo')
                                            ->label('لوجو Schema')
                                            ->image()
                                            ->directory('settings')
                                            ->helperText('اللوجو المستخدم في Schema - يفضل مربع 512x512'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Robots')
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Forms\Components\Section::make('ملف Robots.txt')
                                    ->description('تحكم في كيفية زحف محركات البحث للموقع')
                                    ->schema([
                                        Forms\Components\Textarea::make('robots_txt')
                                            ->label('محتوى robots.txt')
                                            ->rows(10)
                                            ->extraAttributes(['style' => 'font-family: monospace'])
                                            ->helperText('هذا المحتوى سيكون متاحًا على /robots.txt'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('أكواد مخصصة')
                            ->icon('heroicon-o-code-bracket-square')
                            ->schema([
                                Forms\Components\Section::make('كود مخصص')
                                    ->description('أضف أكواد مخصصة للموقع (JavaScript, CSS, إلخ)')
                                    ->schema([
                                        Forms\Components\Textarea::make('head_code')
                                            ->label('كود في <head>')
                                            ->rows(6)
                                            ->extraAttributes(['style' => 'font-family: monospace; direction: ltr'])
                                            ->helperText('سيتم إضافة هذا الكود قبل </head>'),

                                        Forms\Components\Textarea::make('body_code')
                                            ->label('كود في <body>')
                                            ->rows(6)
                                            ->extraAttributes(['style' => 'font-family: monospace; direction: ltr'])
                                            ->helperText('سيتم إضافة هذا الكود قبل </body>'),
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
            $group = $this->getSettingGroup($key);
            Setting::set($key, $value, $group);
        }

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }

    protected function getSettingGroup(string $key): string
    {
        $groups = [
            'seo' => ['meta_description_ar', 'meta_description_en', 'meta_keywords_ar', 'meta_keywords_en', 'robots_txt'],
            'social' => ['og_image', 'twitter_handle', 'facebook_url', 'instagram_url', 'youtube_url', 'linkedin_url', 'tiktok_url'],
            'analytics' => ['google_analytics_id', 'google_tag_manager_id', 'facebook_pixel_id'],
            'schema' => ['schema_type', 'schema_name', 'schema_logo'],
            'code' => ['head_code', 'body_code'],
            'contact' => ['phone', 'whatsapp', 'email', 'address_ar', 'address_en'],
        ];

        foreach ($groups as $group => $keys) {
            if (in_array($key, $keys)) {
                return $group;
            }
        }

        return 'general';
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
