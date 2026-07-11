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
- [Konfigurasi Integrasi API](#konfigurasi-integrasi-api)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Endpoint & Halaman](#endpoint--halaman)
- [Alur Kerja Aplikasi](#alur-kerja-aplikasi)
- [Struktur File Laravel](#struktur-file-laravel)

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

### 4. Install dependencies (sudah otomatis, tapi kalau perlu):

```bash
composer install
```

### 5. Copy file environment

```bash
cp .env.example .env
```

### 6. Generate application key

```bash
php artisan key:generate
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

- `create.blade.php` — form input data balita
- `show.blade.php` — halaman hasil prediksi
- `index.blade.php` — riwayat semua prediksi

> Isi lengkap masing-masing file ada di dokumen terpisah atau lihat bagian [Isi View](#isi-view) di bawah.

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
