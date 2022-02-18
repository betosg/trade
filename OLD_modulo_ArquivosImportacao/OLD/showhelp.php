<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

	$CodCampo = request("var_chavereg");
	
	$objConn  = abreDBConn(CFG_DB);
	
	try {
		$strSQL = " SELECT obs FROM sys_descritor_campos_edicao WHERE cod_descr_campo = " . $CodCampo; 
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	$Valor = getValue($objRS,"obs");
	
	//faz o replace das três tags
	$Valor = str_ireplace("athinclfile"    ,"ATHINCLFILE"   ,$Valor);
	$Valor = str_ireplace("athinclframe"   ,"ATHINCLFRAME"  ,$Valor);
	$Valor = str_ireplace("athinclcontent" ,"ATHINCLCONTENT",$Valor);
	
	//TAG athInclFile: lê o conteúdo do arquivo e coloca num textarea
	$ArqNome = getTextBetweenTags($Valor, "<ATHINCLFILE>", "</ATHINCLFILE>", $PosIni, $PosFim);
	while ($ArqNome != "") {
		$ArqConteudo = "";
		if (file_exists($ArqNome)) {
			$ArqHandle = fopen($ArqNome, "rt");
			$ArqConteudo = fread($ArqHandle,100000);
			$ArqConteudo = "<br><textarea style='width:95%' rows='30' readonly='readonly' wrap='off'>" . $ArqConteudo . "</textarea><br>";
			fclose($ArqHandle);
		}
		$Valor = substr_replace($Valor, $ArqConteudo, $PosIni, $PosFim - $PosIni);
		$ArqNome = getTextBetweenTags($Valor, "<ATHINCLFILE>", "</ATHINCLFILE>", $PosIni, $PosFim);
	}
	//TAG athInclFrame: cria um iframe e coloca no source o link pro arquivo
	$ArqNome = getTextBetweenTags($Valor, "<ATHINCLFRAME>", "</ATHINCLFRAME>", $PosIni, $PosFim);
	while ($ArqNome != "") {
		$ArqConteudo = "<br><iframe src='" . $ArqNome . "' width='95%' height='300' frameborder='1' scrolling='auto'></iframe><br>";
		$Valor = substr_replace($Valor, $ArqConteudo, $PosIni, $PosFim - $PosIni);
		$ArqNome = getTextBetweenTags($Valor, "<ATHINCLFRAME>", "</ATHINCLFRAME>", $PosIni, $PosFim);
	}
	//TAG athInclContent: lê o conteúdo do arquivo e insere direto no corpo da mensagem
	$ArqNome = getTextBetweenTags($Valor, "<ATHINCLCONTENT>", "</ATHINCLCONTENT>", $PosIni, $PosFim);
	while ($ArqNome != "") {
		$ArqConteudo = "";
		if (file_exists($ArqNome)) {
			if ((strpos($ArqNome, ".jpg") === false) && (strpos($ArqNome, ".jpeg") === false) && (strpos($ArqNome, ".gif") === false) && (strpos($ArqNome, ".png") === false) && (strpos($ArqNome, ".bmp") === false)) {
				$ArqHandle = fopen($ArqNome, "rt");
				$ArqConteudo = "<br>" . fread($ArqHandle,100000) . "<br>";
				$ArqConteudo = str_ireplace("\n" ,"<br>" ,$ArqConteudo);
				fclose($ArqHandle);
			}
			else {
				$ArqConteudo = "<br><img src='" . $ArqNome . "'><br>";
			}
		}
		$Valor = substr_replace($Valor, $ArqConteudo, $PosIni, $PosFim - $PosIni);
		$ArqNome = getTextBetweenTags($Valor, "<ATHINCLCONTENT>", "</ATHINCLCONTENT>", $PosIni, $PosFim);
	}
	
	mensagem("Ajuda: ",$Valor,"","javascript:window.close();","standardinfo",1);
	$objConn = NULL;
?>