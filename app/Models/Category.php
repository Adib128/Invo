<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'slug'
    ];

    public function products(){
        return $this->hasMany(Product::class);
    }
}
