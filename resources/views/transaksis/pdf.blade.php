<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    
    <link href="{{ public_path('assets/css/pdf.css') }}" rel="stylesheet">
</head>
<body>
    <div class="header">
        <h1 class="text-dark">
            <img src="{{ public_path('assets/img/ic.png') }}" alt="Icon RentCar" width="32" height="32">
            RENTCAR
        </h1>

        <h2>Laporan Transaksi</h2>
        <div class="company-info">
            Sistem Manajemen Rental Mobil | Generated Report
        </div>
    </div>

    <div class="report-info">
        <div class="report-info-item">
            <strong>Dicetak oleh:</strong><br>
            {{ $user->name }} ({{ ucfirst($user->role) }})
        </div>
        <div class="report-info-item">
            <strong>Tanggal Cetak:</strong><br>
            {{ now()->setTimezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>

    @if(request('search') || (request('status_filter') && request('status_filter') != 'all') || (request('date_filter') && request('date_filter') != 'all'))
        <div class="filter-info">
            <strong>Filter yang Diterapkan:</strong>
            @if(request('search'))
                Pencarian: "{{ request('search') }}"
                @if(request('status_filter') && request('status_filter') != 'all') | @endif
            @endif
            @if(request('status_filter') && request('status_filter') != 'all')
                Status: {{ request('status_filter') }}
                @if(request('date_filter') && request('date_filter') != 'all') | @endif
            @endif
            @if(request('date_filter') && request('date_filter') != 'all')
                Periode: {{ request('date_filter') }}
            @endif
        </div>
    @endif

    {{-- Statistik Keseluruhan: Hanya tampil jika user adalah admin dan TIDAK ADA filter yang aktif --}}
    @if($user->role === 'admin' && !(request('search') || (request('status_filter') && request('status_filter') != 'all') || (request('date_filter') && request('date_filter') != 'all')) && $transaksis->count() > 0)
        <div class="statistics">
            <div class="stat-card total">
                <h3>{{ \App\Models\Transaksi::withTrashed()->where('status', 'Selesai')->count() }}</h3>
                <p>Total Selesai</p>
            </div>
            <div class="stat-card stat-card-secondary">
                <h3>{{ \App\Models\Transaksi::withTrashed()
                    ->where('status', 'Selesai')
                    ->whereNotNull('tgl_kembali_aktual')
                    ->whereRaw('DATE(tgl_kembali_aktual) < DATE(tgl_kembali)')
                    ->count() }}</h3>
                <p>Lebih Awal</p>
            </div>
            <div class="stat-card ontime">
                <h3>{{ \App\Models\Transaksi::withTrashed()
                    ->where('status', 'Selesai')
                    ->whereNotNull('tgl_kembali_aktual')
                    ->whereRaw('DATE(tgl_kembali_aktual) = DATE(tgl_kembali)')
                    ->count() }}</h3>
                <p>Tepat Waktu</p>
            </div>
            <div class="stat-card late">
                <h3>{{ \App\Models\Transaksi::withTrashed()
                    ->where('status', 'Selesai')
                    ->whereNotNull('tgl_kembali_aktual')
                    ->whereRaw('DATE(tgl_kembali_aktual) > DATE(tgl_kembali)')
                    ->count() }}</h3>
                <p>Terlambat</p>
            </div>
            <div class="stat-card revenue">
                <h3>Rp {{ number_format(\App\Models\Transaksi::withTrashed()
                    ->where('status', 'Selesai')
                    ->sum('total'), 0, ',', '.') }}</h3>
                <p>Total Pendapatan</p>
            </div>
        </div>
    @endif

    {{-- Ringkasan Laporan: Hanya tampil jika ADA filter yang aktif --}}
    @if((request('search') || (request('status_filter') && request('status_filter') != 'all') || (request('date_filter') && request('date_filter') != 'all')) && $transaksis->count() > 0)
        <div class="summary-section">
            <div class="summary-card">
                <div class="summary-value total-transactions">{{ $transaksis->count() }}</div>
                <p class="summary-label">Total Data (Hasil Filter)</p>
            </div>
            <div class="summary-card">
                <div class="summary-value total-revenue">Rp {{ number_format($transaksis->sum('total'), 0, ',', '.') }}</div>
                <p class="summary-label">Total Pendapatan (Hasil Filter)</p>
            </div>
        </div>
    @endif

    @if($transaksis->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    @if($user->role === 'admin')
                        <th width="15%">Customer</th>
                    @endif
                    <th width="{{ $user->role === 'admin' ? '10%' : '15%' }}">Foto</th>
                    <th width="{{ $user->role === 'admin' ? '15%' : '20%' }}">Informasi Mobil</th>
                    <th width="8%">Tgl Booking</th>
                    <th width="8%">Tgl Kembali</th>
                    <th width="5%">Lama</th>
                    <th width="10%">Tgl Kembali Aktual</th>
                    <th width="10%">Total</th>
                    <th width="8%">Status</th>
                    @if($user->role === 'admin')
                        <th width="8%">Ketepatan</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($transaksis as $index => $t)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        
                        @if($user->role === 'admin')
                            <td>
                                <div class="customer-info">
                                    <strong>{{ $t->nama }}</strong><br>
                                    {{ $t->ponsel }}<br>
                                    {{ $t->user->email ?? '-' }}<br>
                                    {{ Str::limit($t->alamat, 30) }}
                                </div>
                            </td>
                        @endif
                        
                        <td class="text-center">
                            @if($t->mobil->foto)
                                <img src="{{ public_path('storage/' . $t->mobil->foto) }}" 
                                     alt="{{ $t->mobil->merek }}" 
                                     style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px;">
                            @else
                                <div style="width: 80px; height: 50px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <span style="color: #6c757d; font-size: 10px;">No Image</span>
                                </div>
                            @endif
                        </td>
                        
                        <td>
                            <div class="car-info">
                                <strong>{{ $t->mobil->merek }}</strong><br>
                                {{ $t->mobil->nopolisi }}<br>
                                {{ $t->mobil->jenis }}<br>
                                {{ $t->mobil->kapasitas }} orang<br>
                                Rp {{ number_format($t->mobil->harga, 0, ',', '.') }}/hari
                            </div>
                        </td>
                        
                        <td class="text-center">{{ $t->created_at->format('d-m-Y') }}</td>
                        <td class="text-center">{{ $t->tgl_kembali->format('d-m-Y') }}</td>
                        <td class="text-center">{{ $t->lama }} hari</td>
                        
                        <td class="text-center">
                            @if($t->tgl_kembali_aktual)
                                <strong>{{ $t->tgl_kembali_aktual->format('d-m-Y') }}</strong>
                            @else
                                <span style="color: #6c757d;">Belum Kembali</span>
                            @endif
                        </td>
                        
                        <td class="text-right">
                            <span class="money">Rp {{ number_format($t->total, 0, ',', '.') }}</span>
                        </td>
                        
                        <td class="text-center">
                            <span class="status-badge status-{{ strtolower($t->status) }}">
                                {{ $t->status }}
                            </span>
                        </td>
                        
                        @if($user->role === 'admin')
                            <td class="text-center">
                                @if($t->tgl_kembali_aktual)
                                    @if($t->tgl_kembali_aktual->format('Y-m-d') < $t->tgl_kembali->format('Y-m-d'))
                                        <span class="return-status return-early">Lebih Awal</span>
                                    @elseif($t->tgl_kembali_aktual->format('Y-m-d') == $t->tgl_kembali->format('Y-m-d'))
                                        <span class="return-status return-ontime">Tepat Waktu</span>
                                    @else
                                        <span class="return-status return-late">Terlambat</span>
                                    @endif
                                @else
                                    <span style="color: #6c757d; font-style: italic;">-</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <h3>Tidak Ada Data</h3>
            <p>Tidak ditemukan transaksi yang sesuai dengan kriteria yang ditentukan.</p>
        </div>
    @endif

    <div class="footer">
        <p>
            <strong>RentCar</strong> | 
            Laporan ini dibuat secara otomatis oleh sistem
        </p>
    </div>
</body>
</html>