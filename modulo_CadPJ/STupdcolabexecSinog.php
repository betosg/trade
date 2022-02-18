<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

$strMsg 	 = "";
$strEntidade = "";

/*** RECEBE PARAMETROS ***/
$intCodPJ 		= request("var_cod_pj");
$intCodPF 		= request("var_cod_pf");
$srtFlagUpdate 	= request("var_str_flag");
$strFlagAtivo   = request("var_situacao_colab");

$strNome  		  = (request("var_nome"));
$strApelido		  = (request("var_apelido"));
$dtDataNasc       = request("var_data_nasc");
$strSexo  		  = (request("var_sexo"));
$strEmail         = (request("var_email"));
$strEmailExtra    = (request("var_email_extra"));
$strWebsite       = (request("var_website"));
$strFoto          = request("var_foto");
$strEstadoCivil   = (request("var_estado_civil"));
$strInstrucao     = (request("var_instrucao"));
$strNacionalidade = (request("var_nacionalidade"));
$strNaturalidade  = (request("var_naturalidade"));
$strObs           = (request("var_obs"));
$strNomePai       = (request("var_nome_pai"));
$strNomeMae       = (request("var_nome_mae"));

$strCPF		      = request("var_cpf");
$strRG            = request("var_rg");
$strTratamento    = request("var_tratamento");
$strCNH           = request("var_cnh");
$strPIS           = request("var_pis");
$strCTPS          = request("var_ctps");
$strTITE 		  = request("var_titulo_eleitoral");

$strEndPrinCEP           = request("var_endprin_cep");
$strEndPrinLogradouro    = (request("var_endprin_logradouro"));
$strEndPrinNumero        = request("var_endprin_numero");
$strEndPrinCompl         = request("var_endprin_complemento");
$strEndPrinBairro        = (request("var_endprin_bairro"));
$strEndPrinCidade        = (request("var_endprin_cidade"));
$strEndPrinEstado        = (request("var_endprin_estado"));
$strEndPrinPais          = (request("var_endprin_pais"));
$strEndPrinFone1         = request("var_endprin_fone1");
$strEndPrinFone2         = request("var_endprin_fone2");
$strEndPrinFone3         = request("var_endprin_fone3");
$strEndPrinFone4         = request("var_endprin_fone4");
$strEndPrinFone5         = request("var_endprin_fone5");
$strEndPrinFone6         = request("var_endprin_fone6");

$strEndComCEP           = request("var_endcom_cep");
$strEndComLogradouro    = (request("var_endcom_logradouro"));
$strEndComNumero        = request("var_endcom_numero");
$strEndComCompl         = request("var_endcom_complemento");
$strEndComBairro        = (request("var_endcom_bairro"));
$strEndComCidade        = (request("var_endcom_cidade"));
$strEndComEstado        = (request("var_endcom_estado"));
$strEndComPais          = (request("var_endcom_pais"));
$strEndComFone1         = request("var_endcom_fone1");
$strEndComFone2         = request("var_endcom_fone2");
$strEndComFone3         = request("var_endcom_fone3");
$strEndComFone4         = request("var_endcom_fone4");
$strEndComFone5         = request("var_endcom_fone5");
$strEndComFone6         = request("var_endcom_fone6");

$strCategoria 		 = (request("var_categoria"));
$strTrabFuncao       = (request("var_trab_funcao"));
$strTrabDepartamento = (request("var_trab_departamento"));
$strTrabTipo         = (request("var_trab_tipo"));
$strCodCargo		 = (request("var_cod_cargo"));
$strCodNivel         = (request("var_cod_nivel"));
$strClVip			 = (request("var_classificacao_vip"));

$strTrabObs          = (request("var_trab_obs"));
$dtTrabDtAdmissao    = request("var_trab_dt_admissao");
$dtTrabDtDemissao	 = request("var_trab_dt_demissao");

$strLocation 		 = request("var_redirect");


