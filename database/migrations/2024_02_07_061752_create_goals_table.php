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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->dateTime('baseline_date');
            $table->dateTime('deadline_date');
            $table->integer('target_value');
            $table->string('unit'); // 'Dollar' or 'Euro'
            $table->enum('status', ['Not started', 'In progress', 'Completed', 'Expired'])->default('Not started');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
