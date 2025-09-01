<?php

namespace App\Models\Registration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_product',
        'name',
        'regtype_id',
        'early_bird_idr',
        'early_bird_usd',
        'early_bird_start',
        'early_bird_end',
        'regular_idr',
        'regular_usd',
        'on_site_idr',
        'on_site_usd',
        'regular_start',
        'regular_end',
        'on_site_start',
        'on_site_end',
        'quota',
        'is_active',
        'is_early_bird',
        'is_regular',
        'is_on_site',
    ];

    public function regtype()
    {
        return $this->belongsTo(RegistrationType::class, 'regtype_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

}
