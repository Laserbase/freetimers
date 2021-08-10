<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Requirements: Create a PHP object that will act as a Bin in a warehouse.
 * Your bin should have (but not be resticted to) the following methods:
 * A method to add stock to the bin
 * A method to remove stock from the bin
 * A method to move stock from the bin to another bin
 * A method to mark stock in the bin as spoiled
 * A method to calculate the value of the stock in the bin using the following methods
 *      Last in first out
 *      First in first out
 *      Average stock price
 */

class StorageException extends \Exception { }
class StorageBin extends Model
{
    //use HasFactory;
    private string $product_id = "";
    private int $level = 0;
    private $control = [];
    private $overflow = 0;
    private string $status = 'undefined';

    public function __construct(string $product_id, int $quantity, float $cost, string $date_purchased)
    {
        if ($this->level === 0) {
            $this->product_id = $product_id;
            $this->level = 0;
            $this->control = [];
            $this->status = 'ok';
        }

        $this->add($product_id, $quantity, $cost, $date_purchased);
    }
    public function add(string $product_id, int $quantity, float $cost, string $date_purchased)
    {
        $this->check_quantity($quantity)
            ->check_product_id($product_id)
            ->check_can_add($quantity);

        $this->level += $quantity;
        $this->setStatus('ok');
        $this->overflow = 0;
        $this->control[] = ["quantity" => $quantity, "cost" => $cost, "date" => $date_purchased];
    }

    public function remove(string $product_id, int $quantity) : int
    {
        $this->check_quantity($quantity)
            ->check_product_id($product_id)
            ->check_stock_level($product_id, $quantity)
            ->check_can_remove($quantity);
        
        $quantity_to_remove = $quantity;
        foreach ($this->control as $key => $control) {
            if ($quantity_to_remove < 1) {
                break; // already complete
            }

            $quantity_available = $control['quantity'];
            if ($quantity_available < 1) {
                continue; // already picked
            }

            if ($quantity_to_remove > $quantity_available) {
                $quantity_to_remove -= $quantity_available;
            } else {
                $quantity_available = $quantity_to_remove;
                $quantity_to_remove = 0;
            }

            $this->level -= $quantity_available;
            $this->control[$key]['quantity'] -= $quantity_available;

        
        }

        if ($this->level < 1) {
            $this->status = 'empty';
        }

        return $this->overflow = $quantity_to_remove;
    }
    public function move(StorageBin $to_bin, int $quantity) : int
    {
        $product_id = $this->product_id;
        $to_product_id = $to_bin->getStatus()['product_id'];
        if ($product_id !== $to_product_id) {
            throw new StorageException("Unable to move '{$quantity}' of '{$product_id}' to '{$to_product_id}', they must be the same product");
        }

        $this->check_quantity($quantity)
            ->check_stock_level($product_id, $quantity)
            ->check_can_remove($quantity);

        $quantity_to_move = $quantity;
        foreach ($this->control as $key => $control) {
            if ($quantity_to_move < 1) {
                break;
            }

            $quantity_available = $control['quantity'];
            if ($quantity_available < 1) {
                continue; // already picked
            }

            if ($quantity_to_move > $quantity_available) {
                $quantity_to_move -= $quantity_available;
            } else {
                $quantity_available = $quantity_to_move;
                $quantity_to_move = 0;
            }

            $this->level -= $quantity_available;
            $this->control[$key]['quantity'] -= $quantity_available;
            
            $cost = $control['cost'];
            $date = $control['date'];
            $to_bin->add($product_id, $quantity_available, $cost, $date);
        }

        if ($this->level < 1) {
            $this->status = 'empty';
        }

        return $this->overflow = $quantity_to_move;
    }
    public function spoil()
    {
        $this->setStatus('spoiled');
    }

    public function getStatus() {
        return [
            "product_id" => $this->product_id, 
            "status" => $this->status,
            "level" => $this->level, 
            "control" => $this->control,
            "overflow" => $this->overflow
        ];
    }
    private function setStatus(string $status = 'OK')
    {
        switch ($status) {
            case 'ok':
            case 'empty':
            case 'spoiled':
                $this->status = $status;
                return $this;
            default:
                throw new StorageException("Unable to set status to unknown value '{$status}'");
        }
    }

    //--- private ---
    private function check_can_add(int $quantity)
    {
        switch ($this->status) {
            case 'ok':
            case 'empty':
                return $this;
            default:
                throw new StorageException("Unable to add '{$quantity}' item from storage bin for '$this->product_id', as it is '{$this->status}'");
        }
    }
    private function check_can_remove(int $quantity)
    {
        switch ($this->status) {
            case 'ok':
                return $this;
            default:
                throw new StorageException("Unable to add '{$quantity}' item from storage bin for '$this->product_id', as it is '{$this->status}'");
        }

    }
    private function check_product_id(string $product_id)
    {
        if ($this->product_id !== $product_id) {
            throw new StorageException("This storage bin is for '$this->product_id', unable to add '{$product_id}'");
        }

        return $this;
    }
    private function check_quantity(int $quantity) 
    {
        if ($quantity < 1) {
            throw new StorageException("Unable to use quantity '{$quantity}' as it is less than 1 (one)");
        }

        return $this;
    }
    private function check_stock_level(string $product_id, int $quantity)
    {
        if ($this->level === 0) {
            throw new StorageException("Unable to remove '{$quantity}' from '{$product_id}' as the stock level is 0 {zero)");
        }

        return $this;
    }
    public function log(string $msg, int $line = 0)
    {
        \file_put_contents("/Users/johng/src/freetimers/error_log", 
            "[".date('Y-m-d h:i:s')."] '{$msg}}' LINE='{$line}'\n",
            FILE_APPEND
        );
    }
}
