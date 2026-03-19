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
        Schema::create('skls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();            
            $table->string('letter_number'); // Nomor Surat
            $table->enum('status', ['Lulus', 'Tidak Lulus']);
            $table->date('letter_date'); // Tanggal SKL
            $table->dateTime('published_at'); // Waktu Open
            $table->boolean('is_questionnaire_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skls');
    }
};
