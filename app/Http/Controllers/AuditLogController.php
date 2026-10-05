<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:50'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $logs = AuditLog::query()
            ->with(['user'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where('entity', 'like', '%'.$search.'%')
                    ->orWhere('action', 'like', '%'.$search.'%');
            })
            ->when($request->filled('action'), function ($query) use ($request): void {
                $query->where('action', $request->string('action')->toString());
            })
            ->when($request->filled('from'), function ($query) use ($request): void {
                $query->whereDate('created_at', '>=', $request->string('from')->toString());
            })
            ->when($request->filled('to'), function ($query) use ($request): void {
                $query->whereDate('created_at', '<=', $request->string('to')->toString());
            })
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('audit.index', ['logs' => $logs]);
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user']);

        return view('audit.show', ['log' => $auditLog]);
    }
}
