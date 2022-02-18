<!-- DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" //-->
<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");       // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

$strPopulate = "yes";
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session
// verificação de ACESSO
// carrega o prefixo das sessions
$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
// verificação de acesso do usuário corrente
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");

// Abertura de Conexão com o BD
$objConn = abreDBConn(CFG_DB);
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function gerar(opcao)
{
	strExibirMsg = 0;
	
	if ((document.getElementById("var_data_antes").value == "")  && (document.getElementById("var_data_depois").value == "")){
		strExibirMsg = 1;
	}
	if ((document.getElementById("var_vlr_titulo_antes").value == "")   && (document.getElementById("var_vlr_titulo_depois").value == "")){
		strExibirMsg = 1;
	}
	if ((document.getElementById("var_cod_conta_antes").value == "")   && (document.getElementById("var_cod_conta_depois").value == "")){
		strExibirMsg = 1;
	}
	if ((document.getElementById("var_cod_centro_antes").value == "")   && (document.getElementById("var_cod_centro_depois").value == "")){
		strExibirMsg = 1;
	}
	if ((document.getElementById("var_job_antes").value == "")   && (document.getElementById("var_job_depois").value == "")){
		strExibirMsg = 1;
	}
	if ((document.getElementById("var_observacao_antes").value == "")   && (document.getElementById("var_observacao_depois").value == "")){
		strExibirMsg = 1;
	}
	if ((document.getElementById("var_cfg_boleto_antes").value == "")   && (document.getElementById("var_cfg_boleto_depois").value == "")){
		strExibirMsg = 1;
	}
	
	if (strExibirMsg  == 0)	{
	document.formeditalote.submit();
	}
	else {alert('Favor preencher todos os campos do formulário, afim de garantir a integridade das alterações');}
}

