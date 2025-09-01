<?php

namespace App\Filament\Resources\Registration\OrderResource\Schemas;

use App\Models\Registration\Participant;
use App\Models\Registration\Product;
use Illuminate\Support\Str;

class Pricing
{
    public static function getProductUnitBasePrice(Product $product, Participant $participant): float
    {
        $country = (string) ($participant->country ?? '');
        $isIndonesia = Str::of($country)->lower()->contains('indonesia');

        if ($product->is_early_bird) {
            return (float) ($isIndonesia ? $product->early_bird_idr : $product->early_bird_usd);
        }
        if ($product->is_regular) {
            return (float) ($isIndonesia ? $product->regular_idr : $product->regular_usd);
        }
        if ($product->is_on_site) {
            return (float) ($isIndonesia ? $product->on_site_idr : $product->on_site_usd);
        }

        return (float) ($isIndonesia ? ($product->regular_idr ?? 0) : ($product->regular_usd ?? 0));
    }

    public static function calcLineUnitPrice(Product $product, Participant $participant, int $qty): float
    {
        return $qty * self::getProductUnitBasePrice($product, $participant);
    }

    public static function calcTotals(array $items, float $discount = 0): float
    {
        $sum = 0;
        foreach ($items as $it) {
            $sum += (float) ($it['unit_price'] ?? 0);
        }
        return max(0, $sum - max(0, $discount));
    }
}
