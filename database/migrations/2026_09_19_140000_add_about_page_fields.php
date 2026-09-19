<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timeline_events', function (Blueprint $table) {
            $table->string('kicker', 80)->nullable()->after('year');
            $table->string('meta', 160)->nullable()->after('body');
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->string('quote', 300)->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('timeline_events', fn (Blueprint $table) => $table->dropColumn(['kicker', 'meta']));
        Schema::table('team_members', fn (Blueprint $table) => $table->dropColumn('quote'));
    }
};
