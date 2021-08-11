<?php
declare(strict_types = 1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \app\Models;
use \app\Models\StarageBin;

class FreetimersStorageBinAddTest extends TestCase
{
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
    
}