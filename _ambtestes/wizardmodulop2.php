<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
?>
<html>
<head>
	<title>PROEVENTO STUDIO - Assistente de Módulos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<style>
		.boxtitulo { padding:5px 0px; margin-bottom:10px; background-color:#EEEEEE; border-bottom:1px #CCCCCC solid; }
		.titulo    { padding-left:20px; margin:0px; }
		.subtitulo { padding-left:35px; }
		
		.barselect { padding-left:10px; height:20px; background-color:#CCCCCC; cursor:pointer; }
		.conteudoform { padding:10px 0px; }
	</style>
	<script>
		function submeterForm(){
			document.formwizard.submit();
		}
		
		function showContainer(prContainer){
			if(prContainer == "campos_consulta"){
				document.getElementById("campos_consulta").style.display = ""
				document.getElementById("filtros_consulta").style.display = "none";
			}
			else{
				document.getElementById("filtros_consulta").style.display = "";
				document.getElementById("campos_consulta").style.display = "none"
			}
		}
	</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px;" onLoad="document.formwizard.var_nome_modulo.focus();">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="middle">
	<?php athBeginFloatingBox("700","none","Setup de Aplicações - PROEVENTO STUDIO","#AFD987"); ?>
		<table border="0" width="100%" height="450" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formwizard" action="wizardmodulop3.php" method="post">
		   <input type="hidden" name="var_buffer" value="">
			<tr>
				<td width="1" valign="top"><img src="wizard_lotes.jpg" border="0"></td>
				<td align="center" valign="top">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td colspan="2" valign="top">
								<div class="boxtitulo">
									<h5 class="titulo"><b>Aplicação</b></h5>
									<span class="subtitulo">Preencha os campos para a definição da aplicação</span>
								</div>
							</td>
						</tr>
						<tr>
							<td width="5"></td>
							<td class="barselect" onClick="showContainer('campos_consulta')"><b>&bull; Campos obrigatórios</b></td>
						</tr>
						<tr>
							<td width="5"></td>
							<td class="conteudoform">
								<table id="campos_consulta" width="100%" border="0" cellspacing="0" cellpadding="2">
									<tr>
										<td align="right" width="100"><b>Novo módulo:</b>&nbsp;</td>
										<td><input type="text" name="var_nome_modulo" value="modulo_" size="38"></td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" width="100"><b>Tabela:</b>&nbsp;</td>
										<td>
											<select name="var_tabela">
												<?php echo(montaCombo(abreDBConn(CFG_DB)," SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY 1 ","table_name","table_name","")); ?>
											</select>
										</td>
									</tr>
									<tr>
										<td align="right" width="100"><b>Rótulo:</b>&nbsp;</td>
										<td><input type="text" name="var_rotulo" size="38"></td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" width="100" valign="top"><b>Consulta padrão:</b>&nbsp;</td>
										<td><textarea name="var_consulta" rows="4" cols="38"></textarea></td>
									</tr>
									<tr>
										<td align="right" width="100"><b>Ítens por página:</b>&nbsp;</td>
										<td><input type="text" name="var_num_per_page" value="10" size="2"></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td width="5"></td>
							<td class="barselect" onClick="showContainer('filtros_consulta')"><b>&bull; Campos opcionais</b></td>
						</tr>
						<tr>
							<td width="5"></td>
							<td class="conteudoform">
								<table id="filtros_consulta" width="100%" border="0" cellspacing="0" cellpadding="2" style="display:none">
									<tr>
										<td align="right" width="100"><b>Grade padrão:</b>&nbsp;</td>
										<td><input type="text" name="var_grade_padrao" size="38"></td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" width="100"><b>Grupo:</b>&nbsp;</td>
										<td><input type="text" name="var_grupo" size="38"></td>
									</tr>
									<tr>
										<td width="100" align="right" valign="top"><b>Menu Rótulo:</b>&nbsp;</td>
										<td><textarea name="var_menucombo_rotulo" rows="4" cols="38">inserir;configuracoes</textarea></td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td width="100" align="right" valign="top"><b>Menu Valores:</b></td>
										<td><textarea name="var_menucombo_valores" id="var_menucombo_valores" rows="4" cols="38">insupddelmastereditor.php?var_oper=INS;editconfig.php</textarea></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<table width="95%" border="0" cellspacing="0" cellpadding="4">
						<tr><td height="5" colspan="3"></td></tr>
						<tr><td height="1" colspan="3" bgcolor="#DBDBDB"></td></tr>
						<tr>
							<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
								<button onClick="history.back();">Voltar &lt;</button>
								<button onClick="submeterForm();">Avançar &gt;</button>
								<button onClick="location.href='../modulo_Principal/painel.php';"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		  </form>
		</table>
	<?php athEndFloatingBox(); ?>
   </td>
  </tr>
</table>
</body>
</html>