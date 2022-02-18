<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	
	// REQUESTS
	$strBODY   = request("var_content");
	$strACAO   = request("var_acao");
	$strLINK   = request("var_link");
	$strSesPfx = strtoupper(str_replace("modulo_","",basename($strLINK)));
	
	// CABEÇALHOS FORCE-DOWNLOAD
	header("Content-type: application/force-download"); 
	header("Content-Disposition: attachment; filename=Modulo_".$strSesPfx."_".time().$strACAO);
  	
	// REPLACE DE TODAS AS TAGS IMG DO STREAM
	$strBODY = preg_replace('/<img[^>]+>/i','',$strBODY); 
	
	// DEBUGS
	// $strBODY = preg_replace("/\<IMG[A-Za-z0-9_-=\" ;.\']+\/ >/i","",$strBODY);
	// $strBODY = preg_replace("#\<IMG[A-Za-z0-9_-=\" ;.\']+/ > #","",$strBODY);
	// $strBODY = str_replace('<IMG title=editar border=0 src="../img/icon_write.gif">',"",$strBODY);
	
	// COMO STREAM SÓ PEGA O CONTEUDO DO BODY PARA DENTRO
	// PARA EXCLUIR LINK COM ESTILOS EXTERNOS E SCRIPTS,
	// ENTÃO TEMOS DE CONSTRUIR A ESTRUTURA HTML DA VOLTA
	echo("<html>\n\t");
	echo("<head>\n\t");
	echo("<title>".strtoupper(CFG_SYSTEM_NAME)."</title>\n");
	echo("</head>\n");
	echo("<body>");
	echo($strBODY);
	echo("</body>");
	echo("</html>");
?>