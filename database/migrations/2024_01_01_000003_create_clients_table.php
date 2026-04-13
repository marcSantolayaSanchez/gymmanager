<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->decimal('weight', 5, 2)->nullable();   // kg
            $table->decimal('height', 5, 2)->nullable();   // cm
            $table->date('membership_starts_at')->nullable();
            $table->date('membership_expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
