<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Ödeme Başarılı</title>
<style>
  body { margin:0; padding:0; background:#f4f6fb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; color:#1f2d3d; }
  .email-wrap { width:100%; background:#f4f6fb; padding:20px 0; }
  .container { max-width:680px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 6px 18px rgba(31,45,61,0.08); }
  .header { padding:24px; text-align:left; border-bottom:1px solid #eef2f6; }
  .logo { height:36px; }
  .content { padding:24px; }
  h1 { margin:0 0 8px; font-size:20px; color:#0b2545; }
  p { margin:0 0 14px; color:#475569; font-size:15px; line-height:1.5; }
  .order-meta { font-size:13px; color:#7b8794; margin-bottom:18px; }
  table { width:100%; border-collapse:collapse; margin-bottom:18px; }
  th, td { padding:10px 12px; text-align:left; border-bottom:1px solid #eef2f6; font-size:14px; }
  th { background:#fbfdff; color:#0b2545; font-weight:600; }
  .qty { width:72px; text-align:center; }
  .price, .total-col { text-align:right; }
  .summary { padding:14px; background:#f8fafc; border-radius:8px; font-size:15px; display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
  .btn { display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:10px 18px; border-radius:8px; font-weight:600; }
  .footer { padding:18px; border-top:1px solid #eef2f6; font-size:13px; color:#7b8794; text-align:center; }
  @media (max-width:520px){
    .container { margin:0 12px; }
    th, td { padding:10px 8px; font-size:13px; }
    .summary { flex-direction:column; align-items:flex-start; gap:8px; }
  }
</style>
</head>
<body>
  <div class="email-wrap">
    <div class="container" role="article" aria-label="Ödeme Başarılı">
      
      <!-- Header -->
      <div class="header">
        <img src="" alt="Logo" class="logo">
      </div>

      <!-- Content -->
      <div class="content">
        <h1>Ödeme Başarılı 🎉</h1>
        <p>Merhaba {{ $order->user->name }},</p>
        <p>Ödemeniz başarıyla alınmıştır. Aşağıda siparişinizin özeti yer alıyor. İlginiz için teşekkür ederiz!</p>

        <div class="order-meta">
          Sipariş No: <strong>{{ $order->id }}</strong> &nbsp;•&nbsp;
          Tarih: <strong>{{ $order->created_at->format('d.m.Y') }}</strong> &nbsp;•&nbsp;
          Ödeme Yöntemi: <strong>{{ $order->payment_method }}</strong>
        </div>

        <!-- Ürün Tablosu -->
        <table role="table" aria-label="Sipariş detayları">
          <thead>
            <tr>
              <th>Ürün</th>
              <th class="qty">Adet</th>
              <th class="price">Birim Fiyat</th>
              <th class="total-col">Ara Toplam</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->order_items as $item)
            <tr>
              <td>{{ $item->product->name }}</td>
              <td class="qty">{{ $item->quantity }}</td>
              <td class="price">{{ $order->currency }} {{ $item->product_price_snapshot }}</td>
              <td class="total-col">{{ $order->currency }} {{ $item->line_total }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>

        <!-- Toplam -->
        <div class="summary" role="note">
          <div style="text-align:right">
            <div style="font-size:13px; color:#7b8794; margin-top:8px;">Genel Toplam</div>
            <div style="font-weight:900; font-size:20px; margin-top:6px; color:#0b2545;">{{ $order->currency }} {{ $order->total_price }}</div>
          </div>
        </div>

        <!-- CTA -->
        <p style="margin-bottom:4px;">Sipariş detaylarını görmek veya fatura bilgisi almak için butona tıklayın.</p>
        <p><a href="#" class="btn" target="_blank" rel="noopener">Siparişimi Görüntüle</a></p>

        <hr style="border:none; height:1px; background:#eef2f6; margin:20px 0;">

        <p style="color:#7b8794; font-size:13px;">Eğer bu ödemeyi siz gerçekleştirmediyseniz lütfen bizimle hemen iletişime geçin: <a href="#">a@gmail.com</a></p>

      </div>

      <!-- Footer -->
      <div class="footer">
        
        <div style="margin-top:6px;">© {{ date('Y') }} . Tüm hakları saklıdır.</div>
      </div>

    </div>
  </div>
</body>
</html>
