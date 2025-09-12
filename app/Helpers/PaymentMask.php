<?php

namespace App\Helpers;

class PaymentMask
{
    // Sadece rakamları alır, son 4 haneyi açık bırakır
    public static function maskCardNumber(?string $pan): ?string
    {
        if ($pan === null || $pan === '') {
            return $pan;
        }

        // Boşlukları ve tireleri kaldır, sadece rakamları al
        $digitsOnly = preg_replace('/\D+/', '', $pan);
        $length = strlen($digitsOnly);

        if ($length === 0) {
            return $pan;
        }

        // Son 4 açık kalsın, geri kalan yıldız
        $visible = substr($digitsOnly, -4);
        $maskedCount = max(0, $length - 4);
        $masked = str_repeat('*', $maskedCount) . $visible;

        // 4'lü gruplama: 4111 **** **** 1111
        return trim(implode(' ', str_split($masked, 4)));
    }

    // CVV tamamen maskelenir (uzunluk kadar yıldız)
    public static function maskCvv(?string $cvv): ?string
    {
        if ($cvv === null || $cvv === '') {
            return $cvv;
        }
        $len = strlen(preg_replace('/\s+/', '', $cvv));
        return str_repeat('*', max(3, min(4, $len))); // 3-4 char
    }

    // "MM/YY" veya "MM/YYYY" → "MM/**"
    public static function maskExpiry(?string $exp): ?string
    {
        if ($exp === null || $exp === '') {
            return $exp;
        }

        // MM/YY veya MM/YYYY yakala
        if (preg_match('/^\s*(\d{2})\s*\/\s*(\d{2}|\d{4})\s*$/', $exp, $m)) {
            $mm = $m[1];
            return "{$mm}/**";
        }

        // Beklenmeyen formatta ise tamamen maskele
        return '**/**';
    }

    // Generic alan maskeleme (opsiyonel): ilk n karakter görünür, geri kalanı *
    public static function maskKeepPrefix(string $value, int $visiblePrefix = 2): string
    {
        $length = strlen($value);
        if ($length <= $visiblePrefix) {
            return str_repeat('*', $length);
        }
        return substr($value, 0, $visiblePrefix) . str_repeat('*', $length - $visiblePrefix);
    }

    // Ödeme payload’ını güvenli biçimde maskele ve yeni bir dizi döndür
    public static function maskRequestPayload(array $payload): array
    {
        // Orijinali değiştirmemek için kopya üzerinde çalış
        $masked = $payload;

        if (array_key_exists('card_number', $masked)) {
            $masked['card_number'] = self::maskCardNumber((string) $masked['card_number']);
        }

        if (array_key_exists('cvv', $masked)) {
            $masked['cvv'] = self::maskCvv((string) $masked['cvv']);
        }

        if (array_key_exists('expiry_date', $masked)) {
            $masked['expiry_date'] = self::maskExpiry((string) $masked['expiry_date']);
        }
        return $masked;
    }

    // Sağlayıcıdan dönen yanıtta maskelenmesi gereken alanlar varsa (genelde yoktur)
    public static function maskResponsePayload(array $payload): array
    {
        // Varsayılan olarak aynen döndür. Eğer sağlayıcı yanıtında kart/exp gibi alanlar gelirse burada maskele.
        return $payload;
    }
}