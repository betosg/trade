<center>
<?php athBeginWhiteBox("100%"); ?>
<form name="formstatic" action="<?php echo($_SERVER['PHP_SELF']);?>" method="post">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="padrao_gde" align="left" width="01%" nowrap="nowrap" valign="top"><b><?php echo(getTText("painel_de_tarefas_todolist",C_UCWORDS)); ?></b></td>
			<td align="right" valign="top">
				<?php echo(getTText("ultimas",C_NONE))?>
				<select name="var_limit_tarefas" id="var_limit_tarefas" style="width:50px;" onChange="document.formstatic.submit();">
				<?php for($auxCounter = 25; $auxCounter <= 150; $auxCounter++){?>
				<?php if(($auxCounter % 25) == 0){?>
					<option value="<?php echo($auxCounter);?>" <?php echo(($intLimitTarefas == $auxCounter) ? "selected=\"selected\"" : "");?>><?php echo($auxCounter);?></option>
				<?php }?>
				<?php }?>
				</select>
				<?php echo(getTText("tarefas",C_NONE))?>
				<select name="var_situacao_tarefa" id="var_situacao_tarefa" style="width:80px;" onChange="document.formstatic.submit();">
					<option value="" <?php echo(($strSituacao == "") ? "selected=\"selected\"" : "");?>><?php echo(getTText("todos",C_NONE));?></option>
					<option value="aberto" <?php echo(($strSituacao == "aberto") ? "selected=\"selected\"" : "");?>><?php echo(getTText("abertas",C_NONE));?></option>
					<option value="executando" <?php echo(($strSituacao == "executando") ? "selected=\"selected\"" : "");?>><?php echo(getTText("executando",C_NONE));?></option>
					<option value="fechado" <?php echo(($strSituacao == "fechado") ? "selected=\"selected\"" : "");?>><?php echo(getTText("fechadas",C_NONE));?></option>
				</select>
				<?php echo(getTText("onde_sou",C_NONE))?>
				<select name="var_tipo_tarefa_usuario" id="var_tipo_tarefa_usuario" style="width:90px;" onChange="document.formstatic.submit();">
					<option value="todos" <?php echo(($strTipoUsuario == "todos") ? "selected=\"selected\"" : "");?>><?php echo(getTText("todos",C_NONE));?></option>
					<option value="responsavel" <?php echo(($strTipoUsuario == "responsavel") ? "selected=\"selected\"" : "");?>><?php echo(getTText("responsavel",C_NONE));?></option>
					<option value="executor" <?php echo(($strTipoUsuario == "executor") ? "selected=\"selected\"" : "");?>><?php echo(getTText("executor",C_NONE));?></option>
					<option value="equipe" <?php echo(($strTipoUsuario == "equipe") ? "selected=\"selected\"" : "");?>><?php echo(getTText("equipe",C_NONE));?></option>
				</select>
			</td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr>
			<td colspan="2">
				<?php if($objResult->rowCount() > 0){ ?> <!-- SE A CONSULTA VIER VAZIA NÃO PASSA AQUI, ENTRARÁ NO ELSE DESSE IF -->
				<table cellpadding="0" cellspacing="3" width="100%" style="border:1px #EEEEEE solid;" bgcolor="#F7F7F7">
					<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
					<tr>
						<td>
							<table id="tableContent" border="0" cellpadding="0" cellspacing="0" width="100%" background="../img/grid_backheader.gif" style="background-repeat:repeat-x;">
								<tr>
									<!-- CABEÇALHO DA GRADE - [INÍCIO] -->
									<td></td> <!-- Coloca uma coluna mesclada para ajustar a tabela com os ícones que virão abaixo -->
									<!--td height="22">
										<!--table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td width="1%">
													<table border="0" cellpadding="0" cellspacing="0" width="100%">
														<tr><td><a href="javascript:setOrderBy('0','ASC');"><img src="../img/gridlnkASC.gif"  border="0" align="absmiddle"></a></td></tr>
														<tr><td><a href="javascript:setOrderBy('0','DESC');"><img src="../img/gridlnkDESC.gif" border="0" align="absmiddle"></a></td></tr>
													</table>
												</td>
											</tr>
										</table>
									</td-->
									<td class="titulo_grade" width="01%" nowrap><?php echo(getTText("cod_todolist",C_NONE));?></td>
									<td class="titulo_grade" width="10%" nowrap><?php echo(getTText("prev_dt_ini",C_NONE));?></td>

									<td class="titulo_grade" width="05%" nowrap><?php echo(getTText("prev_hr_ini",C_NONE));?></td>
									<td class="titulo_grade" width="10%" nowrap><?php echo(getTText("categoria",C_NONE));?></td>
									<td class="titulo_grade" width="30%" nowrap><?php echo(getTText("titulo",C_NONE));?></td>
									<td class="titulo_grade" width="10%" nowrap><?php echo(getTText("id_responsavel",C_NONE));?></td>
									<td class="titulo_grade" width="10%" nowrap><?php echo(getTText("id_ult_executor",C_NONE));?></td>
									<td class="titulo_grade" width="05%" nowrap><?php echo(getTText("prev_horas",C_NONE));?></td>
									<td class="titulo_grade" width="10%" nowrap><?php echo(getTText("situacao_grid",C_NONE));?></td>
									<td class="titulo_grade" width="10%" nowrap><?php echo(getTText("prioridade_grid",C_NONE));?></td>
									<!-- CABEÇALHO DA GRADE - [FIM] -->
								</tr>
								<tr><td colspan="12" height="3"></td></tr>
								<!-- CONTEÚDO DA GRADE - [INÍCIO] -->
								<?php foreach($objResult as $objRS){ ?>
								<?php // echo(date("Y-m-d")." ".getValue($objRS,"prev_dt_ini")."<br />"); ?>
								<?php $strColor = (date("Y-m-d")  < getValue($objRS,"prev_dt_ini")) ? "#E6E6FA" : $strColor;?>
								<?php $strColor = (date("Y-m-d") == getValue($objRS,"prev_dt_ini")) ? "#FFFACD" : $strColor;?>
								<?php $strColor = (date("Y-m-d")  > getValue($objRS,"prev_dt_ini")) ? "#FFB6C1" : $strColor;?>
								<tr bgcolor="<?php echo($strColor); ?>" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
									<td width="<?php echo(ICONES_WIDTH * ICONES_NUM); ?>">
										<table border="0" cellspacing="0" cellpadding="0" width="<?php echo(CL_LINK_WIDTH * ICONES_NUM); ?>">
											<tr>
												<td width="<?php echo(ICONES_WIDTH)?>"><a href="STupdtarefa.php?var_chavereg=<?php echo(getValue($objRS,"cod_todolist"));?>&var_location=STpaineltarefas.php" ><img src="../img/icon_write.gif" border="0" title="<?php echo(getTText("editar",C_NONE));?>" /></a></td>
												<td width="<?php echo(ICONES_WIDTH)?>"><a href="STvietarefa.php?var_chavereg=<?php echo(getValue($objRS,"cod_todolist"));?>&var_location=STpaineltarefas.php" ><img src="../img/icon_zoom.gif" border="0" title="<?php echo(getTText("visualizar",C_NONE));?>" /></a></td>
												<td width="<?php echo(ICONES_WIDTH)?>"><img src="../img/icon_respostas.gif" border="0" title="<?php echo(getTText("respostas",C_NONE));?>" style="cursor:pointer;" onClick="AbreJanelaPAGE('STifrrespostas.php?var_chavereg=<?php echo(getValue($objRS,"cod_todolist"));?>',800,700);" /></td>
												<td width="<?php echo(ICONES_WIDTH)?>"><a href="STfinalizartarefa.php?var_chavereg=<?php echo(getValue($objRS,"cod_todolist"));?>&var_location=STpaineltarefas.php" ><img src="../img/icon_confirmar_homologacao.gif" border="0" title="<?php echo(getTText("finalizar",C_NONE));?>" /></a></td>
											</tr>
										</table>
									</td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"cod_todolist"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dt_ini"),false));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"prev_hr_ini"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"categoria"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"titulo"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"id_responsavel"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"id_ult_executor"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"prev_horas"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"situacao_grid"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"prioridade_grid"));?></td>
								</tr>
								<?php }?>
								<tr><td colspan="12" height="3"></td></tr>
							</table>
						</td>
					</tr>
				</table>
				<?php
					} 
					else{
						mensagem("alert_consulta_vazia_titulo", "alert_consulta_vazia_desc", "", "", "aviso", 0);
					}
				?>			
			</td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr><td height="3" colspan="2" bgcolor="#BFBFBF"></td></tr>
		<tr><td colspan="2" height="3"></td></tr>
	</table>
</form>
<?php athEndWhiteBox(); ?>
</center>