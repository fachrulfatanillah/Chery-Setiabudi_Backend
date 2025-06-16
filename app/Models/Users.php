<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Users extends Model
{
    // Nama tabel (jika tidak mengikuti konvensi Laravel)
    protected $table = 'users';

    // Primary key (default: id, jadi tidak perlu diubah)
    protected $primaryKey = 'id';

    // UUID tidak auto-increment
    public $incrementing = true;

    // Gunakan tipe int untuk ID, bukan string
    protected $keyType = 'int';

    // Tidak gunakan timestamps default (created_at, updated_at)
    public $timestamps = false;

    protected $hidden = ['id'];

    // Field yang dapat diisi secara massal
    protected $fillable = [
        'uuid',
        'username',
        'email',
        'password',
        'create_on',
        'update_on',
    ];

    // Casting tipe data
    protected $casts = [
        'create_on' => 'datetime',
        'update_on' => 'datetime',
    ];
}
