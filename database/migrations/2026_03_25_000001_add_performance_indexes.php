<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // skls: query dashboard graduation & download chart sering filter by student_id + status/downloaded_at
        Schema::table('skls', function (Blueprint $table) {
            $table->index('status', 'skls_status_idx');
            $table->index('downloaded_at', 'skls_downloaded_at_idx');
            // Composite: JOIN students ON student_id + WHERE status
            $table->index(['student_id', 'status'], 'skls_student_status_idx');
            // Composite: JOIN students ON student_id + WHERE downloaded_at/verification_code
            $table->index(['student_id', 'downloaded_at'], 'skls_student_downloaded_idx');
        });

        // questions: sering di-filter WHERE type='pg' + whereHas questionnaire_id + ORDER BY order
        Schema::table('questions', function (Blueprint $table) {
            $table->index('type', 'questions_type_idx');
            $table->index('order', 'questions_order_idx');
            $table->index(['questionnaire_id', 'type'], 'questions_questionnaire_type_idx');
        });

        // answers: query distribusi jawaban filter by question_id + question_option_id NOT NULL + student_id
        Schema::table('answers', function (Blueprint $table) {
            $table->index(['question_id', 'question_option_id'], 'answers_question_option_idx');
            $table->index(['student_id', 'question_id'], 'answers_student_question_idx');
        });

        // students: sering di-filter by school_year_id (sudah ada FK index, tapi tambah composite dengan id)
        Schema::table('students', function (Blueprint $table) {
            $table->index(['school_year_id', 'id'], 'students_school_year_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('skls', function (Blueprint $table) {
            $table->dropIndex('skls_status_idx');
            $table->dropIndex('skls_downloaded_at_idx');
            $table->dropIndex('skls_student_status_idx');
            $table->dropIndex('skls_student_downloaded_idx');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('questions_type_idx');
            $table->dropIndex('questions_order_idx');
            $table->dropIndex('questions_questionnaire_type_idx');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex('answers_question_option_idx');
            $table->dropIndex('answers_student_question_idx');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_school_year_id_idx');
        });
    }
};
