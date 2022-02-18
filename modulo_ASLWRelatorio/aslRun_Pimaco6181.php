<?php
ini_set("max_execution_time", 300); //300s = 5min
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");

// INI: INCLUDE requests ORDINÁRIOS -------------------------------------------------------------------------------------
/*
 Por definição esses são os parâmetros que a página anterior de preparação (execaslw.php) manda para os executores.
 Cada executor pode utilizar os parâmetros que achar necessário, mas por definição queremos que todos façam os
 requests de todos os parâmetros enviados, como no caso abaixo:
 Variáveis e Carga:
	 -----------------------------------------------------------------------------
	 variável          | "alimentação"
	 -----------------------------------------------------------------------------
	 $data_ini         | DataHora início do relatório
	 $intRelCod		   | Código do relatórioRodapé do relatório
	 $strRelASL		   | ASL - Conulta com parâmetros processados, mas TAGs e Modificadores 
	 $strRelSQL		   | SQL - Consulta no formato SQL (com parâmetros processados e "limpa" de TAGs e Modificadores)
	 $strRelTit		   | Nome/Título do relatório
	 $strRelDesc	   | Descrição do relatório	
	 $strRelHead	   | Cabeçalho do relatório
	 $strRelFoot	   | Rodapé do relatório		
	 $strRelInpts	   | Usado apenas para o log
	 $strDBCampoRet	   | O nome do campo na consulta que deve ser retornado
	 $strDBCampoRet    | **Usado no repasse entre ralatórios - sem o nome da tabela do campo que será retornado
	 -----------------------------------------------------------------------------  */
include_once("_include_aslRunRequest.php");

// FIM: INCLUDE requests ORDIÀRIOS -------------------------------------------------------------------------------------


// INI: INCLUDE funcionalideds BÁSICAS ---------------------------------------------------------------------------------
/* Funções
	 filtraAlias($prValue)
	 ShowDebugConsuta($prA,$prB)
	 ShowCR("CABECALHO/RODAPE",str)
  Ações:
  	 SEGURANÇA: Faz verificação se existe usuário logado no sistema
  Variáveis e Carga:
	 -----------------------------------------------------------------------------
	 variável          | "alimentação"
	 -----------------------------------------------------------------------------
	 $strDIR           | Pega o diretório corrente (usado na exportação) 
	 $arrModificadores | Array contendo os modificadores ([! ], [$ ], ...) do ASL
	 $strSQL           | SQL PURO, ou seja, SEM os MODIFICADORES, TAGS, etc...
	 -----------------------------------------------------------------------------  */
include_once("_include_aslRunBase.php");
// FIM: INCLUDE funcionalideds BÁSICAS ---------------------------------------------------------------------------------

$objConn = abreDBConn(CFG_DB);

$dirCli  = getsession(CFG_SYSTEM_NAME . "_dir_cliente");
$arqNome = $intRelCod . "_" . date("Ymd-His") . ".html";
$local	  = realpath("../../" . $dirCli . "/asl_html/") . "/" . $arqNome;
 
 try { 
 	touch($local); /* Acesso ao arquivo, e se ele nao existir, ele é criado. */ 
	$fp=fopen($local,"w");	// Abre o arquivo pra escrita
 }

 catch(PDOException $e){
	mensagem("Erro de arquivo", "Problema na geração do arquivo HTML deste relatório", "Arquivo: " . $arqNome,  "javascript:window.close();","standarderro",1);
    die();
 }

BeginHtmlBuffer(); //Inicia a captura em buffer (ob_start())
echo("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n");
echo("<html>\n");
echo("<head>\n");
//echo("<title>" . CFG_SYSTEM_TITLE . " - " . $strRelTit ."</title>");
//-- INI: INCLUDE JScript e CSS ***************************************** --
//-- Estilos....: "../_css/[SISTEMA].css" e "../_css/tablesort.css"
/*	 JavaScript..: "../_scripts/tablesort.js"
				  * Funções locais de exportação/impressão e adicionais:					  	
					- swapDisplay, imprimir, exportarAdobe e exportDocument	!--*/
