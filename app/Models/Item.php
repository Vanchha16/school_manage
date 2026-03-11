<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
  protected $primaryKey = 'Itemid';
public $incrementing = true;
protected $keyType = 'int';

  protected $fillable = [
    'name',
    'available',
    'image',
    'qty',
    'borrow',
    'status',
    'description'
  ];
  public function borrows()
  {
    return $this->hasMany(Borrow::class, 'item_id', 'Itemid');
  }
}