function cancelar() {
	document.location.href = "index.php";	
}
</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("750","none","<b>".getTText("edicao_titulo_lote",C_NONE)."</b>",CL_CORBAR_GLASS_1); ?>
      <table id="var_dialog" name="var_dialog" cellpadding="0" cellspacing="0" style="width:730px; border:1px solid #A6A6A6; display:block; background-color:#FFFFFF;">
        <form name="formeditalote" action="STeditaTituloLotePasso2.php" method="post">
		<tr> 
			<td valign="top" style="text-align:center;" align="center">

				<table cellspacing="0" cellpadding="4" style="widh:600px; border:0px solid #F00;" align="center">
					<tr>
						<td align="left" style="padding-left:5px;" colspan="2"><img src="../img/titulo_lote_passo01.gif"></td>
					</tr>					
                    <tr>
						<td align="left" style="padding-left:5px;" colspan="2"><strong><?php echo(getTText("preparacao_edita_titulo",C_NONE)); ?></strong></td>
					</tr>
					<tr><td height="22" colspan="2"></td></tr>
					<tr>
						<td align="center" style="padding-left:5px;" colspan="2">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="34%">&nbsp;</td>
							<td width="33%"><strong><?php echo(getTText("antes",C_NONE)); ?></strong></td>
                            <td width="33%"><strong><?php echo(getTText("depois",C_NONE)); ?></strong></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="2"><hr></td>
						</tr>
                        <tr>
							<td width="34%" align="right" style="padding-right:10px;">* <?php echo(getTText("dt_vencimento",C_NONE)); ?></td>
							<td width="33%" align="left"><input type="text" name="var_data_antes" id="var_data_antes" style="width:60px" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="return validateNumKey(event);"></td>
							<td width="33%" align="left"><input type="text"  name="var_data_depois" id="var_data_depois" style="width:60px" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="return validateNumKey(event);"></td>
						</tr>
						<tr> 
							<td width="34%" align="right" style="padding-right:10px;">* <?php echo(getTText("vlr_titulo",C_NONE)); ?></td>
							<td width="33%" align="left"><input type="text" name="var_vlr_titulo_antes" id="var_vlr_titulo_antes" style="width:60px" maxlength="10" onKeyPress="return validateFloatKeyNew(this,event);"></td>    
							<td width="33%" align="left"><input type="text"  name="var_vlr_titulo_depois" id="var_vlr_titulo_depois" style="width:60px" maxlength="10" onKeyPress="return validateFloatKeyNew(this,event);"></td>
						</tr>
                        <tr>
							<td width="34%" align="right" style="padding-right:10px;">* <?php echo(getTText("plano_conta",C_NONE)); ?></td>
							<td width="35%" align="left">
                                <select name="var_cod_conta_antes" id="var_cod_conta_antes" class="edtext" style="width:230px;">
                                	<option value=""></option>
									<?php echo(montaCombo($objConn," SELECT cod_plano_conta, cod_reduzido || ' ' || nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ","cod_plano_conta","rotulo",'')); ?>									

                                </select>
                            </td>
							<td width="35%" align="left">
                            	<select name="var_cod_conta_depois" id="var_cod_conta_depois" class="edtext" style="width:230px;">
                                	<option value=""></option>
									<?php echo(montaCombo($objConn," SELECT cod_plano_conta, cod_reduzido || ' ' || nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ","cod_plano_conta","rotulo",'')); ?>									
                                </select>                            
                            </td>
						</tr>                        
                        <tr>
							<td width="34%" align="right" style="padding-right:10px;">* <?php echo(getTText("centro_custo",C_NONE)); ?></td>
							<td width="35%" align="left">
                            	<select name="var_cod_centro_antes" id="var_cod_centro_antes" class="edtext" style="width:230px;">
									<option value=""></option>
									<?php echo(montaCombo($objConn,"SELECT cod_centro_custo, nome FROM fin_centro_custo ORDER BY 2","cod_centro_custo","nome",'')); ?>									
								</select>
                            </td>
							<td width="35%" align="left">
                               	<select name="var_cod_centro_depois" id="var_cod_centro_depois" class="edtext" style="width:230px;">
									<option value=""></option>
									<?php echo(montaCombo($objConn,"SELECT cod_centro_custo, nome FROM fin_centro_custo ORDER BY 2","cod_centro_custo","nome",'')); ?>
								</select>
                            </td>
						</tr>
                        <tr>
							<td width="65%" align="right"  style="padding-right:10px;">* <?php echo(getTText("job",C_NONE)); ?></td>
							<td width="65%"  align="left">
                                <select name="var_job_antes" id="var_job_antes" class="edtext" style="width:230px;">
                                    <option value=""></option>
                                    <?php echo(montaCombo($objConn," SELECT cod_job, nome FROM fin_job WHERE dtt_inativo IS NULL ORDER BY nivel, ordem ","cod_job","nome",'')); ?>
                                </select>                            
                            </td>
							<td width="65%" align="left">
                                <select name="var_job_depois" id="var_job_depois" class="edtext" style="width:230px;">
                                    <option value=""></option>
                                    <?php echo(montaCombo($objConn," SELECT cod_job, nome FROM fin_job WHERE dtt_inativo IS NULL ORDER BY nivel, ordem ","cod_job","nome",'')); ?>
                                </select>                           
                            </td>
						</tr>                        
                        <tr>
							<td width="65%" align="right"  style="padding-right:10px;">* Boleto</td>
							<td width="65%"  align="left">
                                <select name="var_cfg_boleto_antes" id="var_cfg_boleto_antes" class="edtext" style="width:230px;">
                                    <option value=""></option>
                                    <?php echo(montaCombo($objConn,"SELECT cod_cfg_boleto, descricao FROM cfg_boleto Where length(descricao) >1  Order By cod_cfg_boleto desc","cod_cfg_boleto","descricao",'')); ?>
                                </select>                            
                            </td>
							<td width="65%" align="left">
                                <select name="var_cfg_boleto_depois" id="var_cfg_boleto_depois" class="edtext" style="width:230px;">
                                    <option value=""></option>
                                    <?php echo(montaCombo($objConn,"SELECT cod_cfg_boleto, descricao FROM cfg_boleto Where length(descricao) >1  Order By cod_cfg_boleto desc","cod_cfg_boleto","descricao",'')); ?>
                                </select>                           
                            </td>
						</tr>
                        
                        
                        
                         <tr>
							<td width="65%" align="right"  style="padding-right:10px;">* <?php echo(getTText("observacao",C_NONE)); ?></td>
							<td width="65%"  align="left">
                                <textarea style="width:230px;" id="var_observacao_antes" name="var_observacao_antes" cols="50" rows="5"></textarea>                        
                            </td>
							<td width="65%" align="left">
                                <textarea style="width:230px;" id="var_observacao_depois" name="var_observacao_depois" cols="50" rows="5"></textarea>                           
                            </td>
						</tr>
                        
						</table>
						</td>
					</tr>
					<tr><td height="10" colspan="2"></td></tr>
					<tr align="left">
						<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px;"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
					</tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
					<tr>
						<td colspan="2">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
										<button onClick="gerar(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
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
</html>
