<center>
<?php athBeginWhiteBox("200"); ?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td align="center" valign="top">
		<?php athBeginWhiteBox("120"); ?>	
			<img src="<?php echo((getsession(CFG_SYSTEM_NAME."_foto_usuario") != "") ? "../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/fotosusuario/".getsession(CFG_SYSTEM_NAME."_foto_usuario") : "../img/unknownuser.jpg");?>" border="0" width="100" />
		<?php athEndWhiteBox();?>
		</td>
	</tr>
	<tr><td align="center" valign="bottom" height="25"><strong><?php echo(getsession(CFG_SYSTEM_NAME."_nome_usuario")." (".getsession(CFG_SYSTEM_NAME."_cod_usuario").")");?></strong></td></tr>
</table>
<?php athEndWhiteBox(); ?>

<br />
<br />

<?php athBeginShapeBox("200","","<span style='float:right;padding-right:3px;'><img src='../img/icon_tree_minus.gif' border='0' onClick=\"showArea('grupo_0','grupo_img_0');\" id='grupo_img_0' style='cursor:pointer' /></span>".getTText("bookmark",C_NONE),CL_CORBAR_GLASS_2); ?>
<div id="grupo_0" style="display:block;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td style="padding-left:5px;">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr><td height="5" colspan="2"></td></tr>
		<tr onClick="collapseItem(0);">
			<td width="99%" bgcolor="#E0E0E0" style="border-bottom:1px solid #999999;padding:3px;cursor:pointer"><b><?php echo(getTText("links",C_TOUPPER));?></b></td>
			<td width="01%" bgcolor="#E0E0E0" style="border-bottom:1px solid #999999;padding:3px;cursor:pointer"><img id="bookmark_img_0" src="../img/collapse_generic_open.gif"></td>
		</tr>
		<tr><td colspan="2" height="5"></td></tr>
		<tr>
			<td colspan="2">
			<table id="bookmark_0" border="0" cellspacing="0" cellpadding="0" width="100%" style="display:block;">
				<tr><td style="padding-left:5px;">&nbsp;- <a href="../modulo_BsAtividade/STindex.php" target="<?php echo(CFG_SYSTEM_NAME."_frmain")?>"><?php echo(getTText("modulo_de_atividades",C_NONE));?></a></td></tr>
				<tr><td style="padding-left:5px;">&nbsp;- <a href="../modulo_Todolist/" target="<?php echo(CFG_SYSTEM_NAME."_frmain")?>"><?php echo(getTText("modulo_de_tarefas",C_NONE));?></a></td></tr>
				<tr><td style="padding-left:5px;">&nbsp;- <a href="../modulo_BsCategoria/" target="<?php echo(CFG_SYSTEM_NAME."_frmain")?>"><?php echo(getTText("categorias_de_atividades",C_NONE));?></a></td></tr>
				<tr><td style="padding-left:5px;">&nbsp;- <a href="../modulo_TlCategoria/" target="<?php echo(CFG_SYSTEM_NAME."_frmain")?>"><?php echo(getTText("categorias_de_tarefas",C_NONE));?></a></td></tr>
				<tr><td style="padding-left:5px;">&nbsp;- <a href="javascript:void(0);" onClick="AbreJanelaPAGE('../modulo_Todolist/STlegendashelp.php','700','550');"><?php echo(getTText("entenda_as_legendas",C_NONE));?></a></td></tr>
				<tr><td height="5" colspan="2"></td></tr>
			</table>
			</td>
		</tr>
		<tr><td align="left" valign="top"></td></tr>
	</table>
</td>
</tr>
</table>
</div>
<?php athEndShapeBox(); ?>
<br />
<br />

<?php include_once("STtarefasvencidas.php"); ?>
<br />
<br />

<?php include_once("STtarefasdodia.php"); ?>
<br />
<br />

<?php include_once("STtarefasadiantadas.php"); ?>
</center>