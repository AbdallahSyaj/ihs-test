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
        Schema::create('admin_security_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_admin_id')->nullable()->index();
            $table->unsignedBigInteger('subject_admin_id')->nullable()->index();
            $table->string('action', 64);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_security_audits');
    }
};
