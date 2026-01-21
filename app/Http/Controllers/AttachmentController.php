<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Download or display an attachment.
     */
    public function show(Attachment $attachment)
    {
        $path = 'attachments/' . $attachment->filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found.');
        }

        $file = Storage::disk('public')->get($path);
        $mimeType = $attachment->mime_type;

        // For images and viewable content, display inline
        $disposition = 'inline';

        // For other files, force download
        if (!str_starts_with($mimeType, 'image/') && !str_starts_with($mimeType, 'video/')) {
            $disposition = 'attachment';
        }

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', $disposition . '; filename="' . $attachment->original_filename . '"');
    }

    /**
     * Force download an attachment.
     */
    public function download(Attachment $attachment)
    {
        $path = 'attachments/' . $attachment->filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($path, $attachment->original_filename);
    }
}

