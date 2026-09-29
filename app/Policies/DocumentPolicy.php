<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /** Private evidence may be downloaded by uploader, related achievement owner/creator, or admins. */
    public function download(User $user, Document $document): bool
    {
        if (! $document->is_private) {
            return true;
        }

        if ($user->isAdmin() || $user->id === $document->uploaded_by) {
            return true;
        }

        $parent = $document->documentable;

        if ($parent instanceof \App\Models\Achievement) {
            return $user->id === $parent->owner_user_id || $user->id === $parent->created_by;
        }

        return false;
    }
}
