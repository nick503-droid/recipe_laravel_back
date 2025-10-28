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
        Schema::create('recipes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->text('short_description');
        $table->longText('long_description');
        $table->string('image_url', 2048);
        $table->json('ingredients'); // Almacenaremos ingredientes como JSON
        $table->json('steps');       // Almacenaremos los pasos como JSON
        $table->string('difficulty'); // Fácil, Media, Difícil
        $table->string('prep_time');  // Ej: "30 minutos"
        $table->string('servings');   // Ej: "4 personas"
        $table->integer('calories')->nullable();
        $table->integer('proteins')->nullable();
        $table->integer('fats')->nullable();
        $table->integer('carbohydrates')->nullable();
        $table->integer('fiber')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
