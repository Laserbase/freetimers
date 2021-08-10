<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \app\Models;
use \app\Models\StarageBin;

class FreetimersTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function test_starage_bin_exists()
    {   // string $product_id, int $quantity, float $cost, string $date_purchased
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $this->assertIsObject($bin);
    }

    public function test_can_add_stock()
    {
        $this->expectException(\app\Models\StorageException::class);
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $bin->add("screws", 10, 10.99, date("2021-01-01"));
    }

    public function test_adding_zero_stock()
    {
        $this->expectException(\app\Models\StorageException::class);
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $bin->add("bolts", 0, 10.99, date("2021-01-01"));
    }
    public function test_adding_negative_stock()
    {
        $this->expectException(\app\Models\StorageException::class);
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $bin->add("bolts", -10, 10.99, date("2021-01-01"));
    }
}
