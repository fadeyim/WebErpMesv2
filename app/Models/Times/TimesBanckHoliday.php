<?php

namespace App\Models\Times;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimesBanckHoliday extends Model
{
    use HasFactory;

    // Fillable attributes for mass assignment
    protected $fillable= ['fixed',  'date',  'label'];
}
