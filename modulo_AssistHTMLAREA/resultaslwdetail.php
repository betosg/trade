<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
  
$strAcao       = request("var_acao");       // Ação para exportação (excel, word...)
$strAcaoGrid   = request("var_acaogrid");   // Ação de retorno da grade (single, multiple)
$strSQLRelOrig = request("var_strparam");   // A consulta deve chegar com as TAGs do tipo (<ASLW_APOSTROFE>, etc...) 
$strDescricao  = request("var_descricao");  // A descrição do relatório (inativo)
$strNome       = request("var_nome");       // O nome do campo para retorno para o formulário
$strCampoRet   = request("var_camporet");   // O nome do campo no formulário para qual o relatório deve retornar o valor
$strDBCampoRet = request("var_dbcamporet"); // O nome do campo na cosulta que deve ser retornado
$strDBCampoLbl = request("var_dbcampolbl"); // O label do campo na cosulta que deve ser retornado
$strDialogGrp  = request("var_dialog_grp"); // O índice do formulário que deve ser retornado
$strRelatTitle = request("var_relat_title");// O nome do relatório, caso ele for um ASLW
$strHTMLBody   = ""; //Variável que receberá o HTML da página para ser exibido posteriormente. (Para não usar muitos echos)

$strDBCampoRet = preg_replace("/[[:alnum:]_]+\./i","",$strDBCampoRet); //Para tirar o nome da tabela do campo que será retornado

function filtraAlias($prValue){
	return(strtolower(preg_replace("/([[:alnum:]_\"\(\)\.\+\-\*\/\^' ]+ AS )|([[:alnum:]_\"]+\.)|/i","",$prValue)));
}

/********* Verificação de acesso e localização do módulo *********/
$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

/********* Preparação SQL - Início *********/
$strSQLRel = removeTagSQL($strSQLRelOrig); //Remove as tags
$strSQLRel = replaceParametersSession($strSQLRel); //Coloca os valores de sistema (session)
//preg_match_all("/\[(?<operador>[[:punct:]]?) +(?<campo>[[:alnum:]_\"\(\)\.\+\-\*\/\^' ]+( AS [[:alnum:]_\"]+)*)\]/i",$strSQLRel,$arrParams); //Verifica se há funções ASLW e as coloca num array
preg_match_all("/\[([[:punct:]]?) +([[:alnum:]_\"\(\)\.\+\-\*\/\^' ]+( AS [[:alnum:]_\"]+)*)\]/i",$strSQLRel,$arrParams); //Verifica se há funções ASLW e as coloca num array
$strSQLRel = preg_replace("/\[[[:punct:]]|\]|\"/","",$strSQLRel); //retira as funções do SQL deixando somente o nome do campo com suas dependencias
/********* Preparação SQL - Fim *********/

$objConn = abreDBConn(CFG_DB);

$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc") || ($strAcao == ".pdf");

//Exportação para excel, word e adobe reader
if($boolIsExportation){
	if($strAcao == ".pdf"){
		//Redireciona para página que faz a exportação para adode reader
		redirect("exportpdf.php");
	}
	else{
		//Coloca o cabeçalho de download do arquivo no formato especificado de exportação
		header("Content-type: application/force-download"); 
		header("Content-Disposition: attachment; filename=Relatorio_" . time() . $strAcao);
	}
}  

