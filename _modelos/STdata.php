<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
 
$objConn = abreDBConn(CFG_DB); // Abertura de banco

/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente


/***           DEFINIÇÃO DE CONSTANTES             ***/
/*****************************************************/
define("ICONES_NUM"     ,2);     // NÚMERO DE ÍCONES DA GRADE
define("ICONES_WIDTH"   ,20);    // LARGURA DOS ÍCONES DA GRADE
define("GRADE_NUM_ITENS",20);    // NÚMERO DE ITENS DA GRADE (PAGINAÇÃO)
define("GRADE_ACAO_DEFAULT",""); // AÇÃO PADRÃO DA TECLA ENTER NA GRADE


/***           DEFINIÇÃO DE PARÂMETROS            ***/
/****************************************************/
$strOrderCol      = request("var_order_column");   // Índice da coluna para ordenação
$strOrderDir      = request("var_order_direct");   // Direção da ordenação (ASC ou DESC)
$intNumCurPage    = request("var_curpage");        // Página corrente
$strAcao   	      = request("var_acao");           // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$strSQLParam      = request("var_sql_param");      // Parâmetro com o SQL vindo do bookmark
$strPopulate      = request("var_populate");       // Flag de verificação se necessita popular o session ou não


/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
/****************************************************/
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo


/***        AÇÃO DE EXPORTAÇÃO DA GRADE          ***/
/***************************************************/
//Define uma variável booleana afim de verificar se é um tipo de exportação ou não
$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc") || ($strAcao == ".pdf");

//Exportação para excel, word e adobe reader
if($boolIsExportation){
	if($strAcao == ".pdf") {
		redirect("exportpdf.php"); //Redireciona para página que faz a exportação para adode reader
	}
	else{
		//Coloca o cabeçalho de download do arquivo no formato especificado de exportação
		header("Content-type: application/force-download"); 
		header("Content-Disposition: attachment; filename=Modulo_" . getTText(getsession($strSesPfx . "_titulo"),C_NONE) . "_". time() . $strAcao);
	}
	
	$strLimitOffSet = "";
} 

/***        AÇÃO DE ALIMENTAÇÃO DA GRADE         ***/
/***************************************************/

$intTotalRegistros = 20;

