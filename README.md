# 🩺 Prediksi Stunting Balita

Aplikasi web berbasis **Laravel** yang terintegrasi dengan **FastAPI ML Model** untuk memprediksi risiko stunting pada balita berdasarkan data klinis dan sosial-ekonomi.

---

## 📋 Daftar Isi

- [Gambaran Umum](#gambaran-umum)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Prasyarat](#prasyarat)
- [Struktur Proyek](#struktur-proyek)
- [Setup FastAPI (ML Model)](#setup-fastapi-ml-model)
- [Setup Laravel](#setup-laravel)
- [Konfigurasi Database](#konfigurasi-database)
- [Membuat File-file Laravel](#membuat-file-file-laravel)
  - [Migration](#2-isi-migration)
  - [Model](#4-isi-model----appmodelsstuntingpredictionphp)
  - [Service](#5-isi-service----appservicesstuntingpredictionservicephp)
  - [Controller](#6-isi-controller----apphttpcontrollersstuntingpredictioncontrollerphp)
  - [Routes](#7-isi-routes----routeswebphp)
  - [Views](#9-buat-file-views)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Endpoint & Halaman](#endpoint--halaman)
- [Alur Kerja Aplikasi](#alur-kerja-aplikasi)
- [Struktur File Laravel](#struktur-file-laravel)
- [Input Data & Validasi](#input-data--validasi)
- [Output Prediksi](#output-prediksi)
- [Troubleshooting](#troubleshooting)

---

## Gambaran Umum

Aplikasi ini terdiri dari dua komponen utama:

1. **FastAPI** — server ML yang memuat model `best_model.pkl` dan menyediakan endpoint prediksi di port `8001`
2. **Laravel** — aplikasi web yang menampilkan form input, mengirim data ke FastAPI, menyimpan hasil ke database MySQL, dan menampilkan riwayat prediksi

---

## Teknologi yang Digunakan

| Layer | Teknologi |
|---|---|
| Backend Web | Laravel 10+ (PHP 8.1+) |
| ML API | FastAPI + Python 3.10+ |
| ML Model | Scikit-learn (Random Forest / Gradient Boosting) |
| Database | MySQL |
| Styling | Tailwind CSS (via CDN) |
| HTTP Client | Laravel Http Facade (Guzzle) |

---

## Prasyarat

Pastikan sudah terinstall:

- PHP >= 8.1
- Composer
- Python >= 3.10
- pip
- MySQL
- Node.js (opsional, jika pakai Vite)

---

## Struktur Proyek

```
root/
├── stunting-laravel/              ← Project Laravel (dibuat manual)
│   ├── app/
│   │   ├── Http/Controllers/StuntingPredictionController.php
│   │   ├── Models/StuntingPrediction.php
│   │   └── Services/StuntingPredictionService.php
│   ├── database/migrations/
│   ├── resources/views/stunting/
│   │   ├── create.blade.php
│   │   ├── show.blade.php
│   │   └── index.blade.php
│   └── routes/web.php
│
└── Dev_Weekend_ML/                ← Project FastAPI (dari git clone)
    └── ML/
        ├── main.py
        ├── best_model.pkl
        ├── requirements.txt
        ├── dataset_stunting_ml_1000.csv
        └── stunting_ml_analysis.ipynb
```

---

## Setup FastAPI (ML Model)

### 1. Clone repository ML

```bash
git clone https://github.com/Aimannawal/Dev_Weekend_ML.git
```

### 2. Masuk ke folder ML

```bash
cd Dev_Weekend_ML/ML
```

### 3. Buka di VSCode

```bash
code .
```

### 4. Prasyarat

Pastikan Python sudah terinstall di sistem Anda. Direkomendasikan untuk menggunakan Virtual Environment.

### 5. Buat virtual environment

```bash
python -m venv venv
```

### 6. Aktifkan virtual environment

```bash
# Windows
venv\Scripts\activate

# Mac / Linux
source venv/bin/activate
```

### 7. Instalasi Dependensi

Install seluruh library yang dibutuhkan berdasarkan file `requirements.txt`:

```bash
pip install -r requirements.txt
```

### 8. Jalankan Server

Setelah proses instalasi selesai, jalankan API dengan perintah berikut (di dalam folder `ML/`):

```bash
uvicorn main:app --reload --port 8001
```

> **Catatan Port:** API dijalankan pada port `8001` untuk menghindari bentrokan (conflict) dengan layanan lain yang mungkin menggunakan port default `8000`.

Server FastAPI aktif di: `http://127.0.0.1:8001`

Akses dokumentasi API interaktif (Swagger UI) di: `http://127.0.0.1:8001/docs`

---

## Setup Laravel

### 1. Buat project Laravel baru

```bash
composer create-project laravel/laravel stunting-laravel
```

### 2. Masuk ke folder project

```bash
cd stunting-laravel
```

### 3. Buka di VSCode

```bash
code .
```

---

## Konfigurasi Database

### 1. Buat database MySQL

```sql
CREATE DATABASE stunting_db;
```

### 2. Edit file `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stunting_db
DB_USERNAME=root
DB_PASSWORD=

STUNTING_API_URL=http://127.0.0.1:8001
```

### 3. Tambahkan config service

Buka `config/services.php`, tambahkan di dalam array:

```php
'stunting_api' => [
    'url' => env('STUNTING_API_URL', 'http://127.0.0.1:8001'),
],
```

---

## Membuat File-file Laravel

### 1. Buat migration, model, dan controller

```bash
php artisan make:migration create_stunting_predictions_table
php artisan make:model StuntingPrediction
php artisan make:controller StuntingPredictionController
mkdir -p app/Services
```

### 2. Isi migration

Buka file di `database/migrations/xxxx_create_stunting_predictions_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stunting_predictions', function (Blueprint $table) {
            $table->id();
            $table->string('nama_balita')->nullable();
            $table->integer('usia_bulan');
            $table->string('jenis_kelamin');
            $table->float('berat_lahir_kg');
            $table->float('panjang_lahir_cm');
            $table->string('asi_eksklusif');
            $table->float('protein_harian');
            $table->integer('frekuensi_makan');
            $table->float('tinggi_ibu_cm');
            $table->integer('riwayat_diare');
            $table->float('pendapatan_keluarga');
            $table->string('sanitasi_layak');
            $table->string('imunisasi_lengkap');
            $table->float('risk_score');
            $table->tinyInteger('prediction_code');
            $table->string('prediction_status');
            $table->float('probability_stunting_percent')->nullable();
            $table->string('predicted_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stunting_predictions');
    }
};
```

### 3. Jalankan migration

```bash
php artisan migrate
```

### 4. Isi Model — `app/Models/StuntingPrediction.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StuntingPrediction extends Model
{
    protected $fillable = [
        'nama_balita',
        'usia_bulan',
        'jenis_kelamin',
        'berat_lahir_kg',
        'panjang_lahir_cm',
        'asi_eksklusif',
        'protein_harian',
        'frekuensi_makan',
        'tinggi_ibu_cm',
        'riwayat_diare',
        'pendapatan_keluarga',
        'sanitasi_layak',
        'imunisasi_lengkap',
        'risk_score',
        'prediction_code',
        'prediction_status',
        'probability_stunting_percent',
        'predicted_by',
    ];
}
```

### 5. Isi Service — `app/Services/StuntingPredictionService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class StuntingPredictionService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.stunting_api.url', 'http://127.0.0.1:8001');
    }

    public function predict(array $data): array
    {
        $response = Http::timeout(30)
            ->post("{$this->baseUrl}/predict", $data);

        if ($response->failed()) {
            throw new Exception('FastAPI error: HTTP ' . $response->status());
        }

        return $response->json();
    }
}
```

### 6. Isi Controller — `app/Http/Controllers/StuntingPredictionController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\StuntingPrediction;
use App\Services\StuntingPredictionService;
use Illuminate\Http\Request;

class StuntingPredictionController extends Controller
{
    protected StuntingPredictionService $service;

    public function __construct(StuntingPredictionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $predictions = StuntingPrediction::latest()->paginate(15);
        return view('stunting.index', compact('predictions'));
    }

    public function create()
    {
        return view('stunting.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_balita'         => 'nullable|string|max:100',
            'usia_bulan'          => 'required|integer|min:0|max:60',
            'jenis_kelamin'       => 'required|in:L,P',
            'berat_lahir_kg'      => 'required|numeric|min:0',
            'panjang_lahir_cm'    => 'required|numeric|min:0',
            'asi_eksklusif'       => 'required|in:Ya,Tidak',
            'protein_harian'      => 'required|numeric|min:0',
            'frekuensi_makan'     => 'required|integer|min:0',
            'tinggi_ibu_cm'       => 'required|numeric|min:0',
            'riwayat_diare'       => 'required|integer|min:0',
            'pendapatan_keluarga' => 'required|numeric|min:0',
            'sanitasi_layak'      => 'required|in:Ya,Tidak',
            'imunisasi_lengkap'   => 'required|in:Ya,Tidak',
            'risk_score'          => 'required|numeric',
        ]);

        try {
            $payload = collect($validated)->except('nama_balita')->toArray();

            $payload['usia_bulan']          = (int)   $payload['usia_bulan'];
            $payload['berat_lahir_kg']      = (float) $payload['berat_lahir_kg'];
            $payload['panjang_lahir_cm']    = (float) $payload['panjang_lahir_cm'];
            $payload['protein_harian']      = (float) $payload['protein_harian'];
            $payload['frekuensi_makan']     = (int)   $payload['frekuensi_makan'];
            $payload['tinggi_ibu_cm']       = (float) $payload['tinggi_ibu_cm'];
            $payload['riwayat_diare']       = (int)   $payload['riwayat_diare'];
            $payload['pendapatan_keluarga'] = (float) $payload['pendapatan_keluarga'];
            $payload['risk_score']          = (float) $payload['risk_score'];

            $result = $this->service->predict($payload);

            $prediction = StuntingPrediction::create([
                ...$validated,
                'prediction_code'              => $result['prediction_code'],
                'prediction_status'            => $result['prediction_status'],
                'probability_stunting_percent' => $result['probability_stunting_percent'] ?? null,
                'predicted_by'                 => auth()->user()?->name ?? 'Guest',
            ]);

            return redirect()
                ->route('stunting.show', $prediction->id)
                ->with('success', 'Prediksi berhasil disimpan!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['api' => 'Gagal menghubungi API prediksi: ' . $e->getMessage()]);
        }
    }

    public function show(StuntingPrediction $stunting)
    {
        return view('stunting.show', compact('stunting'));
    }
}
```

### 7. Isi Routes — `routes/web.php`

Tambahkan:

```php
use App\Http\Controllers\StuntingPredictionController;

Route::prefix('stunting')->name('stunting.')->group(function () {
    Route::get('/',           [StuntingPredictionController::class, 'index'])->name('index');
    Route::get('/create',     [StuntingPredictionController::class, 'create'])->name('create');
    Route::post('/',          [StuntingPredictionController::class, 'store'])->name('store');
    Route::get('/{stunting}', [StuntingPredictionController::class, 'show'])->name('show');
});
```

### 8. Buat folder views

```bash
mkdir -p resources/views/stunting
```

### 9. Buat file views

Buat tiga file berikut di `resources/views/stunting/`:

---

#### `resources/views/stunting/create.blade.php`

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Stunting</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
<div class="mx-auto px-4 max-w-2xl">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🩺 Prediksi Stunting Balita</h1>
        <a href="{{ route('stunting.index') }}" class="text-sm text-blue-600 hover:underline">← Riwayat</a>
    </div>

    @if ($errors->has('api'))
        <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded-lg mb-4 text-sm">
            ❌ {{ $errors->first('api') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6 space-y-5">
        <form action="{{ route('stunting.store') }}" method="POST">
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
                    <input type="number" name="usia_bulan" value="{{ old('usia_bulan') }}" min="0" max="60"
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
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition text-sm mt-2">
                🔍 Prediksi Sekarang
            </button>
        </form>
    </div>
</div>
</body>
</html>
```

---

#### `resources/views/stunting/show.blade.php`

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Prediksi Stunting</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
<div class="mx-auto px-4 max-w-lg">

    <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Hasil Prediksi Stunting</h1>

    @if($stunting->prediction_code == 1)
        <div class="bg-red-50 border-2 border-red-400 rounded-2xl p-8 mb-5 text-center shadow">
            <p class="text-6xl mb-3">⚠️</p>
            <h2 class="text-3xl font-bold text-red-700">STUNTING</h2>
            <p class="text-red-500 mt-2 text-sm">Balita terdeteksi berisiko stunting. Segera konsultasi ke dokter.</p>
        </div>
    @else
        <div class="bg-green-50 border-2 border-green-400 rounded-2xl p-8 mb-5 text-center shadow">
            <p class="text-6xl mb-3">✅</p>
            <h2 class="text-3xl font-bold text-green-700">TIDAK STUNTING</h2>
            <p class="text-green-500 mt-2 text-sm">Pertumbuhan balita dalam kondisi normal.</p>
        </div>
    @endif

    @if($stunting->probability_stunting_percent !== null)
        <div class="bg-white rounded-xl shadow p-4 mb-5 text-center">
            <p class="text-gray-500 text-sm mb-1">Probabilitas Stunting</p>
            <p class="text-3xl font-bold {{ $stunting->prediction_code == 1 ? 'text-red-600' : 'text-green-600' }}">
                {{ number_format($stunting->probability_stunting_percent, 2) }}%
            </p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-5 mb-5 text-sm text-gray-700 space-y-2">
        <h3 class="font-semibold text-gray-800 mb-3 text-base">Detail Data</h3>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Nama Balita</span><span class="font-medium">{{ $stunting->nama_balita ?? '-' }}</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Usia</span><span class="font-medium">{{ $stunting->usia_bulan }} bulan</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Jenis Kelamin</span><span class="font-medium">{{ $stunting->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Berat Lahir</span><span class="font-medium">{{ $stunting->berat_lahir_kg }} kg</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Panjang Lahir</span><span class="font-medium">{{ $stunting->panjang_lahir_cm }} cm</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ASI Eksklusif</span><span class="font-medium">{{ $stunting->asi_eksklusif }}</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Protein Harian</span><span class="font-medium">{{ $stunting->protein_harian }} g</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Frekuensi Makan</span><span class="font-medium">{{ $stunting->frekuensi_makan }}x/hari</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Tinggi Ibu</span><span class="font-medium">{{ $stunting->tinggi_ibu_cm }} cm</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Riwayat Diare</span><span class="font-medium">{{ $stunting->riwayat_diare }} kali</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Pendapatan Keluarga</span><span class="font-medium">Rp {{ number_format($stunting->pendapatan_keluarga, 0, ',', '.') }}</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Sanitasi Layak</span><span class="font-medium">{{ $stunting->sanitasi_layak }}</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Imunisasi Lengkap</span><span class="font-medium">{{ $stunting->imunisasi_lengkap }}</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Risk Score</span><span class="font-medium">{{ $stunting->risk_score }}</span></div>
        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Diprediksi oleh</span><span class="font-medium">{{ $stunting->predicted_by ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Waktu</span><span class="font-medium">{{ $stunting->created_at->format('d M Y H:i') }}</span></div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('stunting.create') }}"
           class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition text-sm">
            + Prediksi Baru
        </a>
        <a href="{{ route('stunting.index') }}"
           class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 rounded-lg transition text-sm">
            Riwayat
        </a>
    </div>

</div>
</body>
</html>
```

---

#### `resources/views/stunting/index.blade.php`

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Prediksi Stunting</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
<div class="mx-auto px-4 max-w-6xl">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📋 Riwayat Prediksi Stunting</h1>
        <a href="{{ route('stunting.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            + Prediksi Baru
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded-lg mb-4 text-sm">
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
```

---

## Menjalankan Aplikasi

Butuh **dua terminal** yang jalan bersamaan:

### Terminal 1 — FastAPI

```bash
cd Dev_Weekend_ML/ML
venv\Scripts\activate              # Windows
# atau: source venv/bin/activate  # Mac/Linux
uvicorn main:app --reload --port 8001
```

### Terminal 2 — Laravel

```bash
cd stunting-laravel
php artisan serve
```

### Akses di browser

| URL | Halaman |
|---|---|
| `http://127.0.0.1:8000/stunting/create` | Form input prediksi |
| `http://127.0.0.1:8000/stunting` | Riwayat prediksi |
| `http://127.0.0.1:8000/stunting/{id}` | Detail hasil prediksi |
| `http://127.0.0.1:8001/docs` | Swagger FastAPI |

---

## Endpoint & Halaman

### Laravel Routes

| Method | URL | Name | Fungsi |
|---|---|---|---|
| GET | `/stunting` | `stunting.index` | Daftar riwayat prediksi |
| GET | `/stunting/create` | `stunting.create` | Form input |
| POST | `/stunting` | `stunting.store` | Proses + simpan prediksi |
| GET | `/stunting/{id}` | `stunting.show` | Detail hasil prediksi |

### FastAPI Endpoints

| Method | URL | Fungsi |
|---|---|---|
| GET | `http://127.0.0.1:8001/` | Health check |
| POST | `http://127.0.0.1:8001/predict` | Prediksi stunting |

---

## Alur Kerja Aplikasi

```
User isi form (create.blade.php)
        ↓
Laravel validate input
        ↓
StuntingPredictionService::predict()
        ↓
HTTP POST → FastAPI :8001/predict
        ↓
FastAPI preprocessing + model inference
        ↓
Response JSON: { prediction_code, prediction_status, probability }
        ↓
Laravel simpan ke tabel stunting_predictions
        ↓
Redirect ke show.blade.php (hasil prediksi)
```

---

## Struktur File Laravel

```
app/
├── Http/
│   └── Controllers/
│       └── StuntingPredictionController.php
├── Models/
│   └── StuntingPrediction.php
└── Services/
    └── StuntingPredictionService.php

database/
└── migrations/
    └── xxxx_create_stunting_predictions_table.php

resources/
└── views/
    └── stunting/
        ├── create.blade.php
        ├── show.blade.php
        └── index.blade.php

routes/
└── web.php
```

---

## Input Data & Validasi

| Field | Tipe | Nilai Valid |
|---|---|---|
| `nama_balita` | string | opsional |
| `usia_bulan` | integer | 0–60 |
| `jenis_kelamin` | string | `L` atau `P` |
| `berat_lahir_kg` | float | > 0 |
| `panjang_lahir_cm` | float | > 0 |
| `asi_eksklusif` | string | `Ya` atau `Tidak` |
| `protein_harian` | float | > 0 |
| `frekuensi_makan` | integer | > 0 |
| `tinggi_ibu_cm` | float | > 0 |
| `riwayat_diare` | integer | >= 0 |
| `pendapatan_keluarga` | float | > 0 |
| `sanitasi_layak` | string | `Ya` atau `Tidak` |
| `imunisasi_lengkap` | string | `Ya` atau `Tidak` |
| `risk_score` | float | bebas |

---

## Output Prediksi

```json
{
    "status": "success",
    "prediction_code": 0,
    "prediction_status": "Tidak Stunting",
    "probability_stunting_percent": 12.45
}
```

| Field | Keterangan |
|---|---|
| `prediction_code` | `0` = Tidak Stunting, `1` = Stunting |
| `prediction_status` | Label teks hasil prediksi |
| `probability_stunting_percent` | Probabilitas stunting dalam persen |

---

## Troubleshooting

**FastAPI tidak bisa diakses dari Laravel?**
Pastikan FastAPI berjalan di port 8001 dan `STUNTING_API_URL` di `.env` sudah benar.

```bash
# Cek FastAPI aktif
curl http://127.0.0.1:8001/
```

**Error 500 saat predict?**
Cek log Laravel:

```bash
tail -f storage/logs/laravel.log
```

**Migration gagal?**
Pastikan database sudah dibuat dan credential `.env` benar:

```bash
php artisan migrate:fresh
```

**CORS error?**
Tidak akan terjadi karena request dari Laravel backend ke FastAPI (server-to-server), bukan dari browser langsung.
