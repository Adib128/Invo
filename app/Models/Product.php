<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'brand',
        'unit',
        'category_id'
    ];

    public function invoices(){
        return $this->belongsToMany(Invoice::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