if($strSQLRel != ""){
	try{
		$objResult = $objConn->query($strSQLRel);
      	  
		if($objResult->rowCount() == 0 || $objResult == ""){
			mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc", "", "","aviso",1);
			die();
		}
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<?php 
if($strAcao == ""){ 
echo("	<link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\" type=\"text/css\">
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">");
}
?>
	<script language="javascript">
	<!--
	function setOrderBy(prStrOrder,prStrDirect){
		var myStrSQL, myAuxStrSQL, myPos;
		//myStrSQL = document.FormPrSQL.sqlBUFFER.value.toLowerCase();
		myStrSQL = document.FormPrSQL.sqlBUFFER.value;
		myAuxStrSQL = myStrSQL.split("ORDER BY");
		myStrSQL = myAuxStrSQL[0] + " ORDER BY " + prStrOrder + " " + prStrDirect;
		document.location = "resultaslwdetail.php?var_acao=" + document.FormPrSQL.acaoBUFFER.value +
												"&var_descricao=" + document.FormPrSQL.descBUFFER.value +
												"&var_strparam=" + myStrSQL +
												"&var_camporet=<?php echo($strCampoRet); ?>" +
												"&var_dbcamporet=<?php echo($strDBCampoRet); ?>" +
												"&var_dbcampolbl=<?php echo($strDBCampoRet); ?>" +
												"&var_acaogrid=<?php echo($strAcaoGrid); ?>";
	}

	<?php if($strAcaoGrid == "single") { ?>
	function retorna(prValue, prLabel){ 
		var campo   = parent.window.opener.document.formeditor_<?php echo($strDialogGrp . "." . $strCampoRet); ?>;
		campo.value = prValue;
		parent.window.close();
	}
	<?php } else if($strAcaoGrid == "multiple") { ?>
	var arrValues = new Array();
	var intIndex = 0;

	function seleciona(prValue, prObjThis, prOldColor){
		var intAux = 0;
		while(intAux < arrValues.length){
			if(arrValues[intAux] == prValue){
				prObjThis.bgColor = prOldColor;
				arrValues.splice(intAux,1);
				return true;
			}
			intAux++;
		}
		
		prObjThis.bgColor = "#AFD987";
		prObjThis.style.backgroundColor="#AFD987";
		arrValues.push(prValue);
		return true;
	}

	function retornarMultiple(){
		var campo   = parent.window.opener.document.formeditor_<?php echo($strDialogGrp . "." . $strCampoRet); ?>;
		campo.value = arrValues.toString();
		parent.window.close();
	}
	<?php } ?>
//-->
	</script>
</head>
<body style="margin:10px;" bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<?php if(!$boolIsExportation){ ?>
<form name="FormPrSQL">
  <input name="sqlBUFFER"  type="hidden" value="<?php echo(insertTagSQL($strSQLRelOrig)); ?>">
  <input name="acaoBUFFER" type="hidden" value="<?php echo($strAcao); ?>">
  <input name="descBUFFER" type="hidden" value="<?php echo($strDescricao); ?>">
</form>
<?php
} 

athBeginWhiteBox("100%");
	$strHTMLBody .= "
<table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" align=\"center\" style=\"margin-bottom:3px;\">";
	if($strRelatTitle != ""){
		$strHTMLBody .= "\n\t<tr>\n\t\t<td align=\"left\" class=\"padrao_gde\"><b>" . $strRelatTitle . "</b></td>";
		
		if($strAcaoGrid == "multiple"){
			$strHTMLBody .= "\n\t\t<td align=\"right\"><img src=\"../img/icon_encerrado.gif\" title=\"" . getTText("completar_operacao",C_UCWORDS) . "\" onClick=\"retornarMultiple();\" style=\"cursor:pointer\"></td></tr>";
		}
		
		$strHTMLBody .= "\n\t</tr>";
	} 
	
	$strHTMLBody .= "
</table>
<table cellpadding=\"0\" cellspacing=\"3\" width=\"100%\" style=\"border:1px #EEEEEE solid;\" bgcolor=\"#F7F7F7\">
	<tr><td height=\"5\" bgcolor=\"#BFBFBF\"></td></tr>
	<tr>
		<td>
		<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
			<tr>";
	$intI = 0;
	$intJ = 1;
	$objRS = $objResult->fetch();
	
	foreach($objRS as $strCampo => $strDado){
		if($intI % 2 == 0){
			$strHTMLBody .= "
				<td height=\"22\" background=\"../img/grid_backheader.gif\" style=\"background-repeat:repeat-x;\" style=\"padding-left:10px;\">
					<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
						<tr>";
			if(!$boolIsExportation){
				$strHTMLBody .= "
							<td width=\"1%\">
								<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
									<tr><td><a href=\"javascript:setOrderBy('" . strval($intJ) . "','ASC');\"><img src=\"../img/gridlnkASC.gif\"  border=\"0\" align=\"absmiddle\"></a></td></tr>
									<tr><td><a href=\"javascript:setOrderBy('" . strval($intJ) . "','DESC');\"><img src=\"../img/gridlnkDESC.gif\" border=\"0\" align=\"absmiddle\"></a></td></tr>
								</table>
							</td>";
			}
		    $intJ++;
			$strHTMLBody .= "			
							<td class=\"padrao_gde\" style=\"padding-left:10px;padding-right:10px;color:#767676;\" width=\"99%\" nowrap>". $strCampo . "</td>
						</tr>
					</table>
				</td>";
		}
		$intI++;
	}
	$strHTMLBody .= "\n			</tr>";
	
	$strBgColor = CL_CORLINHA_1; $intActuallyCount = 1;
	do{
		$strHTMLBody .= "\n			<tr bgcolor=\"" . $strBgColor . "\" onMouseOver=\"this.style.backgroundColor='#CCCCCC';\" onMouseOut=\"this.style.backgroundColor='';\"";
		if($strAcaoGrid == "single"){
			$strHTMLBody .= " style=\"cursor:pointer\" onClick=\"retorna('" . str_replace("'","\\'",getValue($objRS,$strDBCampoRet)) . "','" . str_replace("'","\\'",getValue($objRS,$strDBCampoLbl)) . "');\"";
		}
		if($strAcaoGrid == "multiple"){
			$strHTMLBody .= " style=\"cursor:pointer\" onClick=\"seleciona('" . str_replace("'","\\'",getValue($objRS,$strDBCampoLbl)) . "',this,'" . $strBgColor . "');\"";
		}
		$strHTMLBody .= ">";
		
		$intI = 0; $intIdxAction = 0; $intRowCount = $objResult->rowCount();
		foreach($objRS as $strCampo => $strDado){
			if($intI % 2 == 0){
				// Código das funções ASLW - Início
				$intIdxAux = $intI/2;
				//if(isset($arrParams["campo"][$intIdxAction]) && filtraAlias($arrParams["campo"][$intIdxAction]) == $strCampo){
				if(isset($arrParams[2][$intIdxAction]) && filtraAlias($arrParams[2][$intIdxAction]) == $strCampo){
					
					//switch($arrParams["operador"][$intIdxAction]){
					switch($arrParams[1][$intIdxAction]){
						case "+": (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = $strDado : $arrValues[$intIdxAux] += $strDado;
						break;
						case "-": (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = $strDado : $arrValues[$intIdxAux] -= $strDado;
						break;
						case "*": (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = $strDado : $arrValues[$intIdxAux] *= $strDado;
						break;
						case "/": (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = $strDado : $arrValues[$intIdxAux] /= $strDado;
						break;
						case "#": (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = 1 : $arrValues[$intIdxAux]++;
						break;
						case "@": (!isset($arrValues[$intIdxAux])) ? $arrValues[$intIdxAux] = $strDado : ($intActuallyCount != $intRowCount) ? $arrValues[$intIdxAux] += $strDado : $arrValues[$intIdxAux] /= $intRowCount ;
						break;
						case "!": (!isset($arrValues[$intIdxAux]) || $arrValues[$intIdxAux] != $strDado) ? $arrValues[$intIdxAux] = $strDado : $strDado = "";
						break;
					}
					$intIdxAction++;
				}
				else{
					$arrValues[$intIdxAux] = false;
				}
				// Código das funções ASLW - Fim
				if(is_date($strDado)) { $strDado = dDate(CFG_LANG,$strDado,false); }
				$strHTMLBody .= "\n				<td height=\"22\" style=\"padding-left:10px;\">&nbsp;" . $strDado . "</td>";
			}
			$intI++;
		}
		
		$strHTMLBody .= "\n			</tr>";
		$strBgColor = ($strBgColor == CL_CORLINHA_2) ? CL_CORLINHA_1 : CL_CORLINHA_2;
		$intActuallyCount++;
	} while($objRS = $objResult->fetch());
	
	$strHTMLBody .= "\n				<tr><td colspan=\"" . ($intI/2) . "\"><hr size=\"2\" color=\"#BFBFBF\"></td></tr>
			<tr><td height=\"5\" colspan=\"" . ($intI/2) . "\"></td></tr>
			<tr>";
			
	foreach($arrValues as $mixValue){ $strHTMLBody .= "\n\t\t\t<td style=\"padding-left:15px;\">" . (($mixValue !== false && is_numeric($mixValue)) ? "<b>" . $mixValue . "</b>" : "") . "</td>"; }
	
	$strHTMLBody .= "
			</tr>
		</table>
		</td>
	</tr>
</table>";

echo($strHTMLBody);

 athEndFloatingBox(); 
?>
</body>
</html>
<?php
$objResult->closeCursor();
$objConn = NULL;
}
else{
	mensagem("info_nova_pesquisa_titulo","info_nova_pesquisa_desc", "", "","info",1);
	die();
}
?>
