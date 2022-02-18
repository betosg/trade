<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	
	$objConn = abreDBConn($strDB);
	$strMsg  = request("var_msg");
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script language="javascript" type="text/javascript">
	function selectClient(prValue){
		location.href='login.php?var_db=' + prValue + "&var_loginaway=<?php echo(request("var_loginaway")); ?>";
	}

	function setFocus(){
			document.frmsolicita.var_cnpj.focus();
	}
	</script>
</head>
<body style="margin:0px;" bgcolor="#CFCFCF" background="../img/bgFrame_corGRAY_collapsed.jpg" onLoad="setFocus();">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="middle">
		<?php athBeginFloatingBox("520","","Passo 1",CL_CORBAR_GLASS_1); ?>
			<table width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid;">
				<tr>
					<td align="center" valign="top"> 
						<form action="STverificaCNPJexec.php" method="post" name="frmsolicita">
						<table width="450" cellpadding="0" cellspacing="4">
							<tr>
								<td>
									<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td colspan="2" height="<?php echo($strAltura); ?>"></td>
										</tr>
										<tr> 
											<td width="20%" align="right" style="padding-right:10px;">
												<?php echo(getTText("cnpj",C_UCWORDS)); ?>:
											</td>
											<td>
												<input type="text" name="var_cnpj" class="textbox" style="width:160px">
											</td>
										</tr>
										<?php
											if($strMsg != ""){ 
												echo("
													  <tr align=\"30\">
														<td align=\"right\"><img src=\"../img/LoginAviso.gif\" alt=\"Aviso\"></td>
														<td class=\"destaque_med\">" . $strMsg . "</td>
													  </tr>
													");
											}
											else{ 
												echo("<tr><td colspan=\"2\" height=\"" . $strAltura . "\"></td></tr>");
											}
										?>
									</table>
								</td>
								<td align="center"><img src="../img/logomarca.gif"></td>
							</tr>
							<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
							<tr><td height="5" colspan="2"></td></tr>
							<tr> 
								<td colspan="2">
									<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-bottom:5px;">
                                    <tr>
                                        <td><div style="padding-left:10px"><?php echo(getTText("mensagem_solicita",C_NONE)); ?></div></td>
                                        <td width="1%" align="right"><button onClick="document.frmsolicita.submit();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button></td>
                                    </tr>
									</table>
								</td>
							</tr>
						</table>
						</form>
					</td>
				</tr>
			</table>
		<?php athEndFloatingBox(); ?>
		</td>
	</tr>
</table>
