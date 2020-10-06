<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SobreviventesM extends Model {

	protected $table = 'tbsobreviventes';
	protected $fillable = ['nome', 'sexo', 'idade', 'latitude', 'longitude', 'zumbi'];
	public $timestamps = false;

	/**
	  Função que remove um sobrevivente
	 * @param $id INT
	 * @return VOID
	 * */
	public function removerSobrevivente($id) {
		DB::beginTransaction();
		try {
			DB::select("DELETE FROM tbsobreviventes_recursos WHERE id_sobrevivente = '{$id}'");
			DB::select("DELETE FROM tbsobreviventes WHERE id = '{$id}'");
			DB::commit();
		} catch (ErrorException $e) {
			DB::rollback();
		}
	}

	/**
	  Função que uma notificação de contaminação
	 * @param $id_informante INT
	 * @param $id_sobrevivente INT
	 * @return VOID
	 * */
	public function informarContaminacao($id_informante, $id_sobrevivente) {
		DB::select("INSERT INTO tbavisos_zumbificacao (id_sobrevivente, id_informante) VALUES ('{$id_sobrevivente}', '{$id_informante}')");
		if ($this->verificarExisteNotificacaoContaminacaoTotal($id_sobrevivente) >= 3) {
			DB::select("UPDATE tbsobreviventes SET zumbi = 1 WHERE id = '{$id_sobrevivente}'");
		}
	}

	/**
	  Função que a situação de um sobrevivente
	 * @param $id_sobrevivente INT
	 * @return int
	 * */
	public function informarSituacaoSobrevivente($id_sobrevivente): int {
		return (int) DB::select("SELECT zumbi FROM tbsobreviventes WHERE id = '{$id_sobrevivente}'")[0]->zumbi;
	}

	/**
	  Função que retorna a quantidade de notificações de contaminação que um determinado sobrevivente fez sobre um suposto contaminado
	 * @param $id_informante INT
	 * @param $id_sobrevivente INT
	 * @return int
	 * */
	public function verificarExisteNotificacaoContaminacao($id_informante, $id_sobrevivente): int {
		$rs = DB::select("SELECT * FROM tbavisos_zumbificacao WHERE id_sobrevivente = '{$id_sobrevivente}' AND id_informante = '{$id_informante}'");
		return sizeof($rs);
	}

	/**
	  Função que retorna a quantidade de notificações de contaminação que um determinado sobrevivente sofreu
	 * @param $id_sobrevivente INT
	 * @return int
	 * */
	public function verificarExisteNotificacaoContaminacaoTotal($id_sobrevivente): int {
		return sizeof(DB::select("SELECT * FROM tbavisos_zumbificacao WHERE id_sobrevivente = '{$id_sobrevivente}'"));
	}

	/**
	  Função que retorna todos os sobreviventes ou um sobrevivente específico
	 * @param $id INT
	 * @return array()
	 * */
	public function retornarSobreviventes(int $id = null) {
		$query = $this->where("zumbi", 0);
		return ($id) ? $query->where("id", $id)->get() : $query->get();
	}

	/**
	  Função que retorna um relatório de infectados e não infectados
	 * @return array()
	 * */
	public function retornarRelatorioInfectados() {
		return DB::select("	SELECT
								(SELECT COUNT(id) FROM tbsobreviventes WHERE zumbi = 1) AS infectados,
								(SELECT COUNT(id) FROM tbsobreviventes WHERE zumbi = 0) AS nao_infectados");
	}

}
