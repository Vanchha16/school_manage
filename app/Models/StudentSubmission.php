<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubmission extends Model
{
    protected $table = 'student_submissions';

protected $fillable = [
    'student_name',
    'gender',
    'phone_number',
    'group_id',
    'item_id',
    'qty',
    'status',
    'note',
    'student_id',
    'is_student_existing',
    'is_student_added',
    'is_borrow_approved',
];

public function group()
{
    return $this->belongsTo(Group::class, 'group_id', 'group_id');
}

public function item()
{
    return $this->belongsTo(Item::class, 'item_id', 'Itemid');
}

public function student()
{
    return $this->belongsTo(Student::class, 'student_id', 'student_id');
}
}