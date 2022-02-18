<?php
	// HEADERS ANTI-CACHE
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodDado = request("var_chavereg");					// COD_CONTA_PAGAR_RECEBER
	
	// TRATAMENTO PARA O CODIGO DO TITULO VAZAIO
	if($intCodDado == ""){
		echo(
			"<center>
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
					<tr>
					<td align=\"center\" valign=\"middle\" width=\"100%\">");
				mensagem("err_dados_titulo","err_sql_desc_card",getTText("cpf_matricula_null",C_NONE),"","aviso",1)	;
				echo 
				   ("</td>
					</tr>
				</table>
			</center>");
		die();
	}
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// VERIFICA SE TITULO É AGRUPADO OU NÃO, PARA REDIRECT CORRETO
	try{
		$strSQL    = "SELECT cod_conta_pagar_receber FROM fin_conta_pagar_receber WHERE cod_agrupador = ".$intCodDado.";";
		//die($strSQL);
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// LOCALIZA O CNPJ E RAZAO_SOCIAL DA PJ PARA INSERIR COMO DICA
	try{
		$strSQL = "
			SELECT 
				       cad_pf.nome  as razao_social,
				       cad_pf.cpf as cnpj
			FROM 
				cad_pf
			INNER JOIN fin_conta_pagar_receber ON (fin_conta_pagar_receber.codigo = cad_pf.cod_pf)
			WHERE fin_conta_pagar_receber.cod_conta_pagar_receber = ".$intCodDado;
		//die($strSQL);
		$objResultJ = $objConn->query($strSQL);
		$objRSJ = $objResultJ->fetch();
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// MONTA DICA PARA 'SACADO'
	$strVALUE  = "";
	$strVALUE .= getValue($objRSJ,"razao_social");
	$strVALUE .= (getValue($objRSJ,"cnpj") != "") ? " (CPF ".getValue($objRSJ,"cnpj").")" : "";
	
	// MONTAR UMA DIALOG QUE PERGUNTA O NOME DO CIDADAO E QUE
	// TENHA SEU ACTION PARA UM DOS DOIS LINKS ABAIXO, OK?
	// DEVE TER TAMBÉM UM HIDDEN CONTENDO O CHAVEREG
	//$strActionForm = ($objResult->rowCount() > 0) ? "STshowreciboagrupado.php" : "STshowrecibonormal.php";
	$strActionForm = "STshowrecibonormal.php";
	// ANULA OBETO DE CONEXÃO COM DB
	$objConn = NULL;
	$objResult->closeCursor();
	
	
	// INICIALIZA VARIÁVEL PARA PINTAR LINHA
	$strColor = CL_CORLINHA_1;
	
	// FUNÇÃO PINTA LINHA
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
			var strLocation = null;
			function ok() {
				var strNomeSacado;
				strNomeSacado 	= document.getElementById('var_nome_sacado').value;
				strCheck2Vias   = (document.getElementById('var_duas_vias').checked == true) ? document.getElementById('var_duas_vias').value : "";
				AbreJanelaPAGE('<?php echo($strActionForm."?var_chavereg=".$intCodDado."&var_nome_sacado=");?>'+strNomeSacado+"&var_duas_vias="+strCheck2Vias,'800','600');
				document.location.href = "../modulo_FinContaPagarReceber/STifrrecibos.php?var_chavereg=<?php echo($intCodDado);?>";
			}

			function cancelar() {
				document.location.href = "../modulo_FinContaPagarReceber/STifrrecibos.php?var_chavereg=<?php echo($intCodDado);?>";
			}

			function submeterForm() {
				document.formstatic_resp.DEFAULT_LOCATION.value = strLocation;
				document.formstatic_resp.submit();
			}
				
		</script>
	</head>
<body bgcolor="#FFFFFF"  style="margin:10px 0px 10px 0px;">
<!-- body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;"  -->

<!-- USO -->
<center>
<?php athBeginFloatingBox("520","",getTText("ins_recibo",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<table cellpadding="0" cellspacing="0" border="0" width="500" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15Px 0px 0px 15px;">
			<strong><?php echo(getTText("confirme_dados_recibo",C_NONE));?>:</strong>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 30px 10px 30px;">
			<table cellspacing="2" cellpadding="3" border="0" width="100%">
				
				<!-- DADOS AGENDA -->
				<tr bgcolor="#FFFFFF">
					<td width="30%" align="right">&nbsp;</td>
					<td width="70%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("definicao_nome_sacado",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<form name="formstatic_resp" action="<?php echo($strActionForm)?>" method="post">
				<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado);?>" />
				<input type="hidden" name="var_cod_conta_pagar_receber" value="<?php echo($intTITULO);?>">
				<input type="hidden" name="DEFAULT_LOCATION" value="" />
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="30%" align="right"><strong>*<?php echo(getTText("nome_sacado",C_UCWORDS));?>:</strong></td>
					<td width="70%" align="left">
						<input type="var_nome_cartao" id="var_nome_cartao_ô" size="60" maxlength="100" value="" />
					</td>
                </tr>

                <tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="30%" align="right"><strong>*<?php echo(getTText("CPF",C_UCWORDS));?>:</strong></td>
					<td width="70%" align="left">
                        <input type="number" placeholder="CPF" id="cartao_var_cpf_titular_ô" name="var_cpf_titular" onkeypress="Javascript:return validateNumKey(event);return false;" maxlength="11" onblur="Javascript://cpfValida(this.value);return false;">
                    </td>
                </tr>

				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
                    <td width="30%" align="right"><strong>* Número Cartão / CVV:</strong></td>
					<td width="70%" align="left">
                        <input type="number" placeholder="Numero cartão" id="cartao_var_num_cartao_ô" name="var_cartao" autofocus="" maxlength="16" onblur="checkCreditCardFlag(this.value)">
                        <input type="number" placeholder="CVV" id="cartao_var_cod_cartao_ô" name="var_cod_cartao" maxlength="5" autofocus="">
                        <input type="hidden" id="bandeira" name="var_bandeira">
                        <div style="width:100%; padding: 5px;">
                                <div id="band_visa" style="margin:auto; width:100px; height:100%; display:inline-block; opacity:0.3;"><img src="./img/band_visa.jpg" border="0"></div>
                                <div id="band_master" style="margin:auto; width:100px; height:100%; display:inline-block; opacity:0.3;"><img src="./img/band_mastercard.jpg" border="0"></div>
                                <!--div id="band_amex" style="margin:auto; width:100px; height:100%; display:inline-block; opacity:0.3;"><img src="./img/band_amex.jpg" border="0"></div//-->
                        </div> 
                    </td>                    
                </tr>
                <tr bgcolor="<?php echo(getLineColor($strColor));?>">
                    <td width="30%" align="right"><strong>* Validade:</strong></td>
                    <td width="70%" align="left">    
                        <select name="var_mes_cartao" id="cartao_var_mes_cartao_ô">                                                    
                            <option value="">MÊS</option>
                            <option value="1">1</option>                                                    
                            <option value="2">2</option>                                                    
                            <option value="3">3</option>                                                    
                            <option value="4">4</option>                                                    
                            <option value="5">5</option>                                                    
                            <option value="6">6</option>                                                    
                            <option value="7">7</option>                                                    
                            <option value="8">8</option>                                                    
                            <option value="9">9</option>                                                    
                            <option value="10">10</option>                                                    
                            <option value="11">11</option>                                                    
                            <option value="12">12</option>                                                    
                        </select>
                        <select name="var_ano_cartao" id="cartao_var_ano_cartao_ô">
                            <option value="">ANO</option>
                        <?php for ($x = date("Y"); $x <= date("Y")+12; $x++) {?>
                            <option value="<?php echo($x)?>"><?php echo($x)?></option>
                        <?php } ?>
                        </select>
                    </td>
                </tr>
				</form>
				<!-- DADOS NOVA RESPOSTA -->
				
				<tr>
					<td colspan="2" style="border-bottom:1px solid #CCC;padding-top:15px;">
						<span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span>
					</td>
				</tr>
								
				
			</table>			
		</td>
	</tr>
	<!-- LINHA DOS BUTTONS E AVISO -->
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td width="20%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr><td align="right" width="23%" style="padding-right:8px;"></td></tr>
						</table>
					</td>
					<!-- goNext() -->
					<td width="55%" align="right"><button onClick="ok();"><?php echo(getTText("ok",C_NONE));?></button></td>
					<td width="25%" align="left" ><button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_NONE));?></button></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
</table>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">

        function checkCreditCardFlag(cardNumber){
            
            document.getElementById('band_visa').style.opacity = 0.3;
            document.getElementById('band_master').style.opacity = 0.3;
            //document.getElementById('band_amex').style.opacity = 0.3;
            
            var isValid = false;
            var ccCheckRegExp = /[^\d ]/;
            isValid = !ccCheckRegExp.test(cardNumber);

            var cardNumbersOnly = cardNumber.replace(/ /g,"");
            var cardNumberLength = cardNumbersOnly.length;
            var lengthIsValid = false;
            var prefixIsValid = false;
            var prefixRegExp;

            //Master
            prefixRegExp = /^5[0-9][0-9]{14}/;
            prefixIsValid = prefixRegExp.test(cardNumbersOnly);
            if(prefixIsValid) { document.getElementById("bandeira").value = 'MASTER'; document.getElementById('band_master').style.opacity = 1; }

            //Visa
            prefixRegExp = /^4[0-9]{12}(?:[0-9]{3})?/;
            prefixIsValid = prefixRegExp.test(cardNumbersOnly);
            if(prefixIsValid) { document.getElementById("bandeira").value = 'VISA'; document.getElementById('band_visa').style.opacity = 1; }

            //Amex
        // prefixRegExp = /^3[47][0-9]{13}/;
            //prefixIsValid = prefixRegExp.test(cardNumbersOnly);
        //if(prefixIsValid) { document.getElementById("bandeira").value = 'AMEX'; document.getElementById('band_amex').style.opacity = 1; }

            
        }
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>