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
        Schema::table('proposal_partner', function (Blueprint $table) {
            $table->integer('order')->after('partner_id')->nullable();

            // Add the 'assignments' column for a short description
            $table->string('assignments', 255)->after('order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_partner', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropColumn('assignments');
        });
    }
};
