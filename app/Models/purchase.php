<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class purchase extends Model
{
    use HasFactory;
    protected $fillable = ['user_id',   'supplier_id',];
    function supplier(): BelongsTo
    {
        return $this->belongsTo(supplier::class);
    }
    function product(): BelongsTo
    {
        return $this->belongsTo(product::class);
    }
}
