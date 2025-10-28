<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->name,
            'descripcion_corta' => $this->short_description,
            'descripcion_larga' => $this->long_description,
            'imagen_url' => $this->image_url,
            'ingredientes' => $this->ingredients,
            'pasos' => $this->steps,
            'dificultad' => $this->difficulty,
            'tiempo_preparacion' => $this->prep_time,
            'porciones' => $this->servings,
            'info_nutricional' => [
                'calorias' => $this->calories,
                'proteinas' => $this->proteins,
                'grasas' => $this->fats,
                'carbohidratos' => $this->carbohydrates,
                'fibra' => $this->fiber,
            ],
            // Incluye la categoría relacionada, usando su propio Resource
            'categoria' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
