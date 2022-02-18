<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn("tradeunion_sindieventos");

$objFile = fopen("./bancosCSV.csv", "r");

$cont = 1;
while (!feof($objFile)){
	$strLine = trim(str_replace("'","''",fgets($objFile, 4096)));
	$arrLine = explode(";",$strLine);
	
	// Insere a PJ
	try {
		$strSQL = " INSERT INTO fin_banco (num_banco, nome) VALUES ('" . $arrLine[0] . "','" . $arrLine[1] . "'); ";
		$objConn->query($strSQL);
		//echo($strSQL . "<br>");
		
		/*$intCodPJ = $objConn->lastInsertId("cad_pj_cod_pj_seq");
		//$intCodPJ = "";
		
		$strCNPJ = str_replace(".","",$arrLine[8]);
		$strCNPJ = str_replace("-","",$strCNPJ);
		$strCNPJ = str_replace("/","",$strCNPJ);
		
		/*$strSQL = " INSERT INTO sys_usuario (id_usuario, senha, tipo, codigo, dir_default, lang, email, grp_user, sys_dtt_ins, sys_usr_ins) 
					VALUES ('" . $strCNPJ . "','" . md5($intCodPJ) . "','j'," . $intCodPJ . ",'../modulo_AreaRestritaExpo/STindex.php', 'ptb', '" . $arrLine[24] . "', 'EXPOSITOR', current_timestamp, 'athenas')";
		$objConn->query($strSQL);
		
		$intCodUsuario = $objConn->lastInsertId("sys_usuario_cod_usuario_seq");
		
		$strSQL = " SELECT * FROM sp_copia_direitos(11," . $intCodUsuario . ")";
		$objConn->query($strSQL);
		*/
		echo("Linha " . $cont . ": " . $arrLine[0] . ";" . "<br>");
		echo("Linha " . $cont . ": " . $arrLine[0] . ";" . "<br>");
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage() . " - " . $e->getLine() . "<br><br>" . $strSQL . " - " . $e->getLine(),"","erro",1);
		die();
	}
	
	/*
	// Insere o Estande
	try {
		$strSQL = " INSERT INTO cad_estande (cod_pj, pavilhao, num_estande, endereco, metragem, temp_responsavel, temp_fone_contato) 
		             VALUES 
					(" . $intCodPJ . ",'" . $arrLine[0] . "','" . str_replace(",",".",$arrLine[1]) . "','" . $arrLine[2] . "','" . $arrLine[3] . "','" . $arrLine[4] . "','" . $arrLine[5] . "'); ";
		$objConn->query($strSQL);
		//echo($strSQL . "<br>");
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage() . " - " . $e->getLine() . "<br><br>" . $strSQL,"","erro",1);
		die();
	}*/
	
	// Insere o CNPJ, mesmo se não tiver CNPJ ele insere um registro vazio..
	/*
	try {
		$strSQL = " INSERT INTO cad_doc_pj (cod_pj, nome, valor) VALUES (" . $intCodPJ . ",'CNPJ','" . $strCNPJ . "'); ";
		$objConn->query($strSQL);
		//echo($strSQL . "<br>");
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage() . " - " . $e->getLine() . "<br><br>" . $strSQL,"","erro",1);
		die();
	}
	
	// Insere o endereco da PJ
	try {
		
		$arrEmail = explode("/",$arrLine[24]);
		
		$strEmail      = trim($arrEmail[0]);
		$strEmailExtra = (isset($arrEmail[1])) ? trim($arrEmail[1]) : "";
		
		if(strtoupper($arrLine[17]) == "BRASIL") {
			$strDDI = "55";
			$arrFone1 = explode("/",$arrLine[21]);
			
			$arrFone       = explode(" ",$arrFone1[0]);
			$strDDD        = (strlen($arrFone[0] == 2)) ? $arrFone[0] : $arrLine[20];
			$strFone       = $arrFone1[0];
			
			$arrFoneExtra1 = explode(" ",$arrLine[22]);
			$strFoneExtra1 = (isset($arrFoneExtra1[1])) ? $arrFoneExtra1[1] : $arrFoneExtra1[0];
			$strDDDExtra1  = (strlen($arrFoneExtra1[0]) == 2) ? $arrFoneExtra1[0] : $arrLine[20];
			
			/*$arrFoneExtra2 = explode(" ",$arrLine[23]);
			$strFoneExtra2 = (isset($arrFoneExtra2[1])) ? $arrFoneExtra2[1] : $arrFoneExtra2[0];
			$strDDDExtra2  = (strlen($arrFoneExtra2[0]) == 2) ? $arrFoneExtra2[0] : $arrLine[20];*/
			
			//$strDDDExtra3  = $arrLine[20];
			//$strFoneExtra3 = (isset($arrFone1[1])) ? $arrFone1[1] : "";
		/*}
		else {
			$strDDI        = $arrLine[20];
			$strDDD        = "";
			
			$arrFone1 = explode("/",$arrLine[21]);
			$strFone = $arrFone1[0];
			
			$strDDDExtra1  = "";
			$strFoneExtra1 = $arrLine[22];
			$strDDDExtra2  = "";
			/*$strFoneExtra2 = $arrLine[23];
			$strFoneExtra2 = "";
			$strDDDExtra3  = "";
			$strFoneExtra3 = $arrFone1[0];
		}
		
		$strSQL = " INSERT INTO cad_endereco_pj (
						  cod_pj
						, logradouro
						, numero
						, complemento
						, bairro
						, cep
						, cidade
						, estado
						, pais
						, ddi
						, ddd
						, fone
						, ddi_extra1
						, ddd_extra1
						, fone_extra1
						, ddi_extra2
						, ddd_extra2
						, fone_extra2
						, ddi_extra3
						, ddd_extra3
						, fone_extra3
						, email
						, email_extra
						, ordem
					) VALUES (
						   " . $intCodPJ . "
						, '" . $arrLine[10] . "'
						, '" . $arrLine[11] . "'
						, '" . $arrLine[12] . "'
						, '" . $arrLine[13] . "'
						, '" . str_replace("-","",$arrLine[14]) . "'
						, '" . $arrLine[15] . "'
						, '" . trim($arrLine[16]) . "'
						, '" . $arrLine[17] . "'
						, '" . $strDDI . "'
						, '" . $strDDD . "'
						, '" . $strFone . "'
						, '" . $strDDI . "'
						, '" . $strDDDExtra1 . "'
						, '" . $strFoneExtra1 . "'
						, '" . $strDDI . "'
						, '" . $strDDDExtra2 . "'
						, '" . $strFoneExtra2 . "'
						, '" . $strDDI . "'
						, '" . $strDDDExtra3 . "'
						, '" . $strFoneExtra3 . "'
						, '" . $strEmail . "'
						, '" . $strEmailExtra . "'
						, 10
					) ";
		$objConn->query($strSQL);
		//echo($strSQL . "<br>");
	}
	catch(PDOException $e) {
		print_r($arrLine);
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage() . " - " . $e->getLine() . "<br><br>" . $strSQL . " - Array = " . count($arrLine) ,"","erro",1);
		die();
	}
	
	//Insere a atividade da PJ
	try {
		$strSQL = " INSERT INTO cad_atividade_pj (cod_pj,cod_atividade) VALUES (" . $intCodPJ . ",223); ";
		$objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage() . " - " . $e->getLine() . "<br><br>" . $strSQL,"","erro",1);
		die();
	}*/
	
	$cont++;
	//echo("<br><br>");
}

fclose($objFile);

$objConn = NULL;
?>