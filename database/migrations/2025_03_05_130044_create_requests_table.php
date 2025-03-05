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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained()->onDelete('cascade');
            $table->string('request_number');
            $table->string('document_type');
            $table->string('request_reason');
            $table->string('civil_center_reference');
            $table->string('birth_act_number');
            $table->date('birth_act_creation_date');
            $table->string('declaration_by');
            $table->string('authorized_by');
            $table->string('first_name');
            $table->string('last_name');
            $table->string(column: 'gender')->default(\App\Enums\GenderType::MASCULIN);
            $table->date('birth_date');
            $table->string('birth_place');
            $table->string('father_name');
            $table->date('father_birth_date');
            $table->string('father_birth_place');
            $table->string('father_profession');
            $table->string('mother_name');
            $table->date('mother_birth_date');
            $table->string('mother_birth_place');
            $table->string('mother_profession');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
