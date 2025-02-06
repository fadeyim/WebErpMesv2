<?php

namespace App\Models\Purchases;

use App\Models\Products\StockMove;
use App\Models\Purchases\PurchaseLines;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products\StockLocationProducts;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReceiptLines extends Model
{
    use HasFactory;

    // Fillable attributes for mass assignment
    protected $fillable= [
        'purchase_receipt_id',
        'purchase_line_id',
        'ordre',
        'receipt_qty',
        'stock_location_products_id',
    ];

    public function purchaseReceipt()
    {
        return $this->belongsTo(PurchaseReceipt::class, 'purchase_receipt_id');
    }

    public function purchaseLines()
    {
        return $this->belongsTo(PurchaseLines::class, 'purchase_line_id');
    }

    public function StockLocationProducts()
    {
        return $this->belongsTo(StockLocationProducts::class, 'stock_location_products_id');
    }

    public function StockMove()
    {
        return $this->hasMany(StockMove::class);
    }

    /**
     * Get the formatted creation date of the line.
     *
     * This accessor method returns the creation date of line
     * formatted as 'day month year' (e.g., '01 January 2023').
     *
     * @return string The formatted creation date.
     */
    public function GetPrettyCreatedAttribute()
    {
        return date('d F Y', strtotime($this->created_at));
    }
}
