<?php
/**
 * SEO Schema Generator - Complete Schema.org Markup
 * For dominating Arabic real estate search results
 */

class SeoSchemas {

    private static $siteUrl;
    private static $siteName;
    private static $siteLogo;
    private static $sitePhone;
    private static $siteEmail;

    public static function init() {
        $settings = getSettings();
        self::$siteUrl = rtrim(getSetting('site_url', 'https://example.com'), '/');
        self::$siteName = $settings['site_name'] ?? 'بي تاج';
        self::$siteLogo = self::$siteUrl . '/images/logo.png';
        self::$sitePhone = $settings['phone'] ?? '';
        self::$siteEmail = $settings['email'] ?? '';
    }

    /**
     * Organization Schema - يظهر في Google Knowledge Panel
     */
    public static function organization() {
        self::init();
        return [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateAgent',
            '@id' => self::$siteUrl . '/#organization',
            'name' => self::$siteName,
            'url' => self::$siteUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => self::$siteLogo,
                'width' => 300,
                'height' => 60
            ],
            'image' => self::$siteLogo,
            'description' => getSetting('site_description', 'أكبر موقع عقارات في مصر والوطن العربي'),
            'telephone' => self::$sitePhone,
            'email' => self::$siteEmail,
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'EG',
                'addressLocality' => getSetting('city', 'القاهرة'),
                'addressRegion' => getSetting('region', 'مصر')
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => getSetting('latitude', '30.0444'),
                'longitude' => getSetting('longitude', '31.2357')
            ],
            'sameAs' => array_filter([
                getSetting('facebook_url'),
                getSetting('twitter_url'),
                getSetting('instagram_url'),
                getSetting('linkedin_url'),
                getSetting('youtube_url')
            ]),
            'priceRange' => '$$',
            'openingHoursSpecification' => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'opens' => '09:00',
                'closes' => '21:00'
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'مصر'
            ]
        ];
    }

    /**
     * WebSite Schema - للظهور في Sitelinks Search Box
     */
    public static function website() {
        self::init();
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => self::$siteUrl . '/#website',
            'name' => self::$siteName,
            'url' => self::$siteUrl,
            'description' => getSetting('site_description', 'منصة عقارات شاملة'),
            'publisher' => [
                '@id' => self::$siteUrl . '/#organization'
            ],
            'inLanguage' => 'ar',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => self::$siteUrl . '/search?q={search_term_string}'
                ],
                'query-input' => 'required name=search_term_string'
            ]
        ];
    }

    /**
     * Property Schema - للعقارات
     */
    public static function property($property) {
        self::init();
        $images = [];
        if ($property['featured_image']) {
            $images[] = self::$siteUrl . '/uploads/properties/' . $property['featured_image'];
        }
        $galleryImages = json_decode($property['images'] ?? '[]', true);
        foreach ($galleryImages as $img) {
            $images[] = self::$siteUrl . '/uploads/properties/' . $img;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateListing',
            '@id' => self::$siteUrl . '/property/' . $property['slug'],
            'name' => $property['title'],
            'description' => strip_tags($property['description'] ?? ''),
            'url' => self::$siteUrl . '/property/' . $property['slug'],
            'datePosted' => date('c', strtotime($property['created_at'])),
            'image' => $images ?: [self::$siteUrl . '/images/no-image.jpg'],

            // السعر
            'offers' => [
                '@type' => 'Offer',
                'price' => $property['price'],
                'priceCurrency' => $property['currency'] ?? 'EGP',
                'availability' => $property['status'] === 'active' ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
                'validFrom' => date('c', strtotime($property['created_at']))
            ],

            // الموقع
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $property['location_name'] ?? '',
                'addressRegion' => $property['governorate_name'] ?? '',
                'addressCountry' => 'EG',
                'streetAddress' => $property['address'] ?? ''
            ],

            // المساحة
            'floorSize' => [
                '@type' => 'QuantitativeValue',
                'value' => $property['area'],
                'unitCode' => 'MTK',
                'unitText' => 'متر مربع'
            ],

            // التفاصيل
            'numberOfRooms' => (int)($property['bedrooms'] ?? 0),
            'numberOfBathroomsTotal' => (int)($property['bathrooms'] ?? 0),
            'numberOfBedrooms' => (int)($property['bedrooms'] ?? 0),

            // الناشر
            'broker' => [
                '@type' => 'RealEstateAgent',
                'name' => self::$siteName,
                'url' => self::$siteUrl
            ]
        ];

        // الإحداثيات
        if (!empty($property['latitude']) && !empty($property['longitude'])) {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $property['latitude'],
                'longitude' => $property['longitude']
            ];
        }

        // المميزات
        $features = json_decode($property['features'] ?? '[]', true);
        if (!empty($features)) {
            $featureLabels = [
                'parking' => 'موقف سيارات',
                'garden' => 'حديقة',
                'pool' => 'حمام سباحة',
                'security' => 'أمن 24 ساعة',
                'elevator' => 'مصعد',
                'ac' => 'تكييف مركزي',
                'gym' => 'صالة رياضية',
                'balcony' => 'بلكونة',
                'view' => 'إطلالة مميزة'
            ];
            $schema['amenityFeature'] = array_map(function($f) use ($featureLabels) {
                return [
                    '@type' => 'LocationFeatureSpecification',
                    'name' => $featureLabels[$f] ?? $f,
                    'value' => true
                ];
            }, $features);
        }

        return $schema;
    }

    /**
     * Product Schema - للعقارات (بديل)
     */
    public static function propertyAsProduct($property) {
        self::init();
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $property['title'],
            'description' => strip_tags($property['description'] ?? ''),
            'image' => $property['featured_image']
                ? self::$siteUrl . '/uploads/properties/' . $property['featured_image']
                : self::$siteUrl . '/images/no-image.jpg',
            'offers' => [
                '@type' => 'Offer',
                'price' => $property['price'],
                'priceCurrency' => $property['currency'] ?? 'EGP',
                'availability' => 'https://schema.org/InStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => self::$siteName
                ]
            ],
            'brand' => [
                '@type' => 'Brand',
                'name' => $property['location_name'] ?? 'مصر'
            ],
            'category' => $property['category_name'] ?? 'عقارات',
            'sku' => 'PROP-' . $property['id']
        ];
    }

    /**
     * Breadcrumb Schema
     */
    public static function breadcrumb($items) {
        self::init();
        $listItems = [];
        foreach ($items as $i => $item) {
            $listItem = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name']
            ];
            if (isset($item['url'])) {
                $listItem['item'] = $item['url'];
            }
            $listItems[] = $listItem;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems
        ];
    }

    /**
     * FAQ Schema
     */
    public static function faq($questions) {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function($q) {
                return [
                    '@type' => 'Question',
                    'name' => $q['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $q['answer']
                    ]
                ];
            }, $questions)
        ];
    }

    /**
     * Location/Area Schema
     */
    public static function location($location) {
        self::init();
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Place',
            'name' => $location['name_ar'],
            'description' => $location['description'] ?? 'منطقة سكنية في ' . $location['name_ar'],
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $location['name_ar'],
                'addressCountry' => 'EG'
            ],
            'geo' => !empty($location['latitude']) ? [
                '@type' => 'GeoCoordinates',
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude']
            ] : null,
            'containedInPlace' => [
                '@type' => 'Country',
                'name' => 'مصر'
            ]
        ];
    }

    /**
     * Search Results Schema (ItemList)
     */
    public static function searchResults($properties, $searchParams = []) {
        self::init();
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'نتائج البحث عن عقارات' . (!empty($searchParams['location']) ? ' في ' . $searchParams['location'] : ''),
            'numberOfItems' => count($properties),
            'itemListElement' => array_map(function($prop, $i) {
                return [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'url' => self::$siteUrl . '/property/' . $prop['slug'],
                    'name' => $prop['title']
                ];
            }, $properties, array_keys($properties))
        ];
    }

    /**
     * Local Business Schema
     */
    public static function localBusiness() {
        self::init();
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            '@id' => self::$siteUrl . '/#localbusiness',
            'name' => self::$siteName,
            'image' => self::$siteLogo,
            'telephone' => self::$sitePhone,
            'email' => self::$siteEmail,
            'url' => self::$siteUrl,
            'priceRange' => '$$',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'EG',
                'addressLocality' => getSetting('city', 'القاهرة')
            ],
            'openingHoursSpecification' => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'opens' => '09:00',
                'closes' => '21:00'
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => '4.8',
                'reviewCount' => '150'
            ]
        ];
    }

    /**
     * Compound/Development Schema
     */
    public static function compound($compound) {
        self::init();
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Residence',
            'name' => $compound['name_ar'],
            'description' => $compound['description'] ?? '',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $compound['city_name'] ?? '',
                'addressCountry' => 'EG'
            ],
            'geo' => !empty($compound['latitude']) ? [
                '@type' => 'GeoCoordinates',
                'latitude' => $compound['latitude'],
                'longitude' => $compound['longitude']
            ] : null,
            'amenityFeature' => [
                ['@type' => 'LocationFeatureSpecification', 'name' => 'أمن 24 ساعة'],
                ['@type' => 'LocationFeatureSpecification', 'name' => 'حدائق'],
                ['@type' => 'LocationFeatureSpecification', 'name' => 'نادي رياضي']
            ]
        ];
    }

    /**
     * Article Schema
     */
    public static function article($article) {
        self::init();
        return [
            '@context' => 'https://schema.org',
            '@type' => $article['schema_type'] ?? 'Article',
            'headline' => $article['title'],
            'description' => $article['meta_description'] ?? $article['excerpt'] ?? '',
            'image' => $article['featured_image']
                ? self::$siteUrl . '/uploads/articles/' . $article['featured_image']
                : self::$siteLogo,
            'datePublished' => date('c', strtotime($article['published_at'] ?? $article['created_at'])),
            'dateModified' => date('c', strtotime($article['updated_at'])),
            'author' => [
                '@type' => 'Person',
                'name' => $article['author_name'] ?? 'فريق التحرير'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => self::$siteName,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => self::$siteLogo
                ]
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => self::$siteUrl . '/blog/' . $article['slug']
            ],
            'wordCount' => str_word_count(strip_tags($article['content'] ?? '')),
            'articleSection' => $article['category_name'] ?? 'عقارات'
        ];
    }

    /**
     * Video Schema (for property videos)
     */
    public static function video($video) {
        self::init();
        return [
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $video['title'],
            'description' => $video['description'] ?? '',
            'thumbnailUrl' => $video['thumbnail'],
            'uploadDate' => date('c', strtotime($video['created_at'])),
            'contentUrl' => $video['url'],
            'embedUrl' => $video['embed_url'] ?? '',
            'publisher' => [
                '@type' => 'Organization',
                'name' => self::$siteName,
                'logo' => self::$siteLogo
            ]
        ];
    }

    /**
     * Helper: Output JSON-LD
     */
    public static function render($schema) {
        if (!$schema) return '';
        return '<script type="application/ld+json">' .
               json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) .
               '</script>';
    }

    /**
     * Render multiple schemas
     */
    public static function renderMultiple($schemas) {
        $output = '';
        foreach ($schemas as $schema) {
            if ($schema) {
                $output .= self::render($schema) . "\n";
            }
        }
        return $output;
    }
}

/**
 * Helper function for quick schema output
 */
function outputSchemas($schemas) {
    echo SeoSchemas::renderMultiple($schemas);
}
