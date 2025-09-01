<?php

namespace App\Models\Registration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'date',
        'time',
    ];

    public function regcategory()
    {
        return $this->belongsTo(RegistrationCategory::class, 'category_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'regtype_id', 'product_id');
    }
}
