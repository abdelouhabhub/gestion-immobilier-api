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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['Appartement', 'Villa', 'Terrain', 'Studio', 'Duplex']);
            $table->integer('rooms')->nullable();
            $table->decimal('surface', 10, 2);
            $table->decimal('price', 15, 2);
            $table->string('city');
            $table->string('neighborhood')->nullable();
            $table->text('description');
            $table->enum('status', ['disponible', 'vendu', 'location']);
            $table->boolean('published')->default(false);
            $table->string('title')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
