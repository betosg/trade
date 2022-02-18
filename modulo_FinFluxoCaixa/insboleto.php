<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$objConn   = abreDBConn(CFG_DB);

$intCodDado   = request("var_chavereg");
//$intCodBoleto = request("var_cod_boleto");
$intCodBoleto = 1;

$arrEstados = array("AC","AL","AP","AM","BA","CE","DF","ES","GO","MA","MG","MT","MS","PA","PB","PE","PI","PR","RJ","RN","RO","RR","RS","SC","SE","SP","TO");
$arrNome    = array("Acre","Alagoas","Amapá","Amazonas","Bahia","Ceará","Distrito Federal","Espírito Santo","Goiás","Maranhão","Minas Gerais", "Mato Grosso",
					"Mato Grosso do Sul","Pará","Paraíba","Pernambuco","Piauí","Paraná","Rio de Janeiro","Rio Grande do Norte","Rondônia","Roraima","Rio Grande do Sul",
					"Santa Catarina","Sergipe","São Paulo","Tocantins");

try{
	$strSQL = " SELECT
					  cedente_nome
					, cedente_agencia
					, cedente_cnpj
					, cedente_codigo
					, cedente_codigo_dv
					, cod_cliente
					, banco_codigo
					, banco_dv
					, banco_img
					, boleto_aceite
					, boleto_carteira
					, boleto_especie
					, boleto_tipo_doc
					, local_pgto
					, instrucoes
					, modelo_html
				FROM
					cfg_boleto
				WHERE
					cod_cfg_boleto = " . $intCodBoleto;
	$objResultBoleto = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

if($objRSBoleto = $objResultBoleto->fetch()) {
	$strFormAction 			  = getValue($objRSBoleto,"modelo_html");
	$strBoletoImgPromo		  = getValue($objRSBoleto,"banco_img");
	
	$strBoletoAgencia		  = getValue($objRSBoleto,"cedente_agencia");
	$strBoletoAceite 		  = getValue($objRSBoleto,"boleto_aceite");
	
	$strBoletoCarteira		  = getValue($objRSBoleto,"boleto_carteira");
	$strBoletoCedenteNome	  = getValue($objRSBoleto,"cedente_nome");
	$strBoletoCedenteCodigo	  = getValue($objRSBoleto,"cedente_codigo");
	$strBoletoCedenteCodigoDV = getValue($objRSBoleto,"cedente_codigo_dv");
	$strBoletoCedenteCNPJ	  = getValue($objRSBoleto,"cedente_cnpj");
	$intBoletoCodBanco		  = getValue($objRSBoleto,"banco_codigo");
	$intBoletoCodBancoDV	  = getValue($objRSBoleto,"banco_dv");
	$strBoletoCodCliente	  = getValue($objRSBoleto,"cod_cliente");
	$strBoletoConta			  = getValue($objRSBoleto,"cedente_codigo");
	$strBoletoContaDV		  = getValue($objRSBoleto,"cedente_codigo_dv");
	
	$strBoletoEspecie 		  = getValue($objRSBoleto,"boleto_especie");
	$strBoletoTipoDoc		  = getValue($objRSBoleto,"boleto_tipo_doc");
	$strBoletoInstrucoes	  = getValue($objRSBoleto,"instrucoes");
	$strBoletoLocalPgto		  = getValue($objRSBoleto,"local_pgto");
	
	$objResultBoleto->closeCursor();
	
	try{
		$strSQL = " SELECT	
						  tipo
						, codigo
						, dt_emissao
						, dt_vcto
						, vlr_conta as valor
						, num_impressoes
						, num_documento
						, num_nf
					FROM
						fin_conta_pagar_receber
					WHERE
						cod_conta_pagar_receber = "	. $intCodDado;
		$objResultConta = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	if($objRSConta = $objResultConta->fetch()){
		$dblBoletoValor         = number_format((double) getValue($objRSConta,"valor"),2,",","");
		$dateBoletoDtVencimento = getValue($objRSConta,"dt_vcto");
		$strBoletoNumDocumento  = getValue($objRSConta,"num_documento");
		$intNumImpressoes	    = getValue($objRSConta,"num_impressoes");
		
		$strBoletoNossoNumero = getValue($objRSConta,"num_nf");
		if($strBoletoNossoNumero == "") { $strBoletoNossoNumero = $intCodDado; }
		
		try{
			$strSQL = " SELECT
						   cad." . ((getValue($objRSConta,"tipo") == "F") ? "nome" : "razao_social") . "	   AS cli_nome
						 , (SELECT valor FROM cad_doc_pf AS doc WHERE doc.nome = '" . ((getValue($objRSConta,"tipo") == "F") ? "CPF" : "CNPJ") . "' AND cad.cod_p" . getValue($objRSConta,"tipo") . " = " . getValue($objRSConta,"codigo") . " LIMIT 1) AS cli_num_doc
						 , endr.cep 		AS cli_cep
						 , endr.endereco	AS cli_ender
						 , endr.numero		AS cli_numero
						 , endr.complemento	AS cli_compl
						 , endr.bairro		AS cli_bairro
						 , endr.cidade		AS cli_cidade
						 , endr.estado		AS cli_estado
						FROM 
						   cad_p" . getValue($objRSConta,"tipo") . "          AS cad
						 , cad_endereco_p" . getValue($objRSConta,"tipo") . " AS endr
						WHERE cad.cod_p" . getValue($objRSConta,"tipo") . " = endr.cod_p" . getValue($objRSConta,"tipo") . "
						  AND cad.cod_p" . getValue($objRSConta,"tipo") . " = " . getValue($objRSConta,"codigo") . "
						ORDER BY endr.cod_endereco ASC
						LIMIT 1 ";
			$objResultSacado = $objConn->query($strSQL);
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
					  
	    if($objRSSacado = $objResultSacado->fetch()) {
			
			$strBoletoSacadoNome	 	  = getValue($objRSSacado,"cli_nome");
			$strBoletoSacadoEndereco	  = getValue($objRSSacado,"cli_ender"); 
			$strBoletoSacadoBairro		  = getValue($objRSSacado,"cli_bairro");
			$strBoletoSacadoCidade		  = getValue($objRSSacado,"cli_cidade");
			$strBoletoSacadoEstado		  = getValue($objRSSacado,"cli_estado");
			$strBoletoSacadoCEP			  = getValue($objRSSacado,"cli_cep");
			$strBoletoSacadoIdentificador = getValue($objRSSacado,"cli_num_doc");
			
			if(getValue($objRSSacado,"cli_numero") != "") { $strBoletoSacadoEndereco .= ", "  . GetValue($objRSSacado,"cli_numero"); } 
			if(getValue($objRSSacado,"cli_compl")  != "") { $strBoletoSacadoEndereco .= " - " . GetValue($objRSSacado,"cli_compl"); } 
			
			$objResultSacado->closeCursor();
		}
	}
	
?> 
<html>
<head>
<title>ProEvento Studio</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function submeterForm(prAcao){
	document.formconf.submit();
}

function searchModulo(prType){
	if(prType == "pessoa"){
		combo         = document.forms[0].var_tipo;
		strModulo     = (combo.options[combo.selectedIndex].value == "F") ? "CadPF" : "CadPJ";
		strComponente = "var_codigo";
	}
	else if(prType == "centrocusto"){
		strModulo     = "FinCentroCusto";
		strComponente = "var_cod_centro_custo";
	}
	else if(prType == "planoconta"){
		strModulo     = "FinPlanoConta";
		strComponente = "var_cod_plano_conta";
	}

	AbreJanelaPAGE("../modulo_" + strModulo + "/?var_acao=single&var_fieldname=" + strComponente + "&var_formname=formconf","800", "600");
}
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%"> 
 <tr> 
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("fin_conta_pagar_receber",C_TOUPPER) . " - " . getTText("lcto_ord_",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formconf" action="../_boletos/<?php echo($strFormAction); ?>" method="post">
			<input name="var_chavereg"				type="hidden" value="<?php echo($intCodDado); ?>">
			<input name="var_boleto_num_impressoes"	type="hidden" value="<?php echo($intNumImpressoes); ?>">	
			<input name="var_boleto_cod_cliente"	type="hidden" value="<?php echo($strBoletoCodCliente); ?>">
			<tr>
				<td align="center" valign="top">
					<table cellspacing="0" cellpadding="4" width="550" border="0">
					  <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					  <tr><td align="left" valign="top" colspan="2" class="destaque_gde" style="padding-left:85px;"><b><?php echo(getTText("dados_cedente",C_UCWORDS)); ?></b></td></tr>
					  <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					  <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					  <tr bgcolor="#FAFAFA">
					    <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">Cedente:&nbsp;</td>
					        <td><input type="text" value="<?php echo($strBoletoCedenteNome); ?>" name="var_boleto_cedente_nome" /></td>
					      </tr>
					    </table></td>
					    <td colspan="2"></td>
					  </tr>
					  <tr bgcolor="#FFFFFF">
					    <td width="50%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">CNPJ:&nbsp;</td>
					        <td><input type="text" onKeyPress="return(validateFloatKeyNew(this,event));" maxlength="18" value="<?php echo($strBoletoCedenteCNPJ); ?>" name="var_boleto_cedente_cnpj" /></td>
					      </table></td>
					    <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="85">Ag&ecirc;ncia:&nbsp;</td>
					        <td><input type="text" onKeyPress="validateNumKey();" maxlength="4" value="<?php echo($strBoletoAgencia); ?>" name="var_boleto_agencia" /></td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr bgcolor="#FAFAFA">
					    <td width="50%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">Conta:&nbsp;</td>
					        <td><input type="text" onKeyPress="validateNumKey();" maxlength="10" value="<?php echo($strBoletoCedenteCodigo); ?>" name="var_boleto_cedente_codigo" />
					          &nbsp;-&nbsp;
					          <input type="text" onKeyPress="validateNumKey();" size="1" maxlength="1" value="<?php echo($strBoletoCedenteCodigoDV); ?>" name="var_boleto_cedente_codigo_dv" />
					        </td>
					      </tr>
					    </table></td>
					    <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="85">Carteira:&nbsp; </td>
					        <td><input type="text" maxlength="3" value="<?php echo($strBoletoCarteira); ?>" name="var_boleto_carteira" /></td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					  <tr><td align="left" valign="top" colspan="2" class="destaque_gde" style="padding-left:85px;"><b><?php echo(getTText("dados_boleto",C_UCWORDS)); ?></b></td></tr>
					  <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					  <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					  <tr bgcolor="#FFFFFF">
					    <td width="50%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">Valor:&nbsp;</td>
					        <td><input type="text" onKeyPress="return(validateFloatKeyNew(this,event));" maxlength="14" value="<?php echo($dblBoletoValor); ?>" name="var_boleto_valor" /></td>
					      </tr>
					    </table></td>
					    <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="85">Dt Vencimento:&nbsp;</td>
					        <td><input type="text" onKeyPress="Javascript:validateNumKey();" id="var_boleto_dt_vencimento" onKeyUp="Javascript:FormataInputData(this);" maxlength="10" value="<?php echo(dDate(CFG_LANG,$dateBoletoDtVencimento,false)); ?>" name="var_boleto_dt_vencimento" /></td>
						 </tr>
					    </table></td>
					  </tr>
					  <tr bgcolor="#FAFAFA">
					    <td width="50%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">Aceite:&nbsp;</td>
					        <td><input type="text" maxlength="14" value="<?php echo($strBoletoAceite); ?>" name="var_boleto_aceite" /></td>
					      </tr>
					    </table></td>
					    <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="85">N&deg;. Documento:&nbsp;</td>
					        <td><input type="text" maxlength="14" value="<?php echo($strBoletoNumDocumento); ?>" name="var_boleto_num_documento" /></td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr bgcolor="#FFFFFF">
					    <td width="50%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">Esp&eacute;cie:&nbsp;</td>
					        <td><input type="text" maxlength="50" value="<?php echo($strBoletoEspecie); ?>" name="var_boleto_especie" /></td>
					      </tr>
					    </table></td>
					    <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="85">Nosso N&uacute;mero:&nbsp;</td>
					        <td><input type="text" maxlength="50" value="<?php echo($strBoletoNossoNumero); ?>" name="var_boleto_nosso_numero" /></td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr bgcolor="#FAFAFA">
					    <td width="50%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">Codigo Banco:&nbsp;</td>
					        <td><input type="text" readonly="readOnly" maxlength="10" value="<?php echo($intBoletoCodBanco); ?>" name="var_boleto_cod_banco" />
					          &nbsp;-&nbsp;
					          <input type="text" readonly="readOnly" size="1" maxlength="1" value="<?php echo($intBoletoCodBancoDV); ?>" name="var_boleto_cod_banco_dv" />

					        </td>
					      </tr>
					    </table></td>
					    <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="85">Esp&eacute;cie Doc.:&nbsp;</td>
					        <td><input type="text" maxlength="50" value="<?php echo($strBoletoTipoDoc); ?>" name="var_boleto_especie_doc" /></td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr bgcolor="#FFFFFF">
					    <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td valign="top" align="right" width="80">Local Pgto:&nbsp;</td>
					        <td><textarea name="var_boleto_LOCAL_PGTO" cols="50" rows="7"><?php echo($strBoletoLocalPgto); ?></textarea></td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					  <tr><td align="left" valign="top" colspan="2" class="destaque_gde" style="padding-left:85px;"><b><?php echo(getTText("dados_sacado",C_UCWORDS)); ?></b></td></tr>
					  <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					  <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					  <tr bgcolor="#FAFAFA">
					    <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">Nome:&nbsp;</td>
					        <td><input type="text" value="<?php echo($strBoletoSacadoNome); ?>" name="var_boleto_sacado_nome" size="40" /></td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr bgcolor="#FFFFFF">
					    <td width="50%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">Endere&ccedil;o:&nbsp;</td>
					        <td><input type="text" value="<?php echo($strBoletoSacadoEndereco); ?>" name="var_boleto_sacado_endereco" size="40"/></td>
					      </tr>
					    </table></td>
					    <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="85">Bairro:&nbsp;</td>
					        <td><input type="text" value="<?php echo($strBoletoSacadoBairro); ?>" name="var_boleto_sacado_bairro" /></td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr bgcolor="#FAFAFA">
					    <td width="50%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">Cidade:&nbsp;</td>
					        <td><input type="text" value="<?php echo($strBoletoSacadoCidade); ?>" name="var_boleto_sacado_cidade" /></td>
					      </tr>
					    </table></td>
					    <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="85">Estado:&nbsp;</td>
					        <td><select name="var_boleto_SACADO_ESTADO">
								<?php
								for($intCont = 0;$intCont < count($arrEstados);$intCont++){
									echo("<option value=\"" . $arrEstados[$intCont] . "\"" . (($arrEstados[$intCont] == $strBoletoSacadoEstado) ? " selected" : "") . ">" . $arrNome[$intCont] . "</option>");
								}
								?>
					        </select>
					        </td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr bgcolor="#FFFFFF">
					    <td width="50%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80">CEP:&nbsp;</td>
					        <td><input type="text" maxlength="12" value="<?php echo($strBoletoSacadoCEP); ?>" name="var_boleto_sacado_cep" /></td>
					      </tr>
					    </table></td>
					    <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="85">Identificador:&nbsp;</td>
					        <td><input type="text" value="<?php echo($strBoletoSacadoIdentificador); ?>" name="var_boleto_sacado_identificador" /></td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr bgcolor="#FAFAFA">
					    <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					      <tr>
					        <td align="right" width="80" valign="top">Instru&ccedil;&otilde;es:&nbsp;</td>
					        <td><textarea name="var_boleto_INSTRUCOES" cols="50" rows="7"><?php echo($strBoletoInstrucoes); ?></textarea></td>
					      </tr>
					    </table></td>
					  </tr>
					  <tr>
						<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
					  </tr>
					  <tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
					  <tr>
						<td align="right" colspan="2" style="padding:10px 0px 10px 10px;">
							<button onClick="submeterForm();"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
							<button onClick="location.href='<?php echo(getsession($strSesPfx . "_grid_default")); ?>';"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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
<?php
}
?>