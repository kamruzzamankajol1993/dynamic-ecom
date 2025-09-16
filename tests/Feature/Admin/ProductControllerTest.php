<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    //use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_can_display_the_product_index_page(): void
    {
        $response = $this->get(route('product.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.product.index');
        $response->assertSee('Product List');
    }

    /**
     * This test is updated to correctly handle pagination.
     */
    #[Test]
 

    public function it_can_fetch_paginated_products_via_ajax(): void
    {
        // Arrange: Create 12 products so we can test the pagination limit.
        Product::factory()->count(12)->create();

        // Act: Make a GET request to the AJAX data route.
        $response = $this->get(route('ajax.product.data'));

        // Assert:
        $response->assertStatus(200);

        // 1. Check that the 'data' array contains 10 items because of paginate(10).
        $response->assertJsonCount(10, 'data');

        // 2. Check that the 'total' in the response is 12, which is the total number of products we created.
        $response->assertJsonPath('total', 12);
    }

    #[Test]
    public function it_can_display_the_create_product_page(): void
    {
        $response = $this->get(route('product.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.product.create');
        $response->assertSee('Create New Product');
    }

    #[Test]
    public function it_can_store_a_new_product(): void
    {
        // Arrange: Prepare the product data
        $productData = [
            'name' => 'New Awesome T-Shirt',
            'base_price' => 199.99,
            'purchase_price' => 99.99,
            'category_id' => Category::factory()->create()->id,
            'unit_id' => Unit::factory()->create()->id,
            'status' => 1,
        ];

        // Act: Send a POST request to the store route
        $response = $this->post(route('product.store'), $productData);

        // Assert
        $response->assertRedirect(route('product.index'));
        $response->assertSessionHas('success', 'Product created successfully.');

        // Also, check if the data is actually in the database
        $this->assertDatabaseHas('products', [
            'name' => 'New Awesome T-Shirt',
            'base_price' => 199.99,
        ]);
    }

    #[Test]
    public function it_can_display_the_show_product_page(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('product.show', $product));

        $response->assertStatus(200);
        $response->assertViewIs('admin.product.show');
        $response->assertSee($product->name);
    }

    #[Test]
    public function it_can_display_the_edit_product_page(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('product.edit', $product));

        $response->assertStatus(200);
        $response->assertViewIs('admin.product.edit');
        $response->assertSee('Edit Product: ' . $product->name);
    }

    #[Test]
    public function it_can_update_an_existing_product(): void
    {
        // Arrange
        $product = Product::factory()->create(['name' => 'Old Product Name']);
        $updatedData = [
            'name' => 'Updated Product Name',
            'base_price' => 123.45,
            'purchase_price' => $product->purchase_price,
            'category_id' => $product->category_id,
            'unit_id' => $product->unit_id,
        ];

        // Act
        $response = $this->put(route('product.update', $product), $updatedData);

        // Assert
        $response->assertRedirect(route('product.index'));
        $response->assertSessionHas('success', 'Product updated successfully.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name'
        ]);
        $this->assertDatabaseMissing('products', [
             'id' => $product->id,
            'name' => 'Old Product Name'
        ]);
    }

    #[Test]
    public function it_can_delete_a_product_via_ajax(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('ajax_products_delete', ['id' => $product->id]));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Product deleted successfully.']);

     
        $this->assertSoftDeleted('products', [
            'id' => $product->id
        ]);
    }
}
