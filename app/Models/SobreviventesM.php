<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SobreviventesM extends Model {
	protected $table = 'tbsobreviventes';
	protected $fillable = ['nome', 'sexo', 'idade', 'latitude', 'longitude', 'zumbi'];
	public $timestamps = false;
	
	public function testeConsulta(){
		$rs = $this->query("SELECT * FROM tbsobreviventes")->get()[0]->attributes;
		print '<pre>';
		print_r($rs);
		print '</pre>';
	}
	
	public function removerSobrevivente($id){
		$this->query("DELETE FROM tbsobreviventes_recursos WHERE id_sobrevivente = '{$id}'");
		$this->query("DELETE FROM tbsobreviventes WHERE id = '{$id}'");
	}
}
