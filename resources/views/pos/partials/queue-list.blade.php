<!-- Status Tabs -->
<div class="flex gap-2 mb-4 flex-wrap">
    @php
        $tabs = [
            'all' => 'Semua',
            'draft' => 'Draft',
            'queue' => 'Antrian',
            'completed' => 'Selesai',
            'cancelled' => 'Batal',
        ];
    @endphp
    @foreach ($tabs as $key => $label)
        <button type="button" data-status="{{ $key }}"
           class="pos-tab flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium border {{ $status === $key ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100' }}">
            {{ $label }}
            <span class="text-xs px-1.5 py-0.5 rounded-full {{ $status === $key ? 'bg-white/20' : 'bg-gray-100 text-gray-500' }}">{{ $counts[$key] }}</span>
        </button>
    @endforeach
</div>

<!-- List -->
<div class="space-y-2">
    @forelse ($workOrders as $wo)
        @php
            $isPaid = $wo->stage === 'completed' && $wo->payment_status === 'paid';
            $badgeColor = match($wo->stage) {
                'draft' => 'bg-gray-100 text-gray-600',
                'queue' => 'bg-blue-100 text-blue-700',
                'completed' => 'bg-green-100 text-green-700',
                'cancelled' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-600',
            };
            $badgeLabel = $tabs[$wo->stage] ?? $wo->stage;
        @endphp
        <a href="{{ $wo->stage === 'completed' ? route('pos.invoice', $wo) : route('pos.queue.show', $wo) }}"
           class="flex items-center justify-between bg-white border border-gray-200 hover:border-orange-300 hover:shadow-sm rounded-lg p-4 transition">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                    <span class="text-gray-800 font-semibold text-sm">{{ $wo->invoice_number ?? 'DRAFT-' . $wo->id }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $badgeColor }}">{{ $badgeLabel }}</span>
                </div>
                <div class="flex items-center gap-4 text-gray-500 text-xs">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $wo->customer->nama }}
                    </span>
                    @if ($wo->customer->plat_nomor)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9M3 3l18 18"/></svg>
                            {{ $wo->customer->plat_nomor }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $wo->created_at->format('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="font-semibold text-sm {{ $isPaid ? 'text-green-600' : 'text-red-500' }}">
                    {{ $isPaid ? '' : '- ' }}Rp {{ number_format($wo->total_amount, 0, ',', '.') }}
                </span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
    @empty
        <div class="text-center text-gray-400 py-12">Tidak ada transaksi.</div>
    @endforelse
</div>

<div class="mt-4">{{ $workOrders->links() }}</div>