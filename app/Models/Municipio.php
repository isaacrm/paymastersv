<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;
<<<<<<< HEAD
    //public $timestamps = false;
=======
>>>>>>> origin/kath02

    public function departamento()
    {
        return $this->belongsTo('App\Models\Departamento');
    }
<<<<<<< HEAD
=======

    public function direcciones()
    {
        return $this->hasMany('App\Models\Direccion');
    }
>>>>>>> origin/kath02
}
