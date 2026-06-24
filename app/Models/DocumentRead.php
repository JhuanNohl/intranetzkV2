<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRead extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'read_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
