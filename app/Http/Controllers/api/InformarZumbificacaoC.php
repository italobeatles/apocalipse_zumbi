<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SobreviventesM;

class InformarZumbificacaoC extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$id_informante = $request->id_informante;
		$id_sobrevivente = $request->id_sobrevivente;
		$obj = new SobreviventesM();
		if ($obj->informarSituacaoSobrevivente($id_informante) == true) {
			print json_encode(array('status' => 'Informante contaminado! Como um zumbi poderia informar a contaminação de um sobrevivente?'));
		} elseif ($obj->informarSituacaoSobrevivente($id_sobrevivente) == true) {
			print json_encode(array('status' => 'Este sobrevivente já foi marcado como contaminado!'));
		} elseif ($obj->verificarExisteNotificacaoContaminacao($id_informante, $id_sobrevivente) > 0) {
			print json_encode(array('status' => 'Este informante já "deu o aviso" de contaminação!'));
		} else {
			$obj->informarContaminacao($id_informante, $id_sobrevivente);
			print json_encode(array('status' => 'Obrigado pela informação de contaminação! Tenha cuidado!'));
		}
	}


}
