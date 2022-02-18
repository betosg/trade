<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$objConn = abreDBConn(CFG_DB); // Abertura de banco	
 //"iduser: ".getsession(CFG_SYSTEM_NAME . "_id_usuario");
 //"<br>grp_user: ".getsession(CFG_SYSTEM_NAME . "_grp_user");
if ((getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") == "") && (getsession(CFG_SYSTEM_NAME . "_grp_user") == 'NORMAL')) {
	$intCodPJ = '';
	$strNomePJ = '';
	
	getDadosPJSelected($objConn, "", getsession(CFG_SYSTEM_NAME . "_id_usuario"));
}

//function buscaTotal($prObjConn, $prCodPJ, $prTipo) {
//	$intTotal = 0;
//	
//	try{
//		if ($prTipo == "ped_abertos")     $strSQL = " SELECT COUNT(t1.cod_pedido) AS total FROM prd_pedido t1 WHERE t1.cod_pj = ".$prCodPJ." AND t1.situacao ILIKE 'aberto' ";
//		if ($prTipo == "ped_deletados")   $strSQL = " SELECT COUNT(t1.cod_pedido) AS total FROM prd_pedido_deletado t1 WHERE t1.cod_pj = ".$prCodPJ." AND t1.sys_dtt_ins BETWEEN (CURRENT_DATE - INTERVAL '2 Month') AND CURRENT_TIMESTAMP ";
//		if ($prTipo == "sd_anuncios")     $strSQL = " SELECT COUNT(t1.cod_pedido) AS total FROM sd_anuncio t1    , prd_pedido t2 WHERE t1.cod_pedido = t2.cod_pedido AND t1.dtt_inativo IS NULL AND t1.dt_validade >= CURRENT_DATE AND t1.cod_pj = ".$prCodPJ;
//		if ($prTipo == "sd_certificados") $strSQL = " SELECT COUNT(t1.cod_pedido) AS total FROM sd_certificado t1, prd_pedido t2 WHERE t1.cod_pedido = t2.cod_pedido AND t1.dtt_inativo IS NULL AND t1.dt_validade >= CURRENT_DATE AND t1.cod_pj = ".$prCodPJ;
//		if ($prTipo == "sd_perfis")       $strSQL = " SELECT COUNT(t1.cod_pedido) AS total FROM sd_perfil t1     , prd_pedido t2 WHERE t1.cod_pedido = t2.cod_pedido AND t1.dtt_inativo IS NULL AND t1.dt_validade >= CURRENT_DATE AND t1.cod_pj = ".$prCodPJ;
//		if ($prTipo == "sd_catalogos")    $strSQL = " SELECT COUNT(t1.cod_pedido) AS total FROM sd_catalogo t1   , prd_pedido t2 WHERE t1.cod_pedido = t2.cod_pedido AND t1.dtt_inativo IS NULL AND t1.dt_validade >= CURRENT_DATE AND t1.cod_pj = ".$prCodPJ;
//		if ($prTipo == "sd_credenciais")  $strSQL = " SELECT COUNT(t1.cod_pedido) AS total FROM sd_credencial t1 , prd_pedido t2 WHERE t1.cod_pedido = t2.cod_pedido AND t1.dtt_inativo IS NULL AND t1.dt_validade >= CURRENT_DATE AND t1.cod_pj = ".$prCodPJ;
//		if ($prTipo == "sd_homologacoes") $strSQL = " SELECT COUNT(t1.cod_pedido) AS total FROM sd_homologacao t1, prd_pedido t2 WHERE t1.cod_pedido = t2.cod_pedido AND t1.dtt_inativo IS NULL AND t1.cod_pj = ".$prCodPJ;
//		
//		$objResult = $prObjConn->query($strSQL);
//	}catch(PDOException $e) {
//		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
//		die();
//	}
//	if($objResult->rowCount()>0){
//		$objRS = $objResult->fetch();
//		$intTotal = getValue($objRS,"total");
//	}
//	$objResult->closeCursor();
//	
//	return $intTotal;
//}
//
//if (getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") != "") {
//	$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
//	
//	$intTotal_PedAbertos     = buscaTotal($objConn, $intCodDado, "ped_abertos");
//	$intTotal_PedDeletados   = buscaTotal($objConn, $intCodDado, "ped_deletados");
//	$intTotal_SdAnuncios     = buscaTotal($objConn, $intCodDado, "sd_anuncios");
//	$intTotal_SdCertificados = buscaTotal($objConn, $intCodDado, "sd_certificados");
//	$intTotal_SdPerfis       = buscaTotal($objConn, $intCodDado, "sd_perfis");
//	$intTotal_SdCatalogos    = buscaTotal($objConn, $intCodDado, "sd_catalogos");
//	$intTotal_SdCredenciais  = buscaTotal($objConn, $intCodDado, "sd_credenciais");
//	$intTotal_SdHomologacoes = buscaTotal($objConn, $intCodDado, "sd_homologacoes");
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
		<tr>
			<td height="89" bgcolor="#EDF2F6" style="background-image:url(../img/BgBannerTop.jpg); background-position:right top; background-repeat:no-repeat;">
				<table width="90%">
				<tr>
					<td style="text-align:left; vertical-align:top;"><?php include("STincludebannertop.php"); ?></td>
				</tr>
                </table>
			</td>
		</tr>
		<?php 
		//$intAltura = 30;
		//if (($intTotal_PedAbertos > 0) || ($intTotal_PedDeletados > 0)) $intAltura = 120;
		?>
		<!--tr>
			<td height="<?php// echo($intAltura);?>" style="vertical-align:top">
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
	//	$intAltura = 30;
		//if (($intTotal_SdAnuncios > 0) || ($intTotal_SdCertificados > 0) || ($intTotal_SdPerfis > 0) || ($intTotal_SdCatalogos > 0) || ($intTotal_SdCredenciais > 0) || ($intTotal_SdHomologacoes > 0)) $intAltura = 150;
		?>
		<!--tr>
			<td height="<?php// echo($intAltura);?>" style="vertical-align:top">
			<div style="margin:10px 0px 0px 0px; background-color:transparent;">
				<?php //athBeginWhiteBox("100%",$intAltura,"<strong><a href=\"javascript:reSizeiFrame(".CFG_SYSTEM_NAME."_detailiframe_produtos_comprados.document.body,'".CFG_SYSTEM_NAME."_detailiframe_produtos_comprados',false,true);\"><img src='../img/BulletExpand.gif' border='0'></a>&nbsp;".getTText("produtos_comprados",C_TOUPPER)."</strong>","#DBDBDB"); ?>
				<iframe name="<?php //echo(CFG_SYSTEM_NAME);?>_detailiframe_produtos_comprados" 
						id="<?php //echo(CFG_SYSTEM_NAME);?>_detailiframe_produtos_comprados" 
						width="100%" 
						height="<?php echo($intAltura-20);?>" 
						src="STincludeprodutoscomprados.php" 
						frameborder="0" 
						scrolling="yes"
                        style="display:inline-table;">
				</iframe>
				<?php// athEndWhiteBox(); ?>
			</div>
			</td>
		</tr//-->
		<tr>
			<td height="200" style="vertical-align:top">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="90%" style="vertical-align:top">
					<div style="margin:10px 10px 0px 0px; background-color:transparent;">
						<?php athBeginWhiteBox("100%","220","<a href=\"javascript:reSizeiFrame(".CFG_SYSTEM_NAME."_detailiframe_titulos.document.body,'".CFG_SYSTEM_NAME."_detailiframe_titulos',false,true);\"><img src='../img/BulletExpand.gif' border='0'></a>&nbsp;<a href='../modulo_PainelPJ/STTitulos.php'><strong>".getTText("ult_titulos",C_TOUPPER)."</strong></a>","#DBDBDB"); ?>
						<iframe name="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_titulos" 
                        	    id="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_titulos" 
                                width="100%" 
                                height="100%" 
                                src="STincludetitulosSinog.php" 
                                frameborder="0" 
                                scrolling="yes"
		                        style="display:inline-table;">
						</iframe>
						<?php athEndWhiteBox(); ?>
					</div>
					</td>
					<!--td width="160" style="vertical-align:top">
					<div style="margin:10px 0px 0px 0px; background-color:transparent;">
						<?php //athBeginWhiteBox("160","160","<strong><a href='../modulo_PainelPJ/STColabAtivos.php' target=".CFG_SYSTEM_NAME."_frmain>".getTText("colaboradores",C_TOUPPER)."</a></strong>","#DBDBDB");?>
						<iframe name="<?php //echo(CFG_SYSTEM_NAME);?>_detailiframe_colaboradores" 
                        	    id="<?php //echo(CFG_SYSTEM_NAME);?>_detailiframe_colaboradores" 
                                width="100%" 
                                height="100%" 
                                src="STincludecolaboradores.php" 
                                frameborder="0" 
                                scrolling="yes"
		                        style="display:inline-table;">
						</iframe>
						<?php //athEndWhiteBox(); ?>
					</div>
					</td//-->
				</tr>
				</table>
			</td>
		</tr>
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