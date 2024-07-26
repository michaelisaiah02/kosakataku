<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Latihan extends Model
{
    use HasFactory;

    protected $table = 'latihan';

    protected $fillable = [
        'id_user',
        'id_bahasa',
        'id_kategori',
        'id_tingkat_kesulitan',
        'jumlah_kata',
        'jumlah_benar',
        'list',
        'bantuan_suara',
        'selesai'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function bahasa()
    {
        return $this->belongsTo(Bahasa::class, 'id_bahasa');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function tingkatKesulitan()
    {
        return $this->belongsTo(TingkatKesulitan::class, 'id_tingkat_kesulitan');
    }
}
