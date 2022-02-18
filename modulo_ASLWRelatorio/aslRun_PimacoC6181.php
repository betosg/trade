<?php
/* 
 CONFIGURAÇÃO DA PÁGINA PARA IMPRESSÃO DE ETIQUETAS 
  Margem Superior =  10   Milimitros 
  Margem Inferior =  5,1  Milimitros 
  Margem Esquerda =  5,1  Milimitros 
  Margem Direita  =  5,55 Milimitros 
*/

ini_set("max_execution_time", 280);
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
	 $strDIR           | Pega o diretporio corrente (usado na exportação) 
	 $arrModificadores | Array contendo os modificadores ([! ], [$ ], ...) do ASL
	 $strSQL           | SQL PURO, ou seja, SEM os MODIFICADORES, TAGS, etc...
	 -----------------------------------------------------------------------------  */
include_once("_include_aslRunBase.php");
// FIM: INCLUDE funcionalideds BÁSICAS ---------------------------------------------------------------------------------

$objConn = abreDBConn(CFG_DB);

BeginHtmlBuffer(); //Inicia a captura em buffer (ob_start())
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE . " - " . $strRelTit); ?></title>
	<!-- INI: INCLUDE JScript e CSS ***************************************** -->
	<!-- Estilos....: "../_css/[SISTEMA].css" e "../_css/tablesort.css"
		 JavaScrit..: "../_scripts/tablesort.js"
					  * Funções locais de exportação/impressão e adicionais:					  	
						- swapDisplay, imprimir, exportarAdobe e exportDocument	!-->
	<?php include_once("_include_aslRunScript.php"); ?>
	<!-- FIM: INCLUDE JScript e CSS ***************************************** -->
	
	<!-- Exemplo de CSS específica para formatos diferentes de etiquetas, etc... -->
	<style>
		table.pagina { border:0px #FFFFFF solid; width:820px; background-color:#FFF; }
		div.box 	 { border:1px #FFFFFF solid; margin-bottom:8px; float:left; margin-left:8px; width:240px; height:95px; }
		div.linha    { width:390px; height:18px; overflow:hidden; }
		div.conteudo { width:390px; height:95px; padding:0px 0px 0px 0px; text-align:left; vertical-align:middle; background:#FFF;}
	</style>
	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
</head>
<body style='margin:10px;' bgcolor='#FFFFFF'>
<!-- INI: Título, Descrição e controles de exportação e display/hidden descrição -->
<!-- Controle de expand/collapse da descrição do relatório, formulários  de exprtação,
	 icones de exportação e teste/echo do DEBUG -->
<?php include_once("_include_aslRunControles.php"); ?>
<!-- FIM: Título, Descrição e controles de exportação e display/hidden descrição -->


<div id='mainPage'><!-- INI: Div mainPage - usado na Exportação... -->
<?php
	echo("<table class='pagina' cellpadding='0' cellspacing='0' align='left'><tr><td nowrap='nowrap' valign='top'>");

	/* Obs.: Relatórios ASL que chamam este executor, não devem ter CABEÇALHO e RODAPÉ, afinal este é um executor que imprime etiquetas
	 de qualeur forma, paramanter o padrão, colocamos aqui os códigos de leitura destes campos */
	echo(ShowCR("CABECALHO",$strRelHead)); // CABEÇALHO do relatório

	/* INI: Execução do SQL ---------------------------------------------------------------------------------------------- */
	/*
			// INI: SIMULAÇÃO ----------------------------------------------------------------------------
			for($i=1;$i<=33;$i++){
				$strAux  = "<div class='linha'><strong>FULANO DE TAL</div>";
				$strAux .= "<div class='linha'>abc tres ARQUITETOS ASSOCIADOS LTDA</strong></div>";
				$strAux .= "<div class='linha'>Rua Alvaro Seixas,60 - Engenho Novo</div>";
				$strAux .= "<div class='linha'>RIO DE JANEIRO - RJ</div>";
				$strAux .= "<div class='linha'><strong>20665-445</strong></div>";
				// boxEtiqueta 
				echo("<div class='box'>\n");
				echo("<div class='conteudo'>" . $strAux . "</div>\n");
				echo("</div>\n");
			}
			// FIM: SIMULAÇÂO ---------------------------------------------------------------------------- 
			*/
	if($strSQL != ""){
		try{
			$objResult = $objConn->query($strSQL);
			  
			if($objResult->rowCount() == 0 || $objResult == ""){
				mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc", "", "","aviso",1);
				die();
			}
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
	} else {
		mensagem("info_nova_pesquisa_titulo","info_nova_pesquisa_desc", "", "","info",1);
		die();
	}
	/* FIM: Execução do SQL ---------------------------------------------------------------------------------------------- */

	$intI = 0;
	$intActuallyCount = 1;
	while($objRS = $objResult->fetch()) {
	 	$outBuf .= FlushHtmlBuffer(); //Descarrega o Buffer

		$intI         = 0; 
		$intIdxAction = 0; 
		$intRowCount  = $objResult->rowCount();
		$strHTMLBody  = "";
		foreach($objRS as $strCampo => $strDado) {
			if($intI % 2 == 0){
				$intIdxAux = $intI/2;
				$boolFormatDouble = false;
				$strOperator = "";
				
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
					} elseif(preg_match("/\>([0-9])+/i",$strOperator) !== false) { $strDado = "<a onClick=\"window.open('execaslw.php?var_chavereg=" . str_replace(">","",$strOperator) . "&var_valor_aux=" . $strDado . "','','width=700,height=600,scrollbars=yes,resizable=yes,menubar=no');\" style=\"cursor:pointer;\">" . $strDado . "</a>";
					}
					$intIdxAction++;
				} else {
					$arrValues[$intIdxAux] = false;
				}
				if(is_date($strDado)) { $strDado = dDate(CFG_LANG,$strDado,false); }
				$strHTMLBody  .= "<div class='linha'>";
				$strHTMLBody  .= ($boolFormatDouble == false)?$strDado:number_format((double) $strDado, 2, ",", ".");
				$strHTMLBody  .= "</div>";
			}
			$intI++;
		}

		// INI: boxEtiqueta ----------------------------------------
		echo("<div class='box'>\n");
		echo("<div class='conteudo'>" . $strHTMLBody . "</div>\n");
		echo("</div>\n");
		if (($intActuallyCount % 20)==0) { echo("<div style='page-break-after:always;'></div>\n"); }
		// FIM: boxEtiqueta  ---------------------------------------
		$intActuallyCount++;
	} 
	echo("</td></tr></table>");
	echo(ShowCR("RODAPE",$strRelFoot)); // RODAPÉ do relatório

	$outBuf .= EndHtmlBuffer(); //Descarrega e finaliza o buffering
?>
</div><!-- FIM: Div mainPage - usado na Exportação... -->
</body>
</html>
<?php
    include_once("_include_aslRunHtmlSave.php");  // Grava o Arquivo HTML 
    include_once("_include_aslRunHtmlLog.php");   // Grava Log de execução do Relatório (com o nome do HTML gerado)
    include_once("_include_aslRunHtmlClear.php"); // Apaga arquivos html de relatórios antigos

	$objResult->closeCursor();
	$objConn = NULL;
?>