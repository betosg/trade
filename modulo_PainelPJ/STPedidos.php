<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente


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

$strExibir = request("var_exibir");
if ($strExibir == "") $strExibir = "faturar";

/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
/****************************************************/
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

/***        FUNÇÕES AUXILIARES - OPCIONAL         ***/
/****************************************************/
function filtro($prValue) {
	global $strIndice, $strValor;
	
	$strLine = trim($prValue);
	
	$strLine = preg_replace("/define\(|\)\;(.*)/i","",$strLine);
	$arrLine = explode(",",$strLine);
	
	$arrLine[0] = str_replace("\"","",$arrLine[0]);
	$strLineValor = (isset($arrLine[1])) ? $arrLine[1] : "";
	
	if(($strIndice == "" || strpos($arrLine[0],$strIndice) !== false) 
	   && ($strValor == "" || strpos($strLineValor,$strValor) !== false) 	
	   && (trim($strLine) != "" && trim($strLine) != "<?php" && trim($strLine) != "?>")
	   && (strpos($strLine,"//") === false)) {
	    return($prValue);
	}
}

/***        AÇÃO DE EXPORTAÇÃO DA GRADE          ***/
/***************************************************/
//Define uma variável booleana afim de verificar se é um tipo de exportação ou não
$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc") || ($strAcao == ".pdf");

