<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'model',
    ];

    public function systems()
    {
        return $this->belongsToMany(IntegrationSystem::class)
            ->withPivot('compatible_model')
            ->withTimestamps();
    }
}
