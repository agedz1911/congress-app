<?php

namespace App\Models\Accommodation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_code',
        'name',
        'hotel_star',
        'distance',
        'description',
        'feature_image',
        'galleries',
        'is_active'
    ];

    protected $casts = [
        'galleries' => 'array',
    ];

    public function scopeSearch($query, $value)
    {
        $query->where('name', 'like', "%{$value}%");
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class);
    }
}
