<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strErr       = request("var_error");
$strFileName  = request("var_filename");
$strFormName  = request("var_formname");
$strFieldName = request("var_fieldname");
$strDirUpload = str_replace("\\", "\\\\",request("var_dir"));
$strFunc      = request("var_func");
$strPrefix    = request("var_prefix");

if($strFunc == ""){
	$strFunc = "1";
}

//$strPath = Replace(Request.Cookies("ATHCSM")("CLI_DIR_PHYSICAL_PATH"),"\","\\")
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script>
		function setParentField(){
			self.opener.setFormField('<?php echo($strFormName); ?>','<?php echo($strFieldName); ?>','<?php echo($strFileName); ?>');
		}
		
		function submeter(){
			document.formupload.submit();
		}
		
		function fecharJanela(){
			setParentField();
			window.close();
		}
	</script>
</head>
<body bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php 
		athBeginFloatingBox("500","none",getTText("ath_uploader",C_UCWORDS),CL_CORBAR_GLASS_1);
		echo("
			  <table border=\"0\" width=\"100%\" bgcolor=\"#FFFFFF\" style=\"border:1px #A6A6A6 solid;\">
				<tr>
					<td align=\"center\" valign=\"top\">");
			switch($strFunc){
				case "1":
								echo("
									  <form name=\"formupload\" action=\"athuploaderexec.php\" method=\"post\" enctype=\"multipart/form-data\">
										<input type=\"hidden\" name=\"var_formname\" value=\"" . $strFormName . "\">
										<input type=\"hidden\" name=\"var_fieldname\" value=\"" . $strFieldName . "\">
										<input type=\"hidden\" name=\"var_dir\" value=\"" . $strDirUpload . "\">
										<input type=\"hidden\" name=\"var_prefix\" value=\"" . $strPrefix . "\">
										
										<table width=\"450\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
											<tr>
											  <td colspan=\"2\" align=\"left\"><strong>Instruções:</strong>&nbsp; Para enviar o seu arquivo siga as instruções abaixo:</td>
											</tr>
											<tr>
											  <td colspan=\"2\" align=\"right\">&nbsp;</td>
											</tr>
											<tr>
											  <td colspan=\"2\" align=\"left\">&nbsp;</td>
											</tr>
											<tr>
											  <td colspan=\"2\" align=\"left\">1. Clique no bot&atilde;o PROCURAR&nbsp;</td>
											</tr>
											<tr>
											  <td colspan=\"2\" align=\"left\">2. Selecione o arquivo no seu computador&nbsp;</td>
											</tr>
											<tr>
											  <td colspan=\"2\" align=\"left\">3. Clique no botão Ok&nbsp;</td>
											</tr>
											<tr>
											  <td colspan=\"2\" align=\"left\">&nbsp;</td>
											</tr>
											<tr>
											  <td width=\"50%\" height=\"21\" align=\"right\"><strong>Caminho do Arquivo:&nbsp;</strong></td>
											  <td width=\"50%\" align=\"left\"><input name=\"file1\" type=\"file\"></td>
											</tr>
											<tr><td colspan=\"2\" align=\"right\">&nbsp;</td></tr>
											<tr>
											  <td colspan=\"2\" align=\"left\">
												<strong>Nota:</strong> 
												O envio de arquivos pode levar alguns minutos. Aguarde até que o final do processamento	seja concluído totalmente.
											  </td>
											</tr>
											<tr><td colspan=\"2\" height=\"10\"></td></tr>
											<tr><td height=\"1\" colspan=\"2\" bgcolor=\"#CCCCCC\"></td></tr>
											<tr>
												<td colspan=\"2\" align=\"right\" style=\"padding:10px;\">
													<button onClick=\"submeter();\">" . getTText("ok",C_UCWORDS) . "</button>
													<button onClick=\"window.close();\">" . getTText("cancelar",C_UCWORDS) . "</button>
												</td>
											</tr>
										</table>
									  </form>");
				break;
				
				case "2":
							if($strErr != ""){
								echo("
										<table width=\"450\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
											<tr>
												<td align=\"left\"><strong>Mensagem:</strong>&nbsp; Ocorreu um erro ao tentar enviar o seu arquivo</td>
											</tr>
											<tr>
												<td align=\"left\">&nbsp;</td>
											</tr>
											<tr>
												<td align=\"left\"><strong>ERRO:</strong><br>&nbsp;" . $strErr ."</td>
											</tr>
											<tr>
												<td align=\"left\">
													&nbsp; Clique no botão VOLTAR para tentar novamente o upload
													ou em CANCELAR para sair.
												</td>
											</tr>
											<tr><td colspan=\"2\" height=\"10\"></td></tr>
											<tr><td height=\"1\" colspan=\"2\" bgcolor=\"#CCCCCC\"></td></tr>
											<tr>
												<td align=\"right\" colspan=\"2\" style=\"padding:10px;\">
													<button onClick=\"location.href=athuploader.php;\">" . getTText("voltar",C_UCWORDS) . "</button>
													<button onClick=\"window.close();\">" . getTText("cancelar",C_UCWORDS) . "</button>
												</td>
											</tr>
										</table>");
							}
							else{
								echo("
										<table width=\"450\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
											<tr>
												<td align=\"left\">
													<strong>Mensagem:</strong>&nbsp;
													Upload do arquivo [". $strFileName ."] efetuado com sucesso
												</td>
											</tr>
											<tr>
												<td align=\"left\">&nbsp;</td>
											</tr>
											<tr>
												<td align=\"left\">
													&nbsp; Clique no botão FECHAR para sair ou simplesmente feche essa janela.
												</td>
											</tr>
											<tr><td colspan=\"2\" height=\"10\"></td></tr>
											<tr><td height=\"1\" colspan=\"2\" bgcolor=\"#CCCCCC\"></td></tr>
											<tr>
												<td align=\"right\" colspan=\"2\" style=\"padding:10px;\">
													<button onClick=\"fecharJanela();\">" . getTText("fechar",C_UCWORDS) . "</button>
												</td>
											</tr>
										</table>
								");
							}
				break;
			}
		echo("
					</td>
				</tr>
			  </table>
			");
		athEndFloatingBox();
    ?>
		</td>
	</tr>
</table>
</body>
</html>