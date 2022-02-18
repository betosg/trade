<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
erroReport();
$objConn = abreDBConn(CFG_DB); // Abertura de banco	
//$objConnSinog   = abreDBConn("tradeunion_sinog"); // Abertura de banco	
//$objConnSinange = abreDBConn("tradeunion_abramge"); // Abertura de banco	
//$objConnAbramge = abreDBConn("tradeunion_sinamge"); // Abertura de banco
//
//$strSQL = "SELECT cod_usuario,id_usuario, nome from sys_usuario where cod_usuario = 164365";
//$objResultAbramge = $objConnAbramge->query($strSQL);
//$objRS = $objResultAbramge->fetch();
//echo(getValue($objRS,"cod_usuario")." / ". getValue($objRS,"id_usuario")." / ". getValue($objRS,"nome"));
//		$intTotal = getValue($objRS,"total");
 //echo "iduser: ".getsession(CFG_SYSTEM_NAME . "_id_usuario");
 //echo "<br>grp_user: ".getsession(CFG_SYSTEM_NAME . "_grp_user");
if ((getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") == "") && (getsession(CFG_SYSTEM_NAME . "_grp_user") == 'NORMAL')) {
	$intCodPJ = '';
	$strNomePJ = '';
	
	getDadosPFSelected($objConn, "", getsession(CFG_SYSTEM_NAME . "_id_usuario"));
}

$strMsgBoasVindas = getVarEntidade($objConn, "msg_boas_vindas_ar");

if (getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") != "") {
	$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
	
	
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" rel="stylesheet" type="text/css">
</head>
<body style="margin:0px 0px 0px 0px;" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<img style="display:none" id="img_collapse">
<table width="100%" cellpadding="6" cellspacing="0" border="0" align="center">
<tr>
	<td width="99%" style="vertical-align:top">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td height="18" bgcolor="#DBDBDB"><div style="padding-left:5px;"><?php echo getTText("ola",C_NONE); ?>&nbsp;<strong><?php echo getsession(CFG_SYSTEM_NAME."_pj_selec_nome"); ?></strong></div></td>
		</tr>
		<tr><td height="5"></td></tr>
		<?php if ($strMsgBoasVindas != ""){?>
		<tr>
			<td height="89" bgcolor="#EDF2F6">
			<!--td height='89' bgcolor="#DBDBDB"-->
				<?php echo($strMsgBoasVindas);?>				
			</td>
		</tr>
		
	<?php }
		//$intAltura = 30;
	//	if (($intTotal_PedAbertos > 0) || ($intTotal_PedDeletados > 0)) $intAltura = 120;
		?>
		<!--tr>
			<td height="<?php //echo($intAltura);?>" style="vertical-align:top">
			<div style="margin:10px 0px 0px 0px; background-color:transparent;">
				<?php //athBeginWhiteBox("100%",$intAltura,"<strong><a href=\"javascript:reSizeiFrame(".CFG_SYSTEM_NAME."_detailiframe_pedidos.document.body,'".CFG_SYSTEM_NAME."_detailiframe_pedidos',false,true);\"><img src='../img/BulletExpand.gif' border='0'></a>&nbsp;".getTText("pedidos",C_TOUPPER)."</strong>","#DBDBDB");  ?>
				<iframe name="<?php //echo(CFG_SYSTEM_NAME);?>_detailiframe_pedidos" 
						id="<?php //echo(CFG_SYSTEM_NAME);?>_detailiframe_pedidos" 
						width="100%" 
						height="<?php //echo($intAltura-20);?>"
						src="STincludepedidos.php" 
						frameborder="0" 
						scrolling="yes"
                        style="display:inline-table;">
				</iframe>
				<?php //athEndWhiteBox(); ?>
			</div>
			</td>
		</tr//-->
		<?php 
		//$intAltura = 30;
		//if (($intTotal_SdAnuncios > 0) || ($intTotal_SdCertificados > 0) || ($intTotal_SdPerfis > 0) || ($intTotal_SdCatalogos > 0) || ($intTotal_SdCredenciais > 0) || ($intTotal_SdHomologacoes > 0)) $intAltura = 150;
		?>
		<!--tr>
			<td height="<?php echo($intAltura);?>" style="vertical-align:top">
			<div style="margin:10px 0px 0px 0px; background-color:transparent;">
				<?php athBeginWhiteBox("100%",$intAltura,"<strong><a href=\"javascript:reSizeiFrame(".CFG_SYSTEM_NAME."_detailiframe_produtos_comprados.document.body,'".CFG_SYSTEM_NAME."_detailiframe_produtos_comprados',false,true);\"><img src='../img/BulletExpand.gif' border='0'></a>&nbsp;".getTText("produtos_comprados",C_TOUPPER)."</strong>","#DBDBDB"); ?>
				<iframe name="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_produtos_comprados" 
						id="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_produtos_comprados" 
						width="100%" 
						height="<?php echo($intAltura-20);?>" 
						src="STincludeprodutoscomprados.php" 
						frameborder="0" 
						scrolling="yes"
                        style="display:inline-table;">
				</iframe>
				<?php athEndWhiteBox(); ?>
			</div>
			</td>
		</tr//-->
		<tr>
			<td height="90%" style="vertical-align:top">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="90%" style="vertical-align:top">
					<div style="margin:10px 10px 0px 0px; background-color:transparent;">
						<?php athBeginWhiteBox("100%","600","<a href=\"javascript:reSizeiFrame(".CFG_SYSTEM_NAME."_detailiframe_titulos.document.body,'".CFG_SYSTEM_NAME."_detailiframe_titulos',false,true);\"><img src='../img/BulletExpand.gif' border='0'></a>
						<strong>Títulos</strong> - <font color='red'>Atenção seu pagamento será processado em até 48 horas</font><!--/a-->","#DBDBDB"); ?>
						<iframe name="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_titulos" 
                        	    id="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_titulos" 
                                width="100%" 
                                height="90%" 
                                src="STincludetitulosPF.php" 
                                frameborder="0" 
                                scrolling="auto"
		                        style="display:inline-table;">
						</iframe>
						<?php athEndWhiteBox(); ?>
					</div>
					</td>
					<!--td width="160" style="vertical-align:top">
					<div style="margin:10px 0px 0px 0px; background-color:transparent;">
						<?php athBeginWhiteBox("160","160","<strong><a href='../modulo_PainelPJ/STColabAtivos.php' target=".CFG_SYSTEM_NAME."_frmain>".getTText("colaboradores",C_TOUPPER)."</a></strong>","#DBDBDB");?>
						<iframe name="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_colaboradores" 
                        	    id="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_colaboradores" 
                                width="100%" 
                                height="100%" 
                                src="STincludecolaboradores.php" 
                                frameborder="0" 
                                scrolling="yes"
		                        style="display:inline-table;">
						</iframe>
						<?php athEndWhiteBox(); ?>
					</div>
					</td//-->
				</tr>
				</table>
			</td>
		</tr>
		<!--tr>
			<td height="89" bgcolor="#EDF2F6" style="background-image:url(../img/BgBannerTop.jpg); background-position:right top; background-repeat:no-repeat;">
				<table width="90%">
				<tr>
					<td style="text-align:left; vertical-align:top;">ABRAMGE</td>
					<td style="text-align:left; vertical-align:top;">SINAMGE</td>
					<td style="text-align:left; vertical-align:top;">UCA</td>
				</tr>
                </table>
			</td>
		</tr//-->
		</table>
	</td>
	<td width="170" style="text-align:center; vertical-align:top"><?php include("STincludebannerright.php"); ?></td>
</tr>
</table>
</body>
</html>
<?php
}
$objConn = NULL;
?>
