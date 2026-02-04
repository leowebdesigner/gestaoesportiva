<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedInteger('external_id')->nullable()->unique()->index();
            $table->foreignUlid('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignUlid('visitor_team_id')->constrained('teams')->cascadeOnDelete();
            $table->unsignedSmallInteger('home_team_score')->default(0);
            $table->unsignedSmallInteger('visitor_team_score')->default(0);
            $table->unsignedSmallInteger('season')->index();
            $table->unsignedTinyInteger('period')->default(0);
            $table->string('status', 50)->index();
            $table->string('time', 20)->nullable();
            $table->boolean('postseason')->default(false)->index();
            $table->date('game_date')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
            $table->index(['season', 'game_date']);
            $table->index(['home_team_id', 'season']);
            $table->index(['visitor_team_id', 'season']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
