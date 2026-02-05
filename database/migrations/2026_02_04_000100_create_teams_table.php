<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedInteger('external_id')->nullable()->unique();
            $table->string('name')->index();
            $table->string('city')->index();
            $table->string('abbreviation', 10)->index();
            $table->string('conference', 50)->index();
            $table->string('division', 100)->index();
            $table->string('full_name');
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
            $table->index(['conference', 'division']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
