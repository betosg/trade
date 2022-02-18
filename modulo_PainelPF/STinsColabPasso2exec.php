<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

$strMsg 	 = "";
$strEntidade = "";

/*** RECEBE PARAMETROS ***/
$strRedirect	= request("DEFAULT_LOCATION");
$intCodPJ 		= request("var_cod_pj");
$intCodPF 		= request("var_cod_pf");
$srtFlagUpdate 	= request("var_str_flag");
$strFlagAtivo   = request("var_situacao_colab");

$strNome  		  = strtoupper(request("var_nome"));
$strApelido		  = strtoupper(request("var_apelido"));
$dtDataNasc       = request("var_data_nasc");
$strSexo  		  = strtoupper(request("var_sexo"));
$strEmail         = strtoupper(request("var_email"));
$strEmailExtra    = strtoupper(request("var_email_extra"));
$strWebsite       = strtoupper(request("var_website"));
$strFoto          = request("var_foto");
$strEstadoCivil   = strtoupper(request("var_estado_civil"));
$strInstrucao     = strtoupper(request("var_instrucao"));
$strNacionalidade = strtoupper(request("var_nacionalidade"));
$strNaturalidade  = strtoupper(request("var_naturalidade"));
$strObs           = strtoupper(request("var_obs"));
$strNomePai       = strtoupper(request("var_nome_pai"));
$strNomeMae       = strtoupper(request("var_nome_mae"));

$strCPF		      = request("var_cpf");
$strRG            = request("var_rg");
$strCNH           = request("var_cnh");
$strPIS           = request("var_pis");
$strCTPS          = request("var_ctps");
$strTITE		  = request("var_titulo_eleitoral");

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
$strEndPrinFone3         = request("var_endprin_fone3");
$strEndPrinFone4         = request("var_endprin_fone4");
$strEndPrinFone5         = request("var_endprin_fone5");
$strEndPrinFone6         = request("var_endprin_fone6");

$strEndComCEP           = request("var_endcom_cep");
$strEndComLogradouro    = strtoupper(request("var_endcom_logradouro"));
$strEndComNumero        = request("var_endcom_numero");
$strEndComCompl         = request("var_endcom_complemento");
$strEndComBairro        = strtoupper(request("var_endcom_bairro"));
$strEndComCidade        = strtoupper(request("var_endcom_cidade"));
$strEndComEstado        = strtoupper(request("var_endcom_estado"));
$strEndComPais          = strtoupper(request("var_endcom_pais"));
$strEndComFone1         = request("var_endcom_fone1");
$strEndComFone2         = request("var_endcom_fone2");
$strEndComFone3         = request("var_endcom_fone3");
$strEndComFone4         = request("var_endcom_fone4");
$strEndComFone5         = request("var_endcom_fone5");
$strEndComFone6         = request("var_endcom_fone6");

$intTrabCodCargo     = request("var_trab_cod_cargo");
$strCategoria 		 = (request("var_categoria") == '' && getsession(CFG_SYSTEM_NAME."_grp_user") == 'NORMAL') ? request("var_categoria_hidden") : request("var_categoria");
$strTrabFuncao       = strtoupper(request("var_trab_funcao"));
$strTrabDepartamento = strtoupper(request("var_trab_departamento"));
$strTrabTipo         = strtoupper(request("var_trab_tipo"));
$strTrabObs          = strtoupper(request("var_trab_obs"));
$dtTrabDtAdmissao    = request("var_trab_dt_admissao");
$dtTrabDtDemissao    = request("var_trab_dt_demissao");

