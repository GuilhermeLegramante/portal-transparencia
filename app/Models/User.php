<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // Define o nome correto da tabela
    protected $table = 'glbcliente';

    // Se a sua chave primária não for 'id', defina ela aqui:
    // protected $primaryKey = 'idcliente'; 
}
        