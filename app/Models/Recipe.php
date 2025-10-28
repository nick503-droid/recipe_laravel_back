<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipe extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'short_description',
        'long_description',
        'image_url',
        'ingredients',
        'steps',
        'difficulty',
        'prep_time',
        'servings',
        'calories',
        'proteins',
        'fats',
        'carbohydrates',
        'fiber',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'steps' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }
}
