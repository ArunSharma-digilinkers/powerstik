<?php

// Editable site settings, grouped as they appear on Admin → Settings.
// Values live in the `settings` table; `default` is used until saved.

return [
    'contact' => [
        'label' => 'Contact',
        'fields' => [
            'phone' => ['label' => 'Phone (display)', 'default' => ''],
            'email' => ['label' => 'Enquiry email', 'type' => 'email', 'default' => ''],
            'whatsapp' => ['label' => 'WhatsApp number', 'help' => 'Digits with country code, no + or spaces, e.g. 919812345678', 'default' => ''],
            'whatsapp_message' => ['label' => 'WhatsApp pre-filled message', 'default' => 'Hello Powerstik, I would like a quote.'],
            'address' => ['label' => 'Plant address', 'type' => 'textarea', 'default' => ''],
            'map_embed_url' => ['label' => 'Google Maps embed URL', 'type' => 'url', 'default' => ''],
            'lead_recipients' => ['label' => 'Lead notification emails', 'help' => 'Comma-separated', 'default' => ''],
        ],
    ],
    'stats' => [
        'label' => 'Proof strip counters',
        'fields' => [
            'since_year' => ['label' => 'Founded (year)', 'type' => 'number', 'default' => '2002'],
            'clients' => ['label' => 'Domestic clients', 'default' => '1,500+'],
            'export_countries' => ['label' => 'Export countries', 'type' => 'number', 'default' => '8'],
            'people' => ['label' => 'Team size', 'default' => '200+'],
            'dispatch_days' => ['label' => 'Dispatch window', 'default' => '2–5 days'],
            'moq' => ['label' => 'Minimum order', 'default' => '50 units'],
        ],
    ],
    'home' => [
        'label' => 'Home page',
        'fields' => [
            'founders_quote' => ['label' => "Founders' note", 'type' => 'textarea', 'help' => 'Shown beside the founders photo. Leave empty to show names only.', 'default' => ''],
        ],
    ],
    'social' => [
        'label' => 'Social links',
        'fields' => [
            'linkedin' => ['label' => 'LinkedIn', 'type' => 'url', 'default' => ''],
            'instagram' => ['label' => 'Instagram', 'type' => 'url', 'default' => ''],
            'facebook' => ['label' => 'Facebook', 'type' => 'url', 'default' => ''],
            'youtube' => ['label' => 'YouTube', 'type' => 'url', 'default' => ''],
        ],
    ],
    'seo' => [
        'label' => 'SEO & analytics',
        'fields' => [
            'seo_title' => ['label' => 'Default page title', 'default' => 'Powerstik — Labels, Printing & Corrugated Packaging'],
            'seo_description' => ['label' => 'Default meta description', 'type' => 'textarea', 'default' => 'Designed like a studio. Delivered like a factory. Battery labels, printing and corrugated packaging since 2002.'],
            'ga4_id' => ['label' => 'GA4 measurement ID', 'help' => 'e.g. G-XXXXXXX. Leave empty to disable.', 'default' => ''],
        ],
    ],
];
