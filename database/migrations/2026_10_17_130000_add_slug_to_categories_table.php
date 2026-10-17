<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug', 255)->nullable()->after('name');
            $table->index('slug');
        });

        $activeSlugs = [];

        $rows = DB::table('categories')->orderBy('id')->get();

        foreach ($rows as $row) {
            $base = Str::slug((string) $row->name);
            if ($base === '') {
                $base = 'category';
            }

            $slug = $base;
            $isActive = $row->deleted_at === null;

            if ($isActive) {
                $suffix = 1;
                while (in_array($slug, $activeSlugs, true)) {
                    $slug = $base.'-'.$suffix;
                    $suffix++;
                }
                $activeSlugs[] = $slug;
            } elseif (in_array($slug, $activeSlugs, true)) {
                $slug = $base.'-'.$row->id;
            }

            DB::table('categories')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });
    }
};
