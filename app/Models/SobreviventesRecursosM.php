<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SobreviventesRecursosM extends Model {
	protected $table = 'tbsobreviventes_recursos';
	protected $fillable = ['id_recurso', 'id_sobrevivente', 'quantidade'];
	public $timestamps = false;
}
