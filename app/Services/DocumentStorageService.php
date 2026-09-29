<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentStorageService
{
    /** Whitelisted evidence types: extension => accepted MIME list. */
    private const EVIDENCE_TYPES = [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
    ];

    /**
     * Validate + store a private evidence file. Throws ValidationException on any violation.
     */
    public function storeEvidence(UploadedFile $file, Model $owner, User $uploader): Document
    {
        $errors = [];

        if ($file->getSize() > 5 * 1024 * 1024) {
            $errors['evidence'] = 'Evidence file exceeds the 5 MB limit.';
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (! array_key_exists($ext, self::EVIDENCE_TYPES)) {
            $errors['evidence'] = 'Evidence must be a PDF, DOC, DOCX, JPG or PNG file.';
        }

        // Server-detected MIME from actual file content (never trust client header).
        $detected = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file->getRealPath());
        if ($ext !== '' && ! in_array($detected, self::EVIDENCE_TYPES[$ext] ?? [], true)) {
            $errors['evidence'] = 'The file content does not match its extension (possible spoofed upload).';
        }

        // Reject anything that smells like executable/script content regardless of label.
        if (in_array($detected, ['text/x-php', 'text/html', 'application/x-httpd-php', 'text/plain'], true)) {
            $errors['evidence'] = 'Unsupported or unsafe file type detected.';
        }

        if ($errors) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }

        $path = 'achievements/'.Str::random(40).'.'.$ext;
        Storage::disk('evidence')->put($path, file_get_contents($file->getRealPath()));

        return Document::create([
            'documentable_type' => get_class($owner),
            'documentable_id' => $owner->getKey(),
            'disk' => 'evidence',
            'path' => $path,
            'original_name' => Str::limit(basename($file->getClientOriginalName()), 120, ''),
            'mime' => $detected,
            'size_bytes' => $file->getSize(),
            'uploaded_by' => $uploader->id,
            'is_private' => true,
        ]);
    }

    public function delete(Document $document): void
    {
        Storage::disk($document->disk)->delete($document->path);
        $document->delete();
    }
}
