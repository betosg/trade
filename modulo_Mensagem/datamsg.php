<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente
 
$objConn = abreDBConn(CFG_DB);

$strOrderCol   = request("var_order_column"); //Índice da coluna para ordenação
$strOrderDir   = request("var_order_direct"); //Direção da ordenação (ASC ou DESC)
$strAcao   	   = request("var_acao");         //Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$strPasta  	   = (request("var_pasta") == "") ? "caixa_entrada" : request("var_pasta") ;        //Indicativo de qual a pasta atual.

//Define uma variável booleana afim de verificar se é um tipo de exportação ou não
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
		header("Content-Disposition: attachment; filename=Modulo_" . getTText(getsession($strSesPfx . "_titulo"),C_UCWORDS) . "_". time() . $strAcao);
	}
	
	$strLimitOffSet = "";
}   
else{/**************************************************************************************************** /
      Esta parte do condicional é para deixar a ordenação na exportação e deixar incluir os scripts de js 
	  e retira a paginação dos resultados caso for requisitada qualquer tipo de exportação 
	/******************************************************************************************************/
	
	include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
	
	//Preparação dos parâmetros necessários para a paginação da grade
	if(empty($intNumCurPage) || $intNumCurPage < 1) {
		$intNumCurPage   = 1;
		$intTotalPaginas = 1;
	}
	
	//Cria um array sendo o ORDER BY como o separador
	$arrSQLGrid = explode(" ORDER BY ", str_replace(";","",getsession($strSesPfx . "_select"))); 
	
	if(!empty($strOrderCol) && !empty($strOrderDir)){
		//Coloca a ordenação solicitada
		$strSQLGrid = $arrSQLGrid[0] . " ORDER BY " . $strOrderCol . " " . $strOrderDir;
	}
	else{
		//Coloca o ORDER BY 1, ou seja, ordena pela primeira coluna as consultas que não tem ordenação
		$strSQLGrid = $arrSQLGrid[0] . " ORDER BY 1 DESC "; 
	}
	
}

