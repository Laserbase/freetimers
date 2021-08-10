<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \app\Models;
use \app\Models\StarageBin;

class FreetimersSpoilTest extends TestCase
{
    public function test_starage_bin_spoil()
    {   // string $product_id, int $quantity, float $cost, string $date_purchased
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $bin->spoil();

        $this->assertIsObject($bin);
    }

}