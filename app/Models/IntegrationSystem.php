<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationSystem extends Model
{
    protected $fillable = [
        'name',
    ];

    public function equipment()
    {
        return $this->belongsToMany(Equipment::class)
            ->withPivot('compatible_model')
            ->withTimestamps();
    }
}
