<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['group_name'];
    protected $primaryKey = 'group_id';

    public function students()
    {
        return $this->hasMany(Student::class, 'group_id', 'group_id');
    }
}
