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
    ];

    public function invoices(){
        return $this->belongsToMany(Invoice::class);
    }
}
