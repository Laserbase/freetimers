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

    public function test_removing_stock()
    {
        $bin = new \App\Models\StorageBin("bolts", 3, 10.00, date("2021-01-01"));
        $status = $bin->getStatus();
        $this->assertTrue($status["level"] === 3);

        $overflow = $bin->remove("bolts", 1);
        $status = $bin->getStatus();
        $this->assertTrue($overflow === 0); // 2
        $this->assertTrue($status["level"] === 2);

        $overflow = $bin->remove("bolts", 1);
        $status = $bin->getStatus();
        $this->assertTrue($overflow === 0); // 1
        $this->assertTrue($status["level"] === 1);

        $overflow = $bin->remove("bolts", 1);
        $this->assertTrue($overflow === 0); // error
        print_r($bin->getStatus());

        $this->expectException(\app\Models\StorageException::class);
        $overflow = $bin->remove("bolts", 1);
//        $this->assertTrue($overflow === 0);

    }

    public function test_removing_stock_10()
    {
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $status = $bin->getStatus();
        $this->assertTrue($status["level"] === 10);

        $overflow = $bin->remove("bolts", 5);
        $status = $bin->getStatus();
        $this->assertTrue($overflow === 0);
        $this->assertTrue($status["level"] === 5);

        $overflow = $bin->remove("bolts", 6);
        $status = $bin->getStatus();
        $this->assertTrue($overflow === 1); 
        $this->assertTrue($status["level"] === 0);

        $this->expectException(\app\Models\StorageException::class);
        $overflow = $bin->remove("bolts", 6);
        $this->assertTrue($overflow === 0); 
        $this->assertTrue($status["level"] === 0);

    }
    public function test_removing_stock_even()
    {
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $status = $bin->getStatus();
        $this->assertTrue($status["level"] === 10);
        
        //--- leave one stock
        $overflow = $bin->remove("bolts", 9);
        $status = $bin->getStatus();
        $this->assertTrue($overflow === 0); 
        $this->assertTrue($status["level"] === 1);

        $bin->add("bolts", 9, 10.00, date("03-03-2021"));
        $status = $bin->getStatus();
        $this->assertTrue($status["level"] === 10);
        
        //--- leave no stock
        $overflow = $bin->remove("bolts", 10);
        $status = $bin->getStatus();
        $this->assertTrue($overflow === 0); 
        $this->assertTrue($status["level"] === 0);

        $bin->add("bolts", 10, 10.00, date("03-03-2021"));
        $status = $bin->getStatus();
        $this->assertTrue($status["level"] === 10);

        //--- overflow one stock
        $overflow = $bin->remove("bolts", 11);
        $status = $bin->getStatus();
        $this->assertTrue($overflow === 1); 
        $this->assertTrue($status["level"] === 0);

        $bin->add("bolts", 10, 10.00, date("03-03-2021"));
        $status = $bin->getStatus();
        $this->assertTrue($status["level"] === 10);
        //---
    }

    public function test_moving_stock()
    {
        $this->expectException(\app\Models\StorageException::class);

        $bolts = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $screws = new \App\Models\StorageBin("screws", 1, 1.00, date("2021-01-01"));
        $overflow = Bolts->move($screws, 1);
        
        $this->assertTrue(false);
    }
}
