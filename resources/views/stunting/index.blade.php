<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Prediksi Stunting</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
<div class="container mx-auto px-4">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📋 Riwayat Prediksi Stunting</h1>
        <a href="{{ route('stunting.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            + Prediksi Baru
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">#</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Nama Balita</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Usia</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Status</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Probabilitas</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Oleh</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Waktu</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($predictions as $p)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $p->id }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $p->nama_balita ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $p->usia_bulan }} bln</td>
                        <td class="px-4 py-3">
                            @if($p->prediction_code == 1)
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold">
                                    ⚠️ Stunting
                                </span>
                            @else
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">
                                    ✅ Tidak Stunting
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $p->probability_stunting_percent !== null
                                ? number_format($p->probability_stunting_percent, 2).'%'
                                : '-' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $p->predicted_by ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $p->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('stunting.show', $p->id) }}"
                               class="text-blue-600 hover:underline text-xs font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                            Belum ada data prediksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $predictions->links() }}
    </div>

</div>
</body>
</html>
