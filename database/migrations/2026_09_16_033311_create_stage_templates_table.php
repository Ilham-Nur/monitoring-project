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
        Schema::create('stage_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('duration_days');
            $table->unsignedInteger('sort_order');
            $table->timestamps();

            $table->unique(['project_type_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_templates');
    }
};
