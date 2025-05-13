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
        Schema::create('papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('file_path');
            $table->string('exam_type'); // final, resit
            $table->integer('exam_year');
            $table->string('semester'); // first, second
            $table->string('student_type'); // HND, B-Tech, Top-up
            $table->integer('level'); // 100, 200, 300, 400
            $table->text('description')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('current_version_id')->nullable(); // References the latest version
            $table->timestamps();
            $table->softDeletes(); // For archiving rather than permanent deletion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('papers');
    }
};