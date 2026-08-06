<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tagihan->nomor_invoice }} — Protika WiFi</title>
    <link rel="icon" type="image/png" href="{{ asset('icon_protika.png') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; font-size: 14px; color: #1a1a1a; line-height: 1.5; background: #f3f4f6; }
        .toolbar { max-width: 720px; margin: 0 auto; padding: 12px 16px; display: flex; gap: 8px; flex-wrap: wrap; }
        .btn { border: 0; border-radius: 8px; padding: 10px 14px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-back { background: #e5e7eb; color: #374151; }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-share { background: #10b981; color: #fff; }
        .btn-pdf { background: #fff; color: #2563eb; border: 1px solid #bfdbfe; }
        .container { padding: 24px 20px 32px; max-width: 720px; margin: 0 auto; background: #fff; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 16px; margin-bottom: 24px; }
        .header-brand { display: flex; align-items: center; gap: 10px; }
        .header-logo { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
        .header h1 { font-size: 22px; color: #2563eb; margin-bottom: 2px; }
        .header p { font-size: 12px; color: #666; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #666; letter-spacing: 0.5px; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .info-box { margin-bottom: 20px; }
        .info-box table { width: 100%; }
        .info-box td { padding: 4px 0; vertical-align: top; }
        .info-box .label { color: #666; width: 130px; }
        .amount-box { background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px 20px; margin: 24px 0; }
        .amount-box .amount { font-size: 24px; font-weight: bold; color: #1d4ed8; }
        .amount-box .terbilang { font-size: 12px; color: #555; margin-top: 4px; font-style: italic; }
        .status-badge { display: inline-block; background: #dcfce7; color: #166534; font-weight: bold; font-size: 12px; padding: 4px 12px; border-radius: 999px; }
        .footer { margin-top: 32px; border-top: 1px solid #e5e7eb; padding-top: 16px; text-align: center; font-size: 11px; color: #888; }
        .text-right { text-align: right; }
        .toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: #111827; color: #fff; padding: 10px 16px; border-radius: 8px; font-size: 13px; opacity: 0; pointer-events: none; transition: opacity .2s; z-index: 50; }
        .toast.show { opacity: 1; }
        @media print {
            body { background: #fff; }
            .toolbar, .toast { display: none !important; }
            .container { max-width: none; padding: 0; }
        }
    </style>
</head>
<body>
@php
    $pelanggan = $tagihan->pelanggan;
    $periode = \Carbon\Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->locale('id')->translatedFormat('F Y');
    $shareText = $tagihan->invoiceShareText();
    $pdfUrl = ($isPublic ?? false)
        ? URL::signedRoute('tagihan.invoice.public.pdf', $tagihan)
        : route('tagihan.invoice.pdf', $tagihan);
    $pdfFilename = $tagihan->nomor_invoice . '.pdf';
@endphp

    <div class="toolbar">
    @unless($isPublic ?? false)
        <button type="button" class="btn btn-back" onclick="history.back()">← Kembali</button>
    @endunless
    <button type="button" class="btn btn-share"
        data-share-pdf
        data-pdf-url="{{ $pdfUrl }}"
        data-filename="{{ $pdfFilename }}"
        onclick="shareInvoiceTagihan(@js($shareText), @js($pdfUrl), @js($pdfFilename))">Bagikan</button>
    <button type="button" class="btn btn-print" id="btnPrint" onclick="window.print()">Cetak</button>
    <a href="{{ $pdfUrl }}"
       download="{{ $pdfFilename }}"
       class="btn btn-pdf">Unduh PDF</a>
</div>

<div class="container">
    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <div class="header-brand">
                        <img src="{{ asset('icon_protika.png') }}" alt="Protika WiFi" class="header-logo">
                        <div>
                            <h1>Protika WiFi</h1>
                            <p>Bukti Pembayaran / Invoice</p>
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    <p style="font-size: 15px; font-weight: bold;">{{ $tagihan->nomor_invoice }}</p>
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
        <p style="font-size: 12px; color: #666; margin-bottom: 4px;">Total Pembayaran</p>
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

<div id="invoiceToast" class="toast" role="status"></div>

<script>
function showInvoiceToast(message) {
    var toast = document.getElementById('invoiceToast');
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(function () { toast.classList.remove('show'); }, 2500);
}

function copyInvoiceText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        return navigator.clipboard.writeText(text);
    }
    return new Promise(function (resolve, reject) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            document.body.removeChild(ta);
            resolve();
        } catch (e) {
            document.body.removeChild(ta);
            reject(e);
        }
    });
}

(function () {
    var isWebView = /wv|WebView/i.test(navigator.userAgent)
        || window.DownloadChannel
        || window.ShareChannel;
    if (isWebView) {
        var btnPrint = document.getElementById('btnPrint');
        if (btnPrint) btnPrint.style.display = 'none';
    }
})();

function shareInvoiceTagihan(text, pdfUrl, pdfFilename) {
    // Di APK → share file PDF ke WhatsApp via native bridge
    if (window.SwfApp && window.SwfApp.sharePdf && pdfUrl) {
        window.SwfApp.sharePdf(pdfUrl, pdfFilename || 'invoice.pdf');
        return;
    }

    // Di browser → fallback share teks seperti sebelumnya
    if (navigator.share) {
        navigator.share({ text: text }).catch(function () {
            copyInvoiceText(text)
                .then(function () { showInvoiceToast('Pesan disalin. Tempel di WhatsApp.'); })
                .catch(function () { window.location.href = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(text); });
        });
        return;
    }

    copyInvoiceText(text)
        .then(function () { showInvoiceToast('Pesan disalin. Tempel di WhatsApp.'); })
        .catch(function () { window.location.href = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(text); });
}
</script>
</body>
</html>
