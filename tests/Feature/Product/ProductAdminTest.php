<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

function makeAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

function makeUser(): User
{
    return User::factory()->create(['role' => 'customer']);
}

function validProductData(): array
{
    $category = Category::factory()->create();
    return [
        'category_id' => $category->id,
        'name'        => 'Produk Test Baru',
        'description' => 'Deskripsi produk test',
        'price'       => 75000,
        'stock'       => 10,
        'is_available'=> true,
    ];
}

it('admin can create a product', function () {
    $admin    = makeAdmin();
    $data     = validProductData();

    $response = $this->actingAs($admin)->post('/admin/products', $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('products', ['name' => 'Produk Test Baru']);
});

it('admin can update a product', function () {
    $admin   = makeAdmin();
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->patch('/admin/products/' . $product->slug, [
        'category_id' => $product->category_id,
        'name'        => 'Nama Diupdate',
        'description' => $product->description,
        'price'       => $product->price,
        'stock'       => $product->stock,
        'is_available'=> true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Nama Diupdate']);
});

it('admin can delete a product', function () {
    $admin   = makeAdmin();
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->delete('/admin/products/' . $product->slug);

    $response->assertRedirect();
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

it('regular user cannot access admin product page', function () {
    $user = makeUser();

    $response = $this->actingAs($user)->get('/admin/products-list');

    $response->assertStatus(403);
});

it('guest cannot access admin product page', function () {
    $response = $this->get('/admin/products-list');

    $response->assertRedirect('/login');
});

it('it validates required fields on create', function () {
    $admin = makeAdmin();

    $response = $this->actingAs($admin)->post('/admin/products', []);

    $response->assertSessionHasErrors(['name', 'price', 'stock']);
});
