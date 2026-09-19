<?php

use App\Http\Controllers\Admin;
use App\Models\User;

/*
 * Content screens in the admin panel. Each entry registers resource routes
 * (admin.{slug}.*) and a sidebar link. Roles: who may use it (admins always can).
 */
return [
    'resources' => [
        // slug => [controller, sidebar group, sidebar label, roles]
        'industries' => [Admin\IndustryController::class, 'Site content', 'Industries', [User::ROLE_EDITOR]],
        'projects' => [Admin\ProjectController::class, 'Site content', 'Our work', [User::ROLE_EDITOR]],
        'clients' => [Admin\ClientController::class, 'Site content', 'Clients', [User::ROLE_EDITOR]],
        'testimonials' => [Admin\TestimonialController::class, 'Site content', 'Testimonials', [User::ROLE_EDITOR]],
        'machines' => [Admin\MachineController::class, 'Site content', 'Machine park', [User::ROLE_EDITOR]],
        'export-countries' => [Admin\ExportCountryController::class, 'Site content', 'Export countries', [User::ROLE_EDITOR]],

        'timeline' => [Admin\TimelineEventController::class, 'About us', 'Our story timeline', [User::ROLE_EDITOR]],
        'team' => [Admin\TeamMemberController::class, 'About us', 'Team', [User::ROLE_EDITOR]],
        'jobs' => [Admin\JobOpeningController::class, 'About us', 'Careers', [User::ROLE_EDITOR]],

        'posts' => [Admin\PostController::class, 'Resources', 'Insights', [User::ROLE_EDITOR]],
        'post-categories' => [Admin\PostCategoryController::class, 'Resources', 'Blog categories', [User::ROLE_EDITOR]],
        'downloads' => [Admin\DownloadController::class, 'Resources', 'Downloads', [User::ROLE_EDITOR]],
        'faqs' => [Admin\FaqController::class, 'Resources', 'FAQs', [User::ROLE_EDITOR]],
        'glossary' => [Admin\GlossaryTermController::class, 'Resources', 'Glossary', [User::ROLE_EDITOR]],

        'product-types' => [Admin\ProductTypeController::class, 'Portfolio filters', 'Product types', [User::ROLE_EDITOR]],
        'technologies' => [Admin\TechnologyController::class, 'Portfolio filters', 'Technologies', [User::ROLE_EDITOR]],

        'redirects' => [Admin\RedirectController::class, 'Administration', 'Redirects', [User::ROLE_ADMIN]],
        'users' => [Admin\UserController::class, 'Administration', 'Staff accounts', [User::ROLE_ADMIN]],
    ],
];
