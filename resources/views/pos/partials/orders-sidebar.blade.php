<div class="w-full lg:w-64 shrink-0 bg-white rounded shadow p-4">
    <h3 class="font-semibold text-sm mb-3">Transaksi Belum Selesai</h3>

    @if (isset($workOrder))
        @php
            $badgeColor = $workOrder->stage === 'draft' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700';
            $badgeLabel = $workOrder->stage === 'draft' ? 'Draft' : 'Antrian';
        @endphp

        <!-- Versi mobile: ringkas 1 baris -->
        <div class="lg:hidden flex items-center justify-between gap-2 border-2 border-blue-400 bg-blue-50 rounded p-2 mb-2 text-sm">
            <span class="truncate">
                @if ($workOrder->technicianInitials())
                    <span class="font-semibold text-blue-700">{{ $workOrder->technicianInitials() }}</span> -
                @endif
                {{ $workOrder->customer->nama }} - Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}
            </span>
            <span class="text-xs px-1.5 py-0.5 rounded shrink-0 {{ $badgeColor }}">{{ $badgeLabel }}</span>
        </div>

        <!-- Versi desktop: kartu lengkap -->
        <div class="hidden lg:block border-2 border-blue-400 bg-blue-50 rounded p-2 mb-2">
            <div class="flex justify-between items-start mb-1">
                <span class="text-xs px-1.5 py-0.5 rounded {{ $badgeColor }}">{{ $badgeLabel }}</span>
                <span class="text-xs text-blue-600 font-medium">Sedang dibuka</span>
            </div>
            <div class="text-sm font-medium">
                @if ($workOrder->technicianInitials())
                    <span class="text-blue-700">{{ $workOrder->technicianInitials() }}</span> -
                @endif
                {{ $workOrder->customer->nama }}
            </div>
            <div class="text-xs text-gray-500">{{ $workOrder->customer->plat_nomor ?? '-' }}</div>
            <div class="text-xs font-semibold mt-1">Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}</div>
        </div>
    @endif

    @forelse ($otherOrders ?? $drafts ?? [] as $order)
        @php
            $badgeColor = $order->stage === 'draft' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700';
            $badgeLabel = $order->stage === 'draft' ? 'Draft' : 'Antrian';
        @endphp

        <!-- Versi mobile: ringkas 1 baris -->
        <a href="{{ route('pos.queue.show', $order) }}"
           class="lg:hidden flex items-center justify-between gap-2 border rounded p-2 mb-2 text-sm hover:border-blue-400 hover:bg-blue-50 transition">
            <span class="truncate">
                @if ($order->technicianInitials())
                    <span class="font-semibold text-blue-700">{{ $order->technicianInitials() }}</span> -
                @endif
                {{ $order->customer->nama }} - Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </span>
            <span class="text-xs px-1.5 py-0.5 rounded shrink-0 {{ $badgeColor }}">{{ $badgeLabel }}</span>
        </a>

        <!-- Versi desktop: kartu lengkap -->
        <a href="{{ route('pos.queue.show', $order) }}"
           class="hidden lg:block border rounded p-2 mb-2 hover:border-blue-400 hover:bg-blue-50 transition">
            <span class="text-xs px-1.5 py-0.5 rounded {{ $badgeColor }}">{{ $badgeLabel }}</span>
            <div class="text-sm font-medium mt-1">
                @if ($order->technicianInitials())
                    <span class="text-blue-700">{{ $order->technicianInitials() }}</span> -
                @endif
                {{ $order->customer->nama }}
            </div>
            <div class="text-xs text-gray-500">{{ $order->customer->plat_nomor ?? '-' }}</div>
            <div class="text-xs font-semibold mt-1">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
        </a>
    @empty
        @if (!isset($workOrder))
            <p class="text-xs text-gray-400">Belum ada draft.</p>
        @endif
    @endforelse
</div>