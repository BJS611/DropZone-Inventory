<?php

namespace App\Http\Middleware;

use App\Services\BorrowingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOverdueBorrowings
{
    public function __construct(private BorrowingService $borrowings) {}

    /**
     * Perbarui status peminjaman yang sudah lewat tenggat.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->borrowings->refreshOverdue();
        } catch (\Throwable $e) {
            // Jangan blokir request hanya karena update status gagal.
            report($e);
        }

        return $next($request);
    }
}
