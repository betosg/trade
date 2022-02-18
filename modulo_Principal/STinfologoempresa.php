<?php athBeginFloatingBox("100%","","<span style='float:right;padding-right:3px;'><img src='../img/icon_tree_plus.gif' border='0' onClick=\"showArea('grupo_1','grupo_img_1');\" id='grupo_img_1' style='cursor:pointer' /></span><strong>Entidade</strong>",CL_CORBAR_GLASS_2);?>
	<div id="grupo_1" style="display:none;">
	<table cellpadding="0" cellspacing="0" style="border:none; width:100%; margin-bottom:0px; background:none">
		<tr style="border: none;">
			<td align="left" valign="top" style="border:none; padding-right: 0px;" class="texto_corpo_peq">
			<?php 
				athBeginWhiteBox("150"); 
			    echo("<center><img src='../../".str_replace(CFG_SYSTEM_NAME."_","",CFG_DB_DEFAULT)."/upload/imgdin/logomarca.gif' alt='".str_replace(CFG_SYSTEM_NAME."_","",CFG_DB_DEFAULT)."' title='".str_replace(CFG_SYSTEM_NAME."_","",CFG_DB_DEFAULT)."' style='width:162px;'/></center>"); 
				athEndWhiteBox();  
			?>
			</td>
		</tr>
	</table>
	</div>
<?php athEndFloatingBox();?>