include_once("_include_aslRunScript.php"); 
//-- FIM: INCLUDE JScript e CSS ***************************************** -->
//Conversão de mm para pixels
$numPIXEL_MM_Y = 3.78;
$numPIXEL_MM_X = 3.78;
/*configuraçõs etiqueta C6181 2 Colunas X 10 linhas = 20 etiquetas por folha. Altura etiqueta = 25,4mm - Largura etiqueta = 101,6 mm.*/
$numCOLUNA = 2;
$numLINHA  = 10;
$numMARGEM_SUPERIOR = round( 2    * $numPIXEL_MM_X,0); //Convertido para pixels
$numMARGEM_ESQUERDA = round(  4    * $numPIXEL_MM_X,0);
$numLARGURA_COLUNA  = round((101.6  * $numPIXEL_MM_X) ,0);
$numALTURA_COLUNA   = round(( 25.4  * $numPIXEL_MM_Y),0);				
$numESPACO_ENTRE_ETIQUETAS_Y = round((5 * $numPIXEL_MM_X),0);
$tamTABLE = round($numLARGURA_COLUNA * $numCOLUNA,0) + $numESPACO_ENTRE_ETIQUETAS_Y;

echo("<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>\n");
echo("</head>\n");
echo("<body leftmargin='".$numMARGEM_ESQUERDA."' topmargin='0' marginwidth='0' marginheight='0' bgcolor='#FFFFFF'>\n");
echo("<div id='mainPage'>");/*INI: Div mainPage - usado na Exportação...*/
//-- INI: Título, Descrição e controles de exportação e display/hidden descrição 
/*-- Controle de expand/collapse da descrição do relatório, formulários  de exprtação,
	 icones de exportação e teste/echo do DEBUG --*/
//include_once("_include_aslRunControles.php"); 
//-- FIM: Título, Descrição e controles de exportação e display/hidden descrição --
//echo $strSQL;
if($strSQL != "") {
	try{
		$objResult = $objConn->query($strSQL); // Rodando a consulta
		if($objResult->rowCount() == 0 || $objResult == ""){
			echo(mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc", "", "","aviso",1));
			die();
		}
	}
	catch(PDOException $e){
		echo(mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1));
		die();
	}
} else {
	echo(mensagem("info_nova_pesquisa_titulo","info_nova_pesquisa_desc", "", "","info",1));
	die();
}
/* FIM: Execução do SQL ---------------------------------------------------------------------------------------------- */

$intContLinha    = 1;
$intContPagina   = 1;
$intContEtiqueta = 1;
$boolFooter      = false;
$var_cont        = 0;

