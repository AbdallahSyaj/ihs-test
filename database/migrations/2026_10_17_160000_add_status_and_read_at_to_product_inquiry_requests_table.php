<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_inquiry_requests', function (Blueprint $table) {
            $table->string('status', 32)->default('new')->after('items');
            $table->timestamp('read_at')->nullable()->after('status');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('product_inquiry_requests', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropColumn(['status', 'read_at']);
        });
    }
};
