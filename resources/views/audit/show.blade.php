<x-layouts.app :title="'Audit Log — ' . config('app.name')">
    <x-page-header title="Detail Audit Log" :subtitle="$log->id" />

    <main class="p-6">
        <div class="dz-card max-w-3xl space-y-6 p-6">
            <div class="flex items-center gap-3">
                <span class="dz-badge dz-badge-info">{{ $log->action }}</span>
                <span class="dz-mono text-xs text-ink-3">{{ $log->id }}</span>
            </div>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="dz-overline">User</dt>
                    <dd class="mt-1 text-ink-2">{{ $log->user?->name ?? 'Sistem' }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Waktu</dt>
                    <dd class="mt-1 text-ink-2">{{ $log->created_at?->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Entitas</dt>
                    <dd class="dz-mono mt-1 text-ink-2">{{ $log->entity ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="dz-overline">Entity ID</dt>
                    <dd class="dz-mono mt-1 text-ink-2">{{ $log->entity_id ?? '-' }}</dd>
                </div>
            </dl>

            @isset($log->metadata)
                <div>
                    <dt class="dz-overline">Metadata</dt>
                    <dd class="mt-1">
                        <pre class="dz-mono overflow-x-auto border border-line bg-surface-3 p-4 text-xs text-ink-2">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </dd>
                </div>
            @endif

            <div class="flex justify-end gap-3 border-t border-line pt-6">
                <a href="{{ route('audit.index') }}" class="dz-btn dz-btn-ghost">Kembali</a>
            </div>
        </div>
    </main>
</x-layouts.app>
