<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Storage;

class DocumentDownloadController extends Controller
{
    public function __construct(private AuditLogger $audit)
    {
    }

    public function download(Document $document)
    {
        // Record-level authorization: uploader, related owner/creator, or admin.
        $this->authorize('download', $document);

        if (! Storage::disk($document->disk)->exists($document->path)) {
            abort(404, 'File not found.');
        }

        $this->audit->log('document.downloaded', "Downloaded evidence #{$document->id}", $document);

        return Storage::disk($document->disk)->download($document->path, $document->original_name);
    }
}
