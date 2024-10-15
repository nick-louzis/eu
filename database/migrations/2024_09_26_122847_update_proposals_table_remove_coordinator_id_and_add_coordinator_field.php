<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProposalsTableRemoveCoordinatorIdAndAddCoordinatorField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proposals', function (Blueprint $table) {
            // Drop the foreign key constraints if applicable, then the fields
            if (Schema::hasColumn('proposals', 'coordinator_id')) {
                
                $table->dropColumn('coordinator_id');
            }

            if (Schema::hasColumn('proposals', 'coordinator_type')) {
                $table->dropColumn('coordinator_type');
            }

            // Add the new coordinator field
            $table->string('coordinator'); // Varchar equivalent
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proposals', function (Blueprint $table) {
            // Add back the coordinator_id and coordinator_type
            $table->unsignedBigInteger('coordinator_id')->nullable();
            $table->string('coordinator_type')->nullable();

            // Re-establish the foreign key constraint if necessary
            // Assuming coordinator_id references users table
            $table->foreign('coordinator_id')->references('id')->on('users')->onDelete('set null');

            // Remove the coordinator field
            $table->dropColumn('coordinator');
        });
    }
}
