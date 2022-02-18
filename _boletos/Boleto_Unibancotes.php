<?php
include_once("../_database/athdbconn.php");

define("CFG_COD_UNIBANCO",409);

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// ***************	UNIBANCO  ***************
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function calcularDVGeral($prNumero, $prCodBanco){
	return(calcularDDVModulo11($prNumero,2,9,$prCodBanco,"DV_CODIGOBARRAS"));
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function montarParteLivre($prCodCliente, $prNossoNumero, $prNossoNumeroDV){
	$intAux = $prCodCliente;
	$strAux = sprintf("%07s",$intAux);
	return("5" . $strAux . "00" . $prNossoNumero . $prNossoNumeroDV);
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function montarLinhaDigitavel($prCodigoBarras){
	
	$strCampo1 = substr($prCodigoBarras,0,4) . substr($prCodigoBarras,19,5);
	$strDigito = calcularDDVModulo10($strCampo1,1,2);
	$strCampo1 = substr($strCampo1,0,5) . "." . substr($strCampo1,5,4) . $strDigito;

	$strCampo2 = substr($prCodigoBarras,24,10);
	$strDigito = calcularDDVModulo10($strCampo2,1,2);
	$strCampo2 = substr($strCampo2,0,5) . "." . substr($strCampo2,5,5) . $strDigito;

	$strCampo3 = substr($prCodigoBarras,34,10);
	$strDigito = calcularDDVModulo10($strCampo3,1,2);
	$strCampo3 = substr($strCampo3,0,5) . "." . substr($strCampo3,5,5) . $strDigito;

	$strCampo4 = substr($prCodigoBarras,4,1);
	$strCampo5 = substr($prCodigoBarras,5,14);

	return($strCampo1 . "  " . $strCampo2 . "  " . $strCampo3 . "  " . $strCampo4 . "  " . $strCampo5);	
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function calcularDDVModulo11($prNumero, $prPesoMenor, $prPesoMaior, $prCodBanco, $prCaso){

	$intBase  = $prPesoMenor;
	$dblTotal = 0;
	
	//for $intPos = Len(prNumero) to 1 Step -1 
	for($intPos = strlen($prNumero)-1;$intPos==0;$intPos--){ 
		$dblTotal = $dblTotal + intval(substr($prNumero,intPos,1) * $intBase);
		$intBase++;
		if($intBase > $prPesoMaior) { $intBase = $prPesoMenor; }
	}
	
	if(strval($prCodBanco) == strval(CFG_COD_UNIBANCO)) {  $dblTotal = $dblTotal * 10; }
	$intResto 	= $dblTotal % 11;
	$intRetorno	= intval($intResto);
	
	if(strval($prCodBanco) == strval(CFG_COD_UNIBANCO)) { 
		if($prCaso = "DV_CODIGOBARRAS") { 
			if(($intResto == 0) || ($intResto == 1) || ($intResto == 10)) { $intRetorno = 1; }
		}
		
		if($prCaso = "DV_NOSSONUMERO") { 
			if(($intResto == 0) || ($intResto == 10)) {  $intRetorno = 0; }
		}
	}
	
	return($intRetorno);
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function calcularDDVModulo10($prNumero, $prPesoMenor, $prPesoMaior){
	
	$intBase = $prPesoMaior;
	$dblTotal = 0;
	//for intPos = Len(prNumero) to 1 Step - 1
	for($intPos = strlen($prNumero);$intPos==1;$intPos--){ 
		$dblValue = substr($prNumero, $intPos, 1) * $intBase;
		
		$dblTotal += ($dblValue > 9) ? ($dblValue - 9) :  $dblValue;
		$intBase  =  ($intBase == $prPesoMaior) ? $prPesoMenor : $prPesoMaior;
	}
	
	$intRetorno = $dblTotal % 10;
	if(intval($intRetorno) == 10 || intval($intRetorno) == 0) { 
		$intRetorno = 0;
	}
	else {
		$intRetorno = intval(10 - $intRetorno);
	}
	
	
	return($intRetorno);
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function montarParteFixa($prCodBanco, $prMoeda, $prDtVencimento, $prValor){

	define("DT_BASE_CALC_FATOR_VCTO","07/10/1997");

	$dblValor 	= $prValor;
	$dateDtVcto = $prDtVencimento;

	$prCodBanco	= sprintf("%03s",$prCodBanco);
	$prMoeda 	= sprintf("%01s",$prMoeda);

	$strDtVcto	= strtotime(cDate(CFG_LANG,$dateDtVcto,false)) - strtotime(cDate(CFG_LANG,DT_BASE_CALC_FATOR_VCTO,false));
	$strDtVcto	= sprintf("%04s",$strDtVcto);
	
	$strValor	= number_format((double) $dblValor,2);
	$strValor 	= str_replace(".","",str_replace(",","",$strValor));
	$strValor	= sprintf("%010s",$strValor);
	//echo($prCodBanco . $prMoeda . $strDtVcto . $strValor);
	return($prCodBanco . $prMoeda . $strDtVcto . $strValor);
	
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------

$strUploadPath = getsession(CFG_SYSTEM_NAME . "_cli_dir_logical_path") . "\\img\\";

$intCodContaPagarReceber		= request("var_chavereg");
$strNumImpressoes				= request("var_boleto_num_impressoes");

$strBoletoAceite				= request("var_boleto_aceite");
$strBoletoAgencia				= sprintf("%04s",request("var_boleto_agencia"));

$strBoletoCarteira				= request("var_boleto_carteira");
$strBoletoCedenteNome 			= request("var_boleto_cedente_nome");
$strBoletoCedenteCNPJ			= request("var_boleto_cedente_cnpj");
$intBoletoCodBanco				= request("var_boleto_cod_banco");
$intBoletoCodBancoDV			= request("var_boleto_cod_banco_dv");
$intBoletoCodCliente			= request("var_boleto_cod_cliente");
$strBoletoConta					= sprintf("%06s",left(request("var_boleto_cedente_codigo"),6));
$strBoletoContaDV				= request("var_boleto_cedente_codigo_dv");
$strBoletoEspecieDoc			= request("var_boleto_especie_doc");
$dateBoletoDtVencimento			= request("var_boleto_dt_vencimento");
$strBoletoEspecie				= request("var_boleto_especie");
$strBoletoImgLogo				= request("var_boleto_img_logo");
$strBoletoImgPromo				= request("var_boleto_img_promo");
$strBoletoInstrucoes			= request("var_boleto_instrucoes");
$strBoletoLocalPgto				= request("var_boleto_local_pgto");
$strBoletoNossoNumero			= sprintf("%014s",request("var_boleto_nosso_numero"));
$strBoletoNossoNumeroDV 		= calcularDDVModulo11($strBoletoNossoNumero,2,9,$intBoletoCodBanco,"DV_NOSSONUMERO");
$strBoletoNumDocumento			= request("var_boleto_num_documento");

$strBoletoSacadoBairro			= request("var_boleto_sacado_bairro");
$strBoletoSacadoCEP				= request("var_boleto_sacado_cep");
$strBoletoSacadoCidade			= request("var_boleto_sacado_cidade");
$strBoletoSacadoEndereco		= request("var_boleto_sacado_endereco");
$strBoletoSacadoEstado			= request("var_boleto_sacado_estado");
$strBoletoSacadoIdentificador	= request("var_boleto_sacado_identificador");
$strBoletoSacadoNome			= request("var_boleto_sacado_nome");

$dblBoletoValor					= request("var_boleto_valor");

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------

$strParteFixa	= montarParteFixa($intBoletoCodBanco,9,$dateBoletoDtVencimento,$dblBoletoValor);
$strParteLivre	= montarParteLivre($intBoletoCodCliente,$strBoletoNossoNumero,$strBoletoNossoNumeroDV);

$intDvGeral		= calcularDVGeral($strParteFixa . $strParteLivre, $intBoletoCodBanco);

$strBoletoCodigoBarras		= substr($strParteFixa,0,4) . $intDvGeral . substr($strParteFixa,3,strlen($strParteFixa)) . $strParteLivre;
$strBoletoLinhaDigitavel	= montarLinhaDigitavel($strBoletoCodigoBarras);

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------



$strHTML = "
<html>
<head>
<title>vboss</title>
<style type=text/css>
img { border:0px; }
.cp { font: bold 10px arial; 		color: #000000;	}
.ti { font: 9px  arial, helvetica, sans-serif;		}
.ld { font: bold 15px arial; 		color: #000000;	}
.ct { font: 9px 'arial narrow'; 	color: #000033;	}
.cn { font: 9px arial; 				color: #000000;	}
.bc { font: bold 22px arial; 		color: #000000;	}
</style>
</head>
<body text='#000000' bgcolor='#FFFFFF' topmargin='0' rightmargin='0'>
<table width='666' cellspacing='0' cellpadding='0' border='0'>
	<tr><td valign='top' class='cp'><div align='center'>instruções de impressão</div></td></tr>
	<tr>
		<td valign='top' class='ti'>
			<div align='center'>
				imprimir em impressora jato de tinta (ink jet) ou laser em qualidade normal. (não use modo econômico).<br>
				utilize folha a4 (210 x 297 mm) ou carta (216 x 279 mm) - corte na linha indicada<br>
			</div>
		</td>
	</tr>
</table>
<br>
<table width='666' cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr><td class='ct' width='666'><img src='../img/boleto_6.gif' width='665' height='1'></td></tr>
		<tr><td class='ct' width='666' height='13'><div align='right'><b class='cp'>Recibo do Sacado</b></div></td></tr>
	</tbody>
</table>		
<table width='666' cellspacing='5' cellpadding='0' border='0'><tr><td width='41'></td></tr></table>
";
//----------------------------------------------------------------------------------------------------------------------------------
// *** Imagem personalizada no boleto ***
//----------------------------------------------------------------------------------------------------------------------------------
if($strBoletoImgPromo != "") {
	$strBoletoImgPromo = $strUploadPath . $strBoletoImgPromo;
	$strHTML .= "<table cellspacing='0' cellpadding='0' border='0'><tr><td style='padding-bottom:10px;'><img src='" . $strBoletoImgPromo . "'></td></tr></table>";
}
//----------------------------------------------------------------------------------------------------------------------------------
$strHTML .= "
<table cellspacing='0' cellpadding='0' width='661' border='0'>
	<tbody>
		<tr>
			<td class='cp' width='151'><img src='" . $strUploadPath . "logomarca_boleto.gif'></td>
			<td width='3'   valign='bottom'><img height='22' src='../img/boleto_3.gif' width='2'></td>
			<td width='67'  valign='bottom' class='cp'><div align='center'><font class='bc'>" . $intBoletoCodBanco . "-" . $intBoletoCodBancoDV . "</font></div></td>
			<td width='8'   valign='bottom'><img height='22' src='../img/boleto_3.gif' width='2'></td>
			<td class='ld' align='right' width='437' valign='bottom'><span class='ld'>" . $strBoletoLinhaDigitavel . "</span></td>
		</tr>
	<tr><td colspan='5'><img height='2' src='../img/boleto_2.gif' width='666'></td></tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct' valign='top'>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='298' height='13'>Cedente</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='126' height='13'>Agência/Código do Cedente</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='034' height='13'>Espécie</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='053' height='13'>Quantidade</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='120' height='13'>Nosso número</td>
		</tr>
		<tr class='cp' valign='top'>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='298' height='12'>" . $strBoletoCedenteNome . "</td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='126' height='12'>" . $strBoletoAgencia . "/" . $strBoletoConta . "-" . $strBoletoContaDV . "</td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='034' height='12'>" . $strBoletoEspecie . "</td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='053' height='12'></td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='120' height='12'>" . $strBoletoNossoNumero . "-" . $strBoletoNossoNumeroDV . "</td>
		</tr>
		<tr valign='top'>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='298'><img height='1' src='../img/boleto_2.gif' width='298'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='126'><img height='1' src='../img/boleto_2.gif' width='126'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='034'><img height='1' src='../img/boleto_2.gif' width='034'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='053'><img height='1' src='../img/boleto_2.gif' width='053'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='120'><img height='1' src='../img/boleto_2.gif' width='120'></td>
		</tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct' valign='top'>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td colspan='3' height='13'>Número do documento</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='132' height='13'>CPF/CNPJ</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='134' height='13'>Vencimento</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>Valor documento</td>
		</tr>
		<tr class='cp' valign='top'>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td colspan='3' height='12'>" . $strBoletoNumDocumento . "</td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='132' height='12'>" . $strBoletoCedenteCNPJ . "</td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='134' height='12'>" . $dateBoletoDtVencimento . "</td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='180' height='12' align='right' nowrap>" . number_format((double) $dblBoletoValor,2,",","")  . "</td>
		</tr>
		<tr valign='top'>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='113'><img height='1' src='../img/boleto_2.gif' width='113'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='072'><img height='1' src='../img/boleto_2.gif' width='072'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='132'><img height='1' src='../img/boleto_2.gif' width='132'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='134'><img height='1' src='../img/boleto_2.gif' width='134'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>
		</tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct' valign='top'>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='113' height='13'>(-) Desconto / Abatimentos</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='112' height='13'>(-) Outras deduções</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='113' height='13'>(+) Mora / Multa</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='113' height='13'>(+) Outros acréscimos</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>(=) Valor cobrado</td>
		</tr>
		<tr class='cp' valign='top'>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='113' height='12'></td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='112' height='12'></td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='113' height='12'></td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='113' height='12'></td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='180' height='12'></td>
		</tr>
		<tr valign='top'>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='113'><img height='1' src='../img/boleto_2.gif' width='113'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='112'><img height='1' src='../img/boleto_2.gif' width='112'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='113'><img height='1' src='../img/boleto_2.gif' width='113'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='113'><img height='1' src='../img/boleto_2.gif' width='113'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>
		</tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct' valign='top'><td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='659' height='13'>Sacado</td></tr>
		<tr class='cp' valign='top'><td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='659' height='12'>" . $strBoletoSacadoNome . "</td></tr>
		<tr valign='top'><td width='7'><img src='../img/boleto_2.gif' width='7' height='1' ></td><td width='659'><img height='1' src='../img/boleto_2.gif' width='659'></td></tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct'>
			<td width='7' height='12'></td><td width='564' height='12'>Instruções</td>
			<td width='7' height='12'></td><td width='088' height='12'>Autenticação mecânica</td>
		</tr>
		<tr><td width='7'></td><td width='564'></td><td width='7'></td><td width='88'></td></tr>
	</tbody>
</table>
<table width='666' cellspacing='0' cellpadding='0' border='0'>
	<tbody><tr><td width='7'></td><td width='500' class='cp'>" . $strBoletoInstrucoes . "</td><td width='159'></td></tr></tbody>
</table>
<table width='666' cellspacing='0' cellpadding='0' border='0'>
	<tr><td class='ct' width='666'></td></tr>
	<tbody>
		<tr><td class='ct' width='666'><div align='right'>Corte na linha pontilhada</div></td></tr>
		<tr><td class='ct' width='666'><img height='1' src='../img/boleto_6.gif' width='665'></td></tr>
	</tbody>
</table>
<br>	
<br>
<table width='664' cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr valign='bottom'>
			<td width='151' class='cp'><img src='" . $strUploadPath . "logomarca_boleto.gif'></td><td width='03'><img height='22' src='../img/boleto_3.gif' width='2'></td>
			<td width='65'><div align='center' class='bc'>" . $intBoletoCodBanco . "-" . $intBoletoCodBancoDV . "</div></td>
			<td width='3'><img height='22' src='../img/boleto_3.gif' width='2'></td><td class='ld' width='445' align='right'>" . $strBoletoLinhaDigitavel . "</td>
		</tr>
		<tr><td colspan='5'><img height='2' src='../img/boleto_2.gif' width='666'></td></tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct' valign='top'>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='472' height='13'>Local de pagamento</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>Vencimento</td>
		</tr>
		<tr class='cp' valign='top'>
			<td width='7' height='12'><img height='25' src='../img/boleto_1.gif' width='1'></td><td width='472' height='12'>" . $strBoletoLocalPgto . "</td>
			<td width='7' height='12'><img height='25' src='../img/boleto_1.gif' width='1'></td><td width='180' height='12' align='right' valign='bottom'>" . $dateBoletoDtVencimento . "</td>
		</tr>
		<tr valign='top'>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='472'><img height='1' src='../img/boleto_2.gif' width='472'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>
		</tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct' valign='top'>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='472' height='13'>Cedente</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>Agência/Código cedente</td>
		</tr>
		<tr class='cp' valign='top'>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='472' height='12'>" . $strBoletoCedenteNome . "</td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td>
			<td width='180' height='12' align='right'>" . $strBoletoAgencia . "/" . $strBoletoConta . "-" . $strBoletoContaDV . "</td>
		</tr>
		<tr valign='top'>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='472'><img height='1' src='../img/boleto_2.gif' width='472'></td>
			<td width='7'><img src='../img/boleto_2.gif' width='7' height='1'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>
		</tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct' valign='top'>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='113' height='13'>Data do documento</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='163' height='13'>N<u>o</u> documento</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='062' height='13'>Espécie doc.</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='034' height='13'>Aceite</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='072' height='13'>Data processamento</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>Nosso número</td>
		</tr>
		<tr class='cp' valign='top'>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='113' height='12'><div>" . dDate(CFG_LANG,dateNow(),false) . "</div></td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='163' height='12'><div>" . $strBoletoNumDocumento	. "</div></td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='062' height='12'><div align='center'>" . $strBoletoEspecieDoc . "</div></td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='034' height='12'><div align='center'>" . $strBoletoAceite	 . "</div></td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='072' height='12'><div></div></td>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='180' height='12' align='right'>" . $strBoletoNossoNumero . "-" . $strBoletoNossoNumeroDV . "</td>
		</tr>
		<tr valign='top'>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='113'><img height='1' src='../img/boleto_2.gif' width='113'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='163'><img height='1' src='../img/boleto_2.gif' width='163'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='062'><img height='1' src='../img/boleto_2.gif' width='062'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='034'><img height='1' src='../img/boleto_2.gif' width='034'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='072'><img height='1' src='../img/boleto_2.gif' width='072'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>
		</tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct' valign='top'>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' colspan='3'>Uso do banco</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' width='083'>Carteira</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' width='053'>Espécie</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' width='123'>Quantidade</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' width='072'>Valor</td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' width='180'>(=) Valor documento</td>
		</tr>
		<tr class='cp' valign='top'>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td height='12' colspan='3'></td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td height='12' width='083'><div>" . $strBoletoCarteira . "</div></td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td height='12' width='053'><div align='center'>" . $strBoletoEspecie . "</div></td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td height='12' width='123'></td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td height='12' width='072'></td>
			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='180' height='12' align='right' nowrap>" . number_format((double) $dblBoletoValor,2,",","") . "</td>
		</tr>
		<tr valign='top'>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='007'><img height='1' src='../img/boleto_2.gif' width='075'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='031'><img height='1' src='../img/boleto_2.gif' width='031'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='083'><img height='1' src='../img/boleto_2.gif' width='083'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='053'><img height='1' src='../img/boleto_2.gif' width='053'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='123'><img height='1' src='../img/boleto_2.gif' width='123'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='072'><img height='1' src='../img/boleto_2.gif' width='072'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>
		</tr>
	</tbody>
</table>
<table width='666' cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr>
			<td width='10' align='right' valign='top'>
				<table cellspacing=0 cellpadding=0 border='0' align='left'>
					<tbody>
						<tr><td valign='top' width='7' class='ct'><img height='13' src='../img/boleto_1.gif' width='1'></td></tr>
						<tr><td valign='top' width='7' class='cp'><img height='12' src='../img/boleto_1.gif' width='1'></td></tr>
						<tr><td valign='top' width='7' height='1'><img height='01' src='../img/boleto_2.gif' width='1'></td></tr>
					</tbody>
				</table>
			</td>
			<td valign='top' width='468' rowspan='5'>
				<div class='ct' style='height:12;'>Instruções (texto de responsabilidade do cedente)</div>
				<div class='cp' style='height:12;'>" . $strBoletoInstrucoes . "</div>
			</td>
			<td align='right' width='188'>
				<table cellspacing='0' cellpadding='0' border='0'> 
					<tbody>
						<tr valign='top' class='ct'><td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>(-) Desconto / Abatimentos</td></tr>
						<tr valign='top' class='cp'><td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='180' height='12'></td></tr>
						<tr valign='top'><td width='7'><img src='../img/boleto_2.gif' width='7' height='1'></td><td width='180'><img src='../img/boleto_2.gif' width='180' height='1'></td></tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td width='10' align='right'>
				<table cellspacing='0' cellpadding='0' border='0' align='left'>
					<tbody>
						<tr><td valign='top' width='7' height='13' class='ct'><img height='13' src='../img/boleto_1.gif' width='1'></td></tr>
						<tr><td valign='top' width='7' height='12' class='cp'><img height='12' src='../img/boleto_1.gif' width='1'></td></tr>
						<tr><td valign='top' width='7' height='1'><img height='1' src='../img/boleto_2.gif' width='1'></td></tr>
					</tbody>
				</table>
			</td>
			<td align='right' width='188'>
				<table cellspacing='0' cellpadding='0' border='0'>
					<tbody>
						<tr valign='top' class='ct'><td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>(-) Outras deduções</td></tr>
						<tr valign='top' class='cp'><td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='180' height='12' align='right'></td></tr>
						<tr valign='top'><td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td></tr>
					</tbody>
					</table>
			</td>
		</tr>
		<tr>
			<td align='right' width='10'>
				<table cellspacing='0' cellpadding='0' border='0' align='left'>
					<tbody>
						<tr><td valign='top' width='7' height='13' class='ct'><img height='13' src='../img/boleto_1.gif' width='1'></td></tr>
						<tr><td valign='top' width='7' height='12' class='cp'><img height='12' src='../img/boleto_1.gif' width='1'></td></tr>
						<tr><td valign='top' width='7' height='1'><img height='1' src='../img/boleto_2.gif' width='1'></td></tr>
					</tbody>
				</table>
			</td>
			<td align='right' width='188'>
				<table border='0' cellpadding='0' cellspacing='0'>
					<tbody>
						<tr valign='top' class='ct'><td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>(+) Mora / Multa</td></tr>
						<tr valign='top' class='cp'><td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='180' height='12' align='right'></td></tr>
						<tr valign='top'><td width='7'><img src='../img/boleto_2.gif' width='7' height='1'></td><td width='180'><img src='../img/boleto_2.gif' width='180' height='1'></td></tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td align='right' width='10'>
				<table cellspacing='0' cellpadding='0' border='0' align='left'>
					<tbody>
						<tr><td valign='top' width='7' height='13' class='ct'><img height='13' src='../img/boleto_1.gif' width='1'></td></tr>
						<tr><td valign='top' width='7' height='12' class='cp'><img height='12' src='../img/boleto_1.gif' width='1'></td></tr>
						<tr><td valign='top' width='7' height='1'><img height='1' src='../img/boleto_2.gif' width='1'></td></tr>
					</tbody>
				</table>
			</td>
			<td align='right' width='188'>
				<table cellspacing='0' cellpadding='0' border='0'>
					<tbody>
						<tr valign='top' class='ct'><td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>(+) Outros acréscimos</td></tr>
						<tr valign='top' class='cp'><td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='180' height='12' align='right'></td></tr>
						<tr valign='top'><td width='7'><img src='../img/boleto_2.gif' width='7' height='1'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td></tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td align='right' width='10'>
				<table cellspacing='0' cellpadding='0' border='0' align='left'>
					<tbody>
						<tr><td class='ct' valign='top' width='7' height='13'><img height='13' src='../img/boleto_1.gif' width='1'></td></tr>
						<tr><td class='cp' valign='top' width='7' height='12'><img height='12' src='../img/boleto_1.gif' width='1'></td></tr>
					</tbody>
				</table>
			</td>
			<td align='right' width='188'>
				<table cellspacing='0' cellpadding='0' border='0'>
					<tbody>
						<tr class='ct' valign='top'><td width='007'><img height='13' src='../img/boleto_1.gif' width='1'></td><td width='180' height='13'>(=) Valor cobrado</td></tr>
						<tr class='cp' valign='top'><td width='007'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='180' height='12' align='right'></td></tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<table width='666' cellspacing='0' cellpadding='0' border='0'>
	<tbody><tr><td valign='top' width='666' height='1'><img height='1' src='../img/boleto_2.gif' width='666'></td></tr></tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct' valign='top'><td width='7'><img height='13' src='../img/boleto_1.gif' width='1'></td><td width='659' height='13'>Sacado</td></tr>
		<tr class='cp' valign='top'><td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='659' height='12'>" . $strBoletoSacadoNome . "</td></tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='cp' valign='top'>
			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td>
			<td width='659' height='12'>" . $strBoletoSacadoEndereco . "&nbsp;-&nbsp;" . $strBoletoSacadoBairro . "</td>
		</tr>
	</tbody>
</table>
<table cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr valign='top'>
			<td class='ct' width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td>
			<td class='cp' width='472' height='13'>" . $strBoletoSacadoCEP . "&nbsp;-&nbsp;" . $strBoletoSacadoCidade . "&nbsp;-&nbsp;" . $strBoletoSacadoEstado  . "</td>
			<td class='ct' width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td class='ct' width='180' height='13'>Cód. baixa</td>
		</tr>
		<tr valign='top'>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='472'><img height='1' src='../img/boleto_2.gif' width='472'></td>
			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>
		</tr>
	</tbody>
</table>
<table width='666' cellspacing='0' cellpadding='0' border='0'>
	<tbody>
		<tr class='ct'>
			<td width='7' height='12'></td><td width='409'>Sacador/Avalista</td>
			<td width='250'><div align='right'>Autenticação mecânica - <b class='cp'>Ficha de Compensação</b></div></td>
		</tr>
		<tr><td class='ct' colspan='3'></td></tr>
	</tbody>
</table>
<table width='640' cellspacing='0' cellpadding='0' border='0'>
	<tbody><tr><td valign='bottom' align='left' height='50'><img src='../img/spacer.gif' width='20'>" . BarCode25($strBoletoCodigoBarras) . "</td></tr></tbody>
</table>
<table width='666' cellspacing='0' cellpadding='0' class='ct' border='0'>
	<tr><td width='666'></td></tr>
	<tbody>
		<tr><td width='666'><div align='right'>Corte na linha pontilhada</div></td></tr>
		<tr><td width='666'><img src='../img/boleto_6.gif' width='665' height='1'></td></tr>
	</tbody>
</table>
</body>
</html>";
die($strHTML)/*
set objFileSystemObject = CreateObject("Scripting.FileSystemObject");
//------------------------------------------------------------------------------------------------------------
// Salva arquivo do boleto na pasta FIN_Boletos, que encontra-se dentro da pasta upload do cliente, 
// no seguinte formato: "codigo da conta a pagar/receber"_"numero de impressoes do boleto".htm
//------------------------------------------------------------------------------------------------------------
$strFilePath = Server.MapPath(strUploadPath . "FIN_Boletos/Boleto_" . intCOD_CONTA_PAGAR_RECEBER . "_" . CInt("0" . strNUM_IMPRESSOES)+1 . ".htm");
set $objArquivo = objFileSystemObject.CreateTextFile(strFilePath, true);
$strHTML = Replace(Replace(strHTML,"src='../img/","src='../../../img/"),"src='../upload/" . getsession("VBOSS")("CLINAME") . "/","src='../");

$objArquivo.Write(strHTML);
$objArquivo.Close

set objArquivo = Nothing
//------------------------------------------------------------------------------------------------------------
// Faz a leitura dos dados no arquivo gravado anteriormente
//------------------------------------------------------------------------------------------------------------
set objArquivo = objFileSystemObject.OpenTextFile(strFilePath);

strHTML = objArquivo.ReadAll
objArquivo.Close

set objArquivo 			= Nothing
set objFileSystemObject = Nothing

//------------------------------------------------------------------------------------------------------------
// Altera o caminho das imagens para poder exibir corretamente a partir do modulo atual
//------------------------------------------------------------------------------------------------------------
strHTML = Replace(strHTML,"src='../../../img/","src='./img/");
strHTML = Replace(strHTML,"src='../"		  ,"src='../upload/" . getsession("VBOSS")("CLINAME") . "/");
strHTML = Replace(strHTML,"src='./img/"		  ,"src='../img/");
//------------------------------------------------------------------------------------------------------------

if(Err.Number != 0) { 
	strMSG = "Não foi possível exibir boleto.<br>";
	strMSG = strMSG . Err.Number . " - " . Err.Description
	Mensagem strMSG, "", 1 
elseif(intCOD_CONTA_PAGAR_RECEBER != "" && is_numeric(intCOD_CONTA_PAGAR_RECEBER)) { 
	AbreDBConn $objConn, CFG_DB
	$objConn->query("UPDATE FIN_CONTA_PAGAR_RECEBER SET NUM_IMPRESSOES=NUM_IMPRESSOES+1 WHERE COD_CONTA_PAGAR_RECEBER=" . intCOD_CONTA_PAGAR_RECEBER);
	FechaDBConn $objConn
}

Response.Write(strHTML);*/
?>