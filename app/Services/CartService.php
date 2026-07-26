<?php

namespace App\Services;

use App\Models\CustomizationOption;
use App\Models\Product;

class CartService
{
    /**
     * Resolves cart session array to detailed item objects including Product & CustomizationOption instances.
     *
     * @param array $cart
     * @return array
     */
    public function resolveItems(array $cart): array
    {
        if (empty($cart)) {
            return [];
        }

        $products   = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $optionsMap = $this->loadOptionsFromCart($cart);
        $items      = [];

        foreach ($cart as $id => $item) {
            $product = $products->get($id);
            if ($product) {
                $customizationIds = $this->normalizeCustomizationIds($item['customizations'] ?? []);
                $items[] = [
                    'product'              => $product,
                    'quantity'             => $item['quantity'],
                    'note'                 => $item['note'] ?? null,
                    'customizations'       => $customizationIds,
                    'customizationOptions' => collect($customizationIds)
                        ->map(fn($oid) => $optionsMap->get($oid))
                        ->filter()
                        ->values(),
                ];
            }
        }

        return $items;
    }

    /**
     * Normalizes customization arrays from session (which could be plain ints or {id: "..."} objects).
     *
     * @param array $rawCustomizations
     * @return array<int>
     */
    public function normalizeCustomizationIds(array $rawCustomizations): array
    {
        return array_values(array_filter(
            array_map(fn($c) => is_array($c) ? (int)($c['id'] ?? 0) : (int)$c, $rawCustomizations),
            fn($id) => $id > 0
        ));
    }

    /**
     * Load CustomizationOption models matching all IDs in cart.
     */
    public function loadOptionsFromCart(array $cart)
    {
        $ids = collect($cart)
            ->pluck('customizations')
            ->flatten()
            ->map(fn($c) => is_array($c) ? (int)($c['id'] ?? 0) : (int)$c)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return collect();
        }

        return CustomizationOption::whereIn('id', $ids)->get()->keyBy('id');
    }
}
