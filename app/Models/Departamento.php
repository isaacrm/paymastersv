<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

<<<<<<< HEAD

class Departamento extends Model
{
    use HasFactory;
    
=======
class Departamento extends Model
{
    use HasFactory;

>>>>>>> origin/kath02
    public function municipios()
    {
        return $this->hasMany('App\Models\Municipio');
    }
<<<<<<< HEAD
}

=======

}
>>>>>>> origin/kath02
