<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));


$objConn = abreDBConn(CFG_DB);

$intCodigo = request("var_chavereg");
$strOper   = request("var_oper");

try{
	$strSQL = " SELECT titulo, id_responsavel, id_ult_executor, prev_dt_ini, prev_horas, situacao, prioridade, tl_categoria.nome, tl_todolist.descricao, dt_realizado
				 FROM tl_todolist, tl_categoria 
				WHERE tl_todolist.cod_todolist = " . $intCodigo . "
				  AND tl_todolist.cod_categoria = tl_categoria.cod_categoria ";
	$objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}


if($objRS = $objResult->fetch()){
?>
<html>
<head>
<title>PROEVENTO STUDIO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function collapseItem(prCodBookmark){
	if(document.getElementById("historico") != null){
		if(document.getElementById("historico").style.display == "block"){
			document.getElementById("historico").style.display = "none";
			document.getElementById("historico_img").src = "../img/collapse_generic_close.gif";
		}
		else{
			document.getElementById("historico").style.display = "block";
			document.getElementById("historico_img").src = "../img/collapse_generic_open.gif";
		}
	}
}

function submeterForm(){
	document.forminsresposta.submit();
}

function deleteResposta(prCodResposta, prCodTodolist, prOper){
	if(confirm("<?php echo(getTText("confirm_delete_chamado",C_NONE)); ?>")){
		location.href = "chamadodel.php?var_chavereg=" + prCodResposta + "&var_cod_todolist=" + prCodTodolist + "&var_oper=" + prOper;
	}
}
//-->
</script>
<style>
	.conteudo_grade { padding-left:10px; }
</style>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("550","none",getTText("chamado",C_TOUPPER) . " (" . getTText("visualizar",C_UCWORDS) . ")",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
			<tr>
				<td align="center" valign="top">
					<table width="500" border="0" cellspacing="0" cellpadding="4">
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="100" align="right"><b><?php echo(getTText("cod_todolist",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="650"><?php echo($intCodigo); ?></td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>" width="100" align="right"><b><?php echo(getTText("titulo",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>"><?php echo(getValue($objRS,"titulo")); ?></td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="100" align="right"><b><?php echo(getTText("situacao_dialog",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>"><?php echo(getTText(str_replace("status_img_","",getValue($objRS,"situacao")),C_UCWORDS)); ?></td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>" width="100" align="right"><b><?php echo(getTText("categoria",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>"><?php echo(getValue($objRS,"nome")); ?></td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="100" align="right"><b><?php echo(getTText("prioridade_dialog",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>"><?php echo(getTText(str_replace("status_img_","",getValue($objRS,"prioridade")),C_UCWORDS)); ?></td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>" width="100" align="right"><b><?php echo(getTText("id_responsavel",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>"><?php echo(getValue($objRS,"id_responsavel")); ?></td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="100" align="right"><b><?php echo(getTText("id_ult_executor",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>"><?php echo(getValue($objRS,"id_ult_executor")); ?></td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>" width="100" align="right"><b><?php echo(getTText("prev_dt_ini",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>"><?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dt_ini"),false) . " (" . getValue($objRS,"prev_horas") . ")"); ?></td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="100" align="right"><b><?php echo(getTText("dt_realizado",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_realizado"),false)); ?></td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>" width="100" align="right" valign="top"><b><?php echo(getTText("descricao",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>"><?php echo(getValue($objRS,"descricao")); ?></td>
						</tr>
						<?php /*if($strOper == "INS_RESP") { ?>
						<tr><td colspan="2" align="left" valign="top" bgcolor="#DBDBDB"><b><?php echo(getTText("nova_resposta",C_UCWORDS)); ?></b></td></tr>
						<form name="forminsresposta" action="wizardrespostaexec.php" method="post">
						<input type="hidden" name="var_cod_todolist" value="<?php echo($intCodigo); ?>">
						<input type="hidden" name="var_oper" value="INS_RESP">
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="100" align="right"><b><?php echo(getTText("de",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="650">
								<select name="var_de">
									<?php echo(montaCombo($objConn, " SELECT id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL ", "id_usuario", "id_usuario", getsession(CFG_SYSTEM_NAME . "_id_usuario"), "")); ?>
								</select>
							</td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>" width="100" align="right"><b><?php echo(getTText("para",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>">
								<select name="var_para">
									<option value=""></option>
									<?php echo(montaCombo($objConn, " SELECT id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL ", "id_usuario", "id_usuario", getValue($objRS,"id_ult_executor"), "")); ?>
								</select>
							</td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="100" align="right"><b><?php echo(getTText("situacao_dialog",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="650">
								<select name="var_situacao">
									<option value="status_img_aberto"<?php if(getValue($objRS,"situacao") == "status_img_aberto") echo(" selected"); ?>><?php echo(getTText("aberto",C_UCWORDS)); ?></option>
									<option value="status_img_fechado"<?php if(getValue($objRS,"situacao") == "status_img_fechado") echo(" selected"); ?>><?php echo(getTText("fechado",C_UCWORDS)); ?></option>
									<option value="status_img_executando"<?php if(getValue($objRS,"situacao") == "status_img_executando") echo(" selected"); ?>><?php echo(getTText("executando",C_UCWORDS)); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>" width="100" align="right"><b><?php echo(getTText("prioridade_dialog",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>">
								<select name="var_prioridade">
									<option value="status_img_normal"<?php if(getValue($objRS,"prioridade") == "status_img_normal") echo(" selected"); ?>><?php echo(getTText("normal",C_UCWORDS)); ?></option>
									<option value="status_img_baixa"<?php if(getValue($objRS,"prioridade") == "status_img_baixa") echo(" selected"); ?>><?php echo(getTText("baixa",C_UCWORDS)); ?></option>
									<option value="status_img_media"<?php if(getValue($objRS,"prioridade") == "status_img_media") echo(" selected"); ?>><?php echo(getTText("media",C_UCWORDS)); ?></option>
									<option value="status_img_alta"<?php if(getValue($objRS,"prioridade") == "status_img_alta") echo(" selected"); ?>><?php echo(getTText("alta",C_UCWORDS)); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="100" align="right"><b><?php echo(getTText("dt_realizado",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="650"><input type="text" name="var_dt_realizado" value="<?php echo(dDate(CFG_LANG,getValue($objRS,"dt_realizado"),false)); ?>" size="10" maxlength="10"></td>
						</tr>
						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>" width="100" align="right"><b><?php echo(getTText("resposta",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_2); ?>"><textarea name="var_resposta" rows="5" cols="50"></textarea></td>
						</tr>
 						<tr>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>" width="100" align="right"><b><?php echo(getTText("horas_disp",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td bgcolor="<?php echo(CL_CORLINHA_1); ?>"><input type="text" name="var_horas" size="5" onKeyDown="FormataInputHoraMinuto(this,event);"></td>
						</tr>
						</form>
						<tr><td height="5" colspan="2"></td></tr>
						<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
						<tr>
							<td align="right" colspan="2" style="padding:10px 0px 10px 10px;">
								<button onClick="submeterForm();"><?php echo(getTText("gravar",C_UCWORDS)); ?></button>
								<button onClick="location.href='<?php echo(getsession($strSesPfx . "_grid_default")); ?>';"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
							</td>
						</tr>
						<?php }
							  else{ */?>
						<tr><td height="5" colspan="2"></td></tr>
						<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
						<tr>
							<td align="right" colspan="2" style="padding:10px 0px 10px 10px;">
								<button onClick="window.close();"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
							</td>
						</tr>
						<?php // } ?>
					</table>
				</td>
			</tr>
		</table>
	<?php athEndFloatingBox(); ?>
	<br><br>
	<?php athBeginWhiteBox("550"); ?>
		<table border="0" width="100%">
			<tr>
				<td align="center" valign="top">
					<table width="500" border="0" cellspacing="0" cellpadding="4">
						<tr>
							<td colspan="2" align="left" valign="top" bgcolor="#DBDBDB" onClick="collapseItem();" style="cursor:pointer">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="99%"><b><?php echo(getTText("historico",C_UCWORDS)); ?></b></td>
										<td width="1%"><img id="historico_img" src="../img/collapse_generic_open.gif"></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?php
									try{
										$strSQL = "SELECT cod_resposta, dtt_resposta, id_to, id_from, resposta, horas FROM tl_resposta WHERE cod_todolist = " . $intCodigo . " ORDER BY dtt_resposta DESC ";
										$objResultResposta = $objConn->query($strSQL);
									}
									catch(PDOException $e){
										mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
										die();
									}
									if($objResultResposta->rowCount() > 0){
										echo("
								<table id=\"historico\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" background=\"../img/grid_backheader.gif\" style=\"background-repeat:repeat-x;display:block;\">
									<tr height=\"22\">
										<td align=\"left\" nowrap width=\"08%\"></td>
										<td align=\"left\" nowrap width=\"04%\" class=\"titulo_grade\">" . getTText("dtt_resposta",C_UCWORDS) . "</td>
										<td align=\"left\" nowrap width=\"06%\" class=\"titulo_grade\">" . getTText("de",C_UCWORDS) . "</td>
										<td align=\"left\" nowrap width=\"06%\" class=\"titulo_grade\">" . getTText("para",C_UCWORDS) . "</td>
										<td align=\"left\" width=\"74%\" class=\"titulo_grade\">" . getTText("resposta",C_UCWORDS) . "</td>
										<td align=\"left\" nowrap width=\"02%\" class=\"titulo_grade\">" . getTText("horas_disp",C_UCWORDS) . "</td>
									</tr>
											");
											
										$strCor = "";
										$dblTotalMinutos = 0;
										$intTotalHoras = 0;
										$boolPrimeiraResposta = true;
										
										foreach($objResultResposta as $objRSResposta){
											$strCor = ($strCor != CL_CORLINHA_1) ? CL_CORLINHA_1 : CL_CORLINHA_2 ;
											
											if(getValue($objRSResposta,"horas") != ""){
												$arrHoras = explode(":",getValue($objRSResposta,"horas"));
												
												$dblTotalMinutos += $arrHoras[1]/60;
												$intTotalHoras += $arrHoras[0];
												if($dblTotalMinutos >= 1){	$dblTotalMinutos -= 1;	$intTotalHoras += 1; }
												
											}
											if($boolPrimeiraResposta && getValue($objRSResposta,"id_from") == getsession(CFG_SYSTEM_NAME . "_id_usuario")){
												$strIconTrash = "
													<a href=\"javascript:deleteResposta('" . getValue($objRSResposta,"cod_resposta") . "','" . $intCodigo . "','" . $strOper . "')\">
														<img src=\"../img/icon_trash.gif\" border=\"0\">
													</a>";
												$boolPrimeiraResposta = false;
											}
											else{
												$strIconTrash = "";
											}
											
											if(preg_match("/^@@system_message(.*)/",getValue($objRSResposta,"resposta"))){
												$strMensagem = substr(getValue($objRSResposta,"resposta"),2);
												$arrMensagem = explode("@@",$strMensagem);
												if(is_date($arrMensagem[1]) && is_date($arrMensagem[2])){
													$strMensagem = getTText($arrMensagem[0],C_NONE) . " " . dDate(CFG_LANG,$arrMensagem[1],false) . " " . getTText("para",C_TOLOWER) . " " . dDate(CFG_LANG,$arrMensagem[2],false);
												}
											}
											else{
												$strMensagem = getValue($objRSResposta,"resposta");
											}
											
											echo("
									<tr bgcolor=\"" . $strCor . "\" height=\"22\">
										<td align=\"left\" nowrap width=\"08%\" class=\"conteudo_grade\">" . $strIconTrash . "</td>
										<td align=\"left\" nowrap width=\"04%\" class=\"conteudo_grade\">" . dDate(CFG_LANG,getValue($objRSResposta,"dtt_resposta"),true) . "</td>
										<td align=\"left\" nowrap width=\"06%\" class=\"conteudo_grade\">" . getValue($objRSResposta,"id_from") . "</td>
										<td align=\"left\" nowrap width=\"06%\" class=\"conteudo_grade\">" . getValue($objRSResposta,"id_to") . "</td>
										<td align=\"left\" width=\"74%\" class=\"conteudo_grade\">" . $strMensagem . "</td>
										<td align=\"left\" nowrap width=\"02%\" class=\"conteudo_grade\">" . getValue($objRSResposta,"horas") . "</td>
									</tr>
											");
										}
										
										$intTotalMinutos = $dblTotalMinutos * 60;
										
										if(getValue($objRS,"prev_horas") != ""){
											$arrHorasPrev = explode(":",getValue($objRS,"prev_horas"));
											
											if($intTotalHoras > $arrHorasPrev[0] || ( $intTotalHoras == $arrHorasPrev[0] && $intTotalMinutos > $arrHorasPrev[1])){
												$strCorTotalHoras = "red";
											}
											else{
												$strCorTotalHoras = "green";
											}
										}
										else{
											$strCorTotalHoras = "black";
										}
										
										echo("
									<tr><td align=\"left\" nowrap bgcolor=\"#999999\" colspan=\"6\" height=\"1\" width=\"100%\"></td></tr>
									<tr>
										<td align=\"right\" colspan=\"6\" style=\"padding-top:5px;padding-right:14px;color:" . $strCorTotalHoras . "\">
											<b>Total&nbsp;&nbsp;&nbsp;" . sprintf("%02s:%02s ", $intTotalHoras, $intTotalMinutos) . "</b>
										</td>
									</tr>
								</table>
											");
									}
								?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	<?php athEndWhiteBox(); ?>
   </td>
  </tr>
</table>
</body>
</html>
<?php 
}
$objResult->closeCursor();
?>