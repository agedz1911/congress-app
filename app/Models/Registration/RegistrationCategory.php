<?php

namespace App\Models\Registration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'color',
    ];

    public function regtypes()
    {
        return $this->hasMany(RegistrationType::class, 'category_id');
    }
}
