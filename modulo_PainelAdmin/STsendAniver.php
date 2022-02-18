<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intCodPJ 	= request("var_cod_pj");
$intCodPF 	= request("var_chavereg");
$strCPF  	= request("var_cpf");
$strRedirect = request("var_redirect");

$objConn = abreDBConn(CFG_DB);
?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="javascript">

function ok(){
	
	document.formeditor.submit();	
}

function cancelar(){
	window.close();	
}
 
</script>

</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center">
 <tr>
   <td align="center" valign="top">   
	<?php athBeginFloatingBox("450","none","Felicitações Aniversario",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;" cellspacing="0" cellpadding="4">
	   		<form name="formeditor" action="STsendAniverExec.php" method="post">				
						<input type="hidden" id="var_email" name="var_email" value="<?php echo($strEmail);?>">
                                <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DADOS</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                                <tr bgcolor="#FAFAFA">  
                                	<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_assunto">
											<strong>Assunto:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
										<textarea name="var_assunto" id="var_assunto" cols="60" rows="5" title="Assunto" style="width:350px;" wrap=""></textarea><span class="comment_med">&nbsp;</span>
                                    </td>
								</tr>
                                <tr bgcolor="#FFFFFF">  
                                	<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_mensagem">
											<strong>Mensagem:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
										<textarea name="var_mensagem" id="var_mensagem" cols="60" rows="5" title="Mensagem" style="width:350px;" ></textarea><span class="comment_med">&nbsp;</span>
                                    </td>
								</tr>
                               	<tr bgcolor="#FAFAFA">  
                                	<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_assinatura">
											<strong>Assinatura:</strong>
										</label>
									</td>
                                    <td nowrap align="left" width="99%">
                                        <textarea name="var_assinatura" id="var_assinatura" cols="60" rows="5" title="Assinatura" style="width:350px;" ></textarea><span class="comment_med">&nbsp;</span>
                                    </td>
								</tr>
		  						<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>
								<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
                                <tr>
                                    <td align="right" colspan="3" style="padding:10px 30px 10px 10px;">
                                            <button onClick="ok();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
                                            <button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>						
                                    </td>
                                </tr>		
		  	</form>
        </table>
    <?php athEndFloatingBox(); ?>
	</td>
  </tr>			
</table>	
	
		
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>
<iframe name="gToday:normal:agenda.js" id="gToday:normal:agenda.js"
        src="../_class/calendar/source/ipopeng.htm" scrolling="no" frameborder="0"
        style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe>
<?php
$objConn = NULL;
?>