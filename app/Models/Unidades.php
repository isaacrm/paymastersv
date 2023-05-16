<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidades extends Model
{
    use HasFactory;

    public function pertenece_nivel()
    {
        return $this->belongsTo(Unidades::class, 'nivel_organizacional');
    }

    public function nivel_organizacional()
    {
        return $this->hasMany(Unidades::class, 'nivel_organizacional');
    }

    public function centro_de_costos()
    {
        return $this->hasMany(CentroDeCostos::class, 'centro_de_costos');
    }

    public function superior_id()
    {
        return $this->hasMany(Puesto::class, 'superior_id');
    }
}
