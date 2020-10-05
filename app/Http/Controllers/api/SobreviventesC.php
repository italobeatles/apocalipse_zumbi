<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SobreviventesM;
use App\Models\SobreviventesRecursosM;

class SobreviventesC extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return SobreviventesM::all();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		try {
			$id = SobreviventesM::create(array(
						'nome' => $request->nome,
						'idade' => $request->idade,
						'sexo' => $request->sexo,
						'latitude' => (double) $request->latitude,
						'longitude' => (double) $request->longitude,
						'zumbi' => (boolean) $request->zumbi
					))->id;
			foreach ($request->id_recurso AS $key => $data) {
				SobreviventesRecursosM::create(array(
					'id_sobrevivente' => $id,
					'id_recurso' => $data,
					'quantidade' => $request->quantidade[$key]
				));
			}
			print json_encode(array('status' => 'OK'));
		} catch (\Psy\Exception\ErrorException $e) {
			print json_encode(array('status' => $e->getMessage()));
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//print_r(SobreviventesM::query("SELECT * FROM tbsobreviventes"));
		$obj = new SobreviventesM();
		return $obj->find($id);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		try {
			$sobrevivente = SobreviventesM::find($id);
			$sobrevivente->update($request->all());
			print json_encode(array('status' => 'OK'));
		} catch (ErrorException $e) {
			print json_encode(array('status' => $e->getMessage()));
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		try {
			$obj = new SobreviventesM();
			$obj->removerSobrevivente((int) $id);
			print json_encode(array('status' => 'OK'));
		} catch (ErrorException $e) {
			print json_encode(array('status' => $e->getMessage()));
		}
	}

	public function informarContaminacao(Request $request) {
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
