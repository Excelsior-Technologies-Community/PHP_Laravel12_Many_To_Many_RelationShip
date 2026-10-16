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
        Schema::table('course_student', function (Blueprint $table) {
            $table->string('grade', 5)->nullable()->after('enrolled_at');
            $table->decimal('score', 5, 2)->nullable()->after('grade');
            $table->string('status', 20)->default('active')->after('score'); // active, completed, dropped, waitlisted
            $table->decimal('attendance_percentage', 5, 2)->default(100.00)->after('status');
            $table->timestamp('completed_at')->nullable()->after('attendance_percentage');
            $table->unsignedInteger('waitlist_position')->nullable()->after('completed_at');
            $table->text('notes')->nullable()->after('waitlist_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_student', function (Blueprint $table) {
            $table->dropColumn([
                'grade',
                'score',
                'status',
                'attendance_percentage',
                'completed_at',
                'waitlist_position',
                'notes'
            ]);
        });
    }
};
