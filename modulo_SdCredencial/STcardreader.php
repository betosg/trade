<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// para poder ser chamado de fora do sistema - [Valida��o de cards usa esta p�gina]
	$strDBConnect	= (request("var_db") == "") ? getsession("tradeunion_db_name") : request("var_db");
	if(($strDBConnect == "") || (is_null($strDBConnect))){
		echo(
		"<center>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
				<tr>
				<td align=\"center\" valign=\"middle\" width=\"100%\">");
			mensagem("ERRO NO PROCESSAMENTO","Base n�o localizada","","O banco de dados n�o foi informado","","aviso",1)	;
			echo 
			   ("</td>
				</tr>
			</table>
		</center>");
		die();
	}
	
	//Inicia conex�o com banco
	$objConn = abreDBConn($strDBConnect);
	
	//Recebe cod_pf ou cod_card
	$strClauseCard 	= (request("var_cod_credencial") != "") ? " AND sd_credencial.cod_credencial = ".request("var_cod_credencial")."" : "";
	$intCodPF 		= request("var_chavereg");
echo	$strVarModelo	= request("var_modelo");
	
	//Busca modelo padr�o de CARD para leitura
	//NOTA: � importante que o modelo esteja se-
	//tado com o caminho completo
	$strCardPadrao  = (request("var_modelo")=="") ? getVarEntidade($objConn,"modelo_card") : request("var_modelo");
	
	if(($strCardPadrao == "") || (is_null($strCardPadrao))){
		echo(
		"<center>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
				<tr>
				<td align=\"center\" valign=\"middle\" width=\"100%\">");
			mensagem("err_dados_titulo","err_sql_desc_card",getTText("cpf_matricula_null",C_NONE),"","aviso",1)	;
			echo 
			   ("</td>
				</tr>
			</table>
		</center>");
		die();
	}
	
	//Recebe par�metro para RESIZE da p�gina HTML
	//Caso seja informado a altura e largura 
	//corretamente, faz um resize da p�gina
	$intResizeWidth  = request("var_width");
	$intResizeHeight = request("var_height");
	
	//debug - exemplo $intResizeWidth  = '600';
	//debug - exemplo $intResizeHeight = '500';
	
	//Consulta dados da CREDENCIAL, conforme o
	//cod_pf enviado
	try {
		$strSQL = "
				SELECT 
					sd_credencial.pf_nome, 
					sd_credencial.pf_rg, 
					sd_credencial.pf_cpf, 
					sd_credencial.pf_foto,
					sd_credencial.pf_empresa,
					sd_credencial.pf_funcao,
					sd_credencial.pf_matricula,
					sd_credencial.cod_credencial,
					sd_credencial.dt_validade,
					sd_credencial.dtt_inativo,
					cad_pj.cnpj,
					cad_pj.razao_social,
					cad_pj.nome_fantasia,
					cad_pj.nome_comercial,
					cad_pj.insc_est,
					cad_pj.insc_munic,
					cad_pj.email,
					cad_pj.website,
					cad_pj.contato,
					cad_pj.endcobr_rotulo,
					cad_pj.endprin_fone1,
					cad_pj.endprin_fone2,
					relac_pj_pf.categoria
				FROM
					sd_credencial
				LEFT JOIN
					cad_pj ON (cad_pj.cod_pj = sd_credencial.cod_pj)
				LEFT JOIN 
					relac_pj_pf on (relac_pj_pf.cod_pj_pf = sd_credencial.cod_pj_pf)
				WHERE
					sd_credencial.cod_pf = " . $intCodPF ."
				AND 1 = 1 ".$strClauseCard;
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	//Cria array associativo para o sql gerado acima
	$objRS = $objResult->fetch();
	
	/****
	//Sem tempo agora, inserir tratamento para n�o mostrar o �ltimo sobrenome caso passe de 26 caracteres e
	//caracter anterior for igual a espa�o. tamb�m nao mostrar caso antes desse ultimo sobrenome for um de ou dos
	 ****/
	
	//FORMATA��O EM DATAS PARA MANIPULA��O NO MODELO
	//� poss�vel assim dar mais �nfase ao peda�o ANO, por exemplo
	$dtValidadeTemp	 = explode("/",dDate(CFG_LANG,getValue($objRS,"dt_validade"),false));
	$dtValidadeMes   = $dtValidadeTemp[1];
	$dtValidadeDia   = $dtValidadeTemp[0];
	$dtValidadeAno   = $dtValidadeTemp[2];
	
	//Leitura do HTML do modelo de CARD padr�o
	$strStreamHTML 	 = file_get_contents($strCardPadrao);
	
	//Insere DIA na tag especificada
	$strStreamHTML	   = preg_replace("/\<TAGDTDIA_[A-Za-z0-9_]+\>/",$dtValidadeDia,$strStreamHTML);
	//Insere M�S na tag especificada 
	$strStreamHTML	   = preg_replace("/\<TAGDTMES_[A-Za-z0-9_]+\>/",$dtValidadeMes,$strStreamHTML);
	//Insere ANO na tag especificada
	$strStreamHTML	   = preg_replace("/\<TAGDTANO_[A-Za-z0-9_]+\>/",$dtValidadeAno,$strStreamHTML);
			
	//Troca a string '<TAG_' de Todo stream do 
	//modelo [c�d. html], por um c�digo php que
	//faz a busca dos dados no $objRS, em fetch
	preg_match_all("/\<TAG_[A-Za-z0-9_]+\>/",$strStreamHTML,$arrMatches);
	foreach($arrMatches[0] as $strMatch){
		$strParse 		= preg_replace("/\<TAG_|\>/","",$strMatch);
		$strStreamHTML	= str_replace($strMatch,getValue($objRS,strtolower($strParse)),$strStreamHTML);
	}
	
	//Substitui��o da TAGBARCODE_ por valor de c�digo de barras
	//NOTA: o campo informado ap�s o <TAGBARCORDE_ ser� transformado em cod barras
	preg_match_all("/\<TAGBARCODE_[A-Za-z0-9_]+\>/",$strStreamHTML,$arrMatchesCode);
	foreach($arrMatchesCode[0] as $strMatchCode){
		$strParse 		= preg_replace("/\<TAGBARCODE_|\>/","",$strMatchCode);
		//print_r($strParse);
		$strValue 		= barCode39(getValue($objRS,strtolower($strParse)),true,"CPF");
		$strStreamHTML	= str_replace($strMatchCode,$strValue,$strStreamHTML);
	}
	
	
	//Caso resize tenha sido enviado, faz o resize 
	//com base na altura e lagura enviados
	if((($intResizeWidth != "" || $intResizeWidth != 0))&&(($intResizeHeight != "" || $intResizeHeight != 0))){
		echo("<script type='text/javascript'>");
		echo("window.resizeTo(".$intResizeWidth.",".$intResizeHeight.");");
		echo("</script>");
	}
	
	//exibe CARD em tela
	echo($strStreamHTML);
			
	// NULL no objeto de manipula��o com banco
	$objConn = NULL;
?>