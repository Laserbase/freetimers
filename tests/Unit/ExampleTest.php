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

    //--- create ---
    public function test_starage_bin_exists()
    {   // string $product_id, int $quantity, float $cost, string $date_purchased
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $this->assertIsObject($bin);
    }

    public function test_cannot_create_with_zero_stock()
    {
        $this->expectException(\app\Models\StorageException::class);
        $bin = new \App\Models\StorageBin("bolts", 0, 10.00, date("2021-01-01"));
        $bin->add("screws", 10, 10.99, date("2021-01-01"));
    }

    //--- add ---
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

    //--- remove ---
    public function test_removing_stock()
    {
        $bin = new \App\Models\StorageBin("bolts", 3, 10.00, date("2021-01-01"));
        $status = $bin->getStatus();
        //$bin->log("status='".print_r($status, true)."'", __LINE__);
        $this->assertTrue($status["level"] === 3);

        $overflow = $bin->remove("bolts", 1);
        $status = $bin->getStatus();
        //$bin->log("status='".print_r($status, true)."'", __LINE__);
        $this->assertTrue($overflow === 0); // 2
        $this->assertTrue($status["level"] === 2);

        $overflow = $bin->remove("bolts", 1);
        $status = $bin->getStatus();
        //$bin->log("status='".print_r($status, true)."'", __LINE__);
        $this->assertTrue($overflow === 0); // 1
        $this->assertTrue($status["level"] === 1);

        $overflow = $bin->remove("bolts", 1);
        $this->assertTrue($overflow === 0); // error

        $this->expectException(\app\Models\StorageException::class);
        $overflow = $bin->remove("bolts", 1);
        $this->assertTrue($overflow === 0);

    }

    public function test_removing_stock_10()
    {
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $status = $bin->getStatus();
        //$bin->log("status='".print_r($status, true)."'", __LINE__);
        $this->assertTrue($status["level"] === 10);

        $overflow = $bin->remove("bolts", 5);
        $status = $bin->getStatus();
        //$bin->log("status='".print_r($status, true)."'", __LINE__);
        $this->assertTrue($overflow === 0);
        $this->assertTrue($status["level"] === 5);

        $overflow = $bin->remove("bolts", 6);
        $status = $bin->getStatus();
        //$bin->log("status='".print_r($status, true)."'", __LINE__);
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
        
        //--- reset
        $bin->add("bolts", 9, 10.00, date("03-03-2021"));
        $status = $bin->getStatus();
        $this->assertTrue($status["level"] === 10);
        //--- leave no stock
        $overflow = $bin->remove("bolts", 10);
        $status = $bin->getStatus();
        $this->assertTrue($overflow === 0); 
        $this->assertTrue($status["level"] === 0);

        //--- reset
        $bin->add("bolts", 10, 10.00, date("03-03-2021"));
        $status = $bin->getStatus();
        $this->assertTrue($status["level"] === 10);
        //--- overflow one stock
        $overflow = $bin->remove("bolts", 11);
        $status = $bin->getStatus();
        $this->assertTrue($overflow === 1); 
        $this->assertTrue($status["level"] === 0);

        //--- reset
        $bin->add("bolts", 10, 10.00, date("03-03-2021"));
        $status = $bin->getStatus();
        $this->assertTrue($status["overflow"] === 0); 
        $this->assertTrue($status["level"] === 10);
        //---
    }

    //--- move ---
    public function test_moving_stock_different_products()
    {
        $this->expectException(\app\Models\StorageException::class);
        //$this->expectedExceptionMessage('xxx');

        $bolts = new \App\Models\StorageBin("bolts", 10, 1.00, date("2021-01-01"));
        $screws = new \App\Models\StorageBin("screws", 10, 1.00, date("2021-01-01"));

        $overflow = $bolts->move($screws, 2);
    }
    public function test_moving_zero_stock()
    {
        $this->expectException(\app\Models\StorageException::class);

        $bolts1 = new \App\Models\StorageBin("bolts", 10, 0.00, date("2021-01-01"));
        $bolts2 = new \App\Models\StorageBin("bolts", 10, 1.00, date("2021-01-01"));

        $overflow = $bolts1->move($bolts2, 0);
    }
    public function test_moving_same_stock()
    {
        $bolts1 = new \App\Models\StorageBin("bolts", 1, 1.00, date("2021-01-01"));
        $bolts1->add("bolts", 9, 10.00, date("2021-02-02"));
        $status1 = $bolts1->getStatus();
        $this->assertTrue($status1["level"] === 10);

        $bolts2 = new \App\Models\StorageBin("bolts", 1, 1.00, date("2021-01-01"));
        $bolts2->add("bolts", 9, 10.00, date("2021-02-02"));
        $status2 = $bolts2->getStatus();
        $this->assertTrue($status2["level"] === 10);

        $overflow = $bolts1->move($bolts2, 9);
        $status1 = $bolts1->getStatus();
        $status2 = $bolts2->getStatus();
        //$bolts1->log("bolts1='".print_r($status1, true)."'", __LINE__);
        //$bolts2->log("bolts2='".print_r($status2, true)."'", __LINE__);

        $this->assertTrue($status1['level'] === 1);
        $this->assertTrue($status2['level'] === 19);
    }
    public function test_moving_same_stock_boundary()
    {
        $bolts1 = new \App\Models\StorageBin("bolts", 1, 1.00, date("2021-01-01"));
        $bolts1->add("bolts", 2, 10.00, date("2021-02-02"));
        $status1 = $bolts1->getStatus();
        $this->assertTrue($status1["level"] === 3);

        $bolts2 = new \App\Models\StorageBin("bolts", 1, 1.00, date("2021-01-01"));
        //$bolts2->add("bolts", 9, 10.00, date("2021-02-02"));
        $status2 = $bolts2->getStatus();
        $this->assertTrue($status2["level"] === 1);

        $overflow = $bolts1->move($bolts2, 1);
        $status1 = $bolts1->getStatus();
        $status2 = $bolts2->getStatus();
        $this->assertTrue($overflow === 0);
        $this->assertTrue($status1['level'] === 2);
        $this->assertTrue($status2['level'] === 2);

        $overflow = $bolts1->move($bolts2, 1);
        $status1 = $bolts1->getStatus();
        $status2 = $bolts2->getStatus();
        $this->assertTrue($overflow === 0);
        $this->assertTrue($status1['level'] === 1);
        $this->assertTrue($status2['level'] === 3);

        $overflow = $bolts1->move($bolts2, 1);
        $status1 = $bolts1->getStatus();
        $status2 = $bolts2->getStatus();
        $this->assertTrue($overflow === 0);
        $this->assertTrue($status1['level'] === 0);
        $this->assertTrue($status2['level'] === 4);

        $this->expectException(\app\Models\StorageException::class);
        $overflow = $bolts1->move($bolts2, 1);
        $status1 = $bolts1->getStatus();
        $status2 = $bolts2->getStatus();
        $this->assertTrue($overflow === 1);
        $this->assertTrue($status1['level'] === 0);
        $this->assertTrue($status2['level'] === 4);
    }
    public function test_moving_same_stock_boundary_overflow()
    {
        $bolts1 = new \App\Models\StorageBin("bolts", 1, 1.00, date("2021-01-01"));
        $status1 = $bolts1->getStatus();
        $this->assertTrue($status1["overflow"] === 0);
        $this->assertTrue($status1["level"] === 1);

        $bolts1->add("bolts", 9, 10.00, date("2021-02-02"));
        $status1 = $bolts1->getStatus();
        $this->assertTrue($status1["overflow"] === 0);
        $this->assertTrue($status1["level"] === 10);

        $bolts2 = new \App\Models\StorageBin("bolts", 1, 1.00, date("2021-01-01"));
        $status2 = $bolts2->getStatus();
        $this->assertTrue($status2["overflow"] === 0);
        $this->assertTrue($status2["level"] === 1);

        $overflow = $bolts1->move($bolts2, 20);
        $this->assertTrue($overflow === 10);

        $status1 = $bolts1->getStatus();
        $this->assertTrue($status1["overflow"] === $overflow);
        $this->assertTrue($status1['level'] === 0);

        $status2 = $bolts2->getStatus();
        $this->assertTrue($status2["overflow"] === 0);
        $this->assertTrue($status2['level'] === 11);

        //$bolts1->log("overflow='".print_r($overflow, true)."'", __LINE__);
        //$bolts1->log("bolts1='".print_r($bolts1, true)."'", __LINE__);
    }

}
