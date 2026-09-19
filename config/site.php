<?php

// Public site navigation. Paths follow the sitemap in docs/PLAN.md §3;
// pages that are not built yet will 404 until their designs arrive.

return [
    'nav' => [
        ['About', '/about'],
        ['Capabilities', '/capabilities'],
        ['Battery labels', '/battery-labels', 'flagship' => true],
        ['Industries', '/industries'],
        ['Our work', '/work'],
        ['Global', '/global'],
        ['Resources', '/resources'],
    ],

    'utility' => [
        ['Client login', '/client/login'],
        ['Download company profile', '/resources/downloads'],
    ],

    'footer' => [
        'Capabilities' => [
            ['Design & branding', '/capabilities/design-branding'],
            ['Label printing', '/capabilities/label-printing'],
            ['Corrugated packaging', '/capabilities/corrugated-packaging'],
            ['Machine park', '/capabilities/machine-park'],
            ['Battery labels', '/battery-labels'],
        ],
        'Company' => [
            ['Our story', '/about/our-story'],
            ['Leadership', '/about/leadership'],
            ['Quality & PDI', '/about/quality'],
            ['Global', '/global'],
            ['Careers', '/careers'],
        ],
        'Resources' => [
            ['Company profile (PDF)', '/resources/downloads'],
            ['Paper data sheets', '/resources/downloads'],
            ['Material guide', '/resources/material-guide'],
            ['FAQs', '/resources/faqs'],
            ['Client zone login', '/client/login'],
        ],
    ],

    // Conversion links used across the site
    'quote_url' => '/get-a-quote',
    'callback_url' => '/contact#callback',
    'sample_url' => '/battery-labels#sample',
    'international_url' => '/global#enquiry',
];
