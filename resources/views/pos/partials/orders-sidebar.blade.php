<div class="card">
    <div class="card-header py-2"><h3 class="card-title" style="font-size: 0.95rem;">Transaksi Belum Selesai</h3></div>
    <div class="card-body p-2">

        @if (isset($workOrder))
            @php
                $badgeColor = $workOrder->stage === 'draft' ? 'badge-warning' : 'badge-info';
                $badgeLabel = $workOrder->stage === 'draft' ? 'Draft' : 'Antrian';
            @endphp
            <div class="border border-primary bg-light rounded p-2 mb-2">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="badge {{ $badgeColor }}">{{ $badgeLabel }}</span>
                    <small class="text-primary font-weight-bold">Sedang dibuka</small>
                </div>
                <div class="font-weight-bold small">
                    @if ($workOrder->technicianInitials())
                        <span class="text-primary">{{ $workOrder->technicianInitials() }}</span> -
                    @endif
                    {{ $workOrder->customer->nama }}
                </div>
                <div class="text-muted small">{{ $workOrder->customer->plat_nomor ?? '-' }}</div>
                <div class="small font-weight-bold mt-1">Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}</div>
            </div>
        @endif

        @forelse ($otherOrders ?? $drafts ?? [] as $order)
            @php
                $badgeColor = $order->stage === 'draft' ? 'badge-warning' : 'badge-info';
                $badgeLabel = $order->stage === 'draft' ? 'Draft' : 'Antrian';
            @endphp
            <a href="{{ route('pos.queue.show', $order) }}" class="d-block border rounded p-2 mb-2 text-decoration-none text-dark">
                <span class="badge {{ $badgeColor }}">{{ $badgeLabel }}</span>
                <div class="font-weight-bold small mt-1">
                    @if ($order->technicianInitials())
                        <span class="text-primary">{{ $order->technicianInitials() }}</span> -
                    @endif
                    {{ $order->customer->nama }}
                </div>
                <div class="text-muted small">{{ $order->customer->plat_nomor ?? '-' }}</div>
                <div class="small font-weight-bold mt-1">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
            </a>
        @empty
            @if (!isset($workOrder))
                <p class="text-muted small mb-0">Belum ada draft.</p>
            @endif
        @endforelse
    </div>
</div>