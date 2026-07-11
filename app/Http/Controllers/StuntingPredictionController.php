<?php

namespace App\Http\Controllers;

use App\Models\StuntingPrediction;
use App\Services\StuntingPredictionService;
use Illuminate\Http\Request;

class StuntingPredictionController extends Controller
{
    public function __construct(protected StuntingPredictionService $service) {}

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
            'nama_balita'           => 'nullable|string|max:100',
            'usia_bulan'            => 'required|integer|min:0|max:60',
            'jenis_kelamin'         => 'required|in:L,P',
            'berat_lahir_kg'        => 'required|numeric|min:0',
            'panjang_lahir_cm'      => 'required|numeric|min:0',
            'asi_eksklusif'         => 'required|in:Ya,Tidak',
            'protein_harian'        => 'required|numeric|min:0',
            'frekuensi_makan'       => 'required|integer|min:0',
            'tinggi_ibu_cm'         => 'required|numeric|min:0',
            'riwayat_diare'         => 'required|integer|min:0',
            'pendapatan_keluarga'   => 'required|numeric|min:0',
            'sanitasi_layak'        => 'required|in:Ya,Tidak',
            'imunisasi_lengkap'     => 'required|in:Ya,Tidak',
            'risk_score'            => 'required|numeric',
        ]);

        try {
            // Kirim ke FastAPI (exclude nama_balita, itu cuma untuk DB)
            $payload = collect($validated)->except('nama_balita')->toArray();
            $result  = $this->service->predict($payload);

            // Simpan ke DB
            $prediction = StuntingPrediction::create([
                ...$validated,
                'prediction_code'              => $result['prediction_code'],
                'prediction_status'            => $result['prediction_status'],
                'probability_stunting_percent' => $result['probability_stunting_percent'] ?? null,
                'predicted_by'                 => auth()->user()?->name ?? 'Guest',
            ]);

            return redirect()
                ->route('stunting.show', $prediction->id)
                ->with('success', 'Prediksi berhasil!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['api' => 'Gagal menghubungi API: ' . $e->getMessage()]);
        }
    }

    public function show(StuntingPrediction $stunting)
    {
        return view('stunting.show', compact('stunting'));
    }
}
