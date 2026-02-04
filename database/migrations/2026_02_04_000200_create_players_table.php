<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedInteger('external_id')->nullable()->unique()->index();
            $table->foreignUlid('team_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->string('position', 50)->nullable()->index();
            $table->string('height', 10)->nullable();
            $table->string('weight', 10)->nullable();
            $table->string('jersey_number', 10)->nullable();
            $table->string('college')->nullable();
            $table->string('country', 100)->nullable()->index();
            $table->unsignedSmallInteger('draft_year')->nullable()->index();
            $table->unsignedTinyInteger('draft_round')->nullable();
            $table->unsignedSmallInteger('draft_number')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
            $table->index(['first_name', 'last_name']);
            $table->index(['team_id', 'is_active']);
            $table->index(['position', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
