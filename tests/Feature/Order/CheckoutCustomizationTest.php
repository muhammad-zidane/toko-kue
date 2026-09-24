<?php

use App\Models\Category;
use App\Models\CustomizationOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemCustomization;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

function checkoutCustomizationData(Product $product, array $ids, int $quantity = 1): array
{
    return [
        'delivery_method' => 'pickup',
        'delivery_date' => now()->addDays(3)->format('Y-m-d'),
        'delivery_slot' => '08:00-11:00',
        'payment_method' => 'transfer_bank',
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'customizations' => json_encode($ids),
            ],
        ],
    ];
}

function customizationOption(Category $category, string $type, int $price = 0, bool $active = true): CustomizationOption
{
    return CustomizationOption::create([
        'category_id' => $category->id,
        'type' => $type,
        'name' => ucfirst($type).' '.uniqid(),
        'extra_price' => $price,
        'is_active' => $active,
    ]);
}

it('rejects invalid checkout customizations without creating records or changing stock', function (string $case) {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5, 'price' => 100000]);
    $category = $product->category;
    $otherCategory = Category::factory()->create();

    $ids = match ($case) {
        'nonexistent' => [999999],
        'inactive' => [customizationOption($category, 'rasa', 10000, false)->id],
        'other category' => [customizationOption($otherCategory, 'rasa', 10000)->id],
        'duplicate id' => (function () use ($category) {
            $id = customizationOption($category, 'rasa', 10000)->id;

            return [$id, $id];
        })(),
        'two sizes' => [
            customizationOption($category, 'ukuran', 10000)->id,
            customizationOption($category, 'ukuran', 20000)->id,
        ],
    };

    $response = $this->actingAs($user)->post('/orders', checkoutCustomizationData($product, $ids));

    $response->assertSessionHasErrors('items.0.customizations');
    expect(Order::count())->toBe(0)
        ->and(Payment::count())->toBe(0)
        ->and(OrderItem::count())->toBe(0)
        ->and(OrderItemCustomization::count())->toBe(0)
        ->and($product->fresh()->stock)->toBe(5);
})->with(['nonexistent', 'inactive', 'other category', 'duplicate id', 'two sizes']);

it('prices valid customizations from the database and allows different types and multiple toppings', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5, 'price' => 100000]);
    $category = $product->category;
    $options = [
        customizationOption($category, 'rasa', 10000),
        customizationOption($category, 'ukuran', 20000),
        customizationOption($category, 'lainnya', 5000),
        customizationOption($category, 'topping', 3000),
        customizationOption($category, 'topping', 2000),
    ];
    $ids = array_map(fn ($option) => $option->id, $options);
    $data = checkoutCustomizationData($product, $ids, 2);
    $data['items'][0]['extra_price'] = 1;

    $response = $this->actingAs($user)->post('/orders', $data);

    $response->assertRedirect();
    $order = Order::firstOrFail();
    $item = $order->orderItems()->firstOrFail();
    expect((float) $item->price)->toBe(140000.0)
        ->and((float) $order->total_price)->toBe(280000.0)
        ->and($product->fresh()->stock)->toBe(3)
        ->and(Payment::count())->toBe(1)
        ->and($item->customizations()->count())->toBe(5);
    foreach ($options as $option) {
        $this->assertDatabaseHas('order_item_customizations', [
            'order_item_id' => $item->id,
            'customization_option_id' => $option->id,
            'extra_price' => $option->extra_price,
        ]);
    }
});

it('accepts an active global customization and uses its database price', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5, 'price' => 100000]);
    $option = CustomizationOption::create([
        'category_id' => null,
        'type' => 'rasa',
        'name' => 'Rasa global',
        'extra_price' => 15000,
        'is_active' => true,
    ]);
    $data = checkoutCustomizationData($product, [$option->id], 2);
    $data['items'][0]['extra_price'] = 1;

    $response = $this->actingAs($user)->post('/orders', $data);

    $response->assertSessionHasNoErrors()->assertRedirect();
    $order = Order::firstOrFail();
    $item = $order->orderItems()->firstOrFail();
    $this->assertDatabaseHas('order_item_customizations', [
        'order_item_id' => $item->id,
        'customization_option_id' => $option->id,
        'extra_price' => $option->extra_price,
    ]);
    expect((float) $item->price)->toBe(115000.0)
        ->and((float) $order->total_price)->toBe(230000.0)
        ->and($product->fresh()->stock)->toBe(3);
});

it('rejects malformed customization IDs instead of dropping them', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);
    $data = checkoutCustomizationData($product, []);
    $data['items'][0]['customizations'] = '0';

    $response = $this->actingAs($user)->post('/orders', $data);

    $response->assertSessionHasErrors('items.0.customizations');
    expect(Order::count())->toBe(0)
        ->and(Payment::count())->toBe(0)
        ->and($product->fresh()->stock)->toBe(5);
});

it('keeps customization optional', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5, 'price' => 100000]);

    $response = $this->actingAs($user)->post('/orders', checkoutCustomizationData($product, []));

    $response->assertRedirect();
    expect(OrderItemCustomization::count())->toBe(0)
        ->and((float) Order::firstOrFail()->total_price)->toBe(100000.0);
});
