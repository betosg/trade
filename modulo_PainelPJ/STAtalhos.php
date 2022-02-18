<?php 
  	athBeginFloatingBox("200","","<b>Usuário / Empresa Filiada</b>",CL_CORBAR_GLASS_2);
	
	// Abertura de conexão ao banco
	$objConn = abreDBConn(CFG_DB);	
	
	// busca documento para exibição abaixo
  	// do white box que contém o cod usuario
  	try{
  		$strSQL = "
				SELECT
					arquivo_1
				FROM
					cad_pj
				WHERE
					cad_pj.cod_pj = " . getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
		$objResultArq = $objConn->query($strSQL);
  	}catch(PDOException $e){
  		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
  	}
   	$objRSArq = $objResultArq->fetch();
?>
	<table cellpadding="0" cellspacing="0" style="border:none; width:100%; margin-bottom:0px; background:none">
		<tr style="border: none;">
			<td align="left" valign="top" style="border:none;">
			   <?php include('STFotoUsuario.php');?>
			</td>
			<td align="left" valign="top" style="border:none; padding-right: 0px;" class="texto_corpo_peq">
				<table align="left" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: none;">
					<tr style="padding: 0px; border:none;">
				 		<td style="border: none; padding-right: 0px;">
			   			<?php athBeginWhiteBox("45"); 
						echo("<center>".getsession(CFG_SYSTEM_NAME."_cod_usuario")."</center>"); 
						athEndWhiteBox();  ?>
			   			</td>
					</tr>
					<tr style="padding: 0px; border:none;"><td style="padding: 0px; border:none;">&nbsp;</td></tr>
					<?php if(getValue($objRSArq,"arquivo_1") != "" && !is_null(getValue($objRSArq,"arquivo_1"))){?>
					<tr style="padding: 0px; border:none;">
				 		<td style="border: none; padding-right: 0px;" align="left" valign="middle">
			   			<?php athBeginWhiteBox("45"); 
						echo("<div style=\"padding-left:8px\"><a href=\"../../".getSession(CFG_SYSTEM_NAME . "_dir_cliente")."/upload/docspj/". getValue($objRSArq,"arquivo_1")."\" target=\"_blank\" style=\"text-decoration:none;\"><img src=\"../img/icon_anexo.gif\" alt=\"ARQUIVO\" title=\"ARQUIVO\" border=\"0\" /></a></div>"); 
						athEndWhiteBox();?>
			   			</td>
					</tr>
					<?php }?>
			   </table>
			</td>
		</tr>
	</table>
<?php
 athEndFloatingBox();
?>