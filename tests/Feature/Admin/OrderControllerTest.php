<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
   // use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_can_display_the_order_index_page(): void
    {
        $response = $this->get(route('order.index'));
        $response->assertStatus(200)->assertViewIs('admin.order.index');
    }

    #[Test]
    public function it_can_fetch_paginated_orders_via_ajax(): void
    {
        Order::factory()->count(12)->create();
        $response = $this->get(route('ajax.order.data'));
        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('total', 12);
    }

    #[Test]
    public function it_can_display_the_create_order_page(): void
    {
        $response = $this->get(route('order.create'));
        $response->assertStatus(200)->assertViewIs('admin.order.create');
    }

    #[Test]
    public function it_can_store_a_new_order(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['base_price' => 150]);
        $orderDate = Carbon::now()->format('d-m-Y');

        $orderData = [
            'customer_id' => $customer->id,
            'invoice_no' => 'INV-TEST-123',
            'order_date' => $orderDate,
            'subtotal' => 300,
            'total_amount' => 300,
            'total_pay' => 0,
            'due' => 300,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 150,
                    'size' => 'L',
                    'color' => 'Blue',
                    'discount' => 0,
                ],
            ],
        ];

        $response = $this->post(route('order.store'), $orderData);

        $response->assertRedirect(route('order.index'))
            ->assertSessionHas('success', 'Order created successfully.');

        $this->assertDatabaseHas('orders', ['invoice_no' => 'INV-TEST-123']);
        $this->assertDatabaseHas('order_details', ['product_id' => $product->id, 'quantity' => 2]);
    }

    #[Test]
    public function it_can_display_the_show_order_page(): void
    {
        $order = Order::factory()->create();
        $response = $this->get(route('order.show', $order));
        $response->assertStatus(200)
            ->assertViewIs('admin.order.show')
            ->assertSee($order->invoice_no);
    }

    #[Test]
    public function it_can_display_the_edit_order_page(): void
    {
        $order = Order::factory()->create();
        $response = $this->get(route('order.edit', $order));
        $response->assertStatus(200)->assertViewIs('admin.order.edit');
    }

    #[Test]
    public function it_can_update_an_existing_order(): void
    {
        $order = Order::factory()->has(OrderDetail::factory()->count(1), 'orderDetails')->create();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $orderDate = Carbon::now()->format('d-m-Y');

        $updatedData = [
            'customer_id' => $customer->id,
            'invoice_no' => 'INV-UPDATED-456',
            'order_date' => $orderDate,
            'subtotal' => 500,
            'total_amount' => 500,
            'total_pay' => 100,
            'due' => 400,
            'status' => 'processing',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100,
                    'size' => 'XL',
                    'color' => 'Red',
                    'discount' => 0,
                ],
            ],
        ];

        $response = $this->put(route('order.update', $order), $updatedData);

        $response->assertRedirect(route('order.index'))
            ->assertSessionHas('success', 'Order updated successfully.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'invoice_no' => 'INV-UPDATED-456',
            'status' => 'processing'
        ]);
        $this->assertDatabaseHas('order_details', ['order_id' => $order->id, 'quantity' => 5]);
    }

    #[Test]
    public function it_can_delete_an_order(): void
    {
        $order = Order::factory()->create();
        $response = $this->delete(route('order.destroy', $order));
        $response->assertStatus(200)->assertJson(['message' => 'Order deleted successfully.']);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    #[Test]
    public function it_can_update_order_status(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $response = $this->post(route('order.update-status', $order), ['status' => 'delivered']);

        $response->assertStatus(200)->assertJsonFragment(['message' => 'Order status updated successfully.']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'delivered']);
        $this->assertDatabaseHas('order_trackings', ['order_id' => $order->id, 'status' => 'delivered']);
    }

    #[Test]
    public function it_can_bulk_update_order_status(): void
    {
        $orders = Order::factory()->count(3)->create(['status' => 'pending']);
        $orderIds = $orders->pluck('id')->toArray();

        $response = $this->post(route('order.bulk-update-status'), [
            'ids' => $orderIds,
            'status' => 'shipped'
        ]);

        $response->assertStatus(200)->assertJsonFragment(['message' => 'Selected orders have been updated.']);

        foreach ($orderIds as $id) {
            $this->assertDatabaseHas('orders', ['id' => $id, 'status' => 'shipped']);
        }
    }
}
