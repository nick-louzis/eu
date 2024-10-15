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
        Schema::rename('parthers', 'partners');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('original_misspelling', function (Blueprint $table) {
            //
        });
    }
};
