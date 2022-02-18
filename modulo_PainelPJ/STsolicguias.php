<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// ABERTURA DE CONEXÃO COM BANCO
CFG_DB;
$objConn = abreDBConn(CFG_DB);

$intCodProduto = request("var_chavereg");

// Inicializa variavel para pintar linha
$strColor = CL_CORLINHA_1;

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

$intCodDado = getSession(CFG_SYSTEM_NAME."_pj_selec_codigo");

if ($intCodDado != "") {
	try{
		$strSQL = " SELECT cod_cnae_n2, capital FROM cad_pj WHERE cod_pj = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	if($objResult->rowCount()>0){
		$objRS = $objResult->fetch();
		$strCodAtividade = str_pad(getValue($objRS,"cod_cnae_n2"), 3, "0", STR_PAD_LEFT);
		$vlrCapital = getValue($objRS,"capital");
		if ($strCodAtividade == "000") $strCodAtividade = "";
	}
	$objResult->closeCursor();
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	$strRepProduto = getVarEntidade($objConn, "guia_atraso_repetir_produto");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../_tradeunion/_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<title><?php echo(strtoupper(CFG_SYSTEM_NAME)." - Solicitação Guia Avulsa");?></title>
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
	var strMSG = "";
	
	strMSG += (document.getElementById('var_cod_produto').value	== "")     ? "\nProduto"      : "";
	strMSG += (document.getElementById('var_vlr_conta').value 	== "0,00") ? "\nValor Título" : "";
	strMSG += (document.getElementById('var_vlr_conta').value 	== "")     ? "\nValor Título" : "";
	strMSG += (document.getElementById('var_exercicio').value 	== "")     ? "\nExercicio"    : "";
	strMSG += (document.getElementById('var_dt_admissao').value == "")     ? "\nData Admissão" : "";
	
	// GUARDA PARA DOCUMENTO DIGITALIZADO	
	if (strMSG != "") { 
		alert('Os seguintes campos não foram preenchidos:\n'+strMSG); 
		return(false); 
	}
	else return(true);
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
	return true;
	/*
	if (document.getElementById('var_dt_admissao').value != ''){
		var dataForm = (document.getElementById('var_dt_admissao').value).split("/");  
		var hoje = new Date();  
		var dataInformada = new Date(dataForm[2], dataForm[1], dataForm[0]);  
		if ((hoje < dataInformada)){
			alert("Data de admissão nao pode ser maior que a data atual");
			document.getElementById('var_dt_admissao').value = "";
			document.getElementById('var_dt_admissao').focus();				
		}
	}
	else return false;
	*/
}

