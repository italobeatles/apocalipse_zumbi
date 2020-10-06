<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\SobreviventesRecursosM;
use App\Models\SobreviventesM;

class RelatorioGeralC extends Controller {

	/**
	 * Exibe o relatório geral
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$json = array();
		$objSR = new SobreviventesRecursosM();
		$json['disponibilidade_recursos'] = $this->organizarRelatorioRecursos($objSR->retornarRelatorioRecursos());
		$json['balanco_infectados'] = $this->organizarRelatorioInfectados();
		return response(json_encode($json))->header('Content-Type', 'application/json');
	}

	/**
	 * Organiza um array com os dados sobre os recursos
	 * @param rs array 
	 * @return array
	 */
	private function organizarRelatorioRecursos(array $rs): array {
		$arr = array('disponibilidade_de_recursos_por_pessoa' => array(), 'media_de_recursos_por_pessoa' => array());
		$quantidade = array();
		foreach ($rs AS $data) {
			$arr['disponibilidade_de_recursos_por_pessoa'][$data->sobrevivente][] = array(
				'recurso' => $data->recurso,
				'quantidade' => $data->quantidade,
				'porcentagem' => $data->porcentagem
			);
			$quantidade[$data->recurso] = isset($quantidade[$data->recurso]) ? $quantidade[$data->recurso] + (int) $data->quantidade : (int) $data->quantidade;
		}
		foreach ($quantidade as $key => $data) {
			$arr['media_de_recursos_por_pessoa'][$key] = $data / sizeof($arr['disponibilidade_de_recursos_por_pessoa']);
		}
		return $arr;
	}

	/**
	 * Organiza um array com os dados sobre infectados e não infectados
	 * @return array
	 */
	private function organizarRelatorioInfectados(): array {
		$obj = new SobreviventesM();
		$rs = $obj->retornarRelatorioInfectados()[0];
		$total = $rs->infectados + $rs->nao_infectados;
		return array(
			'total' => $total,
			'infectados_porcentagem' => $rs->infectados * 100 / $total,
			'nao_infectados_porcentagem' => $rs->nao_infectados * 100 / $total,
		);
	}

}
