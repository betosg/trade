<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
error_reporting(E_ALL);

/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"MAN"); //Verificação de acesso do usuário corrente


/***           DEFINIÇÃO DE CONSTANTES             ***/
/*****************************************************/
define("ICONES_NUM"        ,2);     // NÚMERO DE ÍCONES DA GRADE
define("ICONES_WIDTH"      ,20);    // LARGURA DOS ÍCONES DA GRADE
define("GRADE_NUM_ITENS"   ,getsession($strSesPfx . "_num_per_page"));    // NÚMERO DE ITENS DA GRADE (PAGINAÇÃO)
define("GRADE_ACAO_DEFAULT","");    // AÇÃO PADRÃO DA TECLA ENTER NA GRADE
define("ARQUIVO_LEITURA"   ,"STconfiginc.php"); // AÇÃO PADRÃO DA TECLA ENTER NA GRADE


/***           DEFINIÇÃO DE PARÂMETROS            ***/
/****************************************************/
$strOrderCol      = request("var_order_column");   // Índice da coluna para ordenação
$strOrderDir      = request("var_order_direct");   // Direção da ordenação (ASC ou DESC)
$intNumCurPage    = request("var_curpage");        // Página corrente
$strAcao   	      = request("var_acao");           // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$strSQLParam      = request("var_sql_param");      // Parâmetro com o SQL vindo do bookmark
$strPopulate      = request("var_populate");       // Flag de verificação se necessita popular o session ou não
$strIndice        = request("var_indice");         // Campo de filtro
$strValor         = request("var_valor");          // Campo de filtro


/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
/****************************************************/
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

/***        FUNÇÕES AUXILIARES - OPCIONAL         ***/
/****************************************************/
function my_array_filter($array,$function,$preserve=true) {   
	$return = array();
	foreach ($array as $k=>$v) {
		if($function($k,$v)) $return[$k]=$v;
	}
	return ($preserve) ? $return : array_values($return);
}

function filter($prIndex, $prValue) {
	global $strIndice, $strValor;
	
	return (($strIndice == "" || strpos($prIndex,$strIndice) !== false) && ($strValor == "" || strpos($prValue,$strValor) !== false)) ? true : false;
}

/***        AÇÃO DE EXPORTAÇÃO DA GRADE          ***/
/***************************************************/
//Define uma variável booleana afim de verificar se é um tipo de exportação ou não
$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc") || ($strAcao == ".pdf");

//Exportação para excel, word e adobe reader
if($boolIsExportation) {
	if($strAcao == ".pdf") {
		redirect("exportpdf.php"); //Redireciona para página que faz a exportação para adode reader
	} else {
		//Coloca o cabeçalho de download do arquivo no formato especificado de exportação
		header("Content-type: application/force-download"); 
		header("Content-Disposition: attachment; filename=Modulo_" . getTText(getsession($strSesPfx . "_titulo"),C_NONE) . "_". time() . $strAcao);
	}
	
	$strLimitOffSet = "";
} 

/***        AÇÃO DE ALIMENTAÇÃO DA GRADE         ***/
/***************************************************/
$arrArquivo = my_array_filter($_SESSION,"filter"); //Aplica o filtro no array do arquivo

/***              PAGINAÇÃO DA GRADE             ***/
/***************************************************/
if($intNumCurPage == "") { $intNumCurPage = 1; }

$intTotalRegistros = count($arrArquivo);                         // Total de Registros

