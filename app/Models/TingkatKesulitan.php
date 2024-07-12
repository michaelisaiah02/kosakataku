<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TingkatKesulitan extends Model
{
    use HasFactory;

    protected $table = 'tingkat_kesulitan';

    public function latihan()
    {
        return $this->hasMany(Latihan::class, 'id_tingkat_kesulitan');
    }
}
