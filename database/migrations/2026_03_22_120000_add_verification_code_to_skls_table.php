<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skls', function (Blueprint $table) {
            $table->string('verification_code', 20)
                ->nullable()
                ->after('letter_number')
                ->unique();
        });
    }

    public function down(): void
    {
        Schema::table('skls', function (Blueprint $table) {
            $table->dropUnique('skls_verification_code_unique');
            $table->dropColumn('verification_code');
        });
    }
};
