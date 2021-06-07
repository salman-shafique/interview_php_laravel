<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogHour extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'start_time', 'end_time', 'total_time'];
}