?>
<html>
  <head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<?php 
		if(!$boolIsExportation || $strAcao == "print"){
			echo("
				  <link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\">
				  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
				");
		}
	?>
	<script language="JavaScript" type="text/javascript">
		var intCurrentPos = 1;
		var intCurrentPosMouse;
		var strDefaultAction = "<?php echo(GRADE_ACAO_DEFAULT); ?>"; 
		var intTotalPaginas = parseInt("<?php echo($intTotalPaginas); ?>");

		function aplicarFuncao(prValue) {
			if(prValue != "") {
				location.href = prValue;
			}
		}
		
		function setOrderBy(prStrOrder,prStrDirect) {
			location.href = "<?php echo(getsession($strSesPfx . "_grid_default")); ?>?var_order_column=" + prStrOrder + "&var_order_direct=" + prStrDirect;
		}
		
		function paginar(prPagina){
			if(prPagina > 0 && prPagina <= intTotalPaginas){
				document.formpaginacao.var_curpage.value = prPagina;
				document.formpaginacao.submit();
			}	
		}
		
		function switchColor(prObj, prColor){
			prObj.style.backgroundColor = prColor;
		}

		
		var somaCurrentPosDetailUp = 1;
		var somaCurrentPosDetailDown = 1;
		var voltaSetaDown = 1;
		function navigateRow(e) {
			if(!e) { e = window.event; }

			objTable = document.getElementById("tableContent");

			if(e.keyCode == 40){
				switchColor(objTable.rows[intCurrentPos], "");
				if(intCurrentPos < objTable.rows.length-2) {
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
			else if (e.keyCode == 13) {
				if(strDefaultAction != "" && objTable.rows[intCurrentPos].cells[1] != null){
					location.href = strDefaultAction.replace("{0}",objTable.rows[intCurrentPos].cells[1].innerHTML);
				}
			}else if(e.keyCode == 39) {
				proximaPagina = parseInt(document.formpaginacao.var_curpage.value) + 1;
				paginar(proximaPagina);
			}else if(e.keyCode == 37) {
				paginaAnterior = parseInt(document.formpaginacao.var_curpage.value) - 1;
				paginar(paginaAnterior);
			}
			
			if (e.keyCode != 8 && e.keyCode != 13 && (!(e.keyCode > 47 && e.keyCode < 58) && !(e.keyCode > 95 && e.keyCode < 106))){
				return false;
			}
		}
		
		document.onkeydown = navigateRow;
	</script>
  </head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<center>
<?php athBeginWhiteBox("98%"); ?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="padrao_gde" align="left" width="50%" valign="top"><b><?php echo(getTText(getsession($strSesPfx . "_titulo"),C_NONE)); ?></b></td>
			<td align="right"><!-- AQUI LOCAL PARA COLOCAR O MENU --></td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr>
			<td colspan="2">
				<?php if(true){ ?> <!-- SE A CONSULTA VIER VAZIA NÃO PASSA AQUI, ENTRARÁ NO ELSE DESSE IF -->
				<table cellpadding="0" cellspacing="3" width="100%" style="border:1px #EEEEEE solid;" bgcolor="#F7F7F7">
					<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
					<tr>
						<td>
							
							<table id="tableContent" border="0" cellpadding="0" cellspacing="0" width="100%" background="../img/grid_backheader.gif" style="background-repeat:repeat-x;">
								<tr>
									<!-- CABEÇALHO DA GRADE - [INÍCIO] -->
									<td></td> <!-- Coloca uma coluna mesclada para ajustar a tabela com os ícones que virão abaixo -->
									<td height="22">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td width="1%">
													<table border="0" cellpadding="0" cellspacing="0" width="100%">
														<tr><td><a href="javascript:setOrderBy('0','ASC');"><img src="../img/gridlnkASC.gif"  border="0" align="absmiddle"></a></td></tr>
														<tr><td><a href="javascript:setOrderBy('0','DESC');"><img src="../img/gridlnkDESC.gif" border="0" align="absmiddle"></a></td></tr>
													</table>
												</td>
												<td class="titulo_grade" width="99%" nowrap>#ROTULO COLUNA</td>
											</tr>
										</table>
									</td>
									<!-- CABEÇALHO DA GRADE - [FIM] -->
								</tr>
								<tr><td colspan="<?php //Numero de colunas na tabela ?>" height="3"></td></tr>
								<!-- CONTEÚDO DA GRADE - [INÍCIO] -->
								<?php 
									$strBgColor = CL_CORLINHA_2;
									
									for($intI;$intI < 20;$intI++) { 
										
										$strBgColor = ($strBgColor == CL_CORLINHA_2) ? CL_CORLINHA_1 : CL_CORLINHA_2
								?>
								<tr bgcolor="<?php echo($strBgColor); ?>" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
									<td width="<?php echo(ICONES_WIDTH * ICONES_NUM); ?>">
										<table border="0" cellspacing="0" cellpadding="0" width="<?php echo(CL_LINK_WIDTH * NUM_ICONES); ?>">
											<tr>
												<td width="<?php echo(ICONES_WIDTH)?>">xxx</td>
												<td width="<?php echo(ICONES_WIDTH)?>">xxx</td>
											</tr>
										</table>
									</td>
									<td height="22" align="left" style="padding:0px 5px;">#ITENS</td>
								</tr>
								<?php
									}
								?>
								<tr><td colspan="<?php //Numero de colunas na tabela ?>" height="3"></td></tr>
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
		<tr><td height="3" colspan="2" bgcolor="#BFBFBF"></td></tr>
		<tr><td colspan="2" height="3"></td></tr>
		<?php if(true && !$boolIsExportation && GRADE_NUM_ITENS != 0){ ?>
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
				</table>
			</td>
		</tr>
		<?php } ?>
	</table>
 <?php athEndWhiteBox(); ?>
</center>
 </body>
</html>
<?php
//Se a grade tem uma ação default e ela retornou apenas uma linha ela dispara a ação default
//Atenção: usamos índice rows[2] devido ao leiaute da grade que tem duas linhas antes dos dados (barra e cabeçalho)
if ($intTotalRegistros == 1) {
?>
<script language="JavaScript" type="text/javascript">
	if(strDefaultAction != ""){
		objTableDummy = document.getElementById("tableContent");
		location.href = strDefaultAction.replace("{0}",objTableDummy.rows[2].cells[1].innerHTML);
	}
</script>
<?php
}
$objConn = NULL;
?>