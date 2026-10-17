<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Existing contact_us rows receive NULL for page copy fields.
     */
    public function up(): void
    {
        Schema::table('contact_us', function (Blueprint $table) {
            $table->string('page_title_en', 255)->nullable();
            $table->text('page_intro_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_us', function (Blueprint $table) {
            $table->dropColumn(['page_title_en', 'page_intro_en']);
        });
    }
};
