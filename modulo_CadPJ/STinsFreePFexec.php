<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);
$strMsg = "";

/*** RECEBE PARAMETROS ***/
$intCodPJ	 = request("var_cod_pj");
$strLocation = request("var_redirect");
//SE PF já existe, então foi passado seu código, 
//desta forma fará somente a inserçãoda relação
$intCodPF	 = request("var_cod_pf");

$strNome  		  = strtoupper(request("var_nome"));
$strApelido		  = strtoupper(request("var_apelido"));
$dtDataNasc       = request("var_data_nasc");
$strSexo  		  = strtoupper(request("var_sexo"));
$strEmail         = strtoupper(request("var_email"));
$strFoto          = request("var_foto");
$strEstadoCivil   = strtoupper(request("var_estado_civil"));
$strNacionalidade = strtoupper(request("var_nacionalidade"));
$strNaturalidade  = strtoupper(request("var_naturalidade"));
$strObs           = strtoupper(request("var_obs"));

$strEndPrinCEP           = request("var_endprin_cep");
$strEndPrinLogradouro    = strtoupper(request("var_endprin_logradouro"));
$strEndPrinNumero        = request("var_endprin_numero");
$strEndPrinCompl         = request("var_endprin_complemento");
$strEndPrinBairro        = strtoupper(request("var_endprin_bairro"));
$strEndPrinCidade        = strtoupper(request("var_endprin_cidade"));
$strEndPrinEstado        = strtoupper(request("var_endprin_estado"));
$strEndPrinPais          = strtoupper(request("var_endprin_pais"));
$strEndPrinFone1         = request("var_endprin_fone1");
$strEndPrinFone2         = request("var_endprin_fone2");

$strCategoria			 = strtoupper(request("var_categoria"));
$strTrabFuncao			 = strtoupper(request("var_trab_funcao"));
$strTrabDepartamento	 = strtoupper(request("var_trab_departamento"));
$intCodCargo			 = request("var_cod_cargo");
$intCodNivel			 = request("var_cod_nivel");
$strTrabTipo			 = strtoupper(request("var_trab_tipo"));
$dtTrabDtAdmissao		 = request("var_trab_dt_admissao");
$dtTrabDtDemissao		 = request("var_trab_dt_demissao");
$strTrabObs				 = strtoupper(request("var_trab_obs"));
$strClVip			     = strtoupper(request("var_classificacao_vip"));

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
if($strNome == "") 		{ $strMsg .= "Informar Nome<br>"; }
if($intCodPJ == "") 	{ $strMsg .= "Informar Empresa<br>"; }
//if($strCPF == "") 	{ $strMsg .= "Informar CPF<br>"; }
if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

/*** TRATAMENTO DOS CAMPOs ***/
$dtDataNasc = cDate(CFG_LANG, $dtDataNasc, false);
$dtTrabDtAdmissao = cDate(CFG_LANG, $dtTrabDtAdmissao, false);
$dtTrabDtDemissao = cDate(CFG_LANG, $dtTrabDtDemissao, false);
(($dtDataNasc == "") || (!is_date($dtDataNasc))) ? $dtDataNasc = "NULL" : $dtDataNasc = "'" . $dtDataNasc . "'";
(($dtTrabDtAdmissao == "") || (!is_date($dtTrabDtAdmissao))) ? $dtTrabDtAdmissao = "NULL" : $dtTrabDtAdmissao = "'" . $dtTrabDtAdmissao . "'";
(($dtTrabDtDemissao == "") || (!is_date($dtTrabDtDemissao))) ? $dtTrabDtDemissao = "NULL" : $dtTrabDtDemissao = "'" . $dtTrabDtDemissao . "'";

if ($intCodCargo == "") $intCodCargo = "NULL";
if ($intCodNivel == "") $intCodNivel = "NULL";

//----------------------------------
// Insere dados da PESSOA FISICA 
//----------------------------------
$objConn->beginTransaction();
try {
	if ($intCodPF=="") {
		$strSQL  = " INSERT INTO cad_pf ( nome, apelido, data_nasc, sexo, email, foto, estado_civil ";
		$strSQL .= "                    , nacionalidade, naturalidade, obs ";
		$strSQL .= "                    , endprin_cep, endprin_logradouro, endprin_numero, endprin_complemento, endprin_bairro ";
		$strSQL .= "                    , endprin_cidade, endprin_estado, endprin_pais, endprin_fone1, endprin_fone2 ";
		$strSQL .= "                    , sys_dtt_ins, sys_usr_ins ) ";
		$strSQL .= " VALUES ( '" . $strNome . "', '" . $strApelido . "', " . $dtDataNasc . ", '" . $strSexo . "' ";
		$strSQL .= "        , '" . $strEmail . "', '" . $strFoto . "', '" . $strEstadoCivil . "' ";
		$strSQL .= "        , '" . $strNacionalidade . "', '" . $strNaturalidade . "', '" . $strObs . "' ";
		$strSQL .= "        , '" . $strEndPrinCEP . "', '" . $strEndPrinLogradouro . "', '" . $strEndPrinNumero . "', '" . $strEndPrinCompl . "' ";
		$strSQL .= "        , '" . $strEndPrinBairro . "', '" . $strEndPrinCidade . "', '" . $strEndPrinEstado . "', '" . $strEndPrinPais . "' ";
		$strSQL .= "        , '" . $strEndPrinFone1 . "', '" . $strEndPrinFone2 . "' ";
		$strSQL .= "        , CURRENT_TIMESTAMP, '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "') ";
		//die($strSQL);
		$objConn->query($strSQL);
		
		$strSQL = " SELECT MAX(cod_pf) AS cod_pf FROM cad_pf WHERE sys_usr_ins = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
		$objResult = $objConn->query($strSQL);
		if ($objResult->rowCount() > 0) {
			$objRS = $objResult->fetch();
			$intCodPF = getValue($objRS, "cod_pf");
		}
		$objResult->closeCursor();
		
		if ($intCodPF == '') {
			mensagem("err_sql_titulo","err_sql_desc","err_busca_colab_desc","","erro",1);
			$objConn->rollBack();
			die();
		}
	}
	
	//Insere relação de PF com PJ (será gerado um pedido de carteirinha (credencial) por trigger)
	$strSQL  = " INSERT INTO relac_pj_pf (cod_pj, cod_pf, categoria, cod_cargo, cod_nivel_hierarquico, tipo, funcao, departamento, obs, dt_admissao, sys_dtt_ins, sys_usr_ins, classificacao_vip) ";
	$strSQL .= " VALUES ( " . $intCodPJ . ", " . $intCodPF . ", '" . $strCategoria . "', " . $intCodCargo . ", " . $intCodNivel . ", '". $strTrabTipo . "', '" . $strTrabFuncao . "' ";
	$strSQL .= "        , '" . $strTrabDepartamento . "', '" . $strTrabObs . "', " . $dtTrabDtAdmissao . ", CURRENT_TIMESTAMP, '" . $strClVip . "' ";
	$strSQL .= "        , '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "') ";
	//die($strSQL);
	$objConn->query($strSQL);

	$objConn->commit();
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	$objConn->rollBack();
	die();
}
$objConn = NULL;

redirect($strLocation);
?>