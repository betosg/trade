<!-- INI: Título, Descrição e controles de exportação e display/hidden descrição ******************* -->
<div id='divHeader' style='width:100%; margin:0px 0px 10px 0px; border:1px solid #D9D9D9;'>
  <div class='padrao_gde' style='width:100%; height:23px;'>
	  <div align="left"  id='divTitulo' style='float:left; text-align:left;  padding:5px 0px 0px 5px; white-space:nowrap;'><strong><img src='../img/icon_tree_plus.gif' border='0' onclick="javascript:swapDisplay(this,'divDesc');">&nbsp;<?php echo($intRelCod . " - " . $strRelTit); ?></strong></div>
	  <div align="right" id='divIcons'  style='float:right; text-align:right; padding:4px 5px 5px 0px;'>
		<form name='formexport' id='formexport' action='aslExport.php' target='_blank' method='post'>
			<input type='hidden' name='var_content' id='var_content' value='' />
			<input type='hidden' name='var_acao'    id='var_acao'    value='' />
			<input type='hidden' name='var_link'    id='var_link'    value='' />
		</form>
		<img src='../img/iconfooter_print.gif' border='0' onClick='imprimir();'             style='cursor:pointer;'  title='<?php echo(getTText("imprimir",C_UCWORDS));?>'>
		<img src='../img/iconfooter_word.gif'  border='0' onClick="exportDocument('.doc');" style='cursor:pointer;'  title='<?php echo(getTText("exportar_word",C_UCWORDS));?>'>
		<img src='../img/iconfooter_excel.gif' border='0' onClick="exportDocument('.xls');" style='cursor:pointer;'  title='<?php echo(getTText("exportar_excel",C_UCWORDS));?>'>
		<?php /*<img src='../img/iconfooter_adobe.gif' border='0' onClick="exportarAdobe();"style='cursor:pointer;'  title=''> */ ?>
	  </div>
  </div>
  <div id='divDesc' style='width:100%; display:none;'>
	  <div style='width:100%; text-align:left;padding:5px 0px 5px 5px;'>
	    <?php echo($strRelDesc); ?>
		<?php echo( ($strRelInpts != "") ? "<br><br>Parâmetros: " . $strRelInpts : ""); ?>
	  </div>
  </div>
</div>
<!-- FIM: Título, Descrição e controles de exportação e display/hidden descrição ******************* -->
<?php if (CFG_SYSTEM_DEBUG=="true") { echo (ShowDebugConsuta($strRelASL,$strRelSQL)); } ?>