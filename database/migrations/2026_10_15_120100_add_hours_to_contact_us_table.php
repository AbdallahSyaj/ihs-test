<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Existing contact_us rows receive NULL for hours.
     */
    public function up(): void
    {
        Schema::table('contact_us', function (Blueprint $table) {
            $table->text('hours')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Dropping the column discards any saved business hours.
     */
    public function down(): void
    {
        Schema::table('contact_us', function (Blueprint $table) {
            $table->dropColumn('hours');
        });
    }
};