//die;

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
if($intCodPJ == "") 			{ $strMsg .= "Informar Empresa<br>"; }
if($strNome == "") 				{ $strMsg .= "Informar Nome<br>"; }
if($strSexo == "") 				{ $strMsg .= "Informar Sexo<br>"; }
if($strCPF == "") 				{ $strMsg .= "Informar CPF<br>"; }
if($strRG == "") 				{ $strMsg .= "Informar RG<br>"; }
//if($strFoto == "") 			{ $strMsg .= "Informar Foto<br>"; }
if($strTrabFuncao == "") 		{ $strMsg .= "Informar Função<br>"; }
if($strEndPrinCEP == "") 		{ $strMsg .= "Informar CEP<br>"; }
if($strEndPrinLogradouro == "") { $strMsg .= "Informar Logradouro<br>"; }
if($strEndPrinNumero == "")		{ $strMsg .= "Informar Número<br>"; }
if($strEndPrinBairro == "")		{ $strMsg .= "Informar Bairro<br>"; }
if($strEndPrinCidade == "")		{ $strMsg .= "Informar Cidade<br>"; }
if($strEndPrinEstado == "") 	{ $strMsg .= "Informar Estado<br>"; }
if($strEndPrinPais == "") 		{ $strMsg .= "Informar País<br>"; }
if($strEndPrinFone1 == "") 		{ $strMsg .= "Informar Fone 1<br>"; }
//if($strEndPrinFone2 == "") 		{ $strMsg .= "Informar Fone 2<br>"; }
if($dtTrabDtAdmissao == "") 	{ $strMsg .= "Informar Data Admissão<br>"; }

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
$dtInativo = ($strFlagAtivo == "INATIVO") ? "'".cDate(CFG_LANG,request("var_dt_inativo"),false)."'" : "NULL"; 
$strMotivo = ($strFlagAtivo == "INATIVO") ? "'".request("var_motivo_inativo")."'" : "NULL";

if ($intTrabCodCargo == "") $intTrabCodCargo = "NULL";

