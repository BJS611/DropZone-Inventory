<x-layouts.app :title="'Audit Log — ' . config('app.name')">
    <x-page-header title="Audit Log" :subtitle="'Riwayat operasi penting'" />

    <main class="space-y-6 p-6">
        <form method="GET" :action="route('audit.index')" class="dz-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label for="dz-aud-search" class="dz-label">Cari</label>
                    <input id="dz-aud-search" type="search" name="search" value="{{ request()->string('search')->toString() }}" placeholder="Aksi atau entitas" class="dz-input" />
                </div>
                <div class="md:w-48">
                    <label for="dz-aud-action" class="dz-label">Aksi</label>
                    <input id="dz-aud-action" type="text" name="action" value="{{ request()->string('action')->toString() }}" placeholder="stock_in" class="dz-input" />
                </div>
                <button type="submit" class="dz-btn dz-btn-primary">Filter</button>
            </div>
        </form>

        @if ($logs->isEmpty())
            <x-empty-state title="Belum ada log" description="Audit log akan muncul setelah ada operasi penting." />
        @else
            <div class="dz-card overflow-x-auto">
                <table class="dz-table min-w-[760px]">
                    <thead>
                        <tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Entitas</th><th>Detail</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td class="text-xs text-ink-3">{{ $log->created_at?->format('d M Y H:i') }}</td>
                                <td class="text-ink-2">{{ $log->user?->name ?? 'Sistem' }}</td>
                                <td><span class="dz-badge dz-badge-info">{{ $log->action }}</span></td>
                                <td class="dz-mono text-xs text-ink-2">{{ $log->entity ?? '-' }}</td>
                                <td class="dz-mono text-xs text-ink-3">{{ $log->entity_id ? \Illuminate\Support\Str::limit($log->entity_id, 8) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-dz-pagination :paginator="$logs" />
        @endif
    </main>
</x-layouts.app>
