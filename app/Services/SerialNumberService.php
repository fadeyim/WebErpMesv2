<?php

namespace App\Services;

use App\Models\Products\SerialNumbers;
use Illuminate\Support\Str;

class SerialNumberService
{
    public function createSerialNumber($productId, $OrderLineID, $status = 1)
    {
        return SerialNumbers::create([
            'products_id' => $productId,
            'order_line_id' => $OrderLineID,
            'serial_number' => Str::uuid(),
            'status' => $status,
        ]);
    }
}