foreach($objResult as $objRS) {
    //Tabela Margem superior
	if (($intContLinha == 1)&&($intContEtiqueta == 1)){
		echo("<table width='".$tamTABLE."' border='0' cellspacing='0' cellpadding='0'>\n");
		echo("<tr>\n");
		echo("<td colspan='".$numCOLUNA."' height='".$numMARGEM_SUPERIOR."' valign='top'><img src='../img/transparent.gif' width='1' height='".$numMARGEM_SUPERIOR."' style='border:0px solid #000000'></td>\n");
		echo("</tr>\n");
		echo("</table>\n");
	}
	/*finalizar pagina
	if ($intContPagina = 1){
		echo("<table width='".$tamTABLE."' border='0' cellspacing='0' cellpadding='0'>");
      	echo("<tr>");
        echo("<td align='center'><font color='#999999'>Página ".$intContPagina." de TotalPages</font></td>");
      	echo("</tr>");
	  	echo("</table>");
	}		
	*/	
	
    //Quebra 1º página
	/*
	if ($intContPagina == 1){	
		//echo("<br style='page-break-before:always;'>");
		$intContPagina++; 
		//echo("<table width='".$tamTABLE." border='0' cellspacing='0' cellpadding='0'>");
		//echo("<tr>");
		//echo("<td colspan='".$numCOLUNA."' height='".$numMARGEM_SUPERIOR."' valign='top'><img src='../img/transparent.gif' width='1' height='".$numMARGEM_SUPERIOR."'></td>");
		//echo("</tr>");
		//echo("</table>");	
	}  
	*/
	/*Campos da etiqueta*/			
	$razaoSOCIAL  = getValue($objRS,'campo_razao'       );
	$destinatario = getValue($objRS,'campo_destinatario');
	$endereco     = getValue($objRS,'campo_endereco'    );
	$bairro       = getValue($objRS,'campo_bairro'      );					
	$cep          = getValue($objRS,'campo_cep'         );
	$cidade       = getValue($objRS,'campo_cidade'      );
	if (strtoupper(getValue($objRS,'campo_pais')) != 'BRASIL'){
		$pais = strtoupper(getValue($objRS,'campo_pais'));  
		$estado = ""; 
	} else {
		$estado = getValue($objRS,'campo_estado'); 
		$pais="";
	} 	
		
	//Tabela com etiquetas - Equivale a página	
	if ($intContEtiqueta == 1){	
		if ($intContLinha == 1){
			echo("<table id='table01' width='".$tamTABLE."' border='0' cellspacing='0' cellpadding='0' style='overflow:hidden;'>\n");
		}		
		echo("<tr>\n");		
	}	
	/*Célula - equivale a etiqueta*/
	if ($intContEtiqueta == 1){	
    	echo("<td width='".$numLARGURA_COLUNA."' height='".$numALTURA_COLUNA."' style='border:0px solid #000000;margin-top:4px;' nowrap='nowrap'>\n");	
	}else{
   		echo("<td width='".$numLARGURA_COLUNA."' height='".$numALTURA_COLUNA."' style='border:0px solid #000000;margin-top:4px;margin-left:".$numESPACO_ENTRE_ETIQUETAS_Y."px;' nowrap='nowrap'>\n");	
	}	
	echo("<b>".$razaoSOCIAL."</b><br>\n");
	echo("<b>".$destinatario."</b><br>\n");
	echo($endereco." - ".$bairro."<br>\n");	
	if ($pais!=""){
		echo("<b>".$cep."</b>&nbsp;&nbsp;".$cidade."&nbsp;&nbsp;&nbsp;-&nbsp;".$pais."<br>\n");
	}else{
		echo("<b>".$cep."</b>&nbsp;&nbsp;".$cidade."&nbsp;/".$estado."<br>\n");		
	}
	echo("</td>\n");	
	
	/*Imprime 2 etiquetas por linha*/
	$intContEtiqueta++;
	/*Mudança de linha*/
	if($intContEtiqueta>$numCOLUNA){
		echo("</tr>\n");
		$intContEtiqueta = 1;
		$intContLinha++;			
	}
	/*Mudança de página*/
	if ($intContLinha > $numLINHA  ){
		echo("</table>\n");		
		$intContLinha = 1;
		echo("<div style='page-break-before:always;'></div>\n");
		$intContPagina++; 
	}
		
} // foreach

$outBuf .= EndHtmlBuffer(); //Descarrega e finaliza o buffering
echo("</div>\n");/*FIM: Div mainPage - usado na Exportação... */
echo("</body>\n");
echo("</html>\n");

include_once("_include_aslRunHtmlSave.php");  // Grava o Arquivo HTML 			
include_once("_include_aslRunHtmlLog.php");   // Grava Log de execução do Relatório (com o nome do HTML gerado)
include_once("_include_aslRunHtmlClear.php"); // Apaga arquivos html de relatórios antigos

$objResult->closeCursor();
$objConn = NULL;

?>