</script>
</head>
<body bgcolor="#F5F5F5"  background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_collapsed.jpg">
  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="middle">
    	  <?php athBeginFloatingBox("720","none","<strong>Guia de Recolhimento Avulsa</strong>",CL_CORBAR_GLASS_1); ?>
		     <table width="700" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
                   <tr><td colspan="2" height="10">&nbsp;</td></tr>
                   <tr>
					<td align="center" width="100%" valign="top" style="padding:0px 80px 0px 80px;"> 
                         <form name="formstatic" action="STsolicguiasexec.php" method="post">
                            <input type="hidden" name="var_cod_pj" value="<?php echo($intCodDado); ?>" />
							<input type="hidden" name="var_tipo" value="cad_pj" />
                          	<table cellpadding="4" cellspacing="0" border="0" width="100%">
                           		<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                                <tr>
                            		<td></td>
		                            <td align="left" valign="top" class="destaque_gde"><strong>DADOS DA SOLICITA&Ccedil;&Atilde;O</strong></td>
        	                    </tr>
                    	        <tr>
                                    <td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td>
                          		</tr>
                                <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
									<td align="right" width="35%"><strong>*<?php echo(getTText("produto",C_NONE)); ?>:</strong></td>
									<td>
									<select name="var_cod_produto" id="var_cod_produto" style="width:180px;" onChange="javascript:busca_produto(this.value);">
										<option value=""></option>
										<?php
										if ($intCodProduto == "") {
											if ($strRepProduto == "N") {
												$strSQL = " SELECT DISTINCT t1.cod_produto, t1.rotulo || ' (R$ ' || t1.valor || ')' AS rotulo_valor, t2.it_cod_produto
															FROM prd_produto t1
															LEFT JOIN prd_pedido t2 ON (t2.cod_pj = ".$intCodDado." AND t1.cod_produto = t2.it_cod_produto)
															WHERE t1.dtt_inativo IS NULL 
															AND t2.it_cod_produto IS NULL
															AND CURRENT_DATE BETWEEN t1.dt_ini_val_produto AND t1.dt_fim_val_produto 
															AND t1.visualizacao = 'publico' 
															AND ((t1.tipo <> 'homo' AND t1.tipo <> 'card') OR t1.tipo IS NULL) 
															ORDER BY 2 ";
											}
											else {
												$strSQL = " SELECT DISTINCT t1.cod_produto, t1.rotulo || ' (R$ ' || t1.valor || ')' AS rotulo_valor
															FROM prd_produto t1
															WHERE t1.dtt_inativo IS NULL 
															AND CURRENT_DATE BETWEEN t1.dt_ini_val_produto AND t1.dt_fim_val_produto 
															AND t1.visualizacao = 'publico' 
															AND ((t1.tipo <> 'homo' AND t1.tipo <> 'card') OR t1.tipo IS NULL) 
															ORDER BY 2 ";
											}
										}
										else {
											//Se recebeu código do produto buscamos produto independente das condições acima
											$strSQL = " SELECT DISTINCT t1.cod_produto, t1.rotulo || ' (R$ ' || t1.valor || ')' AS rotulo_valor
														FROM prd_produto t1
														WHERE t1.cod_produto = ".$intCodProduto."
														ORDER BY 2 ";
										}
										echo(montaCombo($objConn,$strSQL,"cod_produto","rotulo_valor",$intCodProduto));
										?>
									</select>&nbsp;<span class="comment_peq"><?php echo getTText("valor_referenciado",C_NONE); ?></span>
									</td>
								</tr>
                          		<?php if (getVarEntidade($objConn,"guia_usar_indice") == "usar_indice"){?>
                                <tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
                                    <td align="right"><strong>*<?php echo getTText("capital_social",C_NONE); ?>:</strong></td>
                                    <td align="left" width="65%">
                                      <input name="var_vlr_capital_social" id="var_vlr_capital_social" type="text" onKeyPress="return(validateFloatKeyNew(this,event));" style="width:70px;" value="<?php echo(FloatToMoeda($vlrCapital)); ?>">
                                      <span class="comment_peq"><?php echo getTText("capital_social",C_NONE); ?></span>
									</td>
                                </tr>
                                <?php }?>
                                <tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
                                    <td align="right"><strong>*<?php echo getTText("valor",C_NONE); ?>:</strong></td>
                                    <td align="left" width="65%">
                                      <input name="var_vlr_conta" id="var_vlr_conta" type="text" value="0,00" onKeyPress="return(validateFloatKeyNew(this,event));" style="width:70px;">
                                      <span class="comment_peq"><?php echo getTText("sem_juros_sem_multa",C_NONE); ?></span>
									</td>
                                </tr>
								<?php 
								//Campo reativado dia 31/08/2012 por causa do chamado 13671
								//Tinha sido desativado a pedido pelo chamado 11680 de abr/2012
								//by Clv 03/09/2012
								?>
								<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
									<td align="right"><strong>*<?php echo getTText("dt_admissao",C_NONE); ?>:</strong></td>
									<td align="left" width="65%">
										<input name="var_dt_admissao" id="var_dt_admissao" type="text" onKeyPress="return FormataInputDataNew(this,event);" style="width:70px;">
										<span class="comment_med"><?php echo(getTText("obs_formato_data",C_NONE));?></span>
									</td>
								</tr>
                          		<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
                            		<td align="right" width="35%"><strong>*<?php echo getTText("exercicio",C_NONE); ?>:</strong></td>
                            		<td align="left" width="65%">
										<select name="var_exercicio" id="var_exercicio" style="width:60px;">
											<?php	                                           
												for($i = date("Y") - 5; $i <= date("Y"); $i++) {
													echo("<option value='" .$i . "'");
													if ($i == date("Y")) echo " selected='selected'";
													echo (">".$i."</option>");
												}	
											  ?>
										</select>
									</td>
                          		</tr>
                                 <tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
                                    <td align="right" width="35%"><strong><?php echo getTText("cod_atividade",C_NONE); ?>:</strong></td>
                                    <td align="left" width="65%"><input name="var_codatividade" id="var_codatividade" type="text" onKeyPress="return validateNumKey(event);" value="<?php echo $strCodAtividade; ?>" style="width:70px;"></td>
                                </tr>
								<!--
                                <tr bgcolor="<?php //echo(CL_CORLINHA_2)?>">
                                	<td align="right" width="35%"><strong>Atividade:</strong></td>
                               		<td align="left" width="65%"><input name="var_atividade" id="var_atividade" type="text"></td>
                              	</tr>
								-->
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
	}
	$objConn = NULL;
?>