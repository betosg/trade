<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

$strMsg 	 = "";

/*** RECEBE PARAMETROS ***/
$strOperacao = request("var_oper");
$intCodDado = request("var_chavereg");

$strNome  		  = request("var_nome");
$strApelido		  = request("var_apelido");
$dtDataNasc       = request("var_data_nasc");
$strSexo  		  = request("var_sexo");
$strEmail         = request("var_email");
$strEmailExtra    = request("var_email_extra");
$strWebsite       = request("var_website");
$strFoto          = request("var_foto");
$strEstadoCivil   = request("var_estado_civil");
$strInstrucao     = request("var_instrucao");
$strNacionalidade = request("var_nacionalidade");
$strNaturalidade  = request("var_naturalidade");
$strObs           = request("var_obs");
$strNomePai       = request("var_nome_pai");
$strNomeMae       = request("var_nome_mae");

$strCPF		      = request("var_cpf");
$strRG            = request("var_rg");
$strCNH           = request("var_cnh");
$strPIS           = request("var_pis");
$strCTPS          = request("var_ctps");

$strEndPrinCEP           = request("var_endprin_cep");
$strEndPrinLogradouro    = request("var_endprin_logradouro");
$strEndPrinNumero        = request("var_endprin_numero");
$strEndPrinCompl         = request("var_endprin_complemento");
$strEndPrinBairro        = request("var_endprin_bairro");
$strEndPrinCidade        = request("var_endprin_cidade");
$strEndPrinEstado        = request("var_endprin_estado");
$strEndPrinPais          = request("var_endprin_pais");
$strEndPrinFone1         = request("var_endprin_fone1");
$strEndPrinFone2         = request("var_endprin_fone2");
$strEndPrinFone3         = request("var_endprin_fone3");
$strEndPrinFone4         = request("var_endprin_fone4");
$strEndPrinFone5         = request("var_endprin_fone5");
$strEndPrinFone6         = request("var_endprin_fone6");

$strEndComCEP           = request("var_endcom_cep");
$strEndComLogradouro    = request("var_endcom_logradouro");
$strEndComNumero        = request("var_endcom_numero");
$strEndComCompl         = request("var_endcom_complemento");
$strEndComBairro        = request("var_endcom_bairro");
$strEndComCidade        = request("var_endcom_cidade");
$strEndComEstado        = request("var_endcom_estado");
$strEndComPais          = request("var_endcom_pais");
$strEndComFone1         = request("var_endcom_fone1");
$strEndComFone2         = request("var_endcom_fone2");
$strEndComFone3         = request("var_endcom_fone3");
$strEndComFone4         = request("var_endcom_fone4");
$strEndComFone5         = request("var_endcom_fone5");
$strEndComFone6         = request("var_endcom_fone6");

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
if($intCodPJ == "") 			{ $strMsg .= "Informar Empresa<br>"; }
if($strNome == "") 				{ $strMsg .= "Informar Nome<br>"; }
if($strSexo == "") 				{ $strMsg .= "Informar Sexo<br>"; }
if($strCPF == "") 				{ $strMsg .= "Informar CPF<br>"; }
if($strEndPrinCEP == "") 		{ $strMsg .= "Informar CEP<br>"; }
if($strEndPrinLogradouro == "") { $strMsg .= "Informar Logradouro<br>"; }
if($strEndPrinNumero == "")		{ $strMsg .= "Informar Número<br>"; }
if($strEndPrinBairro == "")		{ $strMsg .= "Informar Bairro<br>"; }
if($strEndPrinCidade == "")		{ $strMsg .= "Informar Cidade<br>"; }
if($strEndPrinEstado == "") 	{ $strMsg .= "Informar Estado<br>"; }
if($strEndPrinPais == "") 		{ $strMsg .= "Informar País<br>"; }
if($strEndPrinFone1 == "") 		{ $strMsg .= "Informar Fone 1<br>"; }
if($strEndPrinFone2 == "") 		{ $strMsg .= "Informar Fone 2<br>"; }

if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

