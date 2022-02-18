<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");

	// REQUESTS
	// indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente
	$intCodAgenda = request("var_chavereg");	// cod_agenda para o qual irá ser update da agenda
	$strFlagRedir = request("var_flag_redir");  // flag para redirecionamento da pagina atual
		
	if($intCodAgenda == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("agenda_cod_null",C_NONE),'','erro','1');
		die();
	}

	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// busca dados sobre a agenda para confirmação antes de DEL
	try{
		$strSQL = "
			SELECT 
				  ag_agenda.cod_agenda 
				, ag_agenda.id_responsavel
  				, ag_agenda.categoria
				, ag_agenda.titulo 
				, ag_agenda.descricao
				, ag_agenda.prioridade 
				, ag_agenda.prev_dtt_ini 
				, ag_agenda.prev_dtt_fim
				, ag_agenda.dtt_realizado 
				, ag_agenda.sys_dtt_ins 
				, ag_agenda.sys_dtt_upd 
				, ag_agenda.sys_usr_ins 
				, ag_agenda.sys_usr_upd 
			FROM
				ag_agenda
			WHERE ag_agenda.cod_agenda = ".$intCodAgenda;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	// fetch dos dados localizados
	$objRS = $objResult->fetch();
	
	
	// localiza todos os citados para a agenda
	// corrente, para ser dado fetch posterior
	try{
		$strSQL 		  = "SELECT id_usuario FROM ag_agenda_citado WHERE cod_agenda = ".$intCodAgenda;
		$objResultCCount  = $objConn->query($strSQL);
		$objResultCitados = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	
	// localiza o número de respostas 
	// referentes a agenda corrente b
	try{
		$strSQL = "
			SELECT 
  				  id_usuario
  				, resposta
 				, dtt_resposta
  				, sys_usr_ins
  				, sys_dtt_ins
			FROM ag_resposta 
			WHERE cod_agenda = ".$intCodAgenda;
		$objResult 		= $objConn->query($strSQL);
		$objResultFetch	= $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
			
	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css">
			.tr_filtro_field { padding-left:5px; }
			.tr_filtro_label { padding-left:5px; padding-top:5px; }
			.td_search_left  { 
				padding:8px;
				border-top:1px solid #C9C9C9;
				border-left:1px solid #C9C9C9;
				border-bottom:1px solid #C9C9C9; 
			}
			.td_search_right  { 
				padding:5px;
				border-top:1px solid #C9C9C9;
				border-right:1px solid #C9C9C9;
				border-left: 1px dashed #C9C9C9;
				border-bottom:1px solid #C9C9C9;
			}
			.table_master{
				background-color:#FFFFFF;
				border-top:   1px solid #E9E9E9;
				border-right: 1px solid #E9E9E9;
				border-bottom:1px solid #E9E9E9;
				border-left:  1px solid #E9E9E9;
				padding-bottom: 5px;
			}
			.td_no_resp{ 
				font-size:11px; 
				font-weight:bold; 
				color:#C9C9C9; 
				text-align:center; 
				border:1px solid #E9E9E9;
				padding:5px 5px 0px 5px;
			}
			.td_resp{ border:1px solid #E9E9E9; padding:5px 0px 2px 10px; }
			.td_resp_cabec{ font-size:11px; font-weight:bold; color:#CCC;}
			.td_resp_conte{ padding:6px 0px 2px 20px; }
			.td_text_resp { border:2px dashed #E9E9E9; padding:4px 9px 4px 9px; }
		</style>
		<script type="text/javascript">
			function showHide(){
				// Esta função exibe ou faz hide na
				// table que contém as respostas, para
				// que a página não abra totalmente
				if (document.getElementById('respostas').style.display == 'block'){
					document.getElementById('respostas').style.display = 'none';
					document.getElementById('img_respostas').innerHTML = "<img src='../img/icon_tree_plus.gif' onclick=\"showHide('respostas');\" style='cursor:pointer;'>";
				}else if(document.getElementById('respostas').style.display == 'none'){
					document.getElementById('respostas').style.display = 'block';
					document.getElementById('img_respostas').innerHTML = "<img src='../img/icon_tree_minus.gif' onclick=\"showHide('respostas');\" style='cursor:pointer;'>";
				}
				return true;
			}
			
			function showCitados(){
				// Esta função exibe ou faz hide na
				// table que contém os citados, para
				// que a página não abra totalmente
				if (document.getElementById('citados').style.display == 'block'){
					document.getElementById('citados').style.display = 'none';
					document.getElementById('img_citados').innerHTML = "<img src='../img/icon_tree_plus.gif' onclick=\"showCitados('citados');\" style='cursor:pointer;'>";
				}else if(document.getElementById('citados').style.display == 'none'){
					document.getElementById('citados').style.display = 'block';
					document.getElementById('img_citados').innerHTML = "<img src='../img/icon_tree_minus.gif' onclick=\"showCitados('citados');\" style='cursor:pointer;'>";
				}
				return true;
			}
	
			function focusField(prIDField){
				// OBS: Esta funcao seta o focus
				// para um campo de id especifico
				// informado como parametro
				strIDField = prIDField;
				document.getElementById(strIDField).focus();
			}
			
			var strLocation = null;
			function ok() {
				<?php if($strFlagRedir == ""){?>
				strLocation = "../modulo_Agenda/";
				<?php } else{?>
				strLocation = "../modulo_Agenda/STdatascheduler.php";
				<?php }?>
				submeterForm();
			}

			function cancelar() {
				<?php if($strFlagRedir == ""){?>
				document.location.href = "../modulo_Agenda/";
				<?php } else{?>
				document.location.href = "../modulo_Agenda/STdatascheduler.php";
				<?php }?>
			}

			function aplicar() {
				strLocation = "../modulo_Agenda/STinsresposta.php?var_chavereg=<?php echo($intCodAgenda);?>";
				submeterForm();
			}

			function submeterForm() {
				document.formstatic.DEFAULT_LOCATION.value = strLocation;
				document.formstatic.submit();
			}
		</script>
	</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("710","",getTText("delete_event",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<form name="formstatic" action="STdeleteeventexec.php" method="post">
<input type="hidden" name="var_chavereg" 	 value="<?php echo($intCodAgenda);?>" />
<input type="hidden" name="var_responsavel"  value="<?php echo(getValue($objRS,"id_responsavel"));?>" />
<input type="hidden" name="var_qnt_resposta" value="<?php echo($objResult->rowCount())?>" />
<input type="hidden" name="DEFAULT_LOCATION" value="../modulo_Agenda/" />
<table cellpadding="0" cellspacing="0" border="0" height="315" width="690" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15Px 0px 0px 15px;">
			<strong><?php echo(getTText("confirme_antes_del",C_NONE));?>:</strong>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 70px 10px 70px;">
			<table cellspacing="2" cellpadding="4" border="0" width="100%">
				
				<!-- DADOS AGENDA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_agenda",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("cod_agenda",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"cod_agenda"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("criador_responsavel",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"id_responsavel"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("data_criacao",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),false));?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("titulo",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"titulo"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("descricao",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"descricao"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("categoria",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"categoria"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("prioridade",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"prioridade"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("previsao_ini",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true));?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("previsao_fim",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dtt_fim"),true));?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("realizado_em",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<?php echo(dDate(CFG_LANG,getValue($objRS,"dtt_realizado"),true));?>
						<?php echo((getValue($objRS,"dtt_realizado") == "") ? "<span class='comment_med'><em>Ainda não realizado!</em></span>" : "");?>
					</td>
				</tr>
				<!-- DADOS AGENDA -->
				
				
				
				<tr><td colspan="2">&nbsp;</td></tr>
				
				
				
				<!-- CITADOS -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">
						<span id="img_citados"><img src="../img/icon_tree_plus.gif" onClick="showCitados('');" style="cursor:pointer;"></span>
					</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("citados",C_TOUPPER)." (".$objResultCCount->rowCount().")");?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				
				<tr>
					<td colspan="2">
						<table cellpadding="3" cellspacing="2" border="0" width="100%" id="citados" style="display:none;">
							<?php
								$intContador = 1; 
								foreach($objResultCitados as $objRSCit){
							?>
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right">
									<strong><?php echo(getTText("citado",C_UCWORDS)." ".$intContador);?>:</strong>
								</td>
								<td width="77%" align="left" style="padding-left:4px;"><?php echo(getValue($objRSCit,"id_usuario"));?></td>
							</tr>
							<?php $intContador++; } ?>
						</table>
					</td>
				</tr>
				<!-- CITADOS -->
				
				
				
				<tr><td colspan="2">&nbsp;</td></tr>
				
				
				
				<!-- RESPOSTAS -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">
						<span id="img_respostas">
							<img src="../img/icon_tree_plus.gif" onClick="showHide('');" style="cursor:pointer;">
						</span>
					</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("respostas",C_TOUPPER)." (".$objResult->rowCount().")");?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				
				<tr>
					<td colspan="2">
						<table cellpadding="1" cellspacing="1" border="0" width="100%" id="respostas" style="display:none;">
							<?php if($objResult->rowCount() <= 0){?>
							<tr bgcolor="#FFFFFF">
								<td colspan="2" class="td_no_resp">
									<?php echo(getTText("nenhuma_resposta",C_UCWORDS));?>
								</td>
							</tr>
							<?php 
								} else { 
								foreach($objResultFetch as $objRSResp){
							?>
							<tr bgcolor="#FFFFFF">
								<td colspan="2" class="td_resp" align="center">
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr><td colspan="2" class="td_resp_cabec">
											<?php echo(getTText("resposta",C_UCWORDS));?>
										</td></tr>
										<tr>
											<td width="20%" class="td_resp_conte">
												<?php echo("<strong>".getTText("por",C_UCWORDS).":</strong> ");?>
												<?php echo(getValue($objRSResp,"id_usuario"));?>
											</td>
											<td width="80%" class="td_resp_conte">
												<?php echo("<strong>".getTText("data",C_UCWORDS).":</strong> ");?>
												<?php echo(dDate(CFG_LANG,getValue($objRSResp,"dtt_resposta"),false));?>
											</td>
										</tr>
										<tr>
											<td colspan="2" class="td_resp_conte" style="padding-top:0px;">
												<?php echo("<span class='comment_peq'>".getTText("registrado_por",C_NONE));?>
												<?php echo(" <i><b>".getValue($objRSResp,"sys_usr_ins")."</b></i>");?>
												<?php echo(" ".getTText("em",C_NONE)." ");?>
												<?php echo(dDate(CFG_LANG,getValue($objRSResp,"sys_dtt_ins"),true)."</span>");?>
											</td>
										</tr>
										<tr height="2"><td colspan="2"></td></tr>
										<tr><td colspan="2" class="td_resp_conte">
											<table cellpadding="0" cellspacing="0" border="0" width="93%">
												<tr>
													<td valign="top"><?php echo("<strong>".getTText("texto",C_UCWORDS).":</strong> ");?></td>
													<td class="td_text_resp"><?php echo(getValue($objRSResp,"resposta"));?></td>
												</tr>
											</table>
										</td></tr>
									</table>
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<?php }} ?>
						</table>
					</td>
				</tr>
				<tr><td colspan="2" style="border-bottom:1px solid #CCC;">&nbsp;</td></tr>
			</table>			
		</td>
	</tr>
	<!-- LINHA DOS BUTTONS E AVISO -->
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td width="63%" align="right">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td align="right" width="30%" style="padding-right:8px;"><img src="../img/mensagem_aviso.gif" /></td>
                            <td align="left"  width="70%"><?php echo(getTText("aviso_del_txt",C_NONE));?></td>
                        </tr>
						</table>
					</td>
					<!-- goNext() -->
					<td width="10%" align="left"><button onClick="ok();return false;"><?php echo(getTText("ok",C_NONE));?></button></td>
					<td width="27%" align="left" style="padding-right:25px;"><button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_NONE));?></button></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
</table>
</form>
<?php athEndFloatingBox();?>
</center>
</body>
</html>