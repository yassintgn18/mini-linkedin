<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_competence', function (Blueprint $table) {
            $table->unsignedBigInteger('profil_id');
            $table->unsignedBigInteger('competence_id');
            $table->enum('niveau', ['débutant', 'intermédiaire', 'expert']);
            $table->primary(['profil_id', 'competence_id']);
            $table->foreign('profil_id')->references('id')->on('profils')->onDelete('cascade');
            $table->foreign('competence_id')->references('id')->on('competences')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_competence');
    }
};