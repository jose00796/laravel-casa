<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'id_product';
    public $incrementing = false;

    protected $fillable = [
        'product_name',
        'cant_product',
        'mark',
        'price',
        'description',
        'company_name'
    ];
}
