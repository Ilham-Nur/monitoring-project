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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('client_id')->constrained('users');
            $table->foreignId('project_type_id')->constrained();
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('active');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
