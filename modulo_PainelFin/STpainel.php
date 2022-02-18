<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE"); //Verificação de acesso do usuário corrente


/***           DEFINIÇÃO DE PARÂMETROS            ***/
/****************************************************/
$strAcao = request("var_acao"); // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

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

$objConn = abreDBConn(CFG_DB);

?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<?php 
			if(!$boolIsExportation || $strAcao == "print") {
				echo("
					  <link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\" type=\"text/css\">
					  <link href=\"../_css/tablesort.css\" rel=\"stylesheet\" type=\"text/css\">
					  <script type=\"text/javascript\" src=\"../_scripts/tablesort.js\"></script>
					");
			}
		?>
		<script language="javascript" type="text/javascript">
			function switchColor(prObj, prColor){
				prObj.style.backgroundColor = prColor;
			}
		</script>
		<style>
			body { margin:10px 0px; }
			ul	 { margin-top: 0px; margin-bottom: 0px; }
			li	 { margin-left: 0px; }
		</style>
	</head>
	<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="left" style="padding-left: 15px;">
					<?php include("STinfotitulos.php"); ?><br>
					<?php include("STinfofichas.php"); ?><br>
					<?php include("STinfoagenda.php"); ?><br>
					<?php include("STinfopedidos.php"); ?>
				</td>
			</tr>
		</table>
	</body>
</html>
<?php $objConn = NULL; ?>
