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
        Schema::table('goals', function (Blueprint $table) {
            $table->integer('daily_progress')->nullable();
            $table->double('current_progress')->default(0);
            $table->timestamp('progress_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn('current_progress');
            $table->dropColumn('daily_progress');
            $table->dropColumn('progress_date');
        });
    }
};
