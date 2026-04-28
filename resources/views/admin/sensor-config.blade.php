@extends('layouts.app')
@section('title', 'Konfigurasi Sensor')
@section('page-title', 'Konfigurasi Batas Sensor')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-1"><i class="bi bi-sliders me-2 text-primary"></i>Ambang Batas Sensor</h6>
            <p class="text-muted small mb-4">Perubahan akan langsung diterapkan ke logika otomatis sistem.</p>

            <form method="POST" action="{{ route('admin.sensor-config.update') }}">
                @csrf @method('PUT')

                <div class="mb-4 p-3 bg-light rounded-3">
                    <h6 class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-3">Sensor Turbidity</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Batas Keruh <span class="badge bg-warning text-dark ms-1">NTU</span>
                        </label>
                        <input type="number" name="turbidity_keruh" step="0.1" min="0"
                            class="form-control rounded-3 @error('turbidity_keruh') is-invalid @enderror"
                            value="{{ old('turbidity_keruh', $thresholds['turbidity_keruh']->value ?? 50) }}">
                        <small class="text-muted">Air dianggap keruh jika ≥ nilai ini</small>
                        @error('turbidity_keruh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-semibold">
                            Batas Sangat Keruh <span class="badge bg-danger ms-1">NTU</span>
                        </label>
                        <input type="number" name="turbidity_sangat_keruh" step="0.1" min="0"
                            class="form-control rounded-3 @error('turbidity_sangat_keruh') is-invalid @enderror"
                            value="{{ old('turbidity_sangat_keruh', $thresholds['turbidity_sangat_keruh']->value ?? 100) }}">
                        <small class="text-muted">Kaporit 1.5× ditambahkan jika ≥ nilai ini</small>
                        @error('turbidity_sangat_keruh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-4 p-3 bg-light rounded-3">
                    <h6 class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-3">Sensor Hujan</h6>
                    <div class="mb-1">
                        <label class="form-label small fw-semibold">
                            Threshold Hujan <span class="badge bg-primary ms-1">ADC</span>
                        </label>
                        <input type="number" name="rain_threshold" step="1" min="0" max="4095"
                            class="form-control rounded-3 @error('rain_threshold') is-invalid @enderror"
                            value="{{ old('rain_threshold', $thresholds['rain_threshold']->value ?? 500) }}">
                        <small class="text-muted">Hujan terdeteksi jika nilai ADC < threshold ini (ADC analog 0–4095)</small>
                        @error('rain_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-4 p-3 bg-light rounded-3">
                    <h6 class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-3">Kaporit Otomatis</h6>
                    <div class="mb-1">
                        <label class="form-label small fw-semibold">
                            Jumlah Kaporit <span class="badge bg-success ms-1">ml</span>
                        </label>
                        <input type="number" name="chlorine_amount_ml" step="0.5" min="0"
                            class="form-control rounded-3 @error('chlorine_amount_ml') is-invalid @enderror"
                            value="{{ old('chlorine_amount_ml', $thresholds['chlorine_amount_ml']->value ?? 50) }}">
                        <small class="text-muted">Volume kaporit standar saat air keruh. Sangat keruh = 1.5× nilai ini.</small>
                        @error('chlorine_amount_ml')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary rounded-3">
                    <i class="bi bi-save me-2"></i>Simpan Konfigurasi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
