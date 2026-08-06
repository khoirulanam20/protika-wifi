<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $tagihan->nomor_invoice }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.5; }
        .container { padding: 32px 40px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 16px; margin-bottom: 24px; }
        .header-brand { display: flex; align-items: center; gap: 10px; }
        .header-logo { width: 40px; height: 40px; border-radius: 6px; }
        .header h1 { font-size: 22px; color: #2563eb; margin-bottom: 2px; }
        .header p { font-size: 11px; color: #666; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #666; letter-spacing: 0.5px; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .info-box { margin-bottom: 20px; }
        .info-box table { width: 100%; }
        .info-box td { padding: 3px 0; }
        .info-box .label { color: #666; width: 130px; }
        .amount-box { background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 16px 20px; margin: 24px 0; }
        .amount-box .amount { font-size: 20px; font-weight: bold; color: #1d4ed8; }
        .amount-box .terbilang { font-size: 11px; color: #555; margin-top: 4px; font-style: italic; }
        .status-badge { display: inline-block; background: #dcfce7; color: #166534; font-weight: bold; font-size: 13px; padding: 4px 12px; border-radius: 4px; letter-spacing: 1px; }
        .footer { margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 16px; text-align: center; font-size: 10px; color: #888; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
@php
    $pelanggan = $tagihan->pelanggan;
    $periode = \Carbon\Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->locale('id')->translatedFormat('F Y');
@endphp
<div class="container">
    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <div class="header-brand">
                        <img src="{{ public_path('icon_protika.png') }}" alt="Protika WiFi" class="header-logo">
                        <div>
                            <h1>Protika WiFi</h1>
                            <p>Bukti Pembayaran / Invoice</p>
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    <p style="font-size: 14px; font-weight: bold;">{{ $tagihan->nomor_invoice }}</p>
                    <p style="font-size: 11px; color: #666;">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <p class="section-title">Data Pelanggan</p>
        <table>
            <tr>
                <td class="label">Nama Pelanggan</td>
                <td>: <strong>{{ $pelanggan->nama_pelanggan }}</strong></td>
            </tr>
            <tr>
                <td class="label">Kecamatan</td>
                <td>: {{ $pelanggan->kecamatan ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Desa</td>
                <td>: {{ $pelanggan->desa ?? '—' }}</td>
            </tr>
            @if($pelanggan->dusun)
            <tr>
                <td class="label">Dusun</td>
                <td>: {{ $pelanggan->dusun->dusun }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="info-box">
        <p class="section-title">Detail Tagihan</p>
        <table>
            <tr>
                <td class="label">Periode Tagihan</td>
                <td>: {{ $periode }}</td>
            </tr>
            <tr>
                <td class="label">Paket Bulanan</td>
                <td>: Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Bayar</td>
                <td>: {{ $tagihan->tanggal_bayar?->format('d/m/Y') ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Kolektor</td>
                <td>: {{ $tagihan->kolektor?->nama_kolektor ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>: <span class="status-badge">LUNAS</span></td>
            </tr>
        </table>
    </div>

    <div class="amount-box">
        <p style="font-size: 11px; color: #666; margin-bottom: 4px;">Total Pembayaran</p>
        <p class="amount">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
        @if($pelanggan->bulanan?->terbilang)
            <p class="terbilang">Terbilang: {{ $pelanggan->bulanan->terbilang }} Rupiah</p>
        @endif
    </div>

    @if($tagihan->keterangan)
    <div class="info-box">
        <p class="section-title">Keterangan</p>
        <p>{{ $tagihan->keterangan }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Dokumen ini sah sebagai bukti pembayaran layanan WiFi Protika.</p>
        <p style="margin-top: 4px;">{{ $tagihan->nomor_invoice }} · {{ $periode }}</p>
    </div>
</div>
</body>
</html>
