<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

/*************** Lista de Indices ****************

	[0] - cod_pf       [6]  - sys_usr_ins      [12] - cep            [18] - estado        [24] - DDD Extra 1      [30] - DDD Extra 3  
	[1] - nome         [7]  - sys_dtt_ins      [13] - logradouro     [19] - pais          [25] - Fone Extra 1     [31] - Fone Extra 3
	[2] - data_nasc    [8]  - sys_usr_upd      [14] - numero         [20] - DDI           [26] - DDI Extra 2      [32] - e-mail 
	[3] - sexo         [9]  - sys_dtt_upd      [15] - complemento    [21] - DDD           [27] - DDD Extra 2      [33] - e-mail extra
	[4] - atuacao      [10] - documentos       [16] - bairro         [22] - Fone          [28] - Fone Extra 2     [34] - homepage   
	[5] - atividade    [11] - tipo endereço    [17] - cidade         [23] - DDI Extra 1   [29] - DDI Extra 3      
	
**************************************************/                                                                                                               

$objConn = abreDBConn(CFG_DB);

$intCodPF        = request("var_cod_pf");
$strNome         = request("var_nome");
$strNomeDoc      = request("var_nome_doc");
$strValorDoc     = request("var_valor_doc");
$strTipoEndereco = request("var_tipo_endereco");

$strSQLAux = "";

if($intCodPF != "") {
	$strSQLAux = "    pf.cod_pf  =  '" . $intCodPF;
} elseif($strNome != "") {
	$strSQLAux = "    pf.nome  =  '" . $strNome . "'";
} elseif($strNomeDoc != "" && $strValorDoc != "") {
	$strSQLAux = "    dpf.nome  = '" .$strNomeDoc . "'
				  AND dpf.valor = '" . $strValorDoc . "'";
}

try {
	$strSQL = " SELECT pf.cod_pf
					 , pf.nome
					 , pf.data_nasc
					 , pf.sexo
					 , pf.sys_usr_ins
					 , pf.sys_dtt_ins
					 , pf.sys_usr_upd
					 , pf.sys_dtt_upd
					 , atu.nome AS atuacao
					 , ati.nome AS atividade
				  FROM cad_pf AS pf
					   " . (($strNomeDoc != "" && $strValorDoc != "") ? "LEFT OUTER JOIN cad_doc_pf AS dpf ON (pf.cod_pf = dpf.cod_pf)" : "") . "
					   LEFT OUTER JOIN cad_atuacao AS atu ON (atu.cod_atuacao = pf.cod_atuacao AND atu.pessoa <=> 'F')
					   LEFT OUTER JOIN cad_atividade AS ati ON (ati.cod_atividade = pf.cod_atividade AND ati.pessoa <=> 'F')
					WHERE " . $strSQLAux . "
				 ORDER BY pf.cod_pf ";
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

if($objRS = $objResult->fetch()) {
	try {
		$strSQL = " SELECT te.nome AS tipo_endereco, epf.cep, epf.logradouro, epf.numero, epf.complemento, epf.bairro, epf.cidade, epf.estado, epf.pais
						 , epf.email, epf.email_extra, epf.homepage
						 , epf.ddd, epf.ddi, epf.fone
						 , epf.ddd_extra1, epf.ddi_extra1, epf.fone_extra1
						 , epf.ddd_extra2, epf.ddi_extra2, epf.fone_extra2
						 , epf.ddd_extra3, epf.ddi_extra3, epf.fone_extra3
					  FROM cad_endereco_pf AS epf INNER JOIN cad_tipo_endereco AS te ON (te.cod_tipo_endereco = epf.cod_tipo_endereco)
					 WHERE epf.cod_pf = " . getValue($objRS,"cod_pf") . "
					   AND " . (($strTipoEndereco != "") ? " te.nome = '" . $strTipoEndereco . "'" : " te.ordem = 10 ") ;
		$objResultEnd = $objConn->query($strSQL);
		
		
		$objRSEnd = $objResultEnd->fetch();
		
		$strSQL = " SELECT dpf.nome, dpf.valor 
					  FROM cad_doc_pf AS dpf
					 WHERE dpf.cod_pf = " .getValue($objRS, "cod_pf");
		$objResultDoc = $objConn->query($strSQL);
	} catch(PDOException $e) {
		header("HTTP/1.0 500 Server internal error");
		echo($e->getMessage());
		die();
	}
	
	$strDocs = "";
	foreach($objResultDoc as $objRSDoc) {
		$strDocs .= ($strDocs == "") ? "'" . getValue($objRSDoc,"nome") . "' => " . getValue($objRSDoc,"valor") : ";'" . getValue($objRSDoc,"nome") . "' => " . getValue($objRSDoc,"valor") ;
	}
	
	$strRetorno = getValue($objRS,"cod_pf") . ";" . getValue($objRS,"nome") . ";" . getValue($objRS,"data_nasc") . ";" . getValue($objRS,"sexo") . ";" . 
				  getValue($objRS,"atuacao") . ";" . getValue($objRS,"atividade") . ";" .
				  getValue($objRS,"sys_usr_ins") . ";" . getValue($objRS,"sys_dtt_ins") . ";" . 
				  getValue($objRS,"sys_usr_upd") . ";" . getValue($objRS,"sys_dtt_upd") . ";" .
				  $strDocs . ";" .
				  getValue($objRSEnd,"tipo_endereco") . ";" .
				  getValue($objRSEnd,"cep") . ";" . getValue($objRSEnd,"logradouro") . ";" . getValue($objRSEnd,"numero") . ";" . getValue($objRSEnd,"complemento") . ";" .
				  getValue($objRSEnd,"bairro") . ";" . getValue($objRSEnd,"cidade") . ";" . getValue($objRSEnd,"estado") . ";" . getValue($objRSEnd,"pais") . ";" .
				  getValue($objRSEnd,"ddi") . ";" . getValue($objRSEnd,"ddd") . ";" . getValue($objRSEnd,"fone") . ";" .
				  getValue($objRSEnd,"ddi_extra1") . ";" . getValue($objRSEnd,"ddd_extra1") . ";" . getValue($objRSEnd,"fone_extra1") . ";" .
				  getValue($objRSEnd,"ddi_extra2") . ";" . getValue($objRSEnd,"ddd_extra2") . ";" . getValue($objRSEnd,"fone_extra2") . ";" .
				  getValue($objRSEnd,"ddi_extra3") . ";" . getValue($objRSEnd,"ddd_extra3") . ";" . getValue($objRSEnd,"fone_extra3") . ";" .
				  getValue($objRSEnd,"email") . ";" . getValue($objRSEnd,"email_extra") . ";" . getValue($objRSEnd,"homepage");

	$objResultEnd->closeCursor();
	$objResultDoc->closeCursor();
} else {
	$strRetorno = NULL;
}

echo(trim($strRetorno));
	
	
$objResult->closeCursor();
?>