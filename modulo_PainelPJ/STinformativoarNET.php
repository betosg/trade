<?php 
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	
	$intCodDado = getSession(CFG_SYSTEM_NAME."_pj_selec_codigo");
	
	
		
		// Função para cores de linhas
		function getLineColor(&$prColor){
			$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
			echo($prColor);
		}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../_tradeunion/_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<title><?php echo(strtoupper(CFG_SYSTEM_NAME)." - Solicitação Guia Avulsa");?></title>
<style type="text/css">
	.span_manual{
		float:right;
		background-image:url(../img/icon_document_pdf.png);
		background-repeat:no-repeat;
		background-position:right;
		height:15px;
		padding-top: 2px;
		padding-right:20px;
		margin-right:5px;
		cursor:pointer;
		font-size:10px;
		color:#009900;
		font-weight:600;
	}
</style>

</head>
<body bgcolor="#F5F5F5"  background="../../_tradeunion/img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_collapsed.jpg">
  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="middle">
    	  <?php athBeginFloatingBox("720","none","<strong>Aviso Importante</strong>",CL_CORBAR_GLASS_1); ?>
		     <table width="700" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
                   <tr><td colspan="2" height="10">&nbsp;</td></tr>
                   <tr>
				   		<td align="center" width="100%" valign="top" style="padding:0px 80px 0px 80px;">               
                         	<?php echo(mensagem("info_aviso_importante_titulo","info_aviso_importante_desc","","","aviso",1)); ?>
                   		</td>
                   </tr>
              </table>
          <?php athEndFloatingBox(); ?>
		</td>
      </tr>	   
    </table>     	
</body>
</html>