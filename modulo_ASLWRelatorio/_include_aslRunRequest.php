<?php
$outBuf = "";
$data_ini = strtotime(date("Y-m-d H:i:s")); // Calculando em segundos e retorna no formato 00:54
// Por definição esses são os parâmetros que a página anterior de preparação (execaslw.php) manda para os executores.
// Cada executor pode utilizar os parâmetros que achar necessário, mas por definição queremos que todos façam os
// requests de todos os parâmetros enviados, como no caso abaixo:
$intRelCod		= request("var_cod");	 	 // Código do relatórioRodapé do relatório
$strRelASL		= request("var_asl"); 		 // ASL - Conulta com parâmetros processados, mas TAGs e Modificadores 
$strRelSQL		= request("var_sql");        // SQL - Consulta no formato SQL (com parâmetros processados e "limpa" de TAGs e Modificadores)
$strRelTit		= request("var_tit");        // Nome/Título do relatório
$strRelDesc		= request("var_desc");		 // Descrição do relatório	
$strRelHead		= request("var_header");	 // Cabeçalho do relatório
$strRelFoot		= request("var_footer");	 // Rodapé do relatório		
$strRelInpts	= request("var_inputs");	 // Usado apenas para o log
$strDBCampoRet	= request("var_dbcamporet"); // O nome do campo na consulta que deve ser retornado
$strDBCampoRet  = preg_replace("/[[:alnum:]_]+\./i","",$strDBCampoRet); //Para tirar o nome da tabela do campo que será retornado
$strCSVSep      = ";"; // Indica o separador para as colunas do arquivo CSV gerado - Pode ser passado por parâmetro futuramente
?>