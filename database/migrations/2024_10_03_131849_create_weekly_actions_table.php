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
        Schema::create('weekly_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('weekly_goal_id');
            $table->text('description');
            $table->timestamps();
            $table->foreign('weekly_goal_id')->references('id')->on('weekly_goals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_actions');
    }
};
