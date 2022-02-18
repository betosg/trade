<?php
ini_set("max_execution_time", 300); //300s = 5min
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");

// INI: INCLUDE requests ORDIÀRIOS -------------------------------------------------------------------------------------
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

/* Este Executor não provê saida visual, ele apenas gera o arquivo html do relatório em questão */
 function echoOnFile($ptFile,$str) {
	/*
	 Os HTML gerados vão para uma pasta do cliente, portanto os caminhos para css, javascript, imagens
	 e chamada para outras págiansphp, devem ser ajustados. O código abaixo ajusta estes caminhos. 
	 **Acho que mais adiante poderemos melhorar essa lógica
	*/ 
	$str = str_replace("../_css/"		,"../../_"  . CFG_SYSTEM_NAME . "/_css/"							 , $str);
	$str = str_replace("../_scripts/"	,"../../_"  . CFG_SYSTEM_NAME . "/_scripts/"						 , $str);
	$str = str_replace("../img/"		,"../../_"  . CFG_SYSTEM_NAME . "/img/"							 	 , $str);
	$str = str_replace("'execaslw.php"	,"'../../_" . CFG_SYSTEM_NAME . "/modulo_ASLWRelatorio/execaslw.php" , $str);
	$str = str_replace("'aslExport.php"	,"'../../_" . CFG_SYSTEM_NAME . "/modulo_ASLWRelatorio/aslExport.php", $str);
	fputs($ptFile,"$str");	// Grava 
 }

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

 try { 
    $localCSV = str_replace(".html",".csv",$local);
 	touch($localCSV); /* Acesso ao arquivo, e se ele nao existir, ele é criado. */ 
	$fpCSV=fopen($localCSV,"w");	// Abre o arquivo pra escrita
 }
 catch(PDOException $e){
	mensagem("Erro de arquivo (CSV)", "Problema na geração do arquivo CSV deste relatório", "Arquivo: " . $arqNome,  "javascript:window.close();","standarderro",1);
    die();
 }

 try { 
    $localXML = str_replace(".html",".xml",$local);
 	touch($localXML); /* Acesso ao arquivo, e se ele nao existir, ele é criado. */ 
	$fpXML=fopen($localXML,"w");	// Abre o arquivo pra escrita
 }
 catch(PDOException $e){
	mensagem("Erro de arquivo (XML)", "Problema na geração do arquivo XML deste relatório", "Arquivo: " . $arqNome,  "javascript:window.close();","standarderro",1);
    die();
 }


