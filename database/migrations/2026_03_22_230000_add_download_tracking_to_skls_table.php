<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('skls', function (Blueprint $table) {
            $table->dateTime('downloaded_at')->nullable()->after('published_at');
            $table->unsignedInteger('download_count')->default(0)->after('downloaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skls', function (Blueprint $table) {
            $table->dropColumn(['downloaded_at', 'download_count']);
        });
    }
};