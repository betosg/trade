<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodDado  = request("var_chavereg"); // Código da Relação PJ x PF
	$strRedirect = request("var_redirect");
	$strOperacao = request("var_oper"); // Operação a ser realizada
	$strExec 	 = request("var_exec"); // Executor externo (fora do kernel)
	$strPopulate = request("var_populate"); // Flag para necessidade de popular o session ou não
	$strAcao 	 = request("var_acao"); // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
	
	// VERIFICAÇÃO DE ACESSO
	//if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "HOMO");
	
	//Inicia objeto para manipulação do banco
	$objConn = abreDBConn(CFG_DB);
	
	// Só é feito a busca e exibição dos dados 
	// seja enviado como parametro para este script.
	if($intCodDado == ""){
		$strErro = "Código de Relação PJxPF inválido.";
		mensagem("err_sql_titulo","err_sql_desc_card",$strErro,"","aviso",1);
		die();
	}
	
	// Verifica a existencia de pedidos abertos de carteirinhas para 
	// a PJ corrente - Busca usando como parâmetro o cod_pedido 
	// tbm enviado para esta pag. - uma das situações
	try{
		$strSQL = "
				SELECT	
					cad_pf.cod_pf
				,	cad_pf.nome
				,	cad_pf.apelido
				,	cad_pf.nome_pai
				,	cad_pf.nome_mae
				,	cad_pf.cpf
				,	cad_pf.rg
				,	cad_pf.ctps
				
				,	cad_pj.cod_pj
				,	cad_pj.razao_social
				,	cad_pj.nome_fantasia
				,	cad_pj.cnpj
				,	cad_pj.endprin_cidade
				,	cad_pj.endprin_estado
				
				,	relac_pj_pf.tipo
				,	relac_pj_pf.dt_admissao
				,	relac_pj_pf.funcao
				FROM relac_pj_pf, cad_pf, cad_pj
				WHERE relac_pj_pf.cod_pj_pf = " . $intCodDado . "
				AND relac_pj_pf.cod_pj = cad_pj.cod_pj
				AND relac_pj_pf.cod_pf = cad_pf.cod_pf ";
		
		$objResult = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	//fetch dos dados
	$objRS = $objResult->fetch();
	
	$intCodPF 	= getValue($objRS,"cod_pf");
	$strNome 	= getValue($objRS,"nome");
	$strApelido = getValue($objRS,"apelido");
	$strNomePai = getValue($objRS,"nome_pai");
	$strNomeMae = getValue($objRS,"nome_mae");
	$strRG 		= getValue($objRS,"rg");
	$strCPF 	= getValue($objRS,"cpf");
	$strCTPS 	= getValue($objRS,"ctps");
	
	$intCodPJ 	= getValue($objRS,"cod_pj");
	$strRazaoSocial 	= getValue($objRS,"razao_social");
	$strNomeFantasia 	= getValue($objRS,"nome_fantasia");
	$strCNPJ 	= getValue($objRS,"cnpj");
	$strCidade 	= getValue($objRS,"endprin_cidade");
	$strEstado 	= getValue($objRS,"endprin_estado");
	
	$strFuncao 	= getValue($objRS,"funcao");
	$strTipo 	= getValue($objRS,"tipo");
	$dtAdmissao = dDate(CFG_LANG,getValue($objRS,"dt_admissao"),false);

?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
		<!--
			//****** Funções de ação dos botões - Início ******
			var strLocation = null;
			function verifica(){
			var strMSG = "";
					
			// CABEÇALHO DE DADOS DO AGENDAMENTO
			if(
				(document.formeditor.var_dtt_ped_agendamento_homo.value == "") ||
				(document.formeditor.var_ped_hr_agendamento_homo.value  == "")
			  ){ strMSG += "\n\nDADOS DO AGENDAMENTO:" }
			if(document.formeditor.var_dtt_ped_agendamento_homo.value == ""){ strMSG += "\nData de Agendamento"; }
			if(document.formeditor.var_ped_hr_agendamento_homo.value  == ""){ strMSG += "\nHorário do Agendamento"; }
			
			if (strMSG == "") {
				//document.formeditor.action.value = "";
				document.formeditor.submit();
			}
			else {
				alert("Informar os campos obrigatórios abaixo:" + strMSG);
			}
		}

			function cancelar() {
				location.href="STColabAtivos.php";
				//window.history.back();
			}
			
			function ajaxGetHorarios(prDia){
			// alert(prDia);
			if(prDia == null || prDia == ""){ return(null); } 
			var objAjax;
			var strReturnValue;
			
			document.getElementById("loader_ajax").innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' />";
						
			objAjax = createAjax();
			objAjax.onreadystatechange = function() {
				if(objAjax.readyState == 4) {
					if(objAjax.status == 200) {
						strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
						// alert(strReturnValue);
						// alert(strReturnValue.search(/@@/ig));
						
						if(strReturnValue.search(/@@/ig) != -1){						
							document.getElementById("loader_ajax").innerHTML = "<span style='color:red;font-size:09px;'>(Nenhum Horário Disponível Neste Dia!)</span>";
							setTimeout("document.getElementById('loader_ajax').innerHTML = '';",4000);
							return(null);
						}
											
						// alert(strReturnValue);
						// Cria uma opção em branco
						var optionBlank   = document.createElement('option');
						optionBlank.text  = "...";
						var obj 		  = document.getElementById("var_ped_hr_agendamento_homo");
						obj.add(optionBlank);
						
						// Dados
						var Item1, Item2, prDados;
						var arrAux1 = null;
						var arrAux2 = null;
						prDados = strReturnValue;
						arrAux1 = prDados.split("|");
																	
						if(prDados.length > 1) {
							for(Item1 in arrAux1) {
								Item2 = arrAux1[Item1];
								//arrAux2 = Item2.split("|");
							
								var optionNew = document.createElement('option');
								optionNew.setAttribute('value',Item2);
								var textOption =  document.createTextNode(Item2);
								optionNew.appendChild(textOption);
								obj.appendChild(optionNew);
							
								//obj.add( new Option(caption,value) );
								//obj.add( new Option(arrAux2[1],arrAux2[0]) );
							}
						}
						document.getElementById("loader_ajax").innerHTML = "";					
					} else {
						alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
					}
				}
			}
			objAjax.open("GET", "../_ajax/STreturnhorarios.php?var_dia=" + prDia,  true); 
			objAjax.send(null); 
		}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("725","none",getTText("gera_homo",C_NONE),CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="705" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #777;">
	<form name="formeditor" action="STGeraHomoexec.php" method="post">
		<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado);?>">
		<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ);?>">
		<input type="hidden" name="var_cod_pf" value="<?php echo($intCodPF);?>">
		<input type="hidden" name="var_nome" value="<?php echo($strNome);?>">
		<input type="hidden" name="var_cpf" value="<?php echo($strCPF);?>">
		<input type="hidden" name="var_redirect" value="<?php echo($strRedirect);?>" />
		<tr>
			<td height="12" style="padding:20px 0px 0px 20px;">
				<strong><?php echo(getTText("confirmacao_homo",C_NONE)); ?></strong>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" style="padding:20px 80px 10px 80px;" width="1%">
				<table cellpadding="4" cellspacing="0" border="0" width="100%">
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS DA EMPRESA</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("razao_social",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strRazaoSocial); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("nome_fantasia",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNomeFantasia); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("cnpj",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCNPJ); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("cidade",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCidade); ?> / <?php echo($strEstado); ?></td>
					</tr>
					<tr><td colspan="2" height="5">&nbsp;</td></tr>
					
					
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS DO COLABORADOR</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("nome",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNome); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("nome_pai",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNomePai); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("nome_mae",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNomeMae); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("rg",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strRG); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("cpf",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCPF); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("ctps",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCTPS); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("tipo",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strTipo); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("funcao",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strFuncao); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("dt_admissao",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($dtAdmissao); ?></td>
					</tr>
					<tr><td colspan="2" height="5">&nbsp;</td></tr>
					
					
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS DA SOLICITAÇÃO</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%"><label for="var_obs"><strong><?php echo(getTText("obs",C_NONE)); ?>:</strong></label></td>
						<td><textarea name="var_obs" id="var_obs" cols="60" rows="5"></textarea><br>
						<span class="comment_med">Informe qualquer comentário sobre esse processo de demiss&atilde;o/rescis&atilde;o. Ele ficará armazenada nos registros da homologação.</span></td>
					</tr>
					<tr><td colspan="2" height="5">&nbsp;</td></tr>
					
					
					<!-- BLOCO: AGENDAMENTO DA HOMOLOGAÇÃO -->
					<!-- BLOCO: AGENDAMENTO DA HOMOLOGAÇÃO -->
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>AGENDAMENTO DA HOMOLOGAÇÃO</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" style="padding-right:5px;"><strong>*Próximos <?php echo(getVarEntidade($objConn,"ag_intervalo_busca_horarios"));?> dias de Atendimento:</strong></td>
								<td>
									<select name="var_dtt_ped_agendamento_homo" style="width:170px;" onChange="limpaSelect('var_ped_hr_agendamento_homo');ajaxGetHorarios(this.value);">
										<option value="" selected="selected">Selecione uma Data...</option>
										<?php if(getVarEntidade($objConn,"ag_atendimento_fds") != 1){?>
											<?php if((getWeekDay($strDATE) != "sabado")&&(getWeekDay($strDATE) != "domingo")){?>
											<option value="<?php echo(date("d-m-Y"));?>"><?php echo("Dia ".dDate(CFG_LANG,now(),false)." - ".ucwords(getWeekDay(date("Y-m-d")))."-feira");?></option>
											<?php }?>
										<?php }?>
										<?php for($auxCounter = 1; $auxCounter <= getVarEntidade($objConn,"ag_intervalo_busca_horarios"); $auxCounter++){?>
										<?php $strDATE = cDate(CFG_LANG,dateAdd("d",$auxCounter,now()),false); ?>
										<?php $arrDATE = explode("-",$strDATE); ?>
										<?php $strDATE = $arrDATE[2]."-".$arrDATE[1]."-".$arrDATE[0];?>
										<?php if(getVarEntidade($objConn,"ag_atendimento_fds") != 1){?>
											<?php if((getWeekDay($strDATE) == "sabado")||(getWeekDay($strDATE) == "domingo")){ continue; }?>
										<?php }?>
										<option value="<?php echo(cDate(CFG_LANG,dateAdd("d",$auxCounter,now()),false));?>">
											<?php 
												echo("Dia ".dDate(CFG_LANG,dateAdd("d",$auxCounter,now()),false));
												echo(((getWeekDay($strDATE) == "sabado")||(getWeekDay($strDATE) == "domingo")) ? " - ".ucwords(getWeekDay($strDATE)) : " - ".ucwords(getWeekDay($strDATE))."-feira");
											?>
										</option>
										<!-- $strSQL = "SELECT ag_agenda.cod_agenda FROM ag_agenda WHERE DATE(ag_agenda.prev_dtt_ini) = CURRENT_DATE + ".$auxCounter." AND ag_agenda.dtt_realizado IS NULL";echo($strSQL."<br />"); -->
										<?php }?>
									</select>
									<!--input name="var_dtt_ped_agendamento_homo" value="" type="text" size="12" maxlength="10" title="Data de Agendamento" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);">&nbsp;&nbsp;<input type="text" name="var_ped_hr_agendamento_homo" size="5" maxlength="5" onKeyPress="FormataInputHoraMinuto(this,event);" /-->
								</td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td width="1%" align="right" valign="top" style="padding-right:5px;"><strong>*Horários Disponíveis:</strong></td>
								<td>
									<select name="var_ped_hr_agendamento_homo" style="width:70px"></select>
									&nbsp;
									<span id="loader_ajax"></span>
									<!--input name="var_dtt_ped_agendamento_homo" value="" type="text" size="12" maxlength="10" title="Data de Agendamento" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);">&nbsp;&nbsp;<input type="text" name="var_ped_hr_agendamento_homo" size="5" maxlength="5" onKeyPress="FormataInputHoraMinuto(this,event);" /-->
									<br /><span class="comment_med">Observações:</span>
									<br /><span class="comment_med">&bull;&nbsp;<?php echo(getTText("horario_atendimento",C_NONE));?></span>
									<br /><span class="comment_med">&bull;&nbsp;Este agendamento será confirmado posteriormente pelo devido responsável, portanto podem haver alterações. Caso sua disponibilidade não se enquadre com os horários e dias disponíveis, por favor entre em contato com nossa sede.</span>
								</td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					
					
					<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>
					<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
					
					
					<tr>
						<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
								<tr>
									<td align="right" width="1%" style="padding: 0px 0px 0px 0px;"><img src="../img/mensagem_aviso.gif"></td>
									<td align="left" width="98%" style="padding: 0px 0px 0px 10px;"><?php echo(getTText("aviso_solicita_homo",C_NONE))?></td>
									<td width="1%" align="left" style="padding:10px 10px 10px 10px;" nowrap>
										<button onClick="verifica(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
										<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
									</td>
								</tr>
							</table>
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
	<script type="text/javascript">
	  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php 
	$objConn = NULL; 
?>