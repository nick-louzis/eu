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
        Schema::table('proposals', function (Blueprint $table) {
            // Drop the existing 'coordinator' field
            $table->dropColumn('coordinator');

            // Add the polymorphic fields
            $table->unsignedBigInteger('coordinatorable_id');
            $table->string('coordinatorable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
