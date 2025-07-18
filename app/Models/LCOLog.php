<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LCOLog extends Model
{
    
    protected $table = 'lcologs';

    
    protected $fillable = [
        'date',
        'done',
        'target',
        'jam' 
    ];

    
    protected $casts = [
        'date' => 'date',
    ];
}
