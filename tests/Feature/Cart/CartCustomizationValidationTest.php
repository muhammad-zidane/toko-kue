<?php

use App\Models\Product;

it('rejects invalid customization JSON without changing the cart', function (string $payload) {
    $product = Product::factory()->create();
    $cart = [$product->id => [
        'quantity' => 2,
        'note' => 'Existing note',
        'customizations' => [3],
    ]];

    $response = $this->withSession(['cart' => $cart])->post('/cart/add', [
        'product_id' => $product->id,
        'quantity' => 1,
        'customizations_json' => $payload,
    ]);

    $response->assertRedirect()->assertSessionHasErrors('customizations_json');
    expect(session('cart'))->toBe($cart);
})->with([
    'malformed JSON' => '[1,',
    'scalar integer' => '1',
    'scalar string' => '"abc"',
    'scalar boolean' => 'true',
    'scalar null' => 'null',
    'object' => '{"id":1}',
    'nested array' => '[[1]]',
    'mixed invalid array' => '[1,"abc"]',
    'object with nested ID' => '[{"id":{"value":1}}]',
    'object without ID' => '[{"price":0}]',
]);

it('accepts integer customization IDs and preserves their normalization', function () {
    $product = Product::factory()->create();

    $response = $this->post('/cart/add', [
        'product_id' => $product->id,
        'customizations_json' => '[1,2]',
    ]);

    $response->assertRedirect(route('cart.index'))->assertSessionHasNoErrors();
    expect(session("cart.{$product->id}.customizations"))->toBe([1, 2]);
});

it('accepts the frontend customization object format', function () {
    $product = Product::factory()->create();

    $response = $this->post('/cart/add', [
        'product_id' => $product->id,
        'customizations_json' => '[{"id":"7","price":0}]',
    ]);

    $response->assertRedirect(route('cart.index'))->assertSessionHasNoErrors();
    expect(session("cart.{$product->id}.customizations"))->toBe([7]);
});

it('accepts an empty customization array', function () {
    $product = Product::factory()->create();

    $response = $this->post('/cart/add', [
        'product_id' => $product->id,
        'customizations_json' => '[]',
    ]);

    $response->assertRedirect(route('cart.index'))->assertSessionHasNoErrors();
    expect(session("cart.{$product->id}.customizations"))->toBe([]);
});