/* INI: Pré-cabeçalho do Arquivo ---------------------------------------------------------------------------------------------- */
echoOnFile($fp,"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>");
echoOnFile($fp,"<html>");
echoOnFile($fp,"<head>");
echoOnFile($fp,"<title>" . CFG_SYSTEM_TITLE . " - " . $strRelTit ."</title>");
echoOnFile($fp,"<link rel='stylesheet' href='../_css/" . CFG_SYSTEM_NAME .".css' type='text/css'>\n
				<link rel='stylesheet' type='text/css' href='../_css/tablesort.css'>\n
				<script type='text/javascript' src='../_scripts/tablesort.js'></script>\n
				<script type=\"text/javascript\" language=\"javascript\">\n
					function swapDisplay(prImg, prObj) {
						if (document.getElementById(prObj).style.display == \"none\") {
						  prImg.src = '../img/icon_tree_minus.gif';
						  document.getElementById(prObj).style.display = \"block\";
						} else {
						  prImg.src = '../img/icon_tree_plus.gif';
						  document.getElementById(prObj).style.display = \"none\";
						}
					}\n
					function imprimir() {
					  var objDiv;

					  //objDiv = document.getElementById(\"divHeader\");  
					  objDiv = document.getElementById(\"divIcons\");   
					   
					  objDiv.style.display = \"none\";
					  window.print();
  					  objDiv.style.display = \"block\";
					}\n
					function exportarAdobe(){
					  var objDiv;

					  //objDiv = document.getElementById(\"divHeader\");  
					  objDiv = document.getElementById(\"divIcons\");   
					   
					  objDiv.style.display = \"none\";
					  window.print();
  					  objDiv.style.display = \"block\";
					}\n
					function exportDocument(prType){
						var objBODY, objFORM, objCONT, objACAO, objLINK, strACAO;
						objACAO = document.getElementById(\"var_acao\");
						objCONT = document.getElementById(\"var_content\");
						objLINK = document.getElementById(\"var_link\");
						objFORM = document.getElementById(\"formexport\");
						strACAO = prType;
				 		objBODY = window.document.getElementById(\"mainPage\");
						objCONT.value = objBODY.innerHTML;
						objACAO.value = strACAO;
						objLINK.value = \"" . $strDIR . "\"
						objFORM.submit();	
					}\n
				</script>");

echoOnFile($fp,"<style>\n
					table.pagina { border:0px #FFFFFF solid; width:810px; background-color:#FFF; }
					div.box 	 { border:1px #FFFFFF solid; margin-bottom:8px; float:left; margin-left:8px; width:240px; height:87px; }
					div.linha    { width:240px; height:18px; overflow:hidden; }
					div.conteudo { width:240px; height:87px; padding:0px 0px 0px 0px; text-align:left; vertical-align:middle; background:#FFF;}
				</style>\n");

echoOnFile($fp,"<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>\n");
echoOnFile($fp,"</head>");
echoOnFile($fp,"<body style='margin:10px;' bgcolor='#FFFFFF'>");
echoOnFile($fp,"\n<!-- INI: Título, Descrição e controles de exportação e display/hidden descrição ******************* -->\n");
echoOnFile($fp,"<div id='divHeader' style='width:100%; margin:0px 0px 10px 0px; border:1px solid #D9D9D9;'>");
echoOnFile($fp,"<div class='padrao_gde' style='width:100%; height:23px;'>");
echoOnFile($fp,"<div align='left'  id='divTitulo' style='float:left; text-align:left;  padding:5px 0px 0px 5px; white-space:nowrap;'><strong><img src='../img/icon_tree_plus.gif' border='0' onclick=\"javascript:swapDisplay(this,'divDesc');\">&nbsp;" . $intRelCod . " - " . $strRelTit . "</strong></div>");
echoOnFile($fp,"<div align='right' id='divIcons'  style='float:right; text-align:right; padding:4px 5px 5px 0px;'>");
echoOnFile($fp,"  <form name='formexport' id='formexport' action='aslExport.php' target='_blank' method='post'>");
echoOnFile($fp,"    <input type='hidden' name='var_content' id='var_content' value='' />");
echoOnFile($fp,"    <input type='hidden' name='var_acao'    id='var_acao'    value='' />");
echoOnFile($fp,"    <input type='hidden' name='var_link'    id='var_link'    value='' />");
echoOnFile($fp,"  </form>");
echoOnFile($fp,"  <img src='../img/iconfooter_print.gif' border='0' onClick=\"imprimir();\"             style='cursor:pointer;'  title='" . getTText("imprimir",C_UCWORDS) . "'>");
echoOnFile($fp,"  <img src='../img/iconfooter_word.gif'  border='0' onClick=\"exportDocument('.doc');\" style='cursor:pointer;'  title='" . getTText("exportar_word",C_UCWORDS) . "'>");
echoOnFile($fp,"  <img src='../img/iconfooter_excel.gif' border='0' onClick=\"exportDocument('.xls');\" style='cursor:pointer;'  title='" . getTText("exportar_excel",C_UCWORDS) ."'>");
echoOnFile($fp,"</div>");
echoOnFile($fp,"</div>");
echoOnFile($fp,"<div id='divDesc' style='width:100%; display:none;'>");
echoOnFile($fp,"<div style='width:100%; text-align:left;padding:5px 0px 5px 5px;'>" . $strRelDesc); 
echoOnFile($fp,"<br><br>Parâmetros: ".$strRelInpts); 
echoOnFile($fp,"</div>"); 
echoOnFile($fp,"</div>"); 
echoOnFile($fp,"</div>\n"); 

echoOnFile($fp,"<div id='mainPage'>\n"); /* INI: DivMainPage - usado na Exportação... */
echoOnFile($fp,ShowCR("CABECALHO",$strRelHead)); // CABEÇALHO do relatório


echoOnFile($fpXML,"<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><?mso-application progid=\"Excel.Sheet\"?>\n");
echoOnFile($fpXML,"<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"\n");
echoOnFile($fpXML," xmlns:o=\"urn:schemas-microsoft-com:office:office\"\n");
echoOnFile($fpXML," xmlns:x=\"urn:schemas-microsoft-com:office:excel\"\n");
echoOnFile($fpXML," xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"\n");
echoOnFile($fpXML," xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n");
echoOnFile($fpXML,"<DocumentProperties xmlns=\"urn:schemas-microsoft-com:office:office\">\n");
echoOnFile($fpXML,"	<Author>KernelPS - ".CFG_SYSTEM_TITLE."</Author>\n");
echoOnFile($fpXML,"	<Created>".date("Y-m-dTH:i:sZ")."</Created>\n"); //echoOnFile($fpXML,"<Created>2012-06-20T14:06:26Z</Created>");
echoOnFile($fpXML,"	<Company>GRUPO PROEVENTO TECHNOLOGIES</Company>\n");
echoOnFile($fpXML,"	<Version>KernelPS</Version>\n");
echoOnFile($fpXML,"</DocumentProperties>\n");
echoOnFile($fpXML,"<ExcelWorkbook xmlns=\"urn:schemas-microsoft-com:office:excel\">\n");
echoOnFile($fpXML,"	<ProtectStructure>False</ProtectStructure>\n");
echoOnFile($fpXML,"	<ProtectWindows>False</ProtectWindows>\n");
echoOnFile($fpXML,"</ExcelWorkbook>\n");
echoOnFile($fpXML,"<Worksheet ss:Name=\"ASLW_REPORT\">\n");
echoOnFile($fpXML,"	<Table x:FullColumns=\"1\"  x:FullRows=\"1\">\n");
echoOnFile($fpXML,"		<Column ss:AutoFitWidth=\"0\"/>\n");

/* FIM: Pré-cabeçalho do Arquivo ---------------------------------------------------------------------------------------------- */


//echo($strSQL);

/* INI: Execução do SQL ---------------------------------------------------------------------------------------------- */
if($strSQL != "") {
	try{
		$objResult = $objConn->query($strSQL); // Rodando a consulta
		if($objResult->rowCount() == 0 || $objResult == ""){
			echoOnFile($fp,mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc", "", "","aviso",1));
			die();
		}
	}
	catch(PDOException $e){
		echoOnFile($fp,mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1));
		die();
	}
} else {
	echoOnFile($fp,mensagem("info_nova_pesquisa_titulo","info_nova_pesquisa_desc", "", "","info",1));
	die();
}

/* FIM: Execução do SQL ---------------------------------------------------------------------------------------------- */



/* INI: GRADE/TABLE do relatório em si ------------------------------------------------------------------------------- */
// Criando a tabela para os dados (uso da classe TableSort)
echoOnFile($fp,"<table align='center' cellpadding='0' cellspacing='1'  style='width:100%' class='tablesort'>");
$intI = 0;
$intJ = 1;
$objRS = $objResult->fetch();

// Monta o cabeçalho(thead) da TableSort
echoOnFile($fp,"<thead><tr>");
echoOnFile($fpXML,"<Row>\n");
foreach($objRS as $strCampo => $strDado){
	if($intI % 2 == 0) { 
	   $intJ++; 
	   echoOnFile($fp,"<th class='sortable'>".$strCampo."</th>"); 
	   echoOnFile($fpXML,"<Cell><Data ss:Type=\"String\">".removeAcento($strCampo)."</Data></Cell>\n");
	   if ($intI>1) { echoOnFile($fpCSV,$strCSVSep . $strCampo); } 
	   else { echoOnFile($fpCSV,$strCampo); }
	 }
	$intI++;	
}
echoOnFile($fp,"</tr></thead>");
echoOnFile($fpCSV,"\n"); 
echoOnFile($fpXML,"</Row>\n");



// Varredura da consulta com tratamento dos modificadores e echo das linhas do relatório
$strCOLOR = "#FFFFFF";
$intActuallyCount = 1;
$boolFooter = false;
echoOnFile($fp,"<tbody>\n");
do{
	$strCOLOR = ($strCOLOR == "#FFFFFF") ? "#F5FAFA" : "#FFFFFF";
	echoOnFile($fp,"<tr bgcolor='" . $strCOLOR . "'>");
	echoOnFile($fpXML,"<Row>\n");
	$intI = 0; 
	$intIdxAction = 0; 
	$intRowCount = $objResult->rowCount();
	foreach($objRS as $strCampo => $strDado) {	
		if($intI % 2 == 0){
			$intIdxAux = $intI/2;
			$boolFormatDouble = false;
			$strOperator = "";
			$strDadoCSV = "";
			$strDadoXML = "";
			
			if(isset($arrModificadores[2][$intIdxAction]) && filtraAlias($arrModificadores[2][$intIdxAction]) == $strCampo){
				$strOperator = $arrModificadores[1][$intIdxAction];
				if($strOperator == "+")       { (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = $strDado : $arrValues[$intIdxAux] += $strDado; $boolFooter = true; $boolFormatDouble = true;
				} elseif($strOperator == "-") { (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = $strDado : $arrValues[$intIdxAux] -= $strDado; $boolFooter = true; $boolFormatDouble = true;
				} elseif($strOperator == "$") { $boolFooter = false; $boolFormatDouble = true;
				} elseif($strOperator == "*") { (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = $strDado : $arrValues[$intIdxAux] *= $strDado; $boolFooter = true; $boolFormatDouble = true;
				} elseif($strOperator == "/") { (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = $strDado : $arrValues[$intIdxAux] /= $strDado; $boolFooter = true; $boolFormatDouble = true;
				} elseif($strOperator == "#") { (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = 1 : $arrValues[$intIdxAux]++; $boolFooter = true;
				} elseif($strOperator == "@") { (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = $strDado : ($intActuallyCount != $intRowCount) ? $arrValues[$intIdxAux] += $strDado : $arrValues[$intIdxAux] /= $intRowCount ; $boolFooter = true; $boolFormatDouble = true;
				} elseif($strOperator == "!") { (!isset($arrValues[$intIdxAux]) || $arrValues[$intIdxAux] != $strDado) ? $arrValues[$intIdxAux] = $strDado : $strDado = ""; 
				} elseif(preg_match("/\>([0-9])+/i",$strOperator) !== false) { 
				   //$strDadoCSV = $strDado;
				   //$strDadoXML = $strDado;
				   $strDado	   = "<a onClick=\"window.open('execaslw.php?var_chavereg=" 
				             	 . str_replace(">","",$strOperator) . "&var_valor_aux=" 
							 	 . $strDado . "','','width=700,height=600,scrollbars=yes,resizable=yes,menubar=no');\" style=\"cursor:pointer;\">" 
							 	 . $strDado . "</a>";	
								 							 
				}
				$intIdxAction++;				
			} else {
				//$intIdxAction++;				
				$arrValues[$intIdxAux] = false;
			}
			if(is_date($strDado)) { $strDado = dDate(CFG_LANG,$strDado,false);   }
			
		    $strDado = ($boolFormatDouble == false)?$strDado:number_format((double) $strDado, 2, ",", ".");

			echoOnFile($fp,"<td style=\"mso-number-format:'\@'\">");
			echoOnFile($fp,$strDado);
			echoOnFile($fp,"</td>");
			
			if ($strDadoCSV == "") $strDadoCSV = $strDado;
		    $strDadoCSV = str_replace(";"," ",$strDadoCSV);
			$strDadoCSV = getNormalStringASLCsv($strDadoCSV);
			if ($intI>1) { echoOnFile($fpCSV,$strCSVSep . $strDadoCSV); } 
			else { echoOnFile($fpCSV, $strDadoCSV); }
			
			if ($strDadoXML == "") $strDadoXML = $strDado;
		    //Optamos por normalizar a string para não dar erro nas saidas do relatório - by Vini 18.03.2013
			$strDadoXML = getNormalStringASLXml($strDadoXML);
			$strDadoXML = str_replace("<","",$strDadoXML);
			$strDadoXML = str_replace(">","",$strDadoXML);
			//$strDadoXML = html_entity_decode($strDadoXML,"ENT_XHTML","ISO-8859-1");
			echoOnFile($fpXML,"  <Cell><Data ss:Type=\"String\">".$strDadoXML."</Data></Cell>\n");
		}
		$intI++;
	}
	echoOnFile($fp,"\n</tr>");
	echoOnFile($fpCSV,"\n");
	echoOnFile($fpXML,"</Row>\n");
	
	$intActuallyCount++;
} while($objRS = $objResult->fetch());
echoOnFile($fp,"</tbody>");


// Monta o rodapé(thead) da TableSort
echoOnFile($fp,"<tfoot><tr><td colspan='" . $intI . "'><hr size='2' color='#BFBFBF'></td></tr>");
if($boolFooter){
	echoOnFile($fp,"<tr>");
	$strCOLOR = ($strCOLOR == "#FFFFFF") ? "#F5FAFA" : "#FFFFFF";
	foreach($arrValues as $mixValue){ echoOnFile($fp,"<td style='padding-left:15px;' bgcolor='".$strCOLOR."'>" . (($mixValue !== false && is_numeric($mixValue)) ? "<b>" . number_format((double) $mixValue,2,",",".") . "</b>" : "") . "</td>"); }
		echoOnFile($fp,"</tr>");
	}
echoOnFile($fp,"</tfoot>");
echoOnFile($fp,"</table>\n"); //Fechando a tabela para os dados (uso da classe TableSort)

/* FIM: GRADE/TABLE do relatório em si ------------------------------------------------------------------------------- */

echoOnFile($fp,ShowCR("RODAPE",$strRelFoot)); // RODAPÉ do relatório
echoOnFile($fp,"</div>\n"); /* FIM: DivMainPage - usado na Exportação... */
echoOnFile($fp,"</body>");
echoOnFile($fp,"</html>");

echoOnFile($fpXML,"</Table></Worksheet></Workbook>"); //Footer do arquivo XML


include_once("_include_aslRunHtmlLog.php");   // Grava Log de execução do Relatório (com o nome do HTML gerado)
include_once("_include_aslRunHtmlClear.php"); // Apaga arquivos html de relatórios antigos

$objResult->closeCursor();
$objConn = NULL;

fclose($fp);
fclose($fpCSV);
fclose($fpXML);

mensagem("info_relgerado_titulo"
        ,"info_relgerado_desc"
		,"<img src='../img/icon_html_view_big.gif' onClick=\"window.open('../../".$dirCli."/asl_html/".$arqNome."','','width=640,height=480,top=30,left=30,scrollbars=1,resizable=yes,status=yes,directories=no,location=0,menubar=no,toolbar=no,titlebar=no');\" style='cursor:pointer; ' border='0' alt='view' title='view'>"
		 ."&nbsp;&nbsp;&nbsp;&nbsp;"
		 ."<a href='aslDownload.php?var_file=../../".$dirCli."/asl_html/".$arqNome."'                             target='_blank' alt='download HTML' title='download HTML'><img src='../img/icon_html_download_big.gif' border='0'></a>&nbsp;"
		 ."<a href='aslDownload.php?var_file=../../".$dirCli."/asl_html/".str_replace(".html",".csv",$arqNome)."' target='_blank' alt='download CSV'  title='download CSV'><img src='../img/icon_csv_download_big.gif' border='0'></a>&nbsp;"
		 ."<a href='aslDownload.php?var_file=../../".$dirCli."/asl_html/".str_replace(".html",".xml",$arqNome)."' target='_blank' alt='download XML'  title='download XML'><img src='../img/icon_xml_download_big.gif' border='0'></a>&nbsp;"
		 ."&nbsp;(". str_replace(".html","",$arqNome). ")"
		,"javascript:history.back();"
		,"info"
		,1);

?>