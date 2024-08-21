<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade'); // Assuming users table holds students
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->enum('attendance', ['present', 'absent', 'leave']);
            $table->date('date');
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'date']); // Ensure one attendance record per day
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }

};
