<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$objConn = abreDBConn(CFG_DB);
?>
<html>
<head>
	<title>PROEVENTO STUDIO - Assistente de Módulos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<style>
		.boxtitulo { padding:5px 0px; margin-bottom:10px; background-color:#EEEEEE; border-bottom:1px #CCCCCC solid; }
		.titulo    { padding-left:20px; margin:0px; }
		.subtitulo { padding-left:25px; }
		
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
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px;" onLoad="document.formwizard.var_nome.focus();">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="middle">
	<?php athBeginFloatingBox("700","none","Setup de Aplicações - PROEVENTO STUDIO","#AFD987"); ?>
		<table border="0" width="100%" height="450" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formwizard" action="editconfigexec.php" method="post">
		   <input type="hidden" name="var_buffer" value="">
			<tr>
				<td width="1" valign="top"><img src="wizard_lotes.jpg" border="0"></td>
				<td align="center" valign="top">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td colspan="2" valign="top">
								<div class="boxtitulo">
									<h5 class="titulo"><b>Campos Aplicação</b></h5>
									<span class="subtitulo">Preencha os campos para a adicionar campos na aplicação</span>
								</div>
							</td>
						</tr>
						<tr>
							<td width="5"></td>
							<td>
								<table id="campos_consulta" width="100%" border="0" cellspacing="0" cellpadding="2">
									<tr>
										<td align="right" width="100"><b>Aplicação:</b>&nbsp;</td>
										<td>
											<select name="var_tabela" style="width:328px">
												<?php echo(montaCombo($objConn," SELECT cod_app, dir_app FROM sys_app ORDER BY 1 ","cod_app","dir_app","")); ?>
											</select>
										</td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" width="100"><b>Tabela:</b>&nbsp;</td>
										<td>
											<select name="var_tabela" style="width:328px">
												<?php echo(montaCombo($objConn," SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY 1 ","table_name","table_name","")); ?>
											</select>
										</td>
									</tr>
									<tr>
										<td align="right" width="100"><b>Nome:</b>&nbsp;</td>
										<td>
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td width="150"><input type="text" name="var_nome" size="30"></td>
													<td width="30"><b>Rótulo:</b>&nbsp;</td>
													<td><input type="text" name="var_rotulo" size="30"></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td align="right" width="100"><b>Operação:</b>&nbsp;</td>
										<td>
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td width="90">
														<select name="dbvar_str_operacao" id="dbvar_str_operacao" size="1">
															<option value="" selected>Todos</option>
															<option value="INS">Inserir</option>
															<option value="UPD">Editar</option>
															<option value="DEL">Deletar</option>
															<option value="VIE">Visualizar</option>
															<option value="FIL">Filtro</option>
														</select>
													</td>
													<td width="35"><b>Classe:</b>&nbsp;</td>
													<td width="90">
														<select name="dbvar_str_classe" id="dbvar_str_classe" size="1" onChange="document.frmformfields.location.href='wizardmoduloformfields.php?var_tipo_campo=' + this.value">
															<option value="CHAVE">CHAVE</option>
															<option value="EDIT" >EDIT </option>
															<option value="COMBO">COMBO</option>
															<option value="MEMO" >MEMO </option>
															<option value="RADIO">RADIO</option>
															<option value="CHECK">CHECK</option>
															<option value="LABEL">LABEL</option>
															<option value="ADD"  >ADD  </option>
															<option value="FILE" >FILE </option>
															<option value="SEARCH_">SEARCH </option>
															<option value="SEARCHPAD">SEARCHPAD</option>
														</select>
													</td>
													<td width="30"><b>Tipo:</b>&nbsp;</td>
													<td>
														<select name="dbvar_str_tipo" id="dbvar_str_tipo" size="1">
															<option value="NUM">NUM</option>
															<option value="STR">STR</option>
															<option value="BOOL">BOOL</option>
															<option value="MOEDA">MOEDA</option>
															<option value="MOEDA4CD">MOEDA4CD</option>
															<option value="DATE">DATE</option>
															<option value="DATETIME">DATETIME</option>
															<option value="AUTODATE">AUTODATE</option>
														</select>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td width="100" align="right"><b>Grupo Dialog:</b>&nbsp;</td>
										<td>
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td>
														<input type="text" name="var_dlg_grp" value="000" maxlength="3" size="3"></td>
													</td>
													<td width="195" align="right"><b>Obrigatório:</b>&nbsp;</td>
													<td>
														<input type="radio" name="var_obrigatorio" value="true" class="inputclean"> Sim
														<input type="radio" name="var_obrigatorio" value="false" checked class="inputclean"> Não
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td width="5"></td>
							<td>
								<iframe name="frmformfields" src="wizardmoduloformfields.php" width="100%" height="120" frameborder="0" scrolling="auto"></iframe>
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