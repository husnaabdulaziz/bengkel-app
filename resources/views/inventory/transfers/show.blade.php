<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Transfer — {{ $transfer->kode_transfer }}</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <div class="bg-white p-6 rounded shadow mb-4">
            <div class="grid grid-cols-2 gap-2 text-sm mb-4">
                <div><strong>Dari:</strong> {{ $transfer->fromBranch->nama_cabang }}</div>
                <div><strong>Ke:</strong> {{ $transfer->toBranch->nama_cabang }}</div>
                <div><strong>Status:</strong> {{ $transfer->status }}</div>
                <div><strong>Diajukan:</strong> {{ $transfer->requested_at?->format('d/m/Y H:i') }}</div>
            </div>

            @if ($transfer->status === 'requested')
                <form method="POST" action="{{ route('stock-transfers.approve', $transfer) }}">
                    @csrf
                    <table class="w-full text-left text-sm mb-4">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2">Produk</th>
                                <th class="p-2 text-right">Qty Diminta</th>
                                <th class="p-2 text-right">Qty Disetujui</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transfer->items as $item)
                                <tr class="border-t">
                                    <td class="p-2">{{ $item->product->nama }}</td>
                                    <td class="p-2 text-right">{{ $item->qty_requested }}</td>
                                    <td class="p-2 text-right">
                                        <input type="number" name="qty_approved[{{ $item->id }}]" value="{{ $item->qty_requested }}" min="0"
                                               class="border rounded px-2 py-1 w-24 text-right">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Setujui Transfer</button>
                </form>
            @endif

            @if ($transfer->status === 'approved')
                <table class="w-full text-left text-sm mb-4">
                    <thead class="bg-gray-100">
                        <tr><th class="p-2">Produk</th><th class="p-2 text-right">Qty Disetujui</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($transfer->items as $item)
                            <tr class="border-t">
                                <td class="p-2">{{ $item->product->nama }}</td>
                                <td class="p-2 text-right">{{ $item->qty_approved }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <form method="POST" action="{{ route('stock-transfers.ship', $transfer) }}"
                      onsubmit="return confirm('Kirim barang sekarang? Stock cabang asal akan berkurang.')">
                    @csrf
                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded">Tandai Sudah Dikirim</button>
                </form>
            @endif

            @if ($transfer->status === 'shipped')
                <table class="w-full text-left text-sm mb-4">
                    <thead class="bg-gray-100">
                        <tr><th class="p-2">Produk</th><th class="p-2 text-right">Qty Dikirim</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($transfer->items as $item)
                            <tr class="border-t">
                                <td class="p-2">{{ $item->product->nama }}</td>
                                <td class="p-2 text-right">{{ $item->qty_shipped }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <form method="POST" action="{{ route('stock-transfers.receive', $transfer) }}"
                      onsubmit="return confirm('Konfirmasi barang sudah diterima? Stock cabang tujuan akan bertambah.')">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Konfirmasi Diterima</button>
                </form>
            @endif

            @if ($transfer->status === 'received')
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr><th class="p-2">Produk</th><th class="p-2 text-right">Qty Diterima</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($transfer->items as $item)
                            <tr class="border-t">
                                <td class="p-2">{{ $item->product->nama }}</td>
                                <td class="p-2 text-right">{{ $item->qty_received }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="text-green-700 font-medium mt-4">Transfer selesai, stock kedua cabang sudah ter-update.</p>
            @endif
        </div>
    </div>
</x-app-layout>