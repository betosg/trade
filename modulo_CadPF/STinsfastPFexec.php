<?php
include_once("../_database/athdbconn.php");
$objConn = abreDBConn(CFG_DB);


$strMsg 	 = '';
$strEntidade = '';

/*** RECEBE PARAMETROS ***/
$intPF			  = trim(request("dbvar_int_cod_pf"));
$strCPF		      = request("dbvar_str_valor");
$strNome  		  = request("dbvar_str_nome");
$dtDataNasc       = request("dbvar_date_data_nasc");
$strSexo  		  = request("dbvar_str_sexo");


$intPJ			  = request("dbvar_cod_pj");
$strCargo		  = request("dbvar_str_cargo");
$strDepartamento  = request("dbvar_str_departamento");
$dtAdmissao		  = request("dbvar_date_data_admissao");

$intCodEnderecoPF = request("dbvar_int_cod_endereco_pf");
$strCEP       	  = request("dbvar_str_cep");
$strLogradouro    = request("dbvar_str_logradouro");
$strNumero        = request("dbvar_str_numero");
$strCompl         = request("dbvar_str_complemento");
$strEndereco      = request("dbvar_str_endereco");
$strBairro        = request("dbvar_str_bairro");
$strCidade        = request("dbvar_str_cidade");
$strUF            = request("dbvar_str_estado");
$strPais          = request("dbvar_str_pais");
$strEmail         = request("dbvar_str_email");
$strEmailExtra    = request("dbvar_str_email_extra");
$strHomepage      = request("dbvar_str_homepage");
$strFone1         = request("dbvar_str_fone");
$strFone2         = request("dbvar_str_fone_extra1");
$strFone3         = request("dbvar_str_fone_extra2");
$strFone4         = request("dbvar_str_fone_extra3");
$sys_usr_ins      = getsession(CFG_SYSTEM_NAME . "_id_usuario");
$sys_usr_upd      = getsession(CFG_SYSTEM_NAME . "_id_usuario");
$var_location     = request("var_location");

/*** TRATAMENTO DA var_location ***/
if($var_location == ''){
	$var_location = 'data.php';
}

/*** TRATAMENTO DA DATA DE FUNDAÇÃO ***/
$dtDataNasc = cDate(CFG_LANG, $dtDataNasc, false);
$dtAdmissao = cDate(CFG_LANG, $dtAdmissao, false);
(($dtDataNasc == "") || (!is_date($dtDataNasc))) ? $dtDataNasc = "NULL" : $dtDataNasc = "'" . $dtDataNasc . "'";

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
if($strNome == "") 							{  $strMsg .= "Informar Nome<br>"; }
if($intPJ == "") 							{  $strMsg .= "Informar Empresa<br>"; }
if(($strCPF == "")) 						{  $strMsg .= "Informar CPF<br>"; }
if(($strSexo == "")) 						{  $strMsg .= "Informar Sexo<br>"; }
if(($strCargo == "")) 						{  $strMsg .= "Informar Cargo<br>"; }
if(($strDepartamento == ""))				{  $strMsg .= "Informar Departamento<br>"; }
if(($dtAdmissao == ""))						{  $strMsg .= "Informar Data de Admissão<br>"; }

if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

//----------------------------------
// Insere dados da PESSOA FISICA 
//----------------------------------
$objConn->beginTransaction();
try{
	//-----------------
	// pessoa Física
	if($intPF != ""){
		$strSQL = "UPDATE cad_pf SET nome='".$strNome."', data_nasc=".$dtDataNasc.", sexo='".$strSexo."', sys_usr_upd='".$sys_usr_upd."', sys_dtt_upd=CURRENT_TIMESTAMP WHERE cod_pf=".$intPF." ";
		$objConn->query($strSQL);
		
		$strSQL = " UPDATE cad_endereco_pf  SET
						 cep = '" . $strCEP . "', 
						 logradouro = '" . $strLogradouro . "', 
						 numero = '" . $strNumero . "', 
						 complemento = '" . $strCompl . "' , 
						 endereco = '" . $strEndereco . "', 
						 bairro = '" . $strBairro . "', 
						 cidade = '" . $strCidade . "', 
						 estado = '" . $strUF . "', 
						 pais = '" . $strPais . "', 
						 fone = '" . $strFone1 . "', 
						 fone_extra1 = '" . $strFone2 . "', 
						 fone_extra2 = '" . $strFone3 . "', 
						 fone_extra3 = '" . $strFone4 . "', 
						 email = '" . $strEmail . "', 
						 email_extra = '" . $strEmailExtra . "', 
						 homepage = '" . $strHomepage . "'
					WHERE
						cod_endereco = ".$intCodEnderecoPF;	 
		//echo($strSQL."<p>");				 
		$objConn->query($strSQL);
		
		$strSQL = " INSERT INTO cad_pf_pj (cod_pf, cod_pj, relacao, cargo, departamento) 
					 VALUES (" . $intPF . ", " . $intPJ . ",'REAL', '".$strCargo."','".$strDepartamento."')";
		//die($strSQL);	
		//echo($strSQL."<p>");
		$objConn->query($strSQL);
	
	}else{
		$strSQL = " INSERT INTO cad_pf (nome, data_nasc, sexo, sys_usr_ins, sys_dtt_ins) 
					 VALUES ('" . $strNome . "', " . $dtDataNasc . ", '" . $strSexo . "', '" . $sys_usr_ins . "', CURRENT_TIMESTAMP)";
		$objConn->query($strSQL);
	
		$strSQL = "select currval('cad_pf_cod_pf_seq') AS mycurrval";
		$objResult = $objConn->query($strSQL);
		$objRS = $objResult->fetch();
		$intCurrVal = getValue($objRS,"mycurrval");

		//--------------
		// documento pf
		$strSQL = " INSERT INTO cad_doc_pf (cod_pf, nome, valor) 
					 VALUES (" . $intCurrVal . ", 'CPF', '" . $strCPF . "')";
		$objConn->query($strSQL);
	
		//-------------
		// endereço pf
		$strSQL = " INSERT INTO cad_endereco_pf (cod_pf, cep, logradouro, numero, complemento, endereco, bairro, cidade, estado, pais, fone, fone_extra1, fone_extra2, fone_extra3, email, email_extra, homepage) 
					 VALUES (" . $intCurrVal . ", '" . $strCEP . "', '" . $strLogradouro . "', '" . $strNumero . "', '" . $strCompl . "', '" . $strEndereco . "', '" . $strBairro . "', '" . $strCidade . "'
					 , '" . $strUF . "', '" . $strPais . "', '" . $strFone1 . "', '" . $strFone2 . "', '" . $strFone3 . "', '" . $strFone4 . "','" . $strEmail . "', '" . $strEmailExtra . "', '" . $strHomepage . "')";
		$objConn->query($strSQL);

		//-------------
	
		// relacao pf pj
		$strSQL = " INSERT INTO cad_pf_pj (cod_pf, cod_pj, relacao, cargo, departamento) 
					 VALUES (" . $intCurrVal . ", " . $intPJ . ",'REAL', '".$strCargo."','".$strDepartamento."')";
		//die($strSQL);	
		$objConn->query($strSQL);
	}
	
	$objConn->commit();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	$objConn->rollBack();
	die();
}

redirect($var_location);
?>