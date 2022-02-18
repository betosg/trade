<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
 
$objConn = abreDBConn(CFG_DB);

$strOrderCol      = request("var_order_column");   // Índice da coluna para ordenação
$strOrderDir      = request("var_order_direct");   // Direção da ordenação (ASC ou DESC)
$intNumCurPage    = request("var_curpage");        // Página corrente
$strAcao   	      = request("var_acao");           // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$intCodDialogGrid = request("var_cod_dialog_grid");// Código da dialog relacionado
$strSQLParam      = request("var_sql_param");      // Parâmetro com o SQL vindo do bookmark
$strPopulate      = request("var_populate");       // Flag de verificação se necessita popular o session ou não

if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente



// Se for do tipo single pega as variaveis necessárias para seu processamento
if(getsession($strSesPfx . "_acao") == "single"){
	$strAcao = getsession($strSesPfx . "_acao");
	$strFieldName = getsession($strSesPfx . "_aux_fieldname");
	$strFormName  = getsession($strSesPfx . "_aux_formname");
}

if($intCodDialogGrid == "" || !is_numeric($intCodDialogGrid)){
	$strSQLGrid       = ($strSQLParam == "" ) ? getsession($strSesPfx . "_select") : $strSQLParam;
	$intNumPerPage    = getsession($strSesPfx . "_num_per_page");
	$strTableLinks    = "sys_descritor_campos_links";
	$strKeyFieldName  = "cod_app";
	$intKeyFieldValue = getsession($strSesPfx . "_chave_app");
	if(getsession($strSesPfx . "_field_detail") != ''){
		$strBodyStyle     = "style=\"margin:10px 0px 10px 0px;\"";
	}else{
		$strBodyStyle     = "style=\"margin:10px 0px 10px 0px;\" background=\"../img/bgFrame_" . CFG_SYSTEM_THEME . "_main.jpg\"";
	}
	}
