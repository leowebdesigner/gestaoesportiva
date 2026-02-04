<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('x_authorization_tokens', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('name');
            $table->json('abilities')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('token');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('x_authorization_tokens');
    }
};
