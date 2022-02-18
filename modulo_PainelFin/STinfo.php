<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE"); //Verificação de acesso do usuário corrente

$objConn = abreDBConn(CFG_DB);

$strHeaderAg = "<span class=\"headerleft\">
					<b>Agenda</b>
				</span>
				<span class=\"headerright\">
					<a href=\"../modulo_Agenda/index.php?var_redirect=insupddelmastereditor.php<PARAM_QM>var_oper=INS<PARAM_EC>var_populate=true\" target=\"_parent\">
						<img src=\"../img/icon_inserir.gif\" border=\"0\" align=\"absmiddle\">
					</a>
				</span>";
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" type="text/css">
		<script language="javascript" type="text/javascript">
			function redirecionaAgenda() {
				parent.location.href = "../modulo_Agenda/index.php?var_redirect=insupddelmastereditor.php<PARAM_QM>var_oper=INS<PARAM_EC>var_populate=yes";
			}
		</script>
		<style>
			body { margin:10px 0px; }
			ul	 { margin-top: 0px; margin-bottom: 0px; }
			li	 { margin-left: 0px; }
			
			.headerleft  { width:86px; vertical-align:middle; text-align:left; display:inline-block; } 
			.headerright { width:85px; vertical-align:middle; text-align:right; display:inline-block; } 
		</style>
	</head>
	<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_filtro.jpg">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td valign="top">
								<?php athBeginFloatingBox("200","","<b>Usuário</b>",CL_CORBAR_GLASS_2); ?>
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="left" valign="top">
											   <?php 
												 athBeginWhiteBox("120"); 
													$strImage = (getsession(CFG_SYSTEM_NAME . "_foto_usuario") != "") ? "../../" . getsession(CFG_SYSTEM_NAME . "_dir_cliente") . "/upload/fotosusuario/" . getsession(CFG_SYSTEM_NAME . "_foto_usuario") : "../img/unknownuser.jpg";
													$intWidth = 100;
													echo("<img src=\"" . $strImage . "\" width=\"" . $intWidth . "\">");
												 athEndWhiteBox();	 
												?>
											</td>
											<td valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td>
														<?php athBeginWhiteBox("45"); ?>
															<center><?php echo(getsession(CFG_SYSTEM_NAME . "_cod_usuario")); ?></center>
														<?php athEndWhiteBox(); ?>
														</td>
													</tr>
											   </table>
											</td>
										</tr>
									</table>
								<?php athEndFloatingBox(); ?>
							</td>
						</tr>
						<tr><td height="10">&nbsp;</td></tr>
						<tr>
							<td align="center" valign="top">
								<?php athBeginFloatingBox("100%","",$strHeaderAg,CL_CORBAR_GLASS_2); ?>
									<iframe id="dbvar_str_agenda" src="STagenda.php" frameborder="0" width="100%" height="100%"></iframe>
								<?php athEndFloatingBox(); 	?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>
<?php $objConn = NULL; ?>