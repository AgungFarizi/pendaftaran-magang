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
        Schema::create('proposal_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Jika sudah punya akun
            $table->string('name');
            $table->string('nim');
            $table->string('email');
            $table->string('university');
            $table->string('phone')->nullable();
            $table->boolean('is_leader')->default(false); // Ketua kelompok
            $table->timestamps();

            // Unique constraint: satu nim hanya boleh di satu proposal
            $table->unique(['proposal_id', 'nim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_members');
    }
};
