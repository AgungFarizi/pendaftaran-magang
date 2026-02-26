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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ketua/Pengaju
            $table->foreignId('internship_period_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('department');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('proposal_file')->nullable(); // PDF file path
            $table->string('supporting_documents')->nullable(); // Additional documents
            $table->enum('status', ['pending', 'reviewed', 'forwarded', 'approved', 'rejected'])->default('pending');
            $table->text('operator_notes')->nullable();
            $table->boolean('manager_approval')->default(false);
            $table->timestamp('manager_approved_at')->nullable();
            $table->boolean('manager_dept_approval')->default(false);
            $table->timestamp('manager_dept_approved_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Index for faster queries
            $table->index(['status', 'department']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
