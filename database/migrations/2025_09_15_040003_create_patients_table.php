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
        Schema::create('patients', function (Blueprint $table) {
            $table->id('POID'); // Primary key with auto-increment 
            $table->string('RegNo', 50)->nullable();
            $table->string('Pname', 50)->nullable();
            $table->string('Paddress', 200)->nullable();
            $table->string('Pcontact', 50)->nullable();
            $table->string('Pgender', 50)->nullable();
            $table->string('Page', 50)->nullable();
            $table->integer('DrOID')->nullable();
            $table->string('Tital', 50)->nullable();
            $table->binary('photo')->nullable();
            $table->integer('MemberID')->nullable();
            $table->string('AdharNo', 50)->nullable();
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
