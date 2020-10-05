<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SobreviventesM extends Model {

	protected $table = 'tbsobreviventes';
	protected $fillable = ['nome', 'sexo', 'idade', 'latitude', 'longitude', 'zumbi'];
	public $timestamps = false;

	public function testeConsulta() {
		die('teste');
		$rs = DB::select("SELECT * FROM tbsobreviventes");
		print '<pre>';
		print_r(DB);
		print '</pre>';
	}

	public function removerSobrevivente($id) {
		DB::select("DELETE FROM tbsobreviventes_recursos WHERE id_sobrevivente = '{$id}'");
		DB::select("DELETE FROM tbsobreviventes WHERE id = '{$id}'");
	}

	public function informarContaminacao($id_informante, $id_sobrevivente) {
		DB::select("INSERT INTO tbavisos_zumbificacao (id_sobrevivente, id_informante) VALUES ('{$id_sobrevivente}', '{$id_informante}')");
		if($this->verificarExisteNotificacaoContaminacaoTotal($id_sobrevivente) >= 3){
			DB::select("UPDATE tbsobreviventes SET zumbi = 1 WHERE id = '{$id_sobrevivente}'");
		}
	}

	public function informarSituacaoSobrevivente($id_sobrevivente) {
		return DB::select("SELECT zumbi FROM tbsobreviventes WHERE id = '{$id_sobrevivente}'")[0]->zumbi;
	}

	public function verificarExisteNotificacaoContaminacao($id_informante, $id_sobrevivente) {
		$rs = DB::select("SELECT * FROM tbavisos_zumbificacao WHERE id_sobrevivente = '{$id_sobrevivente}' AND id_informante = '{$id_informante}'");
		return sizeof($rs);
	}

	public function verificarExisteNotificacaoContaminacaoTotal($id_sobrevivente) {
		return sizeof(DB::select("SELECT * FROM tbavisos_zumbificacao WHERE id_sobrevivente = '{$id_sobrevivente}'"));
	}

}
