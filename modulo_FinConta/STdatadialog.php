<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
 
$objConn = abreDBConn(CFG_DB);

$strOrderCol      = request("var_order_column");    //Índice da coluna para ordenação
$strOrderDir      = request("var_order_direct");    //Direção da ordenação (ASC ou DESC)
$intNumCurPage    = request("var_curpage");         //Página corrente
$strAcao   	      = request("var_acao");            //Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$intCodDialogGrid = request("var_cod_dialog_grid"); //Código da dialog relacionado
$strPopulate      = request("var_populate");        //Flag de verificação se necessita popular o session ou não

if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));            //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente

try{
	$strSQL = " SELECT cod_dialog_grid, grid_query, num_per_page 
				 FROM sys_dialog_grid
				WHERE cod_dialog_grid = " . $intCodDialogGrid;
	$objResultGrid = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

if($objRSGrid = $objResultGrid->fetch()){

$strSQLGrid = str_replace(";","",replaceParametersSession(getValue($objRSGrid,"grid_query")));

//Define uma variável booleana afim de verificar se é um tipo de exportação ou não
$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc") || ($strAcao == ".pdf");

//Exportação para excel, word e adobe reader
if($boolIsExportation){
	if($strAcao == ".pdf"){
		//Redireciona para página que faz a exportação para adode reader
		//redirect("exportpdf.php?var_sqlparam=" . $strSQLGrid); 
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
	$arrSQLGrid = explode("ORDER BY", str_replace(";","",$strSQLGrid)); 
	
	if(!empty($strOrderCol) && !empty($strOrderDir)){
		//Coloca a ordenação solicitada
		$strSQLGrid = $arrSQLGrid[0] . " ORDER BY " . $strOrderCol . " " . $strOrderDir;
	}
	else{
		//Coloca o ORDER BY 1, ou seja, ordena pela primeira coluna as consultas que não tem ordenação
		if(!isset($arrSQLGrid[1])){
			$strSQLGrid = $arrSQLGrid[0] . " ORDER BY 1 ASC "; 
		}
		else{
			$strSQLGrid = implode(" ORDER BY ", $arrSQLGrid);
		}
	}
	
}
try{
	$strLimitOffSet = "";
	
	if(getValue($objRSGrid,"num_per_page") != ""){
		//Recuperação do numero de registros inseridos na tabela do módulo
		$strSQLCount = "SELECT COUNT(*) AS total " . substr($arrSQLGrid[0],strpos($arrSQLGrid[0]," FROM"));
		$objRSCount  = $objConn->query($strSQLCount)->fetch();
		$intTotalRegistros = getValue($objRSCount,"total");
		$intTotalPaginas   = $intTotalRegistros/getValue($objRSGrid,"num_per_page");
		($intTotalPaginas > round($intTotalPaginas)) ? $intTotalPaginas = round($intTotalPaginas) + 1 : $intTotalPaginas = round($intTotalPaginas); //Aqui ele formata o resultado para valor inteiro
		
		//Formatação da paginação dentro da consulta
		$strLimitOffSet = " LIMIT " . getValue($objRSGrid,"num_per_page") . " OFFSET " . strval(getValue($objRSGrid,"num_per_page") * ($intNumCurPage - 1));
	}
} 
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

try{
	//Formatação final da consulta e execução
	$strSQLGrid = removeTagSQL($strSQLGrid);
	$objResult  = $objConn->query($strSQLGrid . $strLimitOffSet);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

$intContIcons = 0;      //Contador para os índices dos ícones

if($strAcao == ""){
	//Seleção dos ícones de ação da grade
	try{
		$strSQL     = " SELECT nome, link, link_img, rotulo, target, width, height
						  FROM sys_dialog_grid_campos_links 
						 WHERE cod_dialog_grid = " . getValue($objRSGrid,"cod_dialog_grid") . " 
						   AND dtt_inativo IS NULL 
						   ORDER BY ordem;
					  ";
		$objResult2 = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

	$arrIconConf = array(); //Declaração do array de configuração dos ícones
	
	$strAcaoDefault = "";
	foreach($objResult2 as $objRS2){
		if(getValue($objRS2,"link_img") != ""){
			$arrIconConf[$intContIcons]["nome"]     = getTText(getValue($objRS2,"nome"),C_UCWORDS);
			$arrIconConf[$intContIcons]["link"]	    = getValue($objRS2,"link");
			$arrIconConf[$intContIcons]["link_img"] = getValue($objRS2,"link_img");
			$arrIconConf[$intContIcons]["target"]   = getValue($objRS2,"target");
			$arrIconConf[$intContIcons]["width"]    = getValue($objRS2,"width");
			$arrIconConf[$intContIcons]["height"]   = getValue($objRS2,"height");
			$arrIconConf[$intContIcons]["rotulo"]   = getTText(getValue($objRS2,"rotulo"),C_UCWORDS);
			$intContIcons++;
		}
	}
	$objResult2->closeCursor();
}
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>

<?php 
	if(!$boolIsExportation || $strAcao == "print" || $strAcao == "single"){
		echo("
			  <link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\">
			  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
			");
	}
?>
<script>
	function setOrderBy(prStrOrder,prStrDirect){
		location.href = "STdatadialog.php?var_cod_dialog_grid=<?php echo($intCodDialogGrid); ?>&var_order_column=" + prStrOrder + "&var_order_direct=" + prStrDirect;
	}
	
	function paginar(prPagina){
		if(prPagina > 0 && prPagina <= <?php echo($intTotalPaginas); ?>){
			document.formpaginacao.var_curpage.value = prPagina;
			document.formpaginacao.submit();
		}	
	}
	
	function switchColor(prObj, prColor){
		prObj.style.backgroundColor = prColor;
	}
</script>
</head>
<body style="margin:0px 0px 10px 0px;" bgcolor="#FFFFFF">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr><td colspan="2" height="3"></td></tr>
		<tr>
			<td colspan="2">
				<?php if($objResult->rowCount() > 0){ ?>
				<table cellpadding="0" cellspacing="3" width="100%" style="border:1px #EEEEEE solid;" bgcolor="#F7F7F7">
					<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
					<tr>
						<td>
							<table id="tableContent" border="0" cellpadding="0" cellspacing="0" width="100%" background="../img/grid_backheader.gif" style="background-repeat:repeat-x;">
								<tr>
									<?php
										/******** Cabeçalho da grade - [Início] ********/
										
										$intI = 2;  //Contador auxiliar para exibição dos campos da consulta. Começa em dois para retornar o numero certo da coluna.
										$objRS = $objResult->fetch(); //Faz o fetch do ResultSet retornando um array com o resultado da consulta
										
										echo("<td></td>"); //Coloca uma coluna mesclada para ajustar a tabela com os ícones que virão abaixo
											
										foreach($objRS as $strCampo => $strDado){
											if($intI % 2 == 0){
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
								    
									$strBgColor = "";
									
									do{
										
										echo("<tr bgcolor=\"" . $strBgColor . "\" onMouseOver=\"switchColor(this,'#CCCCCC');\" onMouseOut=\"switchColor(this,'');\">
												<td width=\"" . 25 * $intContIcons . "\">");
										if($strAcao == "" && $intContIcons > 0){
											echo("
													<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"" . 25 * $intContIcons . "\">
														<tr>");
											
											foreach($arrIconConf as $arrConf){
												$mixPos = strpos($arrConf["link"],"{");
												if($mixPos !== false){
													while($mixPos !== false){
														$strIndex  = substr($arrConf["link"], $mixPos+1 , strpos($arrConf["link"],"}")-($mixPos+1));
														$arrConf["link"] = str_replace("{".$strIndex."}",$objRS[$strIndex],$arrConf["link"]);
														$mixPos = strpos($arrConf["link"],"{");
													}
												}
												
												echo("
															  <td width=\"25\">
																<a " . 
															(($arrConf["target"] != "_blank") ? "href=\"" . $arrConf["link"] . "\" target=\"" . $arrConf["target"] : " onClick=\"window.open('" . $arrConf["link"] . "','','width=" . $arrConf["width"] . ",height=" . $arrConf["height"] . ",scrollbars=1');\" style=\"cursor:pointer\"") 
															         . ">
																 <img src=\"" . $arrConf["link_img"] . "\" border='0' title=\"" . $arrConf["nome"] . "\">
																</a>
															  </td>
													");
											}
											
											echo("  	</tr>
													</table>");
										}
										
										echo("</td>");
										
										$intI = 0;
										foreach($objRS as $strDado){
											if($intI % 2 == 0){
												echo("<td height=\"22\" align=\"left\">");
												if(is_date($strDado)) {
													$strDado = (strpos($strDado,":") !== false) ? dDate(CFG_LANG,$strDado,true) : dDate(CFG_LANG,$strDado,false); 
												}
												if(preg_match("/^status_img_(.*)/",$strDado)){
													$strDado = str_replace("status_img_","",$strDado);
													echo("<img src=\"../img/imgstatus_" . $strDado . ".gif\" title=\"" . getTText($strDado,C_TOUPPER) . "\" hspace=\"2\"></td>");
												}
												else{
													echo($strDado . "</td>");
												}
											}
											$intI++;
										}
										($strBgColor == "") ? $strBgColor = "#FFFFFF"  :  $strBgColor = "";
										
										echo("</tr>");
									} while($objRS = $objResult->fetch());
									
									/******** Conteúdo da grade - [Fim]    ********/
								?>
								<tr><td colspan="<?php echo(intval(($intI/2) - 1)); ?>" height="1"></td></tr>
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
		<?php if($objResult->rowCount() > 0 && !$boolIsExportation && getValue($objRSGrid,"num_per_page") != ""){ ?>
		<tr>
			<td align="left"><?php echo($intTotalRegistros . " " . getTText("reg_encontrados",C_TOLOWER)); ?></td>
			<td align="right">
				<table border="0" cellpadding="0" cellspacing="0">
				  <form name="formpaginacao" action="STdatadialog.php" method="post">
					<input type="hidden" name="var_order_column" value="<?php echo($strOrderCol); ?>">
					<input type="hidden" name="var_order_direct" value="<?php echo($strOrderDir); ?>">
					<input type="hidden" name="var_cod_dialog_grid" value="<?php echo($intCodDialogGrid); ?>">
					<tr>
						<td><img src="../img/grid_arrow_left.gif" onClick="paginar(<?php echo($intNumCurPage - 1)?>)"></td>
						<td style="padding:0px 10px 0px 10px;"><?php echo(getTText("pagina",C_TOLOWER)); ?> <input type="text" name="var_curpage" value="<?php echo($intNumCurPage)?>" size="3"> <?php echo(getTText("de",C_TOLOWER) . " " . $intTotalPaginas); ?></td>
						<td><img src="../img/grid_arrow_right.gif" onClick="paginar(<?php echo($intNumCurPage + 1)?>)"></td>
					</tr>
				  </form>
				</table>
			</td>
		</tr>
		<?php } ?>
	</table>
 </body>
</html>
<?php
$objResult->closeCursor();
}

$objResultGrid->closeCursor();
$objConn = NULL;
?>