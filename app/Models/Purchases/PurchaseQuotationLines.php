<?php

namespace App\Models\Purchases;

use App\Models\Planning\Task;
use Illuminate\Database\Eloquent\Model;
use App\Models\Purchases\PurchasesQuotation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseQuotationLines extends Model
{
    use HasFactory;

    // Fillable attributes for mass assignment
    protected $fillable= ['purchases_quotation_id', 
        'tasks_id', 
        'ordre',
        'qty_to_order',
        'unit_price',
        'total_price',
        'qty_accepted',
        'canceled_qty',
    ];

    public function tasks()
    {
        return $this->belongsTo(Task::class, 'tasks_id');
    }

    public function purchaseQuotation()
    {
        return $this->belongsTo(PurchasesQuotation::class, 'purchases_quotation_id');
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