/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
if($intCodPJ == "") 			{ $strMsg .= "Informar Empresa<br>"; }
if($strNome == "") 				{ $strMsg .= "Informar Nome<br>"; }
//if($strApelido == "") 			{ $strMsg .= "Informar Apelido (Nome Credencial)<br>"; }
//if($strCPF == "") 			{ $strMsg .= "Informar CPF<br>"; }
//if($strRG == "") 				{ $strMsg .= "Informar RG<br>"; }
//if($strFoto == "") 			{ $strMsg .= "Informar Foto<br>"; }
//if($strEndPrinCEP == "") 		{ $strMsg .= "Informar CEP<br>"; }
//if($strEndPrinLogradouro == "") { $strMsg .= "Informar Logradouro<br>"; }
//if($strEndPrinNumero == "")	{ $strMsg .= "Informar Número<br>"; }
//if($strEndPrinBairro == "")	{ $strMsg .= "Informar Bairro<br>"; }
//if($strEndPrinCidade == "")	{ $strMsg .= "Informar Cidade<br>"; }
//if($strEndPrinEstado == "") 	{ $strMsg .= "Informar Estado<br>"; }
//if($strEndPrinPais == "") 	{ $strMsg .= "Informar País<br>"; }
//if($dtTrabDtAdmissao == "") 	{ $strMsg .= "Informar Data Admissão<br>"; }
if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

/*** TRATAMENTO DOS CAMPOs ***/
$dtDataNasc			= cDate(CFG_LANG, $dtDataNasc, false);
$dtTrabDtAdmissao	= cDate(CFG_LANG, $dtTrabDtAdmissao, false);
$dtTrabDtDemissao	= cDate(CFG_LANG, $dtTrabDtDemissao, false);
(($dtDataNasc == "") || (!is_date($dtDataNasc))) ? $dtDataNasc = "NULL" : $dtDataNasc = "'" . $dtDataNasc . "'";
(($dtTrabDtAdmissao == "") || (!is_date($dtTrabDtAdmissao))) ? $dtTrabDtAdmissao = "NULL" : $dtTrabDtAdmissao = "'" . $dtTrabDtAdmissao . "'";
(($dtTrabDtDemissao == "") || (!is_date($dtTrabDtDemissao))) ? $dtTrabDtDemissao = "NULL" : $dtTrabDtDemissao = "'" . $dtTrabDtDemissao . "'";
$dtInativo = ($strFlagAtivo == "INATIVO") ? "'".cDate(CFG_LANG,request("var_dt_inativo"),false)."'" : "NULL"; 
$strMotivo = ($strFlagAtivo == "INATIVO") ? "'".request("var_motivo_inativo")."'" : "NULL";

if ($strCodCargo == "") $strCodCargo = "NULL";
if ($strCodNivel == "") $strCodNivel = "NULL";

$objConn->beginTransaction();
try{
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
	$strSQL .= "   , rg = '" . $strRG . "' ";
	$strSQL .= "   , cpf= '" . $strCPF . "' ";
	$strSQL .= "   , tratamento = '" . $strTratamento . "' ";
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
	$strSQL .= "   , endprin_fone3 = '" . $strEndPrinFone3 . "' ";
	$strSQL .= "   , endprin_fone4 = '" . $strEndPrinFone4 . "' ";
	$strSQL .= "   , endprin_fone5 = '" . $strEndPrinFone5 . "' ";
	
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
		$strSQL .= "   , dt_inativo = ".$dtInativo;
		$strSQL .= "   , motivo_inativo = ".$strMotivo;
		$strSQL .= "   , cod_cargo = ".$strCodCargo;
		$strSQL .= "   , cod_nivel_hierarquico = ".$strCodNivel;
		$strSQL .= "   , classificacao_vip = '" . $strClVip . "' ";
		$strSQL .= " WHERE cod_pf = " . $intCodPF;
		$strSQL .= "   AND cod_pj = " . $intCodPJ;
		$strSQL .= "   AND dt_demissao IS NULL ";
		//die($strSQL);
		$objConn->query($strSQL);
	}
	
		
	$objConn->commit();
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage()."<br>SQL: ". $strSQL,"","erro",1);
	$objConn->rollBack();
	die();
}
$objConn = NULL;

redirect($strLocation);
?>