try{
	//Formatação final da consulta e execução
	$strSQLGrid = removeTagSQL($strSQLGrid);
	$objResult 	= $objConn->query($strSQLGrid);
	
	$objRS = $objResult->fetch();
	
	//Armazena a string SQL básica para que possa ser recuperada em outra instância
	setsession($strSesPfx . "_select", $strSQLGrid);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

?>
<html>
<head>
<title>PROEVENTO STUDIO</title>

<?php 
	if(!$boolIsExportation || $strAcao == "print" || $strAcao == "single"){
		echo("
			  <link href=\"../_css/" . CFG_SYSTEM_NAME . ".css\" rel=\"stylesheet\" type=\"text/css\">
			  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
			");
	}
?>
<script>
	var arrValues = new Array();
	var intIndex = 0;
	var intTotalNaoLido = <?php echo($objRS["nao_lido"]); ?>;
	/***************** Ações da aplicação - Início **************************/
	function aplicarFuncao(prValue){
		if(prValue != "" && prValue.indexOf("javascript") == -1){
			AbreJanelaPAGE(prValue,650,500);
		}
		else if(prValue.indexOf("javascript") > -1) {
			location.href = prValue;
		}
		document.getElementById("var_action").selectedIndex = 0;
	}
	
	function actionRedirect(prAction){
		if(arrValues.length > 0){
			switch(prAction){
				case "deletar":					
					if(confirm("<?php echo(getTText("delecao_confirm_1",C_NONE)); ?>" + arrValues.length + "<?php echo(getTText("delecao_confirm_2",C_NONE)); ?>")){
						parent.location.href = "msgdel.php?var_chavereg=" + arrValues;
					}
				break;
				case "lido":
					parent.location.href = "msgstatus.php?var_chavereg=" + arrValues + "&var_pasta=<?php echo($strPasta); ?>&var_acao=lido";
				break;
				case "não lido":
					parent.location.href = "msgstatus.php?var_chavereg=" + arrValues + "&var_pasta=<?php echo($strPasta); ?>&var_acao=nao_lido";
				break;
				case "resp":
					if(arrValues.length == 1){
						AbreJanelaPAGE("msgresp.php?var_chavereg=" + arrValues,650,500);
					}
				break;
			}
		}
		else{
			alert("<?php echo(getTText("delecao_warning_1",C_NONE)); ?>");
		}
	}
	
	function setOrderBy(prStrOrder,prStrDirect){
		location.href = "datamsg.php?var_order_column=" + prStrOrder + "&var_order_direct=" + prStrDirect;
	}
	
	function switchColor(prObj, prColor){
		prObj.style.backgroundColor = prColor;
	}
	/***************** Ações da grade - Fim **************************/
	
	/***************** Ações de mensagem - Início **************************/
	function popupViewMsg(prValue){
		window.open("msgview.php?var_chavereg=" + prValue,"","popup=yes,width=500,height=500");
	}
	
	function retorna(prObjThis,prValue,prEvent,prOldColor){
		if(!prEvent.ctrlKey){
			parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_viewmsg"); ?>").src="msgview.php?var_chavereg=" + prValue;
			limpaSelect();
			arrValues.splice(0,arrValues.length);
			
			if(prObjThis.style.fontWeight == "bold"){
				intTotalNaoLido--;
				if(intTotalNaoLido == 0){
					parent.parent.<?php echo(CFG_SYSTEM_NAME . "_left"); ?>.document.getElementById("<?php echo($strPasta); ?>").style.fontWeight = "normal";
					parent.parent.<?php echo(CFG_SYSTEM_NAME . "_left"); ?>.document.getElementById("<?php echo($strPasta); ?>").innerHTML = "<?php echo(getTText($strPasta,C_NONE)); ?>";
				}
				else{
					parent.parent.<?php echo(CFG_SYSTEM_NAME . "_left"); ?>.document.getElementById("<?php echo($strPasta); ?>").innerHTML = "<?php echo(getTText($strPasta,C_NONE)); ?> (" + intTotalNaoLido + ")";
				}
			}
			selectRow(prObjThis,prValue,true);
		}
		else{
			var intAux = 0;
			while(intAux < arrValues.length){
				if(arrValues[intAux] == prValue){
					prObjThis.bgColor = prOldColor;
					arrValues.splice(intAux,1);
					return true;
				}
				intAux++;
			}
			
			selectRow(prObjThis,prValue,false);
			return true;
		}
	}
	
	function selectRow(prObjThis,prValue,prSelect){
		prObjThis.bgColor = "<?php echo(CL_CORBAR_GLASS_1); ?>";
		prObjThis.style.backgroundColor = "<?php echo(CL_CORBAR_GLASS_1); ?>";
		(prSelect) ? prObjThis.style.fontWeight = "normal" : null; 
		arrValues.push(prValue);
	}
	
	function limpaSelect(){
		intAux = 0;
		while(eval("document.getElementById('linha_" + intAux + "')") != null){
			linha = eval("document.getElementById('linha_" + intAux + "')");
			linha.bgColor = (intAux % 2 == 0) ? "#F7F7F7" : "#FFFFFF";
			intAux++;
		}
	}
	/***************** Ações da mensagem - Fim **************************/

</script>
</head>
<body style="margin:0px;" bgcolor="#FFFFFF">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="padrao_gde" align="left" width="50%"><b><?php echo(getTText(getsession($strSesPfx . "_titulo"),C_UCWORDS) . " - (" . getTText($strPasta,C_NONE) . ")"); ?></b></td>
		<td align="right" width="50%">
		  <?php if($strAcao == ""){ ?>
			<select id="var_action" onChange="aplicarFuncao(this.value);">
				<option value=""><?php echo(getTText("selecione",C_UCWORDS)."...");  ?></option>
				<?php 
					$arrMenuRotulo  = explode(";",getsession($strSesPfx . "_menucombo_rotulo"));
					$arrMenuValor   = explode(";",getsession($strSesPfx . "_menucombo_valores"));
					$intI = 0;
					while($intI < count($arrMenuRotulo)){
						if($arrMenuValor[$intI] != NULL){
							echo("<option value=\"" . $arrMenuValor[$intI] . "\">" . getTText(trim($arrMenuRotulo[$intI]),C_UCWORDS) . "</option>");
						}
						$intI++;
					}
				?>
			</select>
		  <?php } ?>
		</td>
	</tr>
	<tr><td colspan="2" height="3"></td></tr>
	<tr>
		<td colspan="2">
			<?php if($objResult->rowCount() > 0){ ?>
			<table cellpadding="0" cellspacing="3" width="100%" style="border:1px #EEEEEE solid;" bgcolor="#F7F7F7">
				<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
				<tr>
					<td>
						<table border="0" cellpadding="0" cellspacing="0" width="100%" background="../img/grid_backheader.gif" style="background-repeat:repeat-x;">
							<tr>
								<?php
									/******** Cabeçalho da grade - [Início] ********/
									
									$intI = 2;  //Contador auxiliar para exibição dos campos da consulta. Começa em dois para retornar o numero certo da coluna.
									
									foreach($objRS as $strCampo => $strDado){
										if($intI % 2 == 0 && $strCampo != "nao_lido" && $strCampo != "lido"){
											echo("
												  <td height=\"22\">
													<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
														<tr>
												");
												
												if(!$boolIsExportation){
													echo("	<td width=\"1%\">
																<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
																	<tr><td><a href=\"javascript:setOrderBy('" . $intI/2 . "','ASC');\"><img src=\"../img/gridlnkASC.gif\"  border=\"0\" align=\"absmiddle\"></a></td></tr>
																	<tr><td><a href=\"javascript:setOrderBy('" . $intI/2 . "','DESC');\"><img src=\"../img/gridlnkDESC.gif\" border=\"0\" align=\"absmiddle\"></a></td></tr>
																</table>
															</td>");
												}
												
											$strClass = (getTText($strCampo,C_UCWORDS) != " ") ? "class=\"titulo_grade\"" : "" ;
												
											echo("			<td " . $strClass . " width=\"99%\" nowrap>". getTText($strCampo,C_UCWORDS) . "</td>
														</tr>
													</table>
												  </td>
												");
										}
										$intI++;
									}
									/******** Cabeçalho da grade - [Fim]    ********/
								?>
							</tr>
							<tr><td colspan="<?php echo(intval(($intI/2) - 1)); ?>" height="3"></td></tr>
							<?php
								/******** Conteúdo da grade - [Início] ********/
								
								$strBgColor = "#F7F7F7";
							
								//verfica se os paramêtros "single" não estão vazios e coloca a função apropriada
								$intAux = 0;
								do{
									$strStyle = (!$objRS["lido"]) ? "style=\"font-weight:bold\"" : "";
									
									echo("<tr id=\"linha_" . $intAux . "\" " . $strStyle . " bgcolor=\"" . $strBgColor . "\" onClick=\"retorna(this,'" . getValue($objRS,0) . "',event,'" . $strBgColor . "');\" onDblClick=\"popupViewMsg('" . getValue($objRS,0) . "');\" onMouseOver=\"switchColor(this,'#CCCCCC');\" onMouseOut=\"switchColor(this,'');\">");
									$intI = 0;
									
									foreach($objRS as $strDado){
										if($intI % 2 == 0 && (count($objRS)-4) > $intI){
											echo("<td height=\"15\" align=\"left\">&nbsp;");
											if(is_date($strDado)) {
												$strDado = (strpos($strDado,":") !== false) ? dDate(CFG_LANG,$strDado,true) : dDate(CFG_LANG,$strDado,false); 
											}
											if(strpos($strDado,"status_img_") !== false){
												$strDado = str_replace("status_img_","",$strDado);
												echo("<img src=\"../img/imgstatus_" . $strDado . ".gif\" title=\"" . getTText($strDado,C_TOUPPER) . "\"></td>");
											}
											else{
												echo( $strDado . "</td>");
											}
										}
										$intI++;
									}
									
									($strBgColor == "#F7F7F7") ? $strBgColor = "#FFFFFF"  :  $strBgColor = "#F7F7F7";
									
									echo("</tr>");
									$intAux++;
								}while($objRS = $objResult->fetch());
								
								/******** Conteúdo da grade - [Fim]    ********/
							?>
						</table>
					</td>
				</tr>
			</table>
			<?php
				} 
				else{
					mensagem("alert_consulta_vazia_titulo", "alert_consulta_vazia_desc", "", "", "aviso", 0);
				}
			?>
		</td>
	</tr>
	<tr><td colspan="2" height="3"></td></tr>
	<tr><td height="3" bgcolor="#BFBFBF" colspan="2"></td></tr>
	<tr><td colspan="2" height="3"></td></tr>
</table>
</body>
</html>
<?php
$objResult->closeCursor();
$objConn = NULL;
?>