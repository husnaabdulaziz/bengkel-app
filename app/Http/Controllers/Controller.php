<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /** Ambil ID cabang yang sedang aktif (dari pemilih cabang di navbar), fallback ke cabang pertama user */
    protected function activeBranchId(): ?int
    {
        return session('active_branch_id') ?? auth()->user()?->branches()->value('branches.id');
    }
}