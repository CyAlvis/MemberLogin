<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class relation extends Model
{
    protected $table = "relation";
    public function parent()
    {
        return $this->belongsTo(self::class, 'id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'id');
    }
}
