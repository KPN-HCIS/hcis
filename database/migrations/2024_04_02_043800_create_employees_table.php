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
        Schema::create('employees', function (Blueprint $table) {
            
            $table->string('employee_id', 25)->primary();
            $table->string('fullname', 100);
            $table->enum('gender', ['Male', 'Female']);
            $table->string('email', 100);
            $table->string('group_company', 50);
            $table->string('designation', 50);
            $table->string('job_level', 50);
            $table->string('company_name', 50);
            $table->string('work_area_code', 50);
            $table->string('office_area', 50);
            $table->string('manager_l1_id', 25);
            $table->string('manager_l2_id', 25);
            $table->enum('employee_type', ['Permanent', 'Contract', 'Probation', 'Service Bond']);
            $table->string('unit', 25);
            $table->date('date_of_joining');
            $table->uuid('users_id')->unique();
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
