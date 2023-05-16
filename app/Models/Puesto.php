<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    use HasFactory;

    public function inferior()
    {
        return $this->belongsTo(Puesto::class, 'superior_id');
    }

    public function superior()
    {
        return $this->hasMany(Puesto::class, 'superior_id');
    }

    public function puestos()
    {
        return $this->belongsTo(Unidades::class, 'superior_id');
    }
}
