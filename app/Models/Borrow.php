<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    protected $fillable = [
        'student_id',
        'item_id',
        'qty',          // ✅ add
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'notes',
        'return_notes',
        'condition',
    ];

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id', 'student_id');
    }

    public function item()
    {
        return $this->belongsTo(\App\Models\Item::class, 'item_id', 'Itemid');
    }
    
}
