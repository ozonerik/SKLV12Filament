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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('pob'); // Tempat Lahir
            $table->date('dob');   // Tanggal Lahir
            $table->string('nis')->unique();
            $table->string('nisn')->unique();
            $table->string('father_name');
            $table->string('password');
            $table->foreignId('major_id')->constrained();
            $table->foreignId('school_year_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