//Exportação para excel, word e adobe reader
if($boolIsExportation) {
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

$objConn = abreDBConn(CFG_DB); // Abertura de banco

$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");

if ($intCodDado != "") {
?>
<script>
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
	
	function SetIFrameSource(prPage,prId) {
		document.getElementById(prId).src = prPage;
	}
	
	<?php if(getsession($strSesPfx . "_field_detail") != '') { 	?>
			window.onload = function(){
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = document.body.scrollHeight + 15;
			}
	<?php }	?>
	
</script>
<html>
  <head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<?php 
		if(!$boolIsExportation || $strAcao == "print"){
			echo("
				  <link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\">
			      <link href='../_css/tablesort.css' rel='stylesheet' type='text/css'>
			      <script type='text/javascript' src='../_scripts/tablesort.js'></script>
			      <style>
			  	    ul{ margin-top: 0px; margin-bottom: 0px; }
				    li{ margin-left: 0px; }
			      </style>
			      <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
				");
		}
	?>
	<script language="JavaScript" type="text/javascript">
		function switchColor(prObj, prColor){
			prObj.style.backgroundColor = prColor;
		}
	</script>
  </head>
<body style="margin:10px 0px 10px 0px;" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table cellpadding="0" cellspacing="0" style="border:none;width:100%">
 <tr>
 	<td width="100%" align="left" style="border:none; background:none; padding-left: 15px;">
	<?php 
	try {
		$strSQL  = " SELECT t1.cod_pedido, t1.situacao, t1.it_tipo, t1.obs, t1.sys_dtt_ins, t1.it_descricao, t1.it_valor ";
		$strSQL .= "      , t2.nome, t2.sexo, t2.cpf, t3.gera_ped_on ";
		$strSQL .= "      , (CURRENT_TIMESTAMP - t1.sys_dtt_ins) > '1 hour' AS mais_de_uma_hora ";
		$strSQL .= " FROM prd_pedido t1 ";
		$strSQL .= " LEFT OUTER JOIN cad_pf t2 ON (t1.it_cod_pf = t2.cod_pf) ";
		$strSQL .= " LEFT OUTER JOIN prd_produto t3 ON (t1.it_cod_produto = t3.cod_produto) ";
		$strSQL .= " WHERE t1.cod_pj = " . $intCodDado;
		if ($strExibir == "faturar")    $strSQL .= " AND t1.situacao ILIKE 'aberto' ";
		if ($strExibir == "faturados")  $strSQL .= " AND t1.situacao ILIKE 'faturado' ";
		if ($strExibir == "cancelados") $strSQL .= " AND t1.situacao ILIKE 'cancelado' ";
		if ($strExibir == "de_card") $strSQL .= " AND t1.it_tipo ILIKE 'card' ";
		if ($strExibir == "de_homo") $strSQL .= " AND t1.it_tipo ILIKE 'homo' ";
		$strSQL .= " ORDER BY t2.nome, t1.cod_pedido ";
		
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	athBeginFloatingBox("100%","","<b>".getTText("pedidos",C_NONE)."</b>",CL_CORBAR_GLASS_2);
	?>
	<table bgcolor="#FFFFFF" style=" width:100%;  margin-bottom:0px;" class="tablesort">
		<?php
		if($objResult->rowCount() > 0) {
			?>
			<thead>
			<tr>
				<th width="1%"></th>
				<th width="5%" class="sortable"><?php echo getTText("cod",C_NONE); ?></th>
				<th width="4%" class="sortable"><?php echo getTText("tipo",C_NONE); ?></th>
				<th width="8%" class="sortable"><?php echo getTText("situacao",C_NONE); ?></th>
				<th width="26%" class="sortable"><?php echo getTText("descricao",C_NONE); ?></th>
				<th width="8%" class="sortable-currency" align="right"><?php echo getTText("valor",C_NONE); ?></th>
				<th width="26%" class="sortable" align="left"><?php echo getTText("colaborador",C_NONE); ?></th>
				<th width="8%" class="sortable"><?php echo getTText("cpf",C_NONE); ?></th>
				<th width="14%" class="sortable-date-dmy"><?php echo getTText("solicitacao",C_NONE); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($objResult as $objRS){
				?>
				<tr>
					<?php 
					//pedidos de card devem ser removidos removemndo a PF primeiro 
					?>
					<td>
					<?php 
					if (getValue($objRS,"it_tipo") == "homo") {
						if ((getValue($objRS,"mais_de_uma_hora") == false) && (getValue($objRS,"situacao") == "aberto")) {
							echo "<a href='STDelPedido.php?var_chavereg=".getValue($objRS,"cod_pedido")."'><img src='../img/icon_trash.gif' alt='Remover' border='0'></a>";
						}
					} 
					else if ((getValue($objRS,"it_tipo") != "card") && (getValue($objRS,"situacao") == "aberto"))
							echo "<a href='STDelPedido.php?var_chavereg=".getValue($objRS,"cod_pedido")."'><img src='../img/icon_trash.gif' alt='Remover' border='0'></a>";
					?>
					</td>
					<td><?php echo(getValue($objRS,"cod_pedido")); ?></td>
					<td><?php echo(getValue($objRS,"it_tipo")); ?></td>
					<td><?php echo(getValue($objRS,"situacao")); ?></td>
					<td><?php echo(getValue($objRS,"it_descricao")); ?></td>
					<td align="right"><?php echo(number_format((double) getValue($objRS,"it_valor"),2,",","")); ?></td>
					<td><?php echo(getValue($objRS,"nome")); ?></td>
					<td><?php echo(getValue($objRS,"cpf")); ?></td>
					<td><?php echo(dDate(CFG_LANG, getValue($objRS,"sys_dtt_ins"), true)); ?></td>
				</tr>
				<?php 
			}
			?>
			</tbody>
			<?php 
		}
		else {
			?>
			<tbody>
			<tr>
				<td colspan="6" align="center"><div style="padding-top:2px; padding-bottom:2px;"><?php echo(getTText("alert_consulta_vazia_titulo",C_NONE)); ?></div></td>
			</tr>
			</tbody>
			<?php
		}
		?>
	</table>
	<?php
	athEndFloatingBox();
	$objResult->closeCursor();
	?>
	<br>
	</td>
 </tr>
</table>
</body>
</html>
<?php
}
else {
	echo(mensagem("err_selec_empresa_titulo","err_selec_empresa_desc","","","erro",1));
}
$objConn = NULL;
?>
