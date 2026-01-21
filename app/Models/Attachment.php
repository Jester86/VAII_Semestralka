<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'attachable_type',
        'attachable_id',
        'user_id',
    ];

    /**
     * Get the parent attachable model (Post or Message).
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded the attachment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the attachment is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if the attachment is a video/gif.
     */
    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/') || $this->mime_type === 'image/gif';
    }

    /**
     * Get the URL to the attachment.
     */
    public function getUrlAttribute(): string
    {
        return route('attachments.show', $this);
    }

    /**
     * Get the download URL for the attachment.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('attachments.download', $this);
    }

    /**
     * Get human-readable file size.
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
