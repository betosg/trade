<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

/*************** Lista de Indices ****************

	[0] - cod_pj       [6]  - sys_usr_ins      [12] - cep            [18] - estado        [24] - DDD Extra 1      [30] - DDD Extra 3  
	[1] - nome         [7]  - sys_dtt_ins      [13] - logradouro     [19] - pais          [25] - Fone Extra 1     [31] - Fone Extra 3
	[2] - data_nasc    [8]  - sys_usr_upd      [14] - numero         [20] - DDI           [26] - DDI Extra 2      [32] - e-mail 
	[3] - sexo         [9]  - sys_dtt_upd      [15] - complemento    [21] - DDD           [27] - DDD Extra 2      [33] - e-mail extra
	[4] - atuacao      [10] - documentos       [16] - bairro         [22] - Fone          [28] - Fone Extra 2     [34] - homepage   
	[5] - atividade    [11] - tipo endereço    [17] - cidade         [23] - DDI Extra 1   [29] - DDI Extra 3      
	
**************************************************/                                                                                                               

$objConn = abreDBConn(CFG_DB);

$intCodPJ        = request("var_cod_pj");
$strNome         = request("var_nome");
$strNomeDoc      = request("var_nome_doc");
$strValorDoc     = request("var_valor_doc");
$strTipoEndereco = request("var_tipo_endereco");

$strSQLAux = "";

if($intCodPJ != "") {
	$strSQLAux = "    pj.cod_pj  =  " . $intCodPJ;
} elseif($strNome != "") {
	$strSQLAux = "    pj.nome  =  '" . $strNome . "'";
} elseif($strNomeDoc != "" && $strValorDoc != "") {
	$strSQLAux = "    dpj.nome  = '" .$strNomeDoc . "'
				  AND dpj.valor = '" . $strValorDoc . "'";
}

try {
	$strSQL = " SELECT pj.cod_pj
					 , pj.razao_social
					 , pj.nome_fantasia
					 , pj.data_fundacao
					 , pj.senha
					 , pj.sys_usr_ins
					 , pj.sys_dtt_ins
					 , pj.sys_usr_upd
					 , pj.sys_dtt_upd
					 , atu.nome AS atuacao
					 , ati.nome AS atividade
					 , seg.nome AS segmento
					 , cat.nome AS categoria
					 , cn1.nome AS cnae_n1
					 , cn2.nome AS cnae_n2
					 , cn3.nome AS cnae_n3
					 , cn4.nome AS cnae_n4
					 , tpn.nome AS tipo_normal
					 , tpp.nome AS tipo_prest
				  FROM cad_pj AS pj
					   " . (($strNomeDoc != "" && $strValorDoc != "") ? "LEFT OUTER JOIN cad_doc_pj AS dpj ON (pj.cod_pj = dpj.cod_pj)" : "") . "
					   LEFT OUTER JOIN cad_atuacao AS atu ON (atu.cod_atuacao = pj.cod_atuacao AND atu.pessoa <=> 'J')
					   LEFT OUTER JOIN cad_atividade AS ati ON (ati.cod_atividade = pj.cod_atividade AND ati.pessoa <=> 'J')
					   LEFT OUTER JOIN cad_segmento AS seg ON (seg.cod_segmento = pj.cod_segmento)
					   LEFT OUTER JOIN cad_categoria AS cat ON (cat.cod_categoria = pj.cod_categoria)
					   LEFT OUTER JOIN cad_cnae_secao AS cn1 ON (cn1.cod_cnae_secao = pj.cod_cnae_n1)
					   LEFT OUTER JOIN cad_cnae_divisao AS cn2 ON (cn2.cod_cnae_divisao = pj.cod_cnae_n2)
					   LEFT OUTER JOIN cad_cnae_grupo AS cn3 ON (cn3.cod_cnae_grupo = pj.cod_cnae_n3)
					   LEFT OUTER JOIN cad_cnae_classe AS cn4 ON (cn4.cod_cnae_classe = pj.cod_cnae_n4)
					   LEFT OUTER JOIN cad_tipo AS tpn ON (tpn.cod_tipo = pj.cod_tipo_normal)
					   LEFT OUTER JOIN cad_tipo AS tpp ON (tpp.cod_tipo = pj.cod_tipo_prest)
					WHERE " . $strSQLAux . "
				 ORDER BY pj.cod_pj ";
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

if($objRS = $objResult->fetch()) {
	try {
		$strSQL = " SELECT te.nome AS tipo_endereco, epj.cep, epj.logradouro, epj.numero, epj.complemento, epj.bairro, epj.cidade, epj.estado, epj.pais
						 , epj.email, epj.email_extra, epj.homepage
						 , epj.ddd, epj.ddi, epj.fone
						 , epj.ddd_extra1, epj.ddi_extra1, epj.fone_extra1
						 , epj.ddd_extra2, epj.ddi_extra2, epj.fone_extra2
						 , epj.ddd_extra3, epj.ddi_extra3, epj.fone_extra3
					  FROM cad_endereco_pj AS epj INNER JOIN cad_tipo_endereco AS te ON (te.cod_tipo_endereco = epj.cod_tipo_endereco)
					 WHERE epj.cod_pj = " . getValue($objRS,"cod_pj") . "
					   AND " . (($strTipoEndereco != "") ? " te.nome = '" . $strTipoEndereco . "'" : " te.ordem = 10 ") ;
		$objResultEnd = $objConn->query($strSQL);
		
		$objRSEnd = $objResultEnd->fetch();
		
		$strSQL = " SELECT dpj.nome, dpj.valor 
					  FROM cad_doc_pj AS dpj
					 WHERE dpj.cod_pj = " .getValue($objRS, "cod_pj");
		$objResultDoc = $objConn->query($strSQL);
	} catch(PDOException $e) {
		header("HTTP/1.0 500 Server internal error");
		echo($e->getMessage());
		die();
	}
	
	$strDocs = "";
	foreach($objResultDoc as $objRSDoc) {
		$strDocs .= ($strDocs == "") ? "'" . getValue($objRSDoc,"nome") . "' => " . getValue($objRSDoc,"valor") : " | '" . getValue($objRSDoc,"nome") . "' => " . getValue($objRSDoc,"valor") ;
	}
	
	$strRetorno = getValue($objRS,"cod_pj") . ";" . getValue($objRS,"razao_social") . ";" . getValue($objRS,"nome_fantasia") . ";" . getValue($objRS,"nome_comercial") . ";" . 
				  getValue($objRS,"senha") . ";" . getValue($objRS,"data_fundacao") . ";" . getValue($objRS,"tipo_normal") . ";" . getValue($objRS,"tipo_prest") . ";" .
				  getValue($objRS,"atuacao") . ";" . getValue($objRS,"atividade") . ";" . getValue($objRS,"categoria") . ";" . getValue($objRS,"segmento") . ";" . 
				  getValue($objRS,"cnae_n1") . ";" . getValue($objRS,"cnae_n2") . ";" . getValue($objRS,"cnae_n3") . ";" . getValue($objRS,"cnae_n4") . ";" . 
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