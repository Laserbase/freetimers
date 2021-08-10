<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \app\Models;
use \app\Models\StarageBin;

class FreetimersLifoTest extends TestCase {
    public function test_fifo_calc()
    {   // gaap = Grnerally Accdpted Accounting Principles
        $default = new \App\Models\StorageBin("default", 10, 1.00, date("2021-01-01"));
        $default->add("default", 10, 10.00, date("2021-02-02"));
        $default->add("default", 10, 100.00, date("2021-03-03"));

        $fifo = new \App\Models\StorageBin("fifo", 10, 1.00, date("2021-01-01"), 'fifo');
        $fifo->add("fifo", 10, 10.00, date("2021-02-02"));
        $fifo->add("fifo", 10, 100.00, date("2021-03-03"));

        $lifo = new \App\Models\StorageBin("lifo", 10, 1.00, date("2021-01-01"), 'lifo');
        $lifo->add("lifo", 10, 10.00, date("2021-02-02"));
        $lifo->add("lifo", 10, 100.00, date("2021-03-03"));

        $avco = new \App\Models\StorageBin("avco", 10, 1.00, date("2021-01-01"), 'avco');
        $avco->add("avco", 10, 10.00, date("2021-02-02"));
        $avco->add("avco", 10, 100.00, date("2021-03-03"));

        $status = $default->getStatus();
        $default->log("status='".print_r($status, true)."'", __LINE__);

        $this->assertTrue(false);
    }

}
