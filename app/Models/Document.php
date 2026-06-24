<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'department_id',
        'document_category_id',
        'title',
        'slug',
        'summary',
        'content',
        'source_type',
        'document_type',
        'file_path',
        'original_filename',
        'mime_type',
        'size_bytes',
        'external_url',
        'status',
        'visibility',
        'version',
        'requires_read_confirmation',
        'is_featured',
        'created_by',
        'updated_by',
        'approved_by',
        'published_at',
        'expires_at',
        'archived_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'requires_read_confirmation' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'archived_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    public function visibleDepartments()
    {
        return $this->belongsToMany(Department::class)
            ->withPivot('permission')
            ->withTimestamps();
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function reads()
    {
        return $this->hasMany(DocumentRead::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
