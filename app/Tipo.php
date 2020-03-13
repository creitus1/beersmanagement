<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'condicion'];

public function articulos(){
    
    return $this->hasMany('app/Articulo');
}
}
