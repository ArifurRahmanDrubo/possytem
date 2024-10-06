<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class purchaseProduct extends Model
{
    protected $fillable = ['purchase_id', 'product_id','user_id', 'qty'];

    function product():BelongsTo{
        return $this->belongsTo(Product::class);
    }
}