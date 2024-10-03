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
        Schema::create('monthly_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monthly_goal_id');
            $table->integer('review');//整数のみ
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('monthly_goal_id')->references('id')->on('labels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_checks');
    }
};
