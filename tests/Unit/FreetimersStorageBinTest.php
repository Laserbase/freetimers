<?php
declare(strict_types = 1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \app\Models;
use \app\Models\StarageBin;

class FreetimersStorageBinTest extends TestCase
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

    //--- create ---
    public function test_starage_bin_exists()
    {   // string $product_id, int $quantity, float $cost, string $date_purchased
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $this->assertIsObject($bin);
        $status = $bin->getStatus();
        $this->assertTrue($status['status'] === 'ok');
        $this->assertTrue($status['level'] === 10);
    }

    public function test_can_create_with_zero_stock()
    {
        $bin = new \App\Models\StorageBin("bolts", 0, 10.00, date("2021-01-01"));
        $status = $bin->getStatus();
        $this->assertTrue($status['status'] === 'empty');
        $this->assertTrue($status['level'] === 0);
    }


}
