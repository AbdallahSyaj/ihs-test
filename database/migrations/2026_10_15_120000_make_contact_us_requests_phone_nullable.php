<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Existing non-null phone values are left unchanged. New requests may store null.
     */
    public function up(): void
    {
        Schema::table('contact_us_requests', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Rollback requires every contact_us_requests.phone value to be non-null.
     * A row with a null phone will make this column change fail.
     */
    public function down(): void
    {
        Schema::table('contact_us_requests', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
        });
    }
};
