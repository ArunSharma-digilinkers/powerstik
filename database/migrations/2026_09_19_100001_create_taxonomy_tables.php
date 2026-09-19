<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->string('hero_image')->nullable();
            $table->string('card_image')->nullable();
            $table->text('challenges')->nullable();
            $table->text('what_we_supply')->nullable();
            $table->text('compliance_notes')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Portfolio filter facets
        foreach (['product_types', 'technologies'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->unsignedInteger('sort')->default(0);
                $table->timestamps();
            });
        }

        Schema::create('export_countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('iso2', 2)->unique();
            $table->decimal('lat', 8, 5)->nullable();
            $table->decimal('lng', 8, 5)->nullable();
            $table->string('blurb', 500)->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_countries');
        Schema::dropIfExists('technologies');
        Schema::dropIfExists('product_types');
        Schema::dropIfExists('industries');
    }
};
