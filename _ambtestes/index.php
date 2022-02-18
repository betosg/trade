<?php 
include_once("../_database/athdbconn.php"); 
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), 46, "MAN");
?>
<html>
	<head>
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	</head>
	<body bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_collapsed.jpg">
		<table border="0" cellspacing="0" cellpadding="0" width="50%" align="center" style="border:1px solid #999999;">
			<tr>
				<td width="50%" bgcolor="#AFD987" style="color:#FFFFFF;padding-left:10px; height:20px;" class="padrao_gde"><b>PROEVENTO</b></td>
				<td align="right" bgcolor="#AFD987" style="color:#FFFFFF;padding-right:10px;" class="padrao_gde"><b>Ambiente de Testes (Pastas e Arquivos)</b></td>
			</tr>
			<tr>
				<td colspan="2">
					<table border="0" width="100%" bgcolor="#FAFAFA">
						<tr><td height="10"></td></tr>
						<?php
							//Coloca os nomes dos arquivos num array
							$resDir = opendir("../_ambtestes");
							while(false !== ($strFile = readdir($resDir))) {
								if($strFile != "." && $strFile != ".." && is_dir($strFile)){
									echo("
										<tr>
											<td style=\"padding-left:15px;\">
												<a href=\"" . $strFile . "\"><b>" . $strFile . "</b></a>
											</td>
										</tr>
										");
								}
							}
							$resDir = opendir("../_ambtestes");
							while(false !== ($strFile = readdir($resDir))) {
								if($strFile != "." && $strFile != ".." && !ereg(".gif$|.jpg$|.png$",$strFile) && !is_dir($strFile)){
									echo("
										<tr>
											<td style=\"padding-left:15px;\">
												<a href=\"" . $strFile . "\">" . $strFile . "</a>
											</td>
										</tr>
										");
								}
							}
						?>
						<tr><td height="10"></td></tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>