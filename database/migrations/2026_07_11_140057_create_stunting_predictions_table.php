<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stunting_predictions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable(); // optional, kalau mau link ke pasien
            $table->string('nama_balita')->nullable();

            // Input features
            $table->integer('usia_bulan');
            $table->string('jenis_kelamin');    // L / P
            $table->float('berat_lahir_kg');
            $table->float('panjang_lahir_cm');
            $table->string('asi_eksklusif');    // Ya / Tidak
            $table->float('protein_harian');
            $table->integer('frekuensi_makan');
            $table->float('tinggi_ibu_cm');
            $table->integer('riwayat_diare');
            $table->float('pendapatan_keluarga');
            $table->string('sanitasi_layak');   // Ya / Tidak
            $table->string('imunisasi_lengkap'); // Ya / Tidak
            $table->float('risk_score');

            // Hasil prediksi
            $table->tinyInteger('prediction_code');     // 0 atau 1
            $table->string('prediction_status');        // "Stunting" / "Tidak Stunting"
            $table->float('probability_stunting_percent')->nullable();

            $table->string('predicted_by')->nullable(); // user yang input
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stunting_predictions');
    }
};
