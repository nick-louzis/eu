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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('submission_date');
            $table->text('description');
            $table->string('coordinator_type'); // Can be 'User' or 'Partner'
            $table->foreignId('coordinator_id'); // The ID of the coordinator (either partner or user)
            $table->json('attachments')->nullable(); 
            $table->boolean('approved')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
