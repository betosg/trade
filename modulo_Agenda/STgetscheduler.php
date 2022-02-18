<?php
	header("content-type: text/xml");
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// abertura de conexão com o BD
	$objConn = abreDBConn(CFG_DB);
	
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd()))); // carrega o nome do modulo 'corrente'
	$strSQL = getsession($strSesPfx . "_select"); // com o nome do modulo, busca o select da seção
	
	// efetua busca dos eventos e das agendas
	// com base nos parametros da sessao [SQL]
	// - do modulo corrente
	try{
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
    /* INI: DEBUG - Gerando o XML em ARQUIVO para verificação --------------------
	function echoOnFile($ptFile,$str) { fputs($ptFile,"$str");	}
	$dirCli  = getsession(CFG_SYSTEM_NAME . "_dir_cliente");
	$arqNome = "debug_" . date("Ymd-His") . ".xml";
	$local	  = realpath("../../" . $dirCli . "/asl_html/") . "/" . $arqNome;
	try { 
	 	touch($local); // Acesso ao arquivo, e se ele nao existir, ele é criado.  
		$fp=fopen($local,"w");	// Abre o arquivo pra escrita
	}
	catch(PDOException $e){
		mensagem("Erro de arquivo", "Problema na geração do arquivo HTML deste relatório", "Arquivo: " . $arqNome,  "javascript:window.close();","standarderro",1);
	    die();
	} 
	echoOnFile($fp,"<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>");  
  	echoOnFile($fp,"<data>");
	foreach($objResult as $objRS){
		echoOnFile($fp,"<event id=\"".returnCodigo(getValue($objRS,"cod_agenda"))."\">
			  	<start_date>".returnCodigo(getValue($objRS,"prev_dtt_ini"))."</start_date>
				<end_date>".returnCodigo(substr(getValue($objRS,"prev_dtt_fim"),0,19))."</end_date>
				<text><![CDATA[".returnCodigo(getValue($objRS,"titulo"))."]]></text>
				<ag_categoria><![CDATA[".returnCodigo(getValue($objRS,"categoria"))."]]></ag_categoria>
				<ag_prioridade><![CDATA[".returnCodigo(getValue($objRS,"prioridade"))."]]></ag_prioridade>
				<details><![CDATA[".returnCodigo(getValue($objRS,"descricao"))."]]></details>
			  </event>");
	}
	echoOnFile($fp,"</data>");
	fclose($fp); 
    -- FIM: DEBUG - Gerando o XML em ARQUIVO para verificação -------------------- */
	

    // PARTE REAL (comente quando for acionar a parte de cima de DEBUG  ----------------------------------
  	echo "<?xml version='1.0' encoding='utf-8'?>\n";
	echo "<data>\n";
	foreach($objResult as $objRS){
		//Obs.: troca da "returnCodigo" pela "getNormalString", na tentativa de diminuir as possibilidades de caracteres 
		//estranhos prejudidanco a abertuda do XML que serve para a visualização da grade da agenda, observando que isso 
		//deve afetar somente os texots que são exibidos na grade mas o dado em si deve permanecer intacto.
		
		//Não pode usar "getNormalString" nos campos de DATA HORA senão troca o formato 
		//esperado "yyyy-mm-dd hh:mm:ss", o que invalida o dado e não faz aparecer no calendário
		
		echo "<event id='".getValue($objRS,"cod_agenda")."'>\n";
		echo "  <start_date>".getValue($objRS,"prev_dtt_ini")."</start_date>\n";
		echo "  <end_date>".getValue($objRS,"prev_dtt_fim")."</end_date>\n";
		echo "  <text><![CDATA[".getNormalString(getValue($objRS,"titulo"))."]]></text>\n";
		echo "  <ag_categoria><![CDATA[".getNormalString(getValue($objRS,"categoria"))."]]></ag_categoria>\n";
		echo "  <ag_prioridade><![CDATA[".getNormalString(getValue($objRS,"prioridade"))."]]></ag_prioridade>\n";
		echo "  <details><![CDATA[".getNormalString(getValue($objRS,"descricao"))."]]></details>\n";
		echo "</event>\n";
	}
	echo "</data>\n";
	
	$objResult->closeCursor();
	$objConn = NULL;
?>