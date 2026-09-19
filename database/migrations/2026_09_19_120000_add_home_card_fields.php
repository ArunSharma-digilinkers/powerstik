<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Short card copy used by the home page design.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('industries', function (Blueprint $table) {
            $table->string('note', 120)->nullable()->after('excerpt'); // e.g. "Acid & heat resistant"
        });

        Schema::table('export_countries', function (Blueprint $table) {
            $table->string('note', 120)->nullable()->after('blurb'); // e.g. "Battery labels"
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('card_tag', 120)->nullable()->after('summary');       // e.g. "Battery · Case study"
            $table->string('result_headline', 160)->nullable()->after('result'); // e.g. "Damage claims down 60%"
        });
    }

    public function down(): void
    {
        Schema::table('industries', fn (Blueprint $table) => $table->dropColumn('note'));
        Schema::table('export_countries', fn (Blueprint $table) => $table->dropColumn('note'));
        Schema::table('projects', fn (Blueprint $table) => $table->dropColumn(['card_tag', 'result_headline']));
    }
};
