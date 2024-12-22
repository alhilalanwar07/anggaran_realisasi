<?php

use Livewire\Volt\Component;
use App\Models\Skpd;
use App\Models\SubSkpd;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;

new class extends Component {
    public function with(): array
    {
        return [
            'skpd' => Skpd::count(),
            'unit_skpd' => SubSkpd::count(),
            'program' => Program::count(),
            'kegiatan' => Kegiatan::count(),
            'sub_kegiatan' => SubKegiatan::count(),
        ];
    }
}; ?>

<div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-stats {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .dashboard-stats li {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .dashboard-stats .label {
            flex: 1;
            text-align: left;
        }

        .dashboard-stats .value {
            flex: 1;
            text-align: right;
        }

    </style>
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Dashboard</h3>
            <h6 class="op-7 mb-2"></h6>
        </div>
        {{-- <div class="ms-md-auto py-2 py-md-0">
            <a href="#" class="btn btn-label-info btn-round me-2">Manage</a>
            <a href="#" class="btn btn-primary btn-round">Add Customer</a>
        </div> --}}

    </div>
    <div class="row">
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Anggaran Standar Pelayanan Minimal (SPM)</p>
                                <h4 class="card-title">1,294</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Anggaran Kemiskinan Ekstrem</p>
                                <h4 class="card-title">1303</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-luggage-cart"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Pendapatan</p>
                                <h4 class="card-title">$ 1,345</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                <i class="far fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Belanja</p>
                                <h4 class="card-title">576</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-2">
        <!-- Bagian Kiri -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-24">
                        <span class="badge bg-warning text-white" style="margin-left: 0px;">Data</span>
                        <span class="badge bg-success text-white">Jumlah</span>
                    </div>
                    <ul class="dashboard-stats mt-2">
                        <li><span class="label"><strong>SKPD</strong></span> <span class="value">
                                {{ $skpd }}
                            </span></li>
                        <li><span class="label"><strong>Unit SKPD</strong></span> <span class="value">{{ $unit_skpd }}</span></li>
                        <li><span class="label"><strong>Program</strong></span> <span class="value">{{ $program }}</span></li>
                        <li><span class="label"><strong>Kegiatan</strong></span> <span class="value">{{ $kegiatan }}</span></li>
                        <li><span class="label"><strong>Sub Kegiatan</strong></span> <span class="value">{{ $sub_kegiatan }}</span></li>
                        <li><span class="label"><strong>Rekening Pendapatan</strong></span> <span class="value">348</span></li>
                        <li><span class="label"><strong>Rekening Belanja</strong></span> <span class="value">43796</span></li>
                        <li><span class="label"><strong>Rekening Pembiayaan</strong></span> <span class="value">42</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bagian Kanan -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white text-center">Total Belanja Per Sumber Dana</div>
                <div class="card-body">
                    <canvas id="donutChart"></canvas>
                    <ul class="mt-2">
                        <li><strong>DAK Non Fisik-Tamsil Guru PNSD:</strong> Rp 2.516.518.808,00</li>
                        <li><strong>Pendapatan Bagi Hasil:</strong> Rp 163.024.721.594,00</li>
                        <li><strong>Dana Transfer Umum-Dana Alokasi Umum:</strong> Rp 1.696.436.116.618,00</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Data untuk grafik donat
    const ctx = document.getElementById('donutChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut'
        , data: {
            labels: ['DAK Non Fisik', 'Pendapatan Bagi Hasil', 'Dana Transfer Umum']
            , datasets: [{
                data: [2516518808, 163024721594, 1696436116618]
                , backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        }
        , options: {
            responsive: true
            , plugins: {
                legend: {
                    display: false
                }
            }
            , cutout: '70%'
        }
    });

</script>
</div>
