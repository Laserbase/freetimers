<?php
declare(strict_types = 1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \app\Models;
use \app\Models\StarageBin;

class FreetimersFifoTest extends TestCase {
    public function test_fifo_calc()
    {   // gaap = Grnerally Accdpted Accounting Principles

        //--- default
        $default = new \App\Models\StorageBin("default", 10, 1.00, date("2021-01-01"));
        $default->add("default", 10, 10.00, date("2021-02-02"));
        $default->add("default", 10, 100.00, date("2021-03-03"));
        $overflow = $default->remove("default", 15);
        $calc = $default->calc();
        //$default->log("default='".print_r($calc, true)."'", __LINE__);
        $this->assertTrue($calc['quantity'] === 15);
        $this->assertTrue( (float) $calc['total'] == 1050.00);
        $this->assertTrue( (float) $calc['avco'] === 70.00);

        //--- fifo
        $fifo = new \App\Models\StorageBin("fifo", 10, 1.00, date("2021-01-01"), 'fifo');
        $fifo->add("fifo", 10, 10.00, date("2021-02-02"));
        $fifo->add("fifo", 10, 100.00, date("2021-03-03"));
        $overflow = $fifo->remove("fifo", 15);
        $calc = $fifo->calc();
        $this->assertTrue($calc['quantity'] === 15);
        $this->assertTrue( (float) $calc['total'] == 1050.00);
        $this->assertTrue( (float) $calc['avco'] === 70.00);

        //--- lifo
        $lifo = new \App\Models\StorageBin("lifo", 10, 1.00, date("2021-01-01"), 'lifo');
        $lifo->add("lifo", 10, 10.00, date("2021-02-02"));
        $lifo->add("lifo", 10, 100.00, date("2021-03-03"));
        $overflow = $lifo->remove("lifo", 15);
        $calc = $lifo->calc();
        $this->assertTrue( (int) $calc['quantity'] === 15);
        $this->assertTrue( (float) $calc['total'] === 60.00);
        $this->assertTrue( (float) $calc['avco'] === 4.00);

        //--- avco
        $avco = new \App\Models\StorageBin("avco", 10, 1.00, date("2021-01-01"), 'avco');
        $avco->add("avco", 10, 10.00, date("2021-02-02"));
        $avco->add("avco", 10, 100.00, date("2021-03-03"));
        $overflow = $avco->remove("avco", 15);
        $calc = $avco->calc();
        $this->assertTrue( (int)  $calc['quantity'] === 15);
        $this->assertTrue( (float) $calc['total'] == 1050.00);
        $this->assertTrue( (float) $calc['avco'] === 70.00);
    }

}
