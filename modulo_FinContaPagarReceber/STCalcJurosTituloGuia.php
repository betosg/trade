<?php 
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// ABERTURA DE CONEXÃO COM BANCO
	$objConn = abreDBConn(CFG_DB);
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	
	$intCodDado = request("var_chavereg");
	
	
	$strVlrTitulo = 0;
	$strCodAtividade = "";
	try{
		$strSQL = " SELECT t1.cod_cnae_n2, t1.cod_pj, t1.razao_social, t2.vlr_conta, t2.situacao, t2.nosso_numero
		                 , t2.tipo_documento, t2.num_documento, t2.dt_emissao, t2.dt_vcto, t2.ano_vcto, t2.historico, t2.obs, t2.cod_cfg_boleto
					FROM cad_pj t1, fin_conta_pagar_receber t2 
					WHERE t1.cod_pj = t2.codigo
					AND t2.tipo ILIKE 'cad_pj'
					AND t2.cod_conta_pagar_receber = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	$objRS = $objResult->fetch();
	
	$strVlrTitulo = getValue($objRS,"vlr_conta");
	$strCodAtividade = getValue($objRS,"cod_cnae_n2");
	$strSituacao = getValue($objRS,"situacao");
	$strRazaoSocial = getValue($objRS,"razao_social");
	$strNossoNumero = getValue($objRS,"nosso_numero");
	$strTipoDocumento = getValue($objRS,"tipo_documento");
	$strNumDocumento = getValue($objRS,"num_documento");
	$dateDtEmissao = getValue($objRS,"dt_emissao");
	$dateDtVcto = getValue($objRS,"dt_vcto");
	$intAnoVcto = getValue($objRS,"ano_vcto");
	$strHistorico = getValue($objRS,"historico");
	$strObs = getValue($objRS,"obs");
	$intCodCfgBoleto = getValue($objRS,"cod_cfg_boleto");
	
	$objResult->closeCursor();
	
	if ($strSituacao != "aberto") {
		mensagem("","Erro com situação do título","Título encontra-se em situação diferente de aberto.","","erro",1);
		die();
	}
	
	$strCodAtividade = ($strCodAtividade != "") ? str_pad($strCodAtividade, 3, "0", STR_PAD_LEFT) : "745";
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	$strVlrTitulo = number_format((double) $strVlrTitulo, 2);
	$strVlrTitulo = str_replace(",", "", $strVlrTitulo);
	$strVlrTitulo = str_replace(".", ",", $strVlrTitulo);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../_tradeunion/_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<title><?php echo(strtoupper(CFG_SYSTEM_NAME)." - Cálculo de Juros para Guia");?></title>
<style type="text/css">
	.span_manual{
		float:right;
		background-image:url(../img/icon_document_pdf.png);
		background-repeat:no-repeat;
		background-position:right;
		height:15px;
		padding-top: 2px;
		padding-right:20px;
		margin-right:5px;
		cursor:pointer;
		font-size:10px;
		color:#009900;
		font-weight:600;
	}
</style>
<script>
	function validaCampos(){
		// Esta função faz uma pré-validação via
		// js dos campos marcados com asterisco
		var strMSG  = "";
		
		//strMSG += (document.getElementById('var_vlr_conta').value 			== "0,00") ? "\nValor Título" 		: "";	
		strMSG += (document.getElementById('var_vlr_titulo').value 		== "") ? "\nValor Título" 		: "";	
		strMSG += (document.getElementById('var_exercicio').value 		== "") ? "\nExercicio" 			: "";	
	<?php if (strtoupper($strTipoDocumento) != 'BOLETO_ASSISTENCIAL'){?>
		strMSG += (document.getElementById('var_dt_admissao').value 	== "") ? "\nData Admissão" 		: "";
		strMSG += (document.getElementById('var_cod_cfg_boleto').value 	== "") ? "\nModelo de Boleto" 	: "";
	<?php }?>
		if(strMSG != ""){ alert('Os seguintes campos não foram preenchidos:\n'+strMSG); return(false); }
		else { return(true); }
	}
	
	function ok(){
		if(validaCampos()){
			document.getElementById('formstatic').submit();
		} else{
			return(false);
		}
	}
	
	function cancelar(){
		window.history.back();
	}
	
	function checkDtAdmissao(){
		if (document.getElementById('var_dt_admissao').value != ''){
			var dataForm = (document.getElementById('var_dt_admissao').value).split("/");  
			var hoje = new Date();  
			var dataInformada = new Date(dataForm[2], dataForm[1]-1, dataForm[0]);  
			//alert(hoje);
			//alert(dataInformada);
			if ((hoje < dataInformada)){
				alert("Data de admissão nao pode ser maior que a data atual");
				document.getElementById('var_dt_admissao').value = "";
				document.getElementById('var_dt_admissao').focus();				
			}
		}
		else return false;
	}
	//-->
</script>
</head>
<body bgcolor="#F5F5F5"  background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_collapsed.jpg">
  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="middle">
    	  <?php athBeginFloatingBox("720","none","<strong>Cálculo de Juros para Guia</strong>",CL_CORBAR_GLASS_1); ?>
		     <table width="700" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
                   <tr><td colspan="2" height="10">&nbsp;</td></tr>
                   <tr>
					<td align="center" width="100%" valign="top" style="padding:0px 80px 0px 80px;"> 
                         <form name="formstatic" id="formstatic" action="STCalcJurosTituloGuiaExec.php" method="post">
                            <input type="hidden" name="var_chavereg" value="<?php echo($intCodDado); ?>" />
                            <input type="hidden" name="var_tipo_documento" value="<?php echo($strTipoDocumento); ?>" />
							<input type="hidden" name="var_dt_vcto" value="<?php echo($dateDtVcto); ?>" />							
                            <input type="hidden" name="var_cod_cfg_boleto" value="<?php echo $intCodCfgBoleto; ?>" />
                          	<table cellpadding="4" cellspacing="0" border="0" width="100%">
                           		<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                                <tr>
                            		<td></td>
		                            <td align="left" valign="top" class="destaque_gde"><strong>DADOS DO TÍTULO</strong></td>
        	                    </tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="#FFFFFF">
									<td align="right"><b><?php echo(getTText("cod_conta_pagar_receber",C_UCWORDS)); ?>:&nbsp;</b></td>
									<td><?php echo($intCodDado); ?></td>
								</tr>			 		
								<tr bgcolor="#FAFAFA"> 
									<td align="right"><b><?php echo(getTText("razao_social",C_UCWORDS)); ?>:&nbsp;</b></td>
									<td valign="middle"><?php echo($strRazaoSocial); ?></td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td align="right"><b><?php echo(getTText("nosso_numero",C_UCWORDS)); ?>:&nbsp;</b></td>
									<td><?php echo($strNossoNumero); ?></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td align="right"><b><?php echo(getTText("tipo_documento",C_UCWORDS)); ?>:&nbsp;</b></td>
									<td>
										<table width="100%" cellpadding="0px" cellspacing="0px" border="0px">
											<tr>
												<td width="90px">
													<?php echo(getTText(strtolower($strTipoDocumento),C_UCWORDS)); ?>
												</td>
												<td width="110px" align="right"><b><?php echo(getTText("num_documento",C_UCWORDS)); ?>:&nbsp;</b></td>
												<td><?php echo($strNumDocumento); ?></td>
											</tr>
										</table>
									</td>	
								</tr>
								<tr bgcolor="#FFFFFF"> 
									<td align="right"><b><?php echo(getTText("dt_emissao",C_UCWORDS)); ?>:&nbsp;</b></td>
									<td>
										<table width="100%" cellpadding="0px" cellspacing="0px" border="0px">
											<tr>
												<td width="90px"><?php echo(dDate(CFG_LANG,$dateDtEmissao,false)); ?></td>
												<td width="110px" align="right"><b><?php echo(getTText("dt_vcto",C_UCWORDS)); ?>:&nbsp;</b></td>
												<td><?php echo(dDate(CFG_LANG,$dateDtVcto,false)); ?></td>
											</tr>
										</table>
									</td>		
								</tr>
								<tr bgcolor="#FAFAFA"> 
									<td align="right"><b><?php echo(getTText("historico",C_UCWORDS)); ?>:&nbsp;</b></td>
									<td style="padding-left:2px;"><?php echo($strHistorico); ?></td>
								</tr>
								<tr bgcolor="#FFFFFF"> 
									<td align="right"><b><?php echo(getTText("obs",C_UCWORDS)); ?>:&nbsp;</b></td>
									<td><?php echo($strObs); ?></td>
								</tr>
                                <tr>
                            		<td></td>
		                            <td align="left" valign="top" class="destaque_gde"><strong>DADOS DA SOLICITA&Ccedil;&Atilde;O</strong></td>
        	                    </tr>
                    	        <tr>
                                    <td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td>
                          		</tr>
                                <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                          		<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
                                    <td align="right"><strong>*<?php echo getTText("valor",C_NONE); ?>:</strong></td>
                                    <td align="left" width="65%">
										<input name="var_vlr_titulo" id="var_vlr_titulo" type="text" value="<?php echo $strVlrTitulo; ?>" onKeyPress="return(validateFloatKeyNew(this,event));" style="width:70px;">
									</td>
                                </tr>						  
								<?php /*Só solicitamos para o user esta informação quando não for boleto assistencial - by vini 07.02.2013*/
                                       if(strtoupper($strTipoDocumento) != 'BOLETO_ASSISTENCIAL') { 
								?>
                                       
                                         <tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
                                             <td align="right"><strong>*<?php echo getTText("dt_admissao",C_NONE); ?>:</strong></td>
                                             <td align="left" width="65%">
                                                 <input name="var_dt_admissao" id="var_dt_admissao" type="text" onBlur="checkDtAdmissao();" onKeyPress="return FormataInputDataNew(this,event);" style="width:70px;">
                                                 <span class="comment_med"><?php echo(getTText("obs_formato_data",C_NONE));?></span>
                                             </td>
                                         </tr>
                                <?php } ?>
                          		<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
                            		<td align="right" width="35%"><strong>*<?php echo getTText("exercicio",C_NONE); ?>:</strong></td>
                            		<td align="left" width="65%">
										<select name="var_exercicio" id="var_exercicio" style="width:60px;">
											<?php	                                           
												for($i = date("Y") - getVarEntidade($objConn, "arnet_periodo_exercicio"); $i <= date("Y"); $i++) {
													echo("<option value='" .$i . "'");
													if ($i == date("Y")) echo " selected='selected'";
													echo (">".$i."</option>");
												}	
											  ?>
										</select>
									</td>
                          		</tr>
                                 <tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
                                    <td align="right" width="35%"><strong><?php echo getTText("cod_atividade",C_NONE); ?>:</strong></td>
                                    <td align="left" width="65%"><input name="var_codatividade" id="var_codatividade" type="text" onKeyPress="return validateNumKey(event);" value="<?php echo $strCodAtividade; ?>" style="width:70px;"></td>
                                </tr>
                          		<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
                            		<td align="right" width="35%"><strong>*<?php echo getTText("cod_cfg_boleto",C_NONE); ?>:</strong></td>
                            		<td align="left" width="65%">
                                    <select name="var_cod_cfg_boleto" style="width:230px;">
                                    	<?php echo(montaCombo($objConn," SELECT cod_cfg_boleto, descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY descricao ","cod_cfg_boleto","descricao",$intCodCfgBoleto)); ?>
                                    </select>
                                    </td>
                          		</tr>
                          		<tr>
                               		<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo getTText("campos_obrig",C_NONE); ?></td>
                          		</tr>
                          		<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
                          		<tr>
                            		<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">                                
                                            <tr>
                                                <td align="right" width="1%" style="padding: 0px 0px 0px 0px;"><img src="../img/mensagem_info.gif"></td>
                                                <td align="left" width="98%" style="padding: 0px 0px 0px 10px;"><?php echo getTText("aviso_geracao_guia_recolhimento",C_NONE); ?></td>
                                                <td width="1%" align="left" style="padding:10px 10px 10px 10px;" nowrap>
                                                    <button onClick="ok(); return false;">Ok</button>
                                                    <button onClick="cancelar(); return false;">Cancelar</button>
												</td>
                                            </tr>
                                        </table>
									</td>
                                </tr>
                            </table>
                         </form>
                  </td>
                  </tr>
              </table>
          <?php athEndFloatingBox(); ?>
		</td>
      </tr>	   
    </table>     	
</body>
</html>
<?php
	$objConn = NULL;
?>