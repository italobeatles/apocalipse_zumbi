<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SobreviventesRecursosM extends Model {

	protected $table = 'tbsobreviventes_recursos';
	protected $fillable = ['id_recurso', 'id_sobrevivente', 'quantidade'];
	public $timestamps = false;

	/**
	  Função que retorna os recursos disponíveis de um determinado sobrevivente
	 * @param $id_sobrevivente INT
	 * @return array()
	 * */
	public function retornarRecursosSobrevivente(int $id_sobrevivente): array {
		return DB::select("SELECT
								r.descricao AS recurso,
								sr.quantidade
							FROM
								{$this->table} sr
								JOIN tbrecursos r ON sr.id_recurso = r.id
							WHERE
								id_sobrevivente = '{$id_sobrevivente}'");
	}

	/**
	  Função que retorna quantidade disponível de um recurso de um determinado sobrevivente
	 * @param $id_sobrevivente INT
	 * @param $id_recurso INT
	 * @return INT
	 * */
	public function retornarQuantidadeRecursos(int $id_sobrevivente, int $id_recurso): int {
		return (int) $this->where("id_sobrevivente", $id_sobrevivente)->where("id_recurso", $id_recurso)->get()[0]->quantidade;
	}

	/**
	  Função que salva no banco de dados a troca de recursos
	 * @param $id_sobrevivente array
	 * @param $recursos_troca array
	 * @return boole
	 * */
	public function realizarTroca(array $id_sobrevivente, array $recursos_troca): bool {
		// Inicia transação com banco de dados
		DB::beginTransaction();
		try {
			$this->diminuirInventario($id_sobrevivente[0], $recursos_troca[0]);
			$this->diminuirInventario($id_sobrevivente[1], $recursos_troca[1]);
			$this->aumentarInventario($id_sobrevivente[0], $recursos_troca[1]);
			$this->aumentarInventario($id_sobrevivente[1], $recursos_troca[0]);
			// Efetiva todas as operações
			DB::commit();
			return true;
		} catch (exception $e) {
			// Cancela todas as operações em caso de erro
			DB::rollback();
			return false;
		}
	}

	/**
	  Função que diminui recursos de um sobrevivente
	 * @param $id_sobrevivente array
	 * @param $recursos array
	 * @return void
	 * */
	private function diminuirInventario(int $id_sobrevivente, array $recursos): void {
		foreach ($recursos as $data) {
			DB::select("UPDATE tbsobreviventes_recursos SET quantidade = quantidade - {$data->quantidade} WHERE id_sobrevivente = {$id_sobrevivente} AND id_recurso = {$data->id_recurso}");
		}
	}

	/**
	  Função que aumenta recursos de um sobrevivente
	 * @param $id_sobrevivente array
	 * @param $recursos array
	 * @return void
	 * */
	private function aumentarInventario(int $id_sobrevivente, array $recursos): void {
		foreach ($recursos as $data) {
			DB::select("UPDATE tbsobreviventes_recursos SET quantidade = quantidade + {$data->quantidade} WHERE id_sobrevivente = {$id_sobrevivente} AND id_recurso = {$data->id_recurso}");
		}
	}

	/**
	  Função que retorna um relatório de recursos disponíveis
	 * @return array()
	 * */
	public function retornarRelatorioRecursos(): array {
		return DB::select("	SELECT
								s.nome AS sobrevivente,
								r.descricao AS recurso,
								sr.quantidade,
								sr.quantidade * 100/(SELECT SUM(quantidade) FROM tbsobreviventes_recursos WHERE id_recurso = r.id)  AS porcentagem
							FROM 
								tbsobreviventes_recursos sr
								JOIN tbsobreviventes s ON sr.id_sobrevivente = s.id
								JOIN tbrecursos r ON sr.id_recurso = r.id
							WHERE zumbi = 0
							ORDER BY s.nome ASC, r.id ASC");
	}

}