$objConn->beginTransaction();
try{
	//PF já está cadastrada, apenas atualiza os dados
	if ($intCodPF != '') {
		//die("update aqui".$intCodPF);
		$strSQL  = " UPDATE cad_pf ";
		$strSQL .= " SET nome = '" . $strNome . "' ";
		$strSQL .= "   , apelido = '" . $strApelido . "' ";
		$strSQL .= "   , data_nasc = " . $dtDataNasc;
		$strSQL .= "   , sexo = '" . $strSexo . "' ";
		$strSQL .= "   , email = '" . $strEmail . "' ";
		$strSQL .= "   , email_extra = '" . $strEmailExtra . "' ";
		$strSQL .= "   , website = '" . $strWebsite . "' ";
		$strSQL .= "   , foto = '" . $strFoto . "' ";
		$strSQL .= "   , estado_civil = '" . $strEstadoCivil . "' ";
		$strSQL .= "   , instrucao = '" . $strInstrucao . "' ";
		$strSQL .= "   , nacionalidade = '" . $strNacionalidade . "' ";
		$strSQL .= "   , naturalidade = '" . $strNaturalidade . "' ";
		$strSQL .= "   , obs = '" . $strObs . "' ";
		$strSQL .= "   , nome_pai = '" . $strNomePai . "' ";
		$strSQL .= "   , nome_mae = '" . $strNomeMae . "' ";
		$strSQL .= "   , cpf = '" .$strCPF ."'";
		$strSQL .= "   , rg = '" . $strRG . "' ";
		$strSQL .= "   , cnh = '" . $strCNH . "' ";
		$strSQL .= "   , pis = '" . $strPIS . "' ";
		$strSQL .= "   , ctps = '" . $strCTPS . "' ";
		$strSQL .= "   , titulo_eleitoral = '" . $strTITE . "' ";
		
		$strSQL .= "   , endprin_cep = '" . $strEndPrinCEP . "' ";
		$strSQL .= "   , endprin_logradouro = '" . $strEndPrinLogradouro . "' ";
		$strSQL .= "   , endprin_numero = '" . $strEndPrinNumero . "' ";
		$strSQL .= "   , endprin_complemento = '" . $strEndPrinCompl . "' ";
		$strSQL .= "   , endprin_bairro = '" . $strEndPrinBairro . "' ";
		$strSQL .= "   , endprin_cidade = '" . $strEndPrinCidade . "' ";
		$strSQL .= "   , endprin_estado = '" . $strEndPrinEstado . "' ";
		$strSQL .= "   , endprin_pais = '" . $strEndPrinPais . "' ";
		$strSQL .= "   , endprin_fone1 = '" . $strEndPrinFone1 . "' ";
		$strSQL .= "   , endprin_fone2 = '" . $strEndPrinFone2 . "' ";
		
		$strSQL .= "   , endcom_cep = '" . $strEndComCEP . "' ";
		$strSQL .= "   , endcom_logradouro = '" . $strEndComLogradouro . "' ";
		$strSQL .= "   , endcom_numero = '" . $strEndComNumero . "' ";
		$strSQL .= "   , endcom_complemento = '" . $strEndComCompl . "' ";
		$strSQL .= "   , endcom_bairro = '" . $strEndComBairro . "' ";
		$strSQL .= "   , endcom_cidade = '" . $strEndComCidade . "' ";
		$strSQL .= "   , endcom_estado = '" . $strEndComEstado . "' ";
		$strSQL .= "   , endcom_pais = '" . $strEndComPais . "' ";
		$strSQL .= "   , endcom_fone1 = '" . $strEndComFone1 . "' ";
		$strSQL .= "   , endcom_fone2 = '" . $strEndComFone2 . "' ";
		$strSQL .= " WHERE cod_pf = " . $intCodPF;
		
		$objConn->query($strSQL);
		
		// update na tabela de relacões
		if($srtFlagUpdate != ""){
			$strSQL  = " UPDATE relac_pj_pf ";
			$strSQL .= " SET tipo = '" . $strTrabTipo . "' ";
			$strSQL .= "   , departamento = '" . $strTrabDepartamento . "' ";
			$strSQL .= "   , categoria = '" . $strCategoria . "' ";
			$strSQL .= "   , dt_admissao = " . $dtTrabDtAdmissao;
			$strSQL .= "   , dt_demissao = " . $dtTrabDtDemissao;
			$strSQL .= "   , funcao = '" . $strTrabFuncao . "' ";
			$strSQL .= "   , obs = '" . $strTrabObs . "' ";
			$strSQL .= "   , arquivo_1 = '". prepStr(request("dbvar_str_arquivo_1"))."' ";
			$strSQL .= "   , arquivo_2 = '". prepStr(request("dbvar_str_arquivo_2"))."' ";
			$strSQL .= "   , arquivo_3 = '". prepStr(request("dbvar_str_arquivo_3"))."' ";
			$strSQL .= "   , dt_inativo = ".$dtInativo;
			$strSQL .= "   , motivo_inativo = ".$strMotivo;			
			$strSQL .= " WHERE cod_pf = " . $intCodPF;
			//die($strSQL);			
			$objConn->query($strSQL);
			
		}
		
		
	}
	//PF não está cadastrada, então insere os dados
	else {
		//die("deu insert".$intCodPF);
		$strSQL  = " INSERT INTO cad_pf ( nome, apelido, data_nasc, sexo, nome_pai, nome_mae, email, email_extra, website, foto, estado_civil ";
		$strSQL .= "                    , instrucao, nacionalidade, naturalidade, obs, cpf, rg, cnh, pis, ctps, titulo_eleitoral ";
		
		$strSQL .= "                    , endprin_cep, endprin_logradouro, endprin_numero, endprin_complemento, endprin_bairro ";
		$strSQL .= "                    , endprin_cidade, endprin_estado, endprin_pais, endprin_fone1, endprin_fone2 ";
		
		$strSQL .= "                    , endcom_cep, endcom_logradouro, endcom_numero, endcom_complemento, endcom_bairro ";
		$strSQL .= "                    , endcom_cidade, endcom_estado, endcom_pais, endcom_fone1, endcom_fone2 ";
		
		$strSQL .= "                    , sys_dtt_ins, sys_usr_ins ) ";
		$strSQL .= " VALUES ( '" . $strNome . "', '" . $strApelido . "', " . $dtDataNasc . ", '" . $strSexo . "', '" . $strNomePai . "', '" . $strNomeMae . "' ";
		$strSQL .= "        , '" . $strEmail . "', '" . $strEmailExtra . "', '" . $strWebsite . "', '" . $strFoto . "', '" . $strEstadoCivil . "' ";
		$strSQL .= "        , '" . $strInstrucao . "', '" . $strNacionalidade . "', '" . $strNaturalidade . "', '" . $strObs . "' ";
		$strSQL .= "        , '" . $strCPF . "', '" . $strRG . "', '" . $strCNH . "', '" . $strPIS . "', '" . $strCTPS . "', '" . $strTITE . "' ";
		
		$strSQL .= "        , '" . $strEndPrinCEP . "', '" . $strEndPrinLogradouro . "', '" . $strEndPrinNumero . "', '" . $strEndPrinCompl . "' ";
		$strSQL .= "        , '" . $strEndPrinBairro . "', '" . $strEndPrinCidade . "', '" . $strEndPrinEstado . "', '" . $strEndPrinPais . "' ";
		$strSQL .= "        , '" . $strEndPrinFone1 . "', '" . $strEndPrinFone2 . "' ";
		
		$strSQL .= "        , '" . $strEndComCEP . "', '" . $strEndComLogradouro . "', '" . $strEndComNumero . "', '" . $strEndComCompl . "' ";
		$strSQL .= "        , '" . $strEndComBairro . "', '" . $strEndComCidade . "', '" . $strEndComEstado . "', '" . $strEndComPais . "' ";
		$strSQL .= "        , '" . $strEndComFone1 . "', '" . $strEndComFone2 . "' ";
		
		$strSQL .= "        , CURRENT_TIMESTAMP, '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "') ";
		
		$objConn->query($strSQL);
		
		$strSQL = " SELECT MAX(cod_pf) AS cod_pf FROM cad_pf WHERE sys_usr_ins = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
		$objResult = $objConn->query($strSQL);
		
		if ($objResult->rowCount() > 0) {
			$objRS = $objResult->fetch();
			$intCodPF = getValue($objRS, "cod_pf");
		}
		$objResult->closeCursor();
		
		if ($intCodPF == ''){
			mensagem("err_sql_titulo","err_sql_desc","err_busca_colab_desc","","erro",1);
			$objConn->rollBack();
			die();
		}
	}
	
	if($srtFlagUpdate == ""){
		//die("meu flag nao funcionou");
		//Insere relação de PF com PJ, será gerado um pedido 
		//de carteirinha (credencial) por trigger
		$strSQL  = " INSERT INTO relac_pj_pf (cod_pj, cod_pf, categoria, cod_cargo, tipo, funcao, departamento, obs, dt_admissao, dt_demissao, arquivo_1, arquivo_2, arquivo_3, sys_dtt_ins, sys_usr_ins) ";
		$strSQL .= " VALUES ( " . $intCodPJ . ", " . $intCodPF . ", '" . $strCategoria . "', " . $intTrabCodCargo . ", '" . $strTrabTipo . "', '" . $strTrabFuncao . "' ";
		$strSQL .= "        , '" . $strTrabDepartamento . "', '" . $strTrabObs . "', " . $dtTrabDtAdmissao . ", " . $dtTrabDtDemissao . ", '".prepStr(request("dbvar_str_arquivo_1"))."', '".prepStr(request("dbvar_str_arquivo_2"))."', '".prepStr(request("dbvar_str_arquivo_3"))."', CURRENT_TIMESTAMP ";
		$strSQL .= "        , '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "') ";
		//$strSQL;
		$objConn->query($strSQL);
	}
	$objConn->commit();
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	$objConn->rollBack();
	die();
}
$objConn = NULL;
	
	if($strRedirect != ""){
		redirect($strRedirect);
	}else{
		redirect("STColabAtivos.php");
	}
?>