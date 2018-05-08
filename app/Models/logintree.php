<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class logintree extends Model
{
    protected $table = 'logintree';

    public function scopeSubUsers($query, $ID, $tree)
    {
        $ID = (array) $ID;
        $out = [];

        do {
            $filter = array_filter($tree, function ($n) use ($ID) {
                return in_array($n["parentID"], $ID);
            });
            $ID = array_map(function ($n) {
                return $n['id'];
            }, $filter);
            $out = array_merge($out, $ID);
        } while (count($ID) != 0);

        return $out;
    }
}
