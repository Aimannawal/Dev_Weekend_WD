<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Stunting</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
<div class="container mx-auto px-4 max-w-2xl">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🩺 Prediksi Stunting Balita</h1>
        <a href="{{ route('stunting.index') }}" class="text-sm text-blue-600 hover:underline">← Riwayat</a>
    </div>

    @if ($errors->has('api'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            {{ $errors->first('api') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6">
        <form action="{{ route('stunting.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Balita</label>
                <input type="text" name="nama_balita" value="{{ old('nama_balita') }}"
                       placeholder="Opsional"
                       class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usia (bulan) *</label>
                    <input type="number" name="usia_bulan" value="{{ old('usia_bulan') }}"
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    @error('usia_bulan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                    <select name="jenis_kelamin"
                            class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('jenis_kelamin')=='L'?'selected':'' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin')=='P'?'selected':'' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Berat Lahir (kg) *</label>
                    <input type="number" step="0.01" name="berat_lahir_kg" value="{{ old('berat_lahir_kg') }}"
                           placeholder="cth: 3.2"
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Panjang Lahir (cm) *</label>
                    <input type="number" step="0.1" name="panjang_lahir_cm" value="{{ old('panjang_lahir_cm') }}"
                           placeholder="cth: 50.0"
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ASI Eksklusif *</label>
                    <select name="asi_eksklusif"
                            class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                        <option value="">-- Pilih --</option>
                        <option value="Ya"    {{ old('asi_eksklusif')=='Ya'?'selected':'' }}>Ya</option>
                        <option value="Tidak" {{ old('asi_eksklusif')=='Tidak'?'selected':'' }}>Tidak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Protein Harian (g) *</label>
                    <input type="number" step="0.1" name="protein_harian" value="{{ old('protein_harian') }}"
                           placeholder="cth: 45.0"
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Frekuensi Makan (x/hari) *</label>
                    <input type="number" name="frekuensi_makan" value="{{ old('frekuensi_makan') }}"
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi Ibu (cm) *</label>
                    <input type="number" step="0.1" name="tinggi_ibu_cm" value="{{ old('tinggi_ibu_cm') }}"
                           placeholder="cth: 160.0"
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Riwayat Diare (kali) *</label>
                    <input type="number" name="riwayat_diare" value="{{ old('riwayat_diare') }}"
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pendapatan Keluarga (Rp) *</label>
                    <input type="number" name="pendapatan_keluarga" value="{{ old('pendapatan_keluarga') }}"
                           placeholder="cth: 6000000"
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sanitasi Layak *</label>
                    <select name="sanitasi_layak"
                            class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                        <option value="">-- Pilih --</option>
                        <option value="Ya"    {{ old('sanitasi_layak')=='Ya'?'selected':'' }}>Ya</option>
                        <option value="Tidak" {{ old('sanitasi_layak')=='Tidak'?'selected':'' }}>Tidak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Imunisasi Lengkap *</label>
                    <select name="imunisasi_lengkap"
                            class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                        <option value="">-- Pilih --</option>
                        <option value="Ya"    {{ old('imunisasi_lengkap')=='Ya'?'selected':'' }}>Ya</option>
                        <option value="Tidak" {{ old('imunisasi_lengkap')=='Tidak'?'selected':'' }}>Tidak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Risk Score *</label>
                    <input type="number" step="0.1" name="risk_score" value="{{ old('risk_score') }}"
                           placeholder="cth: 15.0"
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg mt-2 transition">
                🔍 Prediksi Sekarang
            </button>
        </form>
    </div>
</div>
</body>
</html>
