<?php
declare(strict_types = 1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \app\Models;
use \app\Models\StarageBin;

class FreetimersSorageBinRemoveTest extends TestCase
{
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
}