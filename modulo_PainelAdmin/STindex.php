<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

/***            VERIFICA��O DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE"); //Verifica��o de acesso do usu�rio corrente

/***           DEFINI��O DE CONSTANTES             ***/
/*****************************************************/
define("ICONES_NUM"        ,2);     // N�MERO DE �CONES DA GRADE
define("ICONES_WIDTH"      ,20);    // LARGURA DOS �CONES DA GRADE
define("GRADE_NUM_ITENS"   ,getsession($strSesPfx . "_num_per_page"));    // N�MERO DE ITENS DA GRADE (PAGINA��O)
define("GRADE_ACAO_DEFAULT","");    // A��O PADR�O DA TECLA ENTER NA GRADE
define("ARQUIVO_LEITURA"   ,"STconfiginc.php"); // A��O PADR�O DA TECLA ENTER NA GRADE


/***           DEFINI��O DE PAR�METROS            ***/
/****************************************************/
$strOrderCol      = request("var_order_column");   // �ndice da coluna para ordena��o
$strOrderDir      = request("var_order_direct");   // Dire��o da ordena��o (ASC ou DESC)
$intNumCurPage    = request("var_curpage");        // P�gina corrente
$strAcao   	      = request("var_acao");           // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade � exibida normalmente.
$strSQLParam      = request("var_sql_param");      // Par�metro com o SQL vindo do bookmark
$strPopulate      = request("var_populate");       // Flag de verifica��o se necessita popular o session ou n�o
$strIndice        = request("var_indice");         // Campo de filtro
$strValor         = request("var_valor");          // Campo de filtro


/***    A��O DE PREPARA��O DA GRADE - OPCIONAL    ***/
/****************************************************/
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos �tens do m�dulo

/***        FUN��ES AUXILIARES - OPCIONAL         ***/
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

/***        A��O DE EXPORTA��O DA GRADE          ***/
/***************************************************/
//Define uma vari�vel booleana afim de verificar se � um tipo de exporta��o ou n�o
$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc") || ($strAcao == ".pdf");

//Exporta��o para excel, word e adobe reader
if($boolIsExportation) {
	//Coloca o cabe�alho de download do arquivo no formato especificado de exporta��o
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
		/*suas adapta��es css aqui*/
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
