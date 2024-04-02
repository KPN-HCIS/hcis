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
        Schema::create('approval_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('approval_request_id')->unique();
            $table->json('form_data')->nullable();
            $table->uuid('approval_layer_id')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_snapshots');
    }
};
