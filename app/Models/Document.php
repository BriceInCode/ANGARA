<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'file_path',
        'file_type',
        'file_size',
        'checksum',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'checksum' => 'string',
    ];

    protected $dates = [
        'created_at', 'updated_at',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function verifyChecksum(string $filePath, string $checksum): bool
    {
        return hash_file('sha256', $filePath) === $checksum;
    }

    public function deleteFile()
    {
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
    }
}
