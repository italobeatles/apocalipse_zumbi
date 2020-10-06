<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SobreviventesM;
use App\Models\SobreviventesRecursosM;
use App\Models\RecursosM;

class InventarioSobreviventesC extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$obj = new SobreviventesM();
		$objRecursos = new SobreviventesRecursosM();
		$rs = $obj->retornarSobreviventes();
		$arr = array();
		foreach ($rs as $data) {
			$tmpObj = (object) $data['attributes'];
			$tmpObj->recursos = $objRecursos->retornarRecursosSobrevivente((int) $tmpObj->id);
			$arr[] = $tmpObj;
		}
		return response(json_encode($arr))->header('Content-Type', 'application/json');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		if (sizeof($request->id_sobrevivente) != 2 || sizeof($request->recursos_troca) != 2) {
			return response(json_encode(array('status' => 'falha', 'mensagem' => 'pra realizar uma troca, são necessários apenas 2 sobreviventes com duas cargas de recursos para a troca!')))->header('Content-Type', 'application/json');
		}
		foreach ($request->id_sobrevivente as $key => $id) {
			if ($this->verificarZumbi($id)) {
				return response(json_encode(array('status' => 'falha', 'mensagem' => 'Pelo menos um dos dois envolvidos na troca está contaminado!')))->header('Content-Type', 'application/json');
			}
			if ($this->verificarQuantidadeRecursos($id, $request->recursos_troca[$key]) == false) {
				return response(json_encode(array('status' => 'falha', 'mensagem' => 'Um dos sobreviventes não possui os recursos informados na troca!')))->header('Content-Type', 'application/json');
			}
		}
		if (!$this->verificarPontuacaoRecursos($request->recursos_troca)) {
			return response(json_encode(array('status' => 'falha', 'mensagem' => 'A quantidade de pontos dos recursos oferecidos para a troca não são iguais!')))->header('Content-Type', 'application/json');
		}
		if (!$this->realizarTroca($request)) {
			return response(json_encode(array('status' => 'falha', 'mensagem' => 'Erro interno na requisição!')))->header('Content-Type', 'application/json');
		} else {
			return response(json_encode(array('status' => 'sucesso', 'mensagem' => 'Troca efetivada com sucesso!')))->header('Content-Type', 'application/json');
		}
	}

	/**
	 * Verificando se o indivíduo informado está contaminado
	 *
	 * @param	int $id
	 * @return	bool
	 */
	private function verificarZumbi(int $id): bool {
		$obj = new SobreviventesM();
		return $obj->informarSituacaoSobrevivente($id) == 1 ? true : false;
	}

	/**
	 * Verificando se há quantidade de recursos disponíveis para a troca
	 *
	 * @param  int  $id
	 * @param  string  $recursos
	 * @return bool
	 */
	private function verificarQuantidadeRecursos(int $id, string $recursos): bool {
		$arr = json_decode($recursos);
		$obj = new SobreviventesRecursosM();
		foreach ($arr as $data) {
			if ((int) $data->quantidade > $obj->retornarQuantidadeRecursos((int) $id, (int) $data->id_recurso)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Verificando se há igualdade na pontuação de recursos para a troca
	 *
	 * @param  array  $recursos
	 * @return bool
	 */
	private function verificarPontuacaoRecursos(array $recursos): bool {
		$obj = new RecursosM();
		$varPontuacao = array(0 => 0, 1 => 0);
		foreach ($recursos AS $key => $r) {
			$arr = json_decode($r);
			foreach ($arr as $data) {
				$varPontuacao[$key] += (int) $data->quantidade * $obj->retornarQuantidadePontos((int) $data->id_recurso);
			}
		}
		return ($varPontuacao[0] == $varPontuacao[1]) ? true : false;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		$obj = new SobreviventesM();
		$objRecursos = new SobreviventesRecursosM();
		$rs = $obj->retornarSobreviventes($id);
		$arr = array();
		foreach ($rs as $data) {
			$tmpObj = (object) $data['attributes'];
			$tmpObj->recursos = $objRecursos->retornarRecursosSobrevivente((int) $tmpObj->id);
			$arr[] = $tmpObj;
		}
		return response(json_encode($arr))->header('Content-Type', 'application/json');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param	Request $request
	 * @return	bool
	 */
	private function realizarTroca(Request $request): bool {
		$obj = new SobreviventesRecursosM();
		$id_sobrevivente = array($request->id_sobrevivente[0], $request->id_sobrevivente[1]);
		$recursos_troca = array(json_decode($request->recursos_troca[0]), json_decode($request->recursos_troca[1]));
		return $obj->realizarTroca($id_sobrevivente, $recursos_troca);
	}

}
