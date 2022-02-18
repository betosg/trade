<?php  athBeginFloatingBox("100%","","<span style='float:right;padding-right:3px;'><img src='../img/icon_tree_plus.gif' border='0' onClick=\"showArea('grupo_2','grupo_img_2');\" id='grupo_img_2' style='cursor:pointer' /></span><strong>Usuário</strong>",CL_CORBAR_GLASS_2);?>
	<div id="grupo_2" style="display:none;">
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
						echo("<center>".getsession(CFG_SYSTEM_NAME . "_cod_usuario")."</center>"); 
						athEndWhiteBox();  ?>
						
			   			<?php athBeginWhiteBox("45");   ?>
							<form id="chamado" name="chamado" action="http://www.virtualboss.com.br/proevento/default_LoginViasite.asp" target='_blank' method="post">
							  <input type="hidden" id='var_user' name='var_user' value='<?php 
							  if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "sindieventos") echo("tusev");
							  if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "ubrafe")       echo("tubraf");
							  ?>_<?php echo (strtolower(getsession(CFG_SYSTEM_NAME . "_id_usuario")));?>' />
							  <input type="hidden" id='var_password' name='var_password' value='athroute'>
							  <input type="hidden" id='var_db' name='var_db' value='proevento'>
							  
							  <a href='#' onclick='document.chamado.submit();'> <img alt='HelpDesk' src='../img/button_help_desk_ico.gif' border='0'></a>
							</form>
						<?php athEndWhiteBox();  ?>
						
			   			</td>
					</tr>
					<tr style="border: none;">
			   			<td align="left" valign="top" style="border:none;" class="texto_corpo_peq">
			   			<?php 
			   			//athBeginWhiteBox("45"); 
						//echo("<center><a href=\"../upload/manual_util.pdf\" target=\"_blank\" title='Ajuda'><img src=\"../img/icon_help.gif\"></a><br>Ajuda</center>"); 
						//athEndWhiteBox();  ?>
						</td>
			   		</tr>
			   </table>
			</td>
		</tr>
	</table>
	</div>
<?php athEndFloatingBox(); ?>