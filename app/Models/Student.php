<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'student_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
    'student_name',
    'phone_number',
    'gender',
    'group_id',
    'status',
];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }
    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'student_id', 'student_id');
    }
    
}