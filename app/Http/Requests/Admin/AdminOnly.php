<?php

namespace App\Http\Requests\Admin;

trait AdminOnly
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }
}
