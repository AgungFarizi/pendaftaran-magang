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
        Schema::create('internship_periods', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('start_registration');
            $table->date('end_registration');
            $table->date('start_internship');
            $table->date('end_internship');
            $table->json('departments'); // Array of available departments
            $table->integer('quota')->default(50);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_periods');
    }
};
