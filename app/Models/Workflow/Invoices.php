<?php

namespace App\Models\Workflow;

use Carbon\Carbon;
use App\Models\File;
use App\Models\User;
use Spatie\Activitylog\LogOptions;
use App\Models\Companies\Companies;
use App\Models\Workflow\InvoiceLines;
use Illuminate\Database\Eloquent\Model;
use App\Services\InvoiceCalculatorService;
use App\Models\Companies\CompaniesContacts;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Companies\CompaniesAddresses;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoices extends Model
{
    use HasFactory, LogsActivity;

    // Fillable attributes for mass assignment
    protected $fillable= ['uuid',
                            'code', 
                            'label', 
                            'companies_id', 
                            'companies_contacts_id',   
                            'companies_addresses_id',  
                            'statu',
                            'invoice_type',
                            'accounting_status',
                            'user_id',
                            'bank_id',
                            'comment',
                            'order_id',
                            'payment_date',
                            'due_date',
                            'export_date',
                            'incoterm',
                            ];

    // Only log changes
    protected static $logOnlyDirty = true;

    // Add a contextual log
    protected static $logName = 'invoice';

    // Do not store empty values
    protected static $submitEmptyLogs = false;

    // Customize the log description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Invoice has been {$eventName}";
    }

    public function companie()
    {
        return $this->belongsTo(Companies::class, 'companies_id');
    }

    public function contact()
    {
        return $this->belongsTo(CompaniesContacts::class, 'companies_contacts_id');
    }

    public function adresse()
    {
        return $this->belongsTo(CompaniesAddresses::class, 'companies_addresses_id');
    }

    public function UserManagement()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoiceLines()
    {
        return $this->hasMany(InvoiceLines::class)->orderBy('ordre');
    }

    public function GetshortCreatedAttribute()
    {
        return date('d F Y', strtotime($this->created_at));
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
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function getTotalPriceAttribute()
    {
        $InvoiceCalculatorService = new InvoiceCalculatorService($this);
        return $InvoiceCalculatorService->getTotalPrice();
        
    }

    // Relationship with the files associated with the Invoices
    public function files()
    {
        return $this->morphToMany(File::class, 'fileable');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly([
                                                'code', 
                                                'label', 
                                                'companies_id', 
                                                'companies_contacts_id',   
                                                'companies_addresses_id',  
                                                'statu',
                                                'invoice_type',
                                                'accounting_status',
                                                'user_id',
                                                'bank_id',
                                                'comment',
                                                'order_id',
                                                'payment_date',
                                                'due_date',
                                                'export_date',
                                                'incoterm',]);
        // Chain fluent methods for configuration options
    }
}
