<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroDeCostos extends Model
{
    use HasFactory;

    public function unidades()
    {
        return $this->belongsTo(Unidades::class, 'centro_de_costos_id');
    }
}