/*** TRATAMENTO DOS CAMPOs ***/
$dtDataNasc = cDate(CFG_LANG, $dtDataNasc, false);
(($dtDataNasc == "") || (!is_date($dtDataNasc))) ? $dtDataNasc = "NULL" : $dtDataNasc = "'" . $dtDataNasc . "'";

$objConn->beginTransaction();
try{
	//PF já está cadastrada, apenas atualiza os dados
	if ($strOperacao == 'UPD') {
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
		$strSQL .= "   , cpf = '" . $strCPF . "' ";
		$strSQL .= "   , rg = '" . $strRG . "' ";
		$strSQL .= "   , cnh = '" . $strCNH . "' ";
		$strSQL .= "   , pis = '" . $strPIS . "' ";
		$strSQL .= "   , ctps = '" . $strCTPS . "' ";
		
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
		$strSQL .= " WHERE cod_pf = " . $intCodDado;
		
		$objConn->query($strSQL);
	}
	//PF não está cadastrada, então insere os dados
	else {
		$strSQL  = " INSERT INTO cad_pf ( nome, apelido, data_nasc, sexo, nome_pai, nome_mae, email, email_extra, website, foto, estado_civil ";
		$strSQL .= "                    , instrucao, nacionalidade, naturalidade, obs, cpf, rg, cnh, pis, ctps ";
		
		$strSQL .= "                    , endprin_cep, endprin_logradouro, endprin_numero, endprin_complemento, endprin_bairro ";
		$strSQL .= "                    , endprin_cidade, endprin_estado, endprin_pais, endprin_fone1, endprin_fone2 ";
		
		$strSQL .= "                    , endcom_cep, endcom_logradouro, endcom_numero, endcom_complemento, endcom_bairro ";
		$strSQL .= "                    , endcom_cidade, endcom_estado, endcom_pais, endcom_fone1, endcom_fone2 ";
		
		$strSQL .= "                    , sys_dtt_ins, sys_usr_ins ) ";
		$strSQL .= " VALUES ( '" . $strNome . "', '" . $strApelido . "', " . $dtDataNasc . ", '" . $strSexo . "', '" . $strNomePai . "', '" . $strNomeMae . "' ";
		$strSQL .= "        , '" . $strEmail . "', '" . $strEmailExtra . "', '" . $strWebsite . "', '" . $strFoto . "', '" . $strEstadoCivil . "' ";
		$strSQL .= "        , '" . $strInstrucao . "', '" . $strNacionalidade . "', '" . $strNaturalidade . "', '" . $strObs . "' ";
		$strSQL .= "        , '" . $strCPF . "', '" . $strRG . "', '" . $strCNH . "', '" . $strPIS . "', '" . $strCTPS . "' ";
		
		$strSQL .= "        , '" . $strEndPrinCEP . "', '" . $strEndPrinLogradouro . "', '" . $strEndPrinNumero . "', '" . $strEndPrinCompl . "' ";
		$strSQL .= "        , '" . $strEndPrinBairro . "', '" . $strEndPrinCidade . "', '" . $strEndPrinEstado . "', '" . $strEndPrinPais . "' ";
		$strSQL .= "        , '" . $strEndPrinFone1 . "', '" . $strEndPrinFone2 . "' ";
		
		$strSQL .= "        , '" . $strEndComCEP . "', '" . $strEndComLogradouro . "', '" . $strEndComNumero . "', '" . $strEndComCompl . "' ";
		$strSQL .= "        , '" . $strEndComBairro . "', '" . $strEndComCidade . "', '" . $strEndComEstado . "', '" . $strEndComPais . "' ";
		$strSQL .= "        , '" . $strEndComFone1 . "', '" . $strEndComFone2 . "' ";
		
		$strSQL .= "        , current_timestamp, '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "') ";
		
		$objConn->query($strSQL);
	}
	
	$objConn->commit();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	$objConn->rollBack();
	die();
}

$objConn = NULL;

redirect("STinsupdfastPF.php?var_oper=" . $strOperacao . "&var_chavereg=" . $intCodDado);
?>