else{
	try{
		$strSQL = " SELECT grid_query, num_per_page 
					 FROM sys_dialog_grid
					WHERE cod_dialog_grid = " . $intCodDialogGrid;
		$objResultGrid = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	if($objRSGrid = $objResultGrid->fetch()){
		$strSQLGrid       = replaceParametersSession(getValue($objRSGrid,"grid_query"));
		$intNumPerPage    = getValue($objRSGrid,"num_per_page");
		$strTableLinks    = "sys_dialog_grid_campos_links";
		$strKeyFieldName  = "cod_dialog_grid";
	    $intKeyFieldValue = $intCodDialogGrid;
		$strBodyStyle     = "style=\"margin:0px 0px 10px 0px;\"";
	}
}


//Recupera a string SQL do session, tirando ponto e vírgula, que mais tarde pode atrapalhar na manipulação da consulta.
$strSQLGrid = str_replace(";","",$strSQLGrid);

//Cria um array sendo o ORDER BY como o separador
$arrSQLGrid = explode("ORDER BY", str_replace(";","",$strSQLGrid)); 

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
		header("Content-Disposition: attachment; filename=Modulo_" . getTText(getsession($strSesPfx . "_titulo"),C_NONE) . "_". time() . $strAcao);
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
	if($intNumPerPage != ""){
		//Recuperação do numero de registros inseridos na tabela do módulo
		$strSQLCount = "SELECT COUNT(*) AS total FROM " . eregi_replace("select(.*)from","",preg_replace("/\r\n*/i","",$arrSQLGrid[0]));
		$objRSCount  = $objConn->query($strSQLCount)->fetch();
		
		$intTotalRegistros = getValue($objRSCount,"total");
		$intTotalPaginas   = $intTotalRegistros/$intNumPerPage;
		
		//Aqui ele formata o resultado para valor inteiro
		$intTotalPaginas = ($intTotalPaginas > round($intTotalPaginas)) ? round($intTotalPaginas) + 1 : round($intTotalPaginas); 
		
		//Formatação da paginação dentro da consulta
		$strLimitOffSet = " LIMIT " . $intNumPerPage . " OFFSET " . strval($intNumPerPage * ($intNumCurPage - 1));
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
	
	//Armazena a string SQL básica para que possa ser recuperada em outra instância
	if($intCodDialogGrid == "") { setsession($strSesPfx . "_select", $strSQLGrid); }
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

//Estas duas variáveis tem que ficar do lado de fora do IF abaixo para que não gere um WARNING de declaração dessas variáveis.
$intContIcons = 0;      //Contador para os índices dos ícones
$strAcaoDefault = "";   //Inicialização da variável de ação default

if($strAcao == ""){
	//Seleção dos ícones de ação da grade
	try{
		$strSQL     = " SELECT nome, link, link_img, rotulo, default_action, target, width, height, field_master_detail
						  FROM " . $strTableLinks . " 
						 WHERE " . $strKeyFieldName . " = " . $intKeyFieldValue . " 
						   AND dtt_inativo IS NULL 
						   ORDER BY ordem;
					  ";
		$objResult2 = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	//Declaração do array de configuração dos ícones
	$arrIconConf = array();
	
	foreach($objResult2 as $objRS2){
		if(getValue($objRS2,"link_img") != ""){
			$arrIconConf[$intContIcons]["nome"]     = getTText(getValue($objRS2,"nome"),C_NONE);
			$arrIconConf[$intContIcons]["link"]	    = getValue($objRS2,"link");
			$arrIconConf[$intContIcons]["link_img"] = getValue($objRS2,"link_img");
			$arrIconConf[$intContIcons]["target"]   = getValue($objRS2,"target");
			$arrIconConf[$intContIcons]["width"]    = getValue($objRS2,"width");
			$arrIconConf[$intContIcons]["height"]   = getValue($objRS2,"height");
			$arrIconConf[$intContIcons]["field_master_detail"]   = getValue($objRS2,"field_master_detail");
			$arrIconConf[$intContIcons]["rotulo"]   = getTText(getValue($objRS2,"rotulo"),C_NONE);
			$intContIcons++;
		}
		(getValue($objRS2,"default_action")) ? $strAcaoDefault = getValue($objRS2,"link") : "";
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
<script language="JavaScript" type="text/javascript">
	var intCurrentPos = 1;
	var intCurrentPosMouse;
	var strDefaultAction = "<?php echo($strAcaoDefault); ?>";

	function aplicarFuncao(prValue){
		if(prValue != ""){
			location.href = prValue;
		}
	}
	
	function setOrderBy(prStrOrder,prStrDirect){
		location.href = "<?php echo(getsession($strSesPfx . "_grid_default")); ?>?var_cod_dialog_grid=<?php echo($intCodDialogGrid); ?>&var_order_column=" + prStrOrder + "&var_order_direct=" + prStrDirect;
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
	
	<?php if($strAcao == "single"){ ?>
	function retorna(prValue){ 
		var campo = top.window.opener.document.<?php echo($strFormName . "." . $strFieldName); ?>;
		campo.value = prValue;
		top.window.close();
	}
	<?php } ?>
	
	var somaCurrentPosDetailUp = 1;
	var somaCurrentPosDetailDown = 1;
	var voltaSetaDown = 1;
	function navigateRow(e){
		if(!e) { e = window.event; }

		objTable = document.getElementById("tableContent");

		if(e.keyCode == 40){
			switchColor(objTable.rows[intCurrentPos], "");
			if(intCurrentPos < objTable.rows.length-2){
				intCurrentPos += somaCurrentPosDetailUp;
				switchColor(objTable.rows[intCurrentPos], "#CCCCCC");
			}
			else{
				intCurrentPos = objTable.rows.length-1;
			}
			
		}
		else if(e.keyCode == 38){
			switchColor(objTable.rows[intCurrentPos], "");
			if(intCurrentPos > 2){
				intCurrentPos -= somaCurrentPosDetailDown;
				switchColor(objTable.rows[intCurrentPos], "#CCCCCC");
			}
			else{
				intCurrentPos = voltaSetaDown;
			}
		} 
		else if ((e.keyCode == 0 || e.keyCode == null) && e.type == "mouseover") {
			switchColor(objTable.rows[intCurrentPos], "");
			switchColor(objTable.rows[intCurrentPosMouse], "#CCCCCC");
			intCurrentPos = intCurrentPosMouse;
		}
		else if (e.keyCode == 13){
			if(strDefaultAction != "" && objTable.rows[intCurrentPos].cells[1] != null){
				location.href = strDefaultAction.replace("{0}",objTable.rows[intCurrentPos].cells[1].innerHTML);
			}
		}else if(e.keyCode == 39){
			proximaPagina = parseInt(document.formpaginacao.var_curpage.value) + 1;
			paginar(proximaPagina);
		}else if(e.keyCode == 37){
			paginaAnterior = parseInt(document.formpaginacao.var_curpage.value) - 1;
			paginar(paginaAnterior);
		}
		
		if (e.keyCode != 8 && e.keyCode != 13 && (!(e.keyCode > 47 && e.keyCode < 58) && !(e.keyCode > 95 && e.keyCode < 106))){
			return false;
		}
	}
	
	document.onkeydown = navigateRow;
	
	
	var allTrTags = new Array();
	var detailTrFrameAnt = '';
	var moduloDetailAnt = '';
	function showDetailGrid(prChave_reg,prLink, prField){

		if(prLink.indexOf("?") == -1){
			strConcactQueryString = "?"
		}else{
			strConcactQueryString = "&"
		}
		var detailTr = document.getElementById("detailtr_"+prChave_reg).style.display;
		if(detailTr == 'none'){
			 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo CFG_SYSTEM_NAME ?>_detailiframe_"+prChave_reg);
	
			var allTrTags  = document.getElementsByTagName("tr");
			for( i=0; i < allTrTags.length; i++){
				if(allTrTags[i].className == 'iframe_detail'){
					allTrTags[i].style.display = 'none';
				}
			}
			document.getElementById("detailtr_"+prChave_reg).style.display = '';
		}else{
			if( moduloDetailAnt == prLink){
					document.getElementById("detailtr_"+prChave_reg).style.display = 'none';
			}else{
				if(detailTrFrameAnt != "detailtr_"+prChave_reg ){
					 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo CFG_SYSTEM_NAME ?>_detailiframe_"+prChave_reg);
				}
			}

		}
		moduloDetailAnt = prLink;
	}

	function ativaMenu(){
		over = function() {
			var sfEls = document.getElementById("menu_img").getElementsByTagName("li");
			for (var i = 0; i < sfEls.length; i++) {
				sfEls[i].onmouseover = function() {
					this.className += " over";
				}
				sfEls[i].onmouseout = function() {
					this.className = this.className.replace(new RegExp(" over\\b"), "");
				}
			}
		}
		if (window.attachEvent) window.attachEvent("onload", over);
	}

	function SetIFrameSource(prPage,prId) {
		document.getElementById(prId).src = prPage;
	}
	
	<?php
		if(getsession($strSesPfx . "_field_detail") != ''){
	?>
			window.onload = function(){
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = document.body.scrollHeight + 15;
			}
	<?php 
		}
	?>
</script>
</head>
<body bgcolor="#FFFFFF" <?php echo($strBodyStyle); ?>>
<center>
<?php 
  $strIdFrameResize = '';	
  if($intCodDialogGrid == "") { athBeginWhiteBox("98%"); }
?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<?php if($intCodDialogGrid == "") { ?>
		<tr>
			<td class="padrao_gde" align="left" width="50%" valign="top"><b><?php echo(getTText(getsession($strSesPfx . "_titulo"),C_NONE)); ?></b></td>
			<td align="right"><?php 
			    if($strAcao == "" && getsession($strSesPfx . "_menucombo_rotulo") != "" && getsession($strSesPfx . "_menucombo_valores") != "")
				{ 
					$strTpMenu 		= strtoupper(getsession($strSesPfx . "_menucombo_tipo"));
					$arrMenuRotulo  = explode(";",getsession($strSesPfx . "_menucombo_rotulo"));
					$arrMenuValor   = explode(";",getsession($strSesPfx . "_menucombo_valores"));
					$intI = 0;
					switch($strTpMenu)
					{
						case 'TABS':
											echo("<div id='menu_combo_tab'>");
											echo("  <ul>");
											while($intI < count($arrMenuRotulo)){			
												echo("<li title='" . getTText(trim($arrMenuRotulo[$intI]),C_NONE) . "'>");
												echo("	<a href='' onclick=\"javascript:aplicarFuncao('" . trim($arrMenuValor[$intI]) . "');return false;\" target='" . CFG_SYSTEM_NAME . "_frmain" . "'><span>" . getTText(trim($arrMenuRotulo[$intI]),C_NONE) . "</span></a>");
												echo("</li>");
												$intI++;
											}
											echo("</ul></div>");
											break;

						case 'MENU':
											echo("<script>ativaMenu();</script>");
											echo("<div style='float:right;height:22px'>");
											echo("<ul id='menu_img'><li class='menuRotulo'><ul class='submenu_img'>");
											while($intI < count($arrMenuRotulo)){
												echo("<li><img src='../img/bulletBusca.gif' align='absmiddle' border='0'><a href='' target='" . CFG_SYSTEM_NAME . "_frmain" . "' onclick=\"javascript:aplicarFuncao('" . trim($arrMenuValor[$intI]) . "');return false;\">" . getTText(trim($arrMenuRotulo[$intI]),C_NONE) . "</a></li>");
												$intI++;
											}
											echo("</ul></li></ul>");
											echo("</div>");
											break;
						
						case 'IMAGE':	//if (getsession($strSesPfx . "_menucombo_images") != "") {
											$arrMenuImg = explode(";",getsession($strSesPfx . "_menucombo_images"));
											while($intI < count($arrMenuRotulo)){
												if($arrMenuValor[$intI] != NULL){
													echo("<img onclick=\"javascript:aplicarFuncao('" . trim($arrMenuValor[$intI]) . "');\" src='". trim($arrMenuImg[$intI]) ."' title='". getTText(trim($arrMenuRotulo[$intI]),C_NONE) ."' alt='". getTText(trim($arrMenuRotulo[$intI]),C_NONE) . "' hspace='3'>\n");
												}
												$intI++;
											}
										//}
										break;
						case 'IMAGE_POPUP':	//if (getsession($strSesPfx . "_menucombo_images") != "") {
											$arrMenuImg = explode(";",getsession($strSesPfx . "_menucombo_images"));
											while($intI < count($arrMenuRotulo)){
												if($arrMenuValor[$intI] != NULL){
													echo("<img onclick=\"javascript:" . trim($arrMenuValor[$intI]) . "\" src='". trim($arrMenuImg[$intI]) ."' title='". getTText(trim($arrMenuRotulo[$intI]),C_NONE) ."' alt='". getTText(trim($arrMenuRotulo[$intI]),C_NONE) . "' hspace='3'>\n");
												}
												$intI++;
											}
										//}
										break;

						case 'BUTTOM':	while($intI < count($arrMenuRotulo)){
											if($arrMenuValor[$intI] != NULL){
												echo("<button onclick=\"javascript:aplicarFuncao('" . trim($arrMenuValor[$intI]) . "');\">" . getTText(trim($arrMenuRotulo[$intI]),C_NONE) . "</button>\n");
											}
											$intI++;
										}
										break;
						case 'BUTTOM_POPUP':	while($intI < count($arrMenuRotulo)){
											if($arrMenuValor[$intI] != NULL){
												echo("<button onclick=\"javascript:" . trim($arrMenuValor[$intI]) . " \">" . getTText(trim($arrMenuRotulo[$intI]),C_NONE) . "</button>\n");
											}
											$intI++;
										}
										break;
						case 'COMBO';
						case '':		echo("<select name='var_action' onChange='aplicarFuncao(this.value);' style='width:auto;'>");
										echo("<option value=''>");
										echo(getTText("selecione",C_NONE)."...");  
										echo("</option>");
										while($intI < count($arrMenuRotulo)){
											if($arrMenuValor[$intI] != NULL){
												echo("<option value=\"" . $arrMenuValor[$intI] . "\">" . getTText(trim($arrMenuRotulo[$intI]),C_NONE) . "</option>\n");
											}
											$intI++;
										}
										echo("</select>");
										break;
					}
			    } 
			  ?></td>
		</tr>
		<?php } ?>
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
													
												$strClass = (getTText($strCampo,C_NONE) != " ") ? "class=\"titulo_grade\"" : "" ;
													
												echo("			<td " . $strClass . " width=\"99%\" nowrap>". getTText(preg_replace("/(.*)__/","",$strCampo),C_NONE) . "</td>
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
									$boolDetail = false;
									do{
										//verfica se os paramêtros "single" não estão vazios e coloca a função apropriada
										if($strAcao == "single"){
											$strOnClick = "onClick=\"retorna('" . getValue($objRS,0) . "');\" style=\"cursor:pointer\"";
										}
										else{
											$strOnClick = "";
										}
										
										echo("<tr bgcolor=\"" . $strBgColor . "\" onMouseOver=\"intCurrentPosMouse = this.rowIndex;navigateRow(event);\" " . $strOnClick . ">
												<td width=\"" . CL_LINK_WIDTH * $intContIcons . "\">");
										if($strAcao == "" && $intContIcons > 0){
											echo("
													<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"" . CL_LINK_WIDTH * $intContIcons . "\">
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
												
												if($arrConf["target"] == "detail"){
													$strComplementoLink = " onClick=\"showDetailGrid('".getValue($objRS,0)."','".$arrConf["link"]."','".$arrConf["field_master_detail"]."')\" ";
													$boolDetail = true;
												}else if($arrConf["target"] == "popup"){
													$strComplementoLink = " onClick=\"window.open('" . $arrConf["link"] . "','','width=" . $arrConf["width"] . ",height=" . $arrConf["height"] . ",scrollbars=1');\" ";
												}else if($arrConf["target"] == "javascript"){
													$strComplementoLink = " onClick=\"". $arrConf["link"] ."\" ";
												}else{

													$strComplementoLink = "href=\"" . $arrConf["link"] . "\" target=\"" . $arrConf["target"] . "\"";
												}
												
												echo(" <td width='" . CL_LINK_WIDTH . "'>
																<a " .$strComplementoLink. " style=\"cursor:pointer\"" 
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
												echo("<td height=\"22\" align=\"left\" style=\"padding:0px 5px;\">");
												if(is_date($strDado)) {
													$strDado = (strpos($strDado,":") !== false) ? dDate(CFG_LANG,$strDado,true) : dDate(CFG_LANG,$strDado,false); 
												}
												if(ereg("^status_img_(.*)",$strDado)){
													$strDado = str_replace("status_img_","",$strDado);
													echo("<img src=\"../img/imgstatus_" . $strDado . ".gif\" title=\"" . getTText($strDado,C_TOUPPER) . "\" hspace=\"2\"></td>");
												}
												else{
													echo($strDado . "</td>");
												}
											}
											$intI++;
										}
										
										
										echo("</tr>");
										if($boolDetail){
											$strIdFrame = CFG_SYSTEM_NAME."_detailiframe_".getValue($objRS,0);
											echo("<tr  id=\"detailtr_" .getValue($objRS,0). "\" bgColor=\"".$strBgColor."\" style=\"display:none;\" class=\"iframe_detail\">
													<td colspan=\"".$intI."\">
											<iframe name=\"".$strIdFrame."\" id=\"".$strIdFrame."\" width=\"99%\" src=\"\" frameborder=\"0\" 
											 scrolling=\"no\" >
											</iframe>
													</td>
												</tr>");
											   $strIdFrameResize .= "'".$strIdFrame."',"; 		
										}
										$strBgColor = ($strBgColor == "") ? "#FFFFFF" : "";
									} while($objRS = $objResult->fetch());
									if($boolDetail){
										echo ("<script>
											 somaCurrentPosDetailUp = 2;
											 somaCurrentPosDetailDown = 2;
											 voltaSetaDown = 0;
										</script>");
									}
									
									/******** Conteúdo da grade - [Fim]    ********/
								?>
								<tr><td colspan="<?php echo(intval(($intI/2) - 1)); ?>" height="1"></td></tr>
							</table>						</td>
					</tr>
				</table>
				<?php
					} 
					else{
						mensagem("alert_consulta_vazia_titulo", "alert_consulta_vazia_desc", "", "", "aviso", 0);
					}
				?>			</td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr><td height="3" colspan="2" bgcolor="#BFBFBF"></td></tr>
		<tr><td colspan="2" height="3"></td></tr>
		<?php if($objResult->rowCount() > 0 && !$boolIsExportation && $intNumPerPage != ""){ ?>
		<tr>
			<td align="left"><?php echo($intTotalRegistros . " " . getTText("reg_encontrados",C_TOLOWER)); ?></td>
			<td align="right">
				<table border="0" cellpadding="0" cellspacing="0">
				  <form name="formpaginacao" action="data.php" method="post">
					<input type="hidden" name="var_order_column" value="<?php echo($strOrderCol); ?>">
					<input type="hidden" name="var_order_direct" value="<?php echo($strOrderDir); ?>">
					<input type="hidden" name="var_cod_dialog_grid" value="<?php echo($intCodDialogGrid); ?>">
					<tr>
						<td><img src="../img/grid_arrow_left.gif" onClick="paginar(<?php echo($intNumCurPage - 1)?>)"></td>
						<td style="padding:0px 10px 0px 10px;"><?php echo(getTText("pagina",C_TOLOWER)); ?> <input type="text" name="var_curpage" value="<?php echo($intNumCurPage)?>" size="3"> <?php echo(getTText("de",C_TOLOWER) . " " . $intTotalPaginas); ?></td>
						<td><img src="../img/grid_arrow_right.gif" onClick="paginar(<?php echo($intNumCurPage + 1)?>)"></td>
					</tr>
				  </form>
				</table>			</td>
		</tr>
		<?php } ?>
	</table>
 <?php if($intCodDialogGrid == "") { athEndWhiteBox(); } ?>
</center>
 </body>
</html>
<?php
$objResult->closeCursor();
$objConn = NULL;
?>