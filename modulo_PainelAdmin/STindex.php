<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE"); //Verificação de acesso do usuário corrente

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
	//Coloca o cabeçalho de download do arquivo no formato especificado de exportação
	header("Content-type: application/force-download"); 
	header("Content-Disposition: attachment; filename=Modulo_" . getTText(getsession($strSesPfx . "_titulo"),C_NONE) . "_". time() . $strAcao);
	
	$strLimitOffSet = "";
} 

$objConn = abreDBConn(CFG_DB);

?>
<html>
  <head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<style>
		/*suas adaptações css aqui*/
	</style>
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
		function redirecionaAgenda(){
			location.href="../_fontes/insupddelmastereditor.php?var_basename=modulo_Agenda&var_oper=INS7var_populate=yes";
		}
	</script>
</head>
<body style="margin:10px 10px 10px 10px;" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table cellpadding="0" cellspacing="0"  style="border:none; width:100%">
 <tr>
	<td width="100%" align="left" style="border:none; padding-left: 15px; vertical-align:top;">
		<?php include_once("../modulo_PainelTodolist/STdata.php"); ?><br /><br />
		<?php include_once("STInfoCadNovo.php"); ?><br /><br />
		<?php include_once("STInfoPedidos.php"); ?><br /><br />
		<?php include_once("STInfoCards.php"); ?><br /><br />
		<?php include_once("STAniverColaborador.php"); ?><br /><br />
		<?php //include_once("STInfoHomos.php"); ?>
	</td>
 </tr>
</table>
</body>
</html>
<?php
	$objConn = NULL;
?>
