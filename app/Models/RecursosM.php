<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecursosM extends Model {

	protected $table = 'tbrecursos';
	protected $fillable = ['id', 'descricao', 'pontos'];
	public $timestamps = false;

	/**
	Função que retorna a quantidade de pontos de um recurso
	 * @param $id INT
	 * @return INT
	 **/
	public function retornarQuantidadePontos($id): int {
		return (int) $this->where("id", $id)->get()[0]->pontos;
	}

}
