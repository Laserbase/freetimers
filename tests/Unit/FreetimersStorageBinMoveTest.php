<?php
declare(strict_types = 1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \app\Models;
use \app\Models\StarageBin;

class FreetimersStorageBinMoveTest extends TestCase
{
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
        }
    
}