if(GRADE_NUM_ITENS != "") {
	$intTotalPaginas   = ceil($intTotalRegistros / GRADE_NUM_ITENS); // Total de Páginas
	$intLinha          = 0;                                          // Inicializador do contador de linhas;
	$arrArquivoAux     = array_slice($arrArquivo,GRADE_NUM_ITENS * ($intNumCurPage - 1),GRADE_NUM_ITENS,true); //Arquivo já paginado
} else {
	$intTotalPaginas   = 1;
	$intLinha          = 0;
	$arrArquivoAux     = $arrArquivo;
}
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
	<script language="javascript" type="text/javascript">
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
				
			} else if(e.keyCode == 38){
				switchColor(objTable.rows[intCurrentPos], "");
				if(intCurrentPos > 2){
					intCurrentPos -= somaCurrentPosDetailDown;
					switchColor(objTable.rows[intCurrentPos], "#CCCCCC");
				} else {
					intCurrentPos = voltaSetaDown;
				}
			} else if ((e.keyCode == 0 || e.keyCode == null) && e.type == "mouseover") {
				switchColor(objTable.rows[intCurrentPos], "");
				switchColor(objTable.rows[intCurrentPosMouse], "#CCCCCC");
				intCurrentPos = intCurrentPosMouse;
			} else if (e.keyCode == 13) {
				if(strDefaultAction != "" && objTable.rows[intCurrentPos].cells[1] != null){
					location.href = strDefaultAction.replace("{0}",objTable.rows[intCurrentPos].cells[1].innerHTML);
				}
			} else if(e.keyCode == 39) {
				proximaPagina = parseInt(document.formpaginacao.var_curpage.value) + 1;
				paginar(proximaPagina);
			} else if(e.keyCode == 37) {
				paginaAnterior = parseInt(document.formpaginacao.var_curpage.value)  - 1;
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
			<td align="right"><button onClick="javascript:aplicarFuncao('STinsert.php');">inserir</button></td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr>
			<td colspan="2">
				<?php if($intTotalRegistros > 0){ ?> <!-- SE A CONSULTA VIER VAZIA NÃO PASSA AQUI, ENTRARÁ NO ELSE DESSE IF -->
				<table cellpadding="0" cellspacing="3" width="100%" style="border:1px #EEEEEE solid;" bgcolor="#F7F7F7">
					<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
					<tr>
						<td>
							
							<table id="tableContent" border="0" cellpadding="0" cellspacing="0" width="100%" background="../img/grid_backheader.gif" style="background-repeat:repeat-x;">
								<tr>
									<!-- CABEÇALHO DA GRADE - [INÍCIO] -->
									<td></td> <!-- Coloca uma coluna mesclada para ajustar a tabela com os ícones que virão abaixo -->
									<td height="22" class="titulo_grade"><?php echo(getTText("indice",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade"><?php echo(getTText("valor",C_UCWORDS)); ?></td>
									<!-- CABEÇALHO DA GRADE - [FIM] -->
								</tr>
								<tr><td colspan="4" height="3"></td></tr>
								<!-- CONTEÚDO DA GRADE - [INÍCIO] -->
								<?php 
									$strBgColor        = CL_CORLINHA_2;
									
									foreach($arrArquivoAux as $strIndex => $strValue) {
										$strBgColor = ($strBgColor == CL_CORLINHA_2) ? CL_CORLINHA_1 : CL_CORLINHA_2;
								?>
								<tr bgcolor="<?php echo($strBgColor); ?>" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
									<td width="<?php echo(ICONES_WIDTH * ICONES_NUM); ?>">
										<table border="0" cellspacing="0" cellpadding="0" width="<?php echo(CL_LINK_WIDTH * NUM_ICONES); ?>">
											<tr>
												<td width="<?php echo(ICONES_WIDTH)?>">
													<?php if(!preg_match("/^" . CFG_SYSTEM_NAME . "/i",$strIndex)) { ?>
													<a href="STdelete.php?var_chavereg=<?php echo($strIndex); ?>"><img src="../img/icon_trash.gif" border="0"></a>
													<?php } ?>
												</td>
												<td width="<?php echo(ICONES_WIDTH); ?>">
													<a href="STupdate.php?var_chavereg=<?php echo($strIndex); ?>"><img src="../img/icon_write.gif" border="0"></a>
												</td>
											</tr>
										</table>
									</td>
									<td height="22" align="left" style="padding:0px 5px;"><?php echo($strIndex); ?></td>
									<td height="22" align="left" style="padding:0px 5px;"><?php echo(htmlentities($strValue)); ?></td>
								</tr>
								<?php
										$intLinha++;
									}
								?>
								<tr><td colspan="4" height="3"></td></tr>
							</table>
						</td>
					</tr>
				</table>
				<?php
					} else {
						mensagem("alert_consulta_vazia_titulo", "alert_consulta_vazia_desc", "", "", "aviso", 0);
					}
				?>			
			</td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr><td height="3" colspan="2" bgcolor="#BFBFBF"></td></tr>
		<tr><td colspan="2" height="3"></td></tr>
		<?php if(!$boolIsExportation && $intLinha > 0 && GRADE_NUM_ITENS != 0) { ?>
		<tr>
			<td align="left"><?php echo($intTotalRegistros . " " . getTText("reg_encontrados",C_TOLOWER)); ?></td>
			<td align="right">
				<table border="0" cellpadding="0" cellspacing="0">
				  <form name="formpaginacao" action="STdata.php" method="post">
					<input type="hidden" name="var_order_column" value="<?php echo($strOrderCol); ?>">
					<input type="hidden" name="var_order_direct" value="<?php echo($strOrderDir); ?>">
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
if ($intLinha == 1) {
?>
<script language="JavaScript" type="text/javascript">
	if(strDefaultAction != ""){
		objTableDummy = document.getElementById("tableContent");
		location.href = strDefaultAction.replace("{0}",objTableDummy.rows[2].cells[1].innerHTML);
	}
</script>
<?php } ?>