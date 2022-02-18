<!--#include file="../_database/athdbConn.asp"--><%' ATENÇÃO: language, option explicit, etc... estão no athDBConn %>
<!--#include file="../_database/athUtils.asp"-->
<!--#include file="../_database/BarCode25.asp"-->
<%
Dim strBOLETO_ACEITE, strBOLETO_AGENCIA
Dim strBOLETO_CARTEIRA, strBOLETO_CEDENTE_CODIGO, strBOLETO_CEDENTE_CODIGO_DV
Dim strBOLETO_CEDENTE_NOME, strBOLETO_CEDENTE_CNPJ, strBOLETO_CODIGO_BARRAS
Dim strBOLETO_COD_BANCO, strBOLETO_COD_BANCO_DV, strBOLETO_CONTA, strBOLETO_CONTA_DV
Dim strBOLETO_DT_VENCIMENTO, strBOLETO_ESPECIE, strBOLETO_IMG_LOGO, strBOLETO_INSTRUCOES
Dim strBOLETO_LINHA_DIGITAVEL, strBOLETO_LOCAL_PGTO, strBOLETO_NOSSO_NUMERO, strBOLETO_NOSSO_NUMERO_DV
Dim strBOLETO_NUM_DOCUMENTO, strBOLETO_VALOR, strBOLETO_IMG_PROMO, intCOD_CONTA_PAGAR_RECEBER
Dim strBOLETO_SACADO_BAIRRO, strBOLETO_SACADO_CEP, strBOLETO_SACADO_CIDADE
Dim strBOLETO_SACADO_ENDERECO, strBOLETO_SACADO_ESTADO, strNUM_IMPRESSOES
Dim strBOLETO_SACADO_IDENTIFICADOR, strBOLETO_SACADO_NOME
Dim intCOD_CONTA_PAGAR_RECEBERM, strFatorVencimento, strBOLETO_ESPECIE_DOC
Dim strDVGeral, strHTML, dblValorAux

Dim objConn, objRS, strSQL, strMSG
Dim objFileSystemObject, strFilePath, objArquivo, strUploadPath

'---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
'	***************	ITAU	 ***************
'---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Function MontarCodigoBarras(prAgencia, prCodBanco, prCarteira, prConta, prDvAgConta, prMoeda, prDvNossoNumero, prNossoNumero, prVencimento, prValor)
Dim strCodBar,strDv3

	strCodBar = prCodBanco & prMoeda & prVencimento & prValor & prCarteira & prNossoNumero & prDvNossoNumero & prAgencia & prConta & prDvAgConta & "000"	
	strDv3	 = CalcularDDV_Modulo11(strCodBar,9,0)
	
	strCodBar = prCodBanco & prMoeda & strDv3 & prVencimento & prValor & prCarteira & prNossoNumero & prDvNossoNumero & prAgencia & prConta & prDvAgConta & "000"
	
	MontarCodigoBarras = strCodBar
End Function

Function MontarLinhaDigitavel(prCodigoBarras)
Dim strCampoLivre
Dim strCampo1, strCampo2
Dim strCampo3, strCampo4, strCampo5

	strCampoLivre	= Mid(prCodigoBarras,20,25)
	
	strCampo1	= Left(prCodigoBarras,4) & mid(strCampoLivre,1,5)
	strCampo1	= strCampo1 & CalcularDDV_Modulo10(strCampo1)
	strCampo1	= Mid(strCampo1,1,5) & "." & mid(strCampo1,6,5)
	
	strCampo2	= Mid(strCampoLivre,6,10)
	strCampo2	= strCampo2 & CalcularDDV_Modulo10(strCampo2)
	strCampo2	= Mid(strCampo2,1,5) & "." & mid(strCampo2,6,6)
	
	strCampo3	= Mid(strCampoLivre,16,10)
	strCampo3	= strCampo3 & CalcularDDV_Modulo10(strCampo3)
	strCampo3	= Mid(strCampo3,1,5) & "." & mid(strCampo3,6,6)
		
	strCampo4	= Mid(prCodigoBarras,5,1)	
	strCampo5	= Int(Mid(prCodigoBarras,6,14))
	
	if strCampo5=0 then	strCampo5 = "000"
	
	MontarLinhaDigitavel = strCampo1 & "  " & strCampo2 & "  " & strCampo3 & "  " & strCampo4 &"  " & strCampo5	
End Function
'---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Function CalcularDDV_Modulo11(prNumero, prPesoMaior, prRetorno)
Dim dblTotal, dblValue, dblResto
Dim strBase, strRetorno
Dim intPos

	strBase = 1 + (Len(prNumero) mod (prPesoMaior-1))	
	if strBase=1 then	strBase = prPesoMaior	
	dblTotal=0
	
	for intPos = 1 to Len(prNumero)
		dblTotal = dblTotal + (Mid(prNumero,intPos,1) * strBase)
		strBase	= strBase - 1
		if strBase=1 then	strBase = prPesoMaior
	next
	
	dblResto = (dblTotal mod 11)
	if prRetorno<>1 then
		if dblResto=0 or dblResto=1 or dblResto=10 then	
			strRetorno = 1 
		else	
			strRetorno = 11 - dblResto
		end if
		CalcularDDV_Modulo11 = strRetorno
	else
		CalcularDDV_Modulo11 = dblResto
	end if
End Function
'---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Function CalcularDDV_Modulo10(prNumero)
Dim dblTotal, dblValue
Dim strBase, strRetorno
Dim intPos

	strBase = (Len(prNumero) mod 2) 
	strBase = strBase + 1
	dblTotal = 0

	for intPos = 1 to Len(prNumero)
		dblValue = Mid(prNumero,intPos,1) * strBase

		if dblValue>9 then	dblValue = Int(dblValue/10) + (dblValue mod 10)
		dblTotal = dblTotal + dblValue
		if strBase = 2 then strBase = 1 else strBase = 2
	next
	dblTotal = ((10 - (dblTotal mod 10)) mod 10 )
	
	CalcularDDV_Modulo10 = dblTotal
End Function
'---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Function FatorVencimento(prDataVencimento)
Dim strDtVcto
Dim DT_BASE_CALC_FATOR_VCTO

	DT_BASE_CALC_FATOR_VCTO = "07/10/1997"
	strDtVcto = "0000"
	if Len(prDataVencimento)>7 then	strDtVcto = DateValue(prDataVencimento) - DateValue(DT_BASE_CALC_FATOR_VCTO)
	FatorVencimento = strDtVcto
End Function
'---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
strUploadPath = "../upload/" & UCase(Request.Cookies("VBOSS")("CLINAME")) & "/"

intCOD_CONTA_PAGAR_RECEBER			= GetParam("var_chavereg")
strNUM_IMPRESSOES						= GetParam("var_boleto_num_impressoes")

strBOLETO_ACEITE						= GetParam("var_boleto_aceite")
strBOLETO_AGENCIA						= AthFormataTamLeft(GetParam("var_boleto_agencia"),4,"0")

strBOLETO_CARTEIRA					= GetParam("var_boleto_carteira")
'	strBOLETO_CEDENTE_CODIGO			= GetParam("var_boleto_cedente_codigo")
'	strBOLETO_CEDENTE_CODIGO_DV		= GetParam("var_boleto_cedente_codigo_dv")
strBOLETO_CEDENTE_NOME 				= GetParam("var_boleto_cedente_nome")
strBOLETO_CEDENTE_CNPJ				= GetParam("var_boleto_cedente_cnpj")
strBOLETO_COD_BANCO					= GetParam("var_boleto_cod_banco")
strBOLETO_COD_BANCO_DV				= GetParam("var_boleto_cod_banco_dv")
strBOLETO_CONTA						= AthFormataTamLeft(Left(GetParam("var_boleto_cedente_codigo"),5),5,"0")
strBOLETO_CONTA_DV					= GetParam("var_boleto_cedente_codigo_dv")
strBOLETO_ESPECIE_DOC				= GetParam("var_boleto_especie_doc")
strBOLETO_DT_VENCIMENTO				= GetParam("var_boleto_dt_vencimento")
strBOLETO_ESPECIE						= GetParam("var_boleto_especie")

strBOLETO_IMG_LOGO					= GetParam("var_boleto_img_logo")
strBOLETO_IMG_PROMO					= GetParam("var_boleto_img_promo")

strBOLETO_INSTRUCOES					= GetParam("var_boleto_instrucoes")

strBOLETO_LOCAL_PGTO					= GetParam("var_boleto_local_pgto")

strBOLETO_NOSSO_NUMERO				= AthFormataTamLeft(GetParam("var_boleto_nosso_numero"),8,"0") 
strBOLETO_NUM_DOCUMENTO				= GetParam("var_boleto_num_documento")

strBOLETO_SACADO_BAIRRO				= GetParam("var_boleto_sacado_bairro")
strBOLETO_SACADO_CEP					= GetParam("var_boleto_sacado_cep")
strBOLETO_SACADO_CIDADE				= GetParam("var_boleto_sacado_cidade")
strBOLETO_SACADO_ENDERECO			= GetParam("var_boleto_sacado_endereco")
strBOLETO_SACADO_ESTADO				= GetParam("var_boleto_sacado_estado")
strBOLETO_SACADO_IDENTIFICADOR	= GetParam("var_boleto_sacado_identificador")
strBOLETO_SACADO_NOME				= GetParam("var_boleto_sacado_nome")

strBOLETO_VALOR						= GetParam("var_boleto_valor")
'---------------------------------------------------------------------------------------------------------------------------------------------------------------------------

strBOLETO_NOSSO_NUMERO_DV	= CalcularDDV_Modulo10(strBOLETO_AGENCIA & strBOLETO_CONTA & strBOLETO_CARTEIRA & strBOLETO_NOSSO_NUMERO)
strBOLETO_CONTA_DV 			= CalcularDDV_Modulo10(strBOLETO_AGENCIA & strBOLETO_CONTA)

strFatorVencimento = FatorVencimento(strBOLETO_DT_VENCIMENTO)
if strFatorVencimento="0000" then strFatorVencimento = "Contra Apresentação"	

dblValorAux	= ""
if strBOLETO_VALOR<>0 then
	dblValorAux = Replace(Replace(MoedaToFloat(strBOLETO_VALOR),",",""),".","")
	dblValorAux	= String((10 - CInt(Len(dblValorAux))),"0") & dblValorAux
end if

strBOLETO_CODIGO_BARRAS		= MontarCodigoBarras(strBOLETO_AGENCIA, strBOLETO_COD_BANCO, strBOLETO_CARTEIRA, strBOLETO_CONTA, strBOLETO_CONTA_DV,_ 
																9, strBOLETO_NOSSO_NUMERO_DV, strBOLETO_NOSSO_NUMERO,	strFatorVencimento, dblValorAux)
strBOLETO_LINHA_DIGITAVEL	= MontarLinhaDigitavel(strBOLETO_CODIGO_BARRAS)


'----------------------------------------------------------------------
' "TheBugger" King
'----------------------------------------------------------------------
'
'with Response
'	.Write("COD_BANCO: "														)
'	.Write(strBOLETO_COD_BANCO 	& "<br>Moeda: 9<br>Carteira: ")
'	.Write(strBOLETO_CARTEIRA 		& "<br>Nosso Numero: "			)
'	.Write(strBOLETO_NOSSO_NUMERO & "<br>Agencia: "					)
'	.Write(strBOLETO_AGENCIA 		& "<br>DV: "						)
'	.Write(strBOLETO_NOSSO_NUMERO_DV & "<br>Conta: "				)
'	.Write(strBOLETO_CONTA 			& "<br>DV: "						)
'	.Write(strBOLETO_CONTA_DV 		& "<br>Vcto: "						)
'	.Write(strFatorVencimento 		& "<br>Valor: "					)
'	.Write(dblValorAux & "<br><br>"										)
'	.End()
'end with
'----------------------------------------------------------------------


strHTML = ""
strHTML = strHTML &_
"<html>" 																																																			& vbCrlf &_
"<head>" 																																																			& vbCrlf &_
"<title>vboss</title>" 																																															& vbCrlf &_
"<style type=text/css>" 																																														& vbCrlf &_
"img { border:0px; }" 																																															& vbCrlf &_
".cp { font: bold 10px arial; 		color: #000000;	}" 																																			& vbCrlf &_
".ti { font: 9px  arial, helvetica, sans-serif;			}" 																																			& vbCrlf &_
".ld { font: bold 15px arial; 		color: #000000;	}" 																																			& vbCrlf &_
".ct { font: 9px 'arial narrow'; 	color: #000033;	}" 																																			& vbCrlf &_
".cn { font: 9px arial; 				color: #000000;	}" 																																			& vbCrlf &_
".bc { font: bold 22px arial; 		color: #000000;	}" 																																			& vbCrlf &_
"</style>" 																																																			& vbCrlf &_
"</head>" 																																																			& vbCrlf &_
"<body text='#000000' bgcolor='#FFFFFF' topmargin='0' rightmargin='0'>" 																														& vbCrlf &_
"<table width='666' cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"	<tr><td valign='top' class='cp'><div align='center'>instruções de impressão</div></td></tr>" 																						& vbCrlf &_
"	<tr>" 																																																			& vbCrlf &_
"		<td valign='top' class='ti'>" 																																										& vbCrlf &_
"			<div align='center'>" 																																												& vbCrlf &_
"				imprimir em impressora jato de tinta (ink jet) ou laser em qualidade normal. (não use modo econômico).<br>" 														& vbCrlf &_
"				utilize folha a4 (210 x 297 mm) ou carta (216 x 279 mm) - corte na linha indicada - v3<br>" 																					& vbCrlf &_
"			</div>" 																																																	& vbCrlf &_
"		</td>" 																																																		& vbCrlf &_
"	</tr>" 																																																			& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<br>" 																																																				& vbCrlf &_
"<table width='666' cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr><td class='ct' width='666'><img src='../img/boleto_6.gif' width='665' height='1'></td></tr>" 																				& vbCrlf &_
"		<tr><td class='ct' width='666' height='13'><div align='right'><b class='cp'>Recibo do Sacado</b></div></td></tr>" 														& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table width='666' cellspacing='5' cellpadding='0' border='0'><tr><td width='41'></td></tr></table>" 																				
'----------------------------------------------------------------------------------------------------------------------------------
' *** Imagem personalizada no boleto ***
'----------------------------------------------------------------------------------------------------------------------------------
if strBOLETO_IMG_PROMO<>"" then
	strBOLETO_IMG_PROMO = strUploadPath & strBOLETO_IMG_PROMO
	strHTML = strHTML &_
	"<table cellspacing='0' cellpadding='0' border='0'><tr><td style='padding-bottom:10px;'><img src='" & strBOLETO_IMG_PROMO & "'></td></tr></table>"																				
end if
'----------------------------------------------------------------------------------------------------------------------------------
strHTML = strHTML &_
"<table cellspacing='0' cellpadding='0' width='661' border='0'>" 																																	& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr>" 																																																		& vbCrlf &_
"			<td class='cp' width='151'><img src='../img/boleto_logo_" & strBOLETO_IMG_LOGO & ".gif'></td>" 																				& vbCrlf &_
"			<td width='3'   valign='bottom'><img height='22' src='../img/boleto_3.gif' width='2'></td>" 																					& vbCrlf &_
"			<td width='67'  valign='bottom' class='cp'><div align='center'><font class='bc'>" & strBOLETO_COD_BANCO & "-" & strBOLETO_COD_BANCO_DV & "</font></div></td>" & vbCrlf &_
"			<td width='8'   valign='bottom'><img height='22' src='../img/boleto_3.gif' width='2'></td>" 																					& vbCrlf &_
"			<td class='ld' align='right' width='437' valign='bottom'><span class='ld'>" & strBOLETO_LINHA_DIGITAVEL & "</span></td>" 											& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr><td colspan='5'><img height='2' src='../img/boleto_2.gif' width='666'></td></tr>" 																								& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='298' height='13'>Cedente</td>" 													& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='126' height='13'>Agência/Código do Cedente</td>" 							& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='034' height='13'>Espécie</td>" 													& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='053' height='13'>Quantidade</td>" 												& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='120' height='13'>Nosso número</td>" 												& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr class='cp' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='298' height='12'>" & strBOLETO_CEDENTE_NOME & "</td>" 						& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='126' height='12'>" & strBOLETO_AGENCIA & "/" & strBOLETO_CONTA & "-" & strBOLETO_CONTA_DV & "</td>"	& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='034' height='12'>" & strBOLETO_ESPECIE & "</td>" 							& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='053' height='12'></td>" 																& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='120' height='12'>" & strBOLETO_CARTEIRA & "/" & strBOLETO_NOSSO_NUMERO & "-" & strBOLETO_NOSSO_NUMERO_DV & "</td>" 	& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr valign='top'>" 																																														& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='298'><img height='1' src='../img/boleto_2.gif' width='298'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='126'><img height='1' src='../img/boleto_2.gif' width='126'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='034'><img height='1' src='../img/boleto_2.gif' width='034'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='053'><img height='1' src='../img/boleto_2.gif' width='053'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='120'><img height='1' src='../img/boleto_2.gif' width='120'></td>" 		& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td colspan='3' height='13'>Número do documento</td>" 									& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='132' height='13'>CPF/CNPJ</td>" 													& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='134' height='13'>Vencimento</td>" 												& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>Valor documento</td>" 											& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr class='cp' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td colspan='3' height='12'>" & strBOLETO_NUM_DOCUMENTO & "</td>" 					& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='132' height='12'>" & strBOLETO_CEDENTE_CNPJ & "</td>" 						& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='134' height='12'>" & strBOLETO_DT_VENCIMENTO & "</td>" 					& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='180' height='12' align='right' nowrap>" & FormataDecimal(strBOLETO_VALOR,2)  & "</td>" 			& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr valign='top'>" 																																														& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='113'><img height='1' src='../img/boleto_2.gif' width='113'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='072'><img height='1' src='../img/boleto_2.gif' width='072'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='132'><img height='1' src='../img/boleto_2.gif' width='132'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='134'><img height='1' src='../img/boleto_2.gif' width='134'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>" 		& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='113' height='13'>(-) Desconto / Abatimentos</td>" 							& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='112' height='13'>(-) Outras deduções</td>" 									& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='113' height='13'>(+) Mora / Multa</td>" 										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='113' height='13'>(+) Outros acréscimos</td>" 									& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>(=) Valor cobrado</td>" 										& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr class='cp' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='113' height='12'></td>" 											& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='112' height='12'></td>" 											& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='113' height='12'></td>" 											& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='113' height='12'></td>" 											& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td align='right' width='180' height='12'></td>" 											& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr valign='top'>" 																																														& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='113'><img height='1' src='../img/boleto_2.gif' width='113'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='112'><img height='1' src='../img/boleto_2.gif' width='112'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='113'><img height='1' src='../img/boleto_2.gif' width='113'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='113'><img height='1' src='../img/boleto_2.gif' width='113'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>" 		& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct' valign='top'><td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='659' height='13'>Sacado</td></tr>" 				& vbCrlf &_
"		<tr class='cp' valign='top'><td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='659' height='12'>" & strBOLETO_SACADO_NOME & "</td></tr>"	& vbCrlf &_
"		<tr valign='top'><td width='7'><img src='../img/boleto_2.gif' width='7' height='1' ></td><td width='659'><img height='1' src='../img/boleto_2.gif' width='659'></td></tr>" & vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct'>" 																																															& vbCrlf &_
"			<td width='7' height='12'></td><td width='564' height='12'>Instruções</td>" 																										& vbCrlf &_
"			<td width='7' height='12'></td><td width='088' height='12'>Autenticação mecânica</td>" 																						& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr><td width='7'></td><td width='564'></td><td width='7'></td><td width='88'></td></tr>" 																						& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table width='666' cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"	<tbody><tr><td width='7'></td><td width='500' class='cp'>" & strBOLETO_INSTRUCOES & "</td><td width='159'></td></tr></tbody>" 											& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table width='666' cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"	<tr><td class='ct' width='666'></td></tr>" 																																							& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr><td class='ct' width='666'><div align='right'>Corte na linha pontilhada</div></td></tr>" 																					& vbCrlf &_
"		<tr><td class='ct' width='666'><img height='1' src='../img/boleto_6.gif' width='665'></td></tr>" 																				& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<br>" 																																																				& vbCrlf &_
"<br>"																																																				& vbCrlf &_
"<table width='664' cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr valign='bottom'>" 																																													& vbCrlf &_
"			<td width='151' class='cp'><img src='../img/boleto_logo_" & strBOLETO_IMG_LOGO & ".gif'></td><td width='03'><img height='22' src='../img/boleto_3.gif' width='2'></td>" & vbCrlf &_
"			<td width='65'><div align='center' class='bc'>" & strBOLETO_COD_BANCO & "-" & strBOLETO_COD_BANCO_DV & "</div></td>" 												& vbCrlf &_
"			<td width='3'><img height='22' src='../img/boleto_3.gif' width='2'></td><td class='ld' width='445' align='right'>" & strBOLETO_LINHA_DIGITAVEL & "</td>" & vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr><td colspan='5'><img height='2' src='../img/boleto_2.gif' width='666'></td></tr>" 																								& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='472' height='13'>Local de pagamento</td>" 										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>Vencimento</td>" 												& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr class='cp' valign='top'>" 																																										& vbCrlf &_
"			<td width='7' height='12'><img height='25' src='../img/boleto_1.gif' width='1'></td><td width='472' height='12'>" & strBOLETO_LOCAL_PGTO & "</td>" 		& vbCrlf &_
"			<td width='7' height='12'><img height='25' src='../img/boleto_1.gif' width='1'></td><td width='180' height='12' align='right' valign='bottom'>" & strBOLETO_DT_VENCIMENTO & "</td>" & vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr valign='top'>" 																																														& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='472'><img height='1' src='../img/boleto_2.gif' width='472'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>" 		& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='472' height='13'>Cedente</td>" 													& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>Agência/Código cedente</td>" 								& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr class='cp' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='472' height='12'>" & strBOLETO_CEDENTE_NOME & "</td>" 						& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td>" 																											& vbCrlf &_
"			<td width='180' height='12' align='right'>" & strBOLETO_AGENCIA & "/" & strBOLETO_CONTA & "-" & strBOLETO_CONTA_DV & "</td>" 				& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr valign='top'>" 																																														& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='472'><img height='1' src='../img/boleto_2.gif' width='472'></td>" 		& vbCrlf &_
"			<td width='7'><img src='../img/boleto_2.gif' width='7' height='1'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>" 		& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='113' height='13'>Data do documento</td>" 										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='163' height='13'>N<u>o</u> documento</td>" 									& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='062' height='13'>Espécie doc.</td>" 												& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='034' height='13'>Aceite</td>" 														& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='072' height='13'>Data processamento</td>" 										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>Nosso número</td>" 												& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr class='cp' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='113' height='12'><div>" & PrepData(Date,true,false) & "</div></td>" 	& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='163' height='12'><div>" & strBOLETO_NUM_DOCUMENTO 	& "</div></td>" 	& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='062' height='12'><div align='center'>" & strBOLETO_ESPECIE_DOC 			& "</div></td>"	& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='034' height='12'><div align='center'>" & strBOLETO_ACEITE 				& "</div></td>"	& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='072' height='12'><div></div></td>"												& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='180' height='12' align='right'>" & strBOLETO_CARTEIRA & "/" & strBOLETO_NOSSO_NUMERO & "-" & strBOLETO_NOSSO_NUMERO_DV & "</td>" 	& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr valign='top'>" 																																														& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='113'><img height='1' src='../img/boleto_2.gif' width='113'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='163'><img height='1' src='../img/boleto_2.gif' width='163'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='062'><img height='1' src='../img/boleto_2.gif' width='062'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='034'><img height='1' src='../img/boleto_2.gif' width='034'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='072'><img height='1' src='../img/boleto_2.gif' width='072'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>" 		& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' colspan='3'>Uso do banco</td>" 												& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' width='083'>Carteira</td>" 													& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' width='053'>Espécie</td>" 													& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' width='123'>Quantidade</td>" 												& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' width='072'>Valor</td>" 														& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td height='13' width='180'>(=) Valor documento</td>" 									& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr class='cp' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td height='12' colspan='3'></td>" 																& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td height='12' width='083'><div>" & strBOLETO_CARTEIRA & "</div></td>" 			& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td height='12' width='053'><div align='center'>" & strBOLETO_ESPECIE & "</div></td>" 				& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td height='12' width='123'></td>" 																& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td height='12' width='072'></td>" 																& vbCrlf &_
"			<td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='180' height='12' align='right' nowrap>" & FormataDecimal(strBOLETO_VALOR,2)  & "</td>" 			& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr valign='top'>" 																																														& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='007'><img height='1' src='../img/boleto_2.gif' width='075'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='031'><img height='1' src='../img/boleto_2.gif' width='031'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='083'><img height='1' src='../img/boleto_2.gif' width='083'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='053'><img height='1' src='../img/boleto_2.gif' width='053'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='123'><img height='1' src='../img/boleto_2.gif' width='123'></td>"		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='072'><img height='1' src='../img/boleto_2.gif' width='072'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>" 		& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table width='666' cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr>" 																																																		& vbCrlf &_
"			<td width='10' align='right' valign='top'>" 																																					& vbCrlf &_
"				<table cellspacing=0 cellpadding=0 border='0' align='left'>" 																														& vbCrlf &_
"					<tbody>" 																																														& vbCrlf &_
"						<tr><td valign='top' width='7' class='ct'><img height='13' src='../img/boleto_1.gif' width='1'></td></tr>" 													& vbCrlf &_
"						<tr><td valign='top' width='7' class='cp'><img height='12' src='../img/boleto_1.gif' width='1'></td></tr>" 													& vbCrlf &_
"						<tr><td valign='top' width='7' height='1'><img height='01' src='../img/boleto_2.gif' width='1'></td></tr>" 													& vbCrlf &_
"					</tbody>" 																																														& vbCrlf &_
"				</table>" 																																															& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"			<td valign='top' width='468' rowspan='5'>" 																																					& vbCrlf &_
"				<div class='ct' style='height:12;'>Instruções (texto de responsabilidade do cedente)</div>" 																				& vbCrlf &_
"				<div class='cp' style='height:12;'>" & strBOLETO_INSTRUCOES & "</div>" 																											& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"			<td align='right' width='188'>" 																																									& vbCrlf &_
"				<table cellspacing='0' cellpadding='0' border='0'> " 																																	& vbCrlf &_
"					<tbody>" 																																														& vbCrlf &_
"						<tr valign='top' class='ct'><td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>(-) Desconto / Abatimentos</td></tr>" & vbCrlf &_
"						<tr valign='top' class='cp'><td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='180' height='12'></td></tr>" 		& vbCrlf &_
"						<tr valign='top'><td width='7'><img src='../img/boleto_2.gif' width='7' height='1'></td><td width='180'><img src='../img/boleto_2.gif' width='180' height='1'></td></tr>" & vbCrlf &_
"					</tbody>" 																																														& vbCrlf &_
"				</table>" 																																															& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr>" 																																																		& vbCrlf &_
"			<td width='10' align='right'>" 																																									& vbCrlf &_
"				<table cellspacing='0' cellpadding='0' border='0' align='left'>" 																													& vbCrlf &_
"					<tbody>" 																																														& vbCrlf &_
"						<tr><td valign='top' width='7' height='13' class='ct'><img height='13' src='../img/boleto_1.gif' width='1'></td></tr>" 									& vbCrlf &_
"						<tr><td valign='top' width='7' height='12' class='cp'><img height='12' src='../img/boleto_1.gif' width='1'></td></tr>" 									& vbCrlf &_
"						<tr><td valign='top' width='7' height='1'><img height='1' src='../img/boleto_2.gif' width='1'></td></tr>" 													& vbCrlf &_
"					</tbody>" 																																														& vbCrlf &_
"				</table>" 																																															& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"			<td align='right' width='188'>" 																																									& vbCrlf &_
"				<table cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"					<tbody>" 																																														& vbCrlf &_
"						<tr valign='top' class='ct'><td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>(-) Outras deduções</td></tr>" & vbCrlf &_
"						<tr valign='top' class='cp'><td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='180' height='12' align='right'></td></tr>" & vbCrlf &_
"						<tr valign='top'><td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td></tr>" & vbCrlf &_
"					</tbody>" 																																														& vbCrlf &_
"					</table>" 																																														& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr>" 																																																		& vbCrlf &_
"			<td align='right' width='10'>" 																																									& vbCrlf &_
"				<table cellspacing='0' cellpadding='0' border='0' align='left'>" 																													& vbCrlf &_
"					<tbody>" 																																														& vbCrlf &_
"						<tr><td valign='top' width='7' height='13' class='ct'><img height='13' src='../img/boleto_1.gif' width='1'></td></tr>" 									& vbCrlf &_
"						<tr><td valign='top' width='7' height='12' class='cp'><img height='12' src='../img/boleto_1.gif' width='1'></td></tr>" 									& vbCrlf &_
"						<tr><td valign='top' width='7' height='1'><img height='1' src='../img/boleto_2.gif' width='1'></td></tr>" 													& vbCrlf &_
"					</tbody>" 																																														& vbCrlf &_
"				</table>" 																																															& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"			<td align='right' width='188'>" 																																									& vbCrlf &_
"				<table border='0' cellpadding='0' cellspacing='0'>" 																																	& vbCrlf &_
"					<tbody>" 																																														& vbCrlf &_
"						<tr valign='top' class='ct'><td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>(+) Mora / Multa</td></tr>" & vbCrlf &_
"						<tr valign='top' class='cp'><td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='180' height='12' align='right'></td></tr>" & vbCrlf &_
"						<tr valign='top'><td width='7'><img src='../img/boleto_2.gif' width='7' height='1'></td><td width='180'><img src='../img/boleto_2.gif' width='180' height='1'></td></tr>" & vbCrlf &_
"					</tbody>" 																																														& vbCrlf &_
"				</table>" 																																															& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr>" 																																																		& vbCrlf &_
"			<td align='right' width='10'>" 																																									& vbCrlf &_
"				<table cellspacing='0' cellpadding='0' border='0' align='left'>" 																													& vbCrlf &_
"					<tbody>" 																																														& vbCrlf &_
"						<tr><td valign='top' width='7' height='13' class='ct'><img height='13' src='../img/boleto_1.gif' width='1'></td></tr>" 									& vbCrlf &_
"						<tr><td valign='top' width='7' height='12' class='cp'><img height='12' src='../img/boleto_1.gif' width='1'></td></tr>" 									& vbCrlf &_
"						<tr><td valign='top' width='7' height='1'><img height='1' src='../img/boleto_2.gif' width='1'></td></tr>" 													& vbCrlf &_
"					</tbody>" 																																														& vbCrlf &_
"				</table>" 																																															& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"			<td align='right' width='188'>" 																																									& vbCrlf &_
"				<table cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"					<tbody>" 																																														& vbCrlf &_
"						<tr valign='top' class='ct'><td width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td width='180' height='13'>(+) Outros acréscimos</td></tr>" & vbCrlf &_
"						<tr valign='top' class='cp'><td width='7'><img src='../img/boleto_1.gif' width='1' height='12'></td><td width='180' height='12' align='right'></td></tr>" & vbCrlf &_
"						<tr valign='top'><td width='7'><img src='../img/boleto_2.gif' width='7' height='1'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td></tr>" & vbCrlf &_
"					</tbody>" 																																														& vbCrlf &_
"				</table>" 																																															& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr>" 																																																		& vbCrlf &_
"			<td align='right' width='10'>" 																																									& vbCrlf &_
"				<table cellspacing='0' cellpadding='0' border='0' align='left'>" 																													& vbCrlf &_
"					<tbody>" 																																														& vbCrlf &_
"						<tr><td class='ct' valign='top' width='7' height='13'><img height='13' src='../img/boleto_1.gif' width='1'></td></tr>" 									& vbCrlf &_
"						<tr><td class='cp' valign='top' width='7' height='12'><img height='12' src='../img/boleto_1.gif' width='1'></td></tr>" 									& vbCrlf &_
"					</tbody>" 																																														& vbCrlf &_
"				</table>" 																																															& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"			<td align='right' width='188'>" 																																									& vbCrlf &_
"				<table cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"					<tbody>" 																																														& vbCrlf &_
"						<tr class='ct' valign='top'><td width='007'><img height='13' src='../img/boleto_1.gif' width='1'></td><td width='180' height='13'>(=) Valor cobrado</td></tr>"	& vbCrlf &_
"						<tr class='cp' valign='top'><td width='007'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='180' height='12' align='right'></td></tr>" & vbCrlf &_
"					</tbody>" 																																														& vbCrlf &_
"				</table>" 																																															& vbCrlf &_
"			</td>" 																																																	& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table width='666' cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"	<tbody><tr><td valign='top' width='666' height='1'><img height='1' src='../img/boleto_2.gif' width='666'></td></tr></tbody>" 												& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct' valign='top'><td width='7'><img height='13' src='../img/boleto_1.gif' width='1'></td><td width='659' height='13'>Sacado</td></tr>" 				& vbCrlf &_
"		<tr class='cp' valign='top'><td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td><td width='659' height='12'>" & strBOLETO_SACADO_NOME & "</td></tr>" & vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='cp' valign='top'>" 																																										& vbCrlf &_
"			<td width='7'><img height='12' src='../img/boleto_1.gif' width='1'></td>" 																											& vbCrlf &_
"			<td width='659' height='12'>" & strBOLETO_SACADO_ENDERECO & "&nbsp;-&nbsp;" & strBOLETO_SACADO_BAIRRO & "</td>" 														& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table cellspacing='0' cellpadding='0' border='0'>" 																																					& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr valign='top'>" 																																														& vbCrlf &_
"			<td class='ct' width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td>" 																							& vbCrlf &_
"			<td class='cp' width='472' height='13'>" & strBOLETO_SACADO_CEP & "&nbsp;-&nbsp;" & strBOLETO_SACADO_CIDADE & "&nbsp;-&nbsp;" & strBOLETO_SACADO_ESTADO  & "</td>" & vbCrlf &_
"			<td class='ct' width='7'><img src='../img/boleto_1.gif' width='1' height='13'></td><td class='ct' width='180' height='13'>Cód. baixa</td>" 					& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr valign='top'>" 																																														& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='472'><img height='1' src='../img/boleto_2.gif' width='472'></td>" 		& vbCrlf &_
"			<td width='7'><img height='1' src='../img/boleto_2.gif' width='7'></td><td width='180'><img height='1' src='../img/boleto_2.gif' width='180'></td>" 		& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table width='666' cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr class='ct'>" 																																															& vbCrlf &_
"			<td width='7' height='12'></td><td width='409'>Sacador/Avalista</td>" 																												& vbCrlf &_
"			<td width='250'><div align='right'>Autenticação mecânica - <b class='cp'>Ficha de Compensação</b></div></td>" 															& vbCrlf &_
"		</tr>" 																																																		& vbCrlf &_
"		<tr><td class='ct' colspan='3'></td></tr>" 																																						& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table width='640' cellspacing='0' cellpadding='0' border='0'>" 																																	& vbCrlf &_
"	<tbody><tr><td valign='bottom' align='left' height='50'><img src='../img/spacer.gif' width='20'>" & BarCode25(strBOLETO_CODIGO_BARRAS) & "</td></tr></tbody>" 														& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"<table width='666' cellspacing='0' cellpadding='0' class='ct' border='0'>" 																													& vbCrlf &_
"	<tr><td width='666'></td></tr>" 																																											& vbCrlf &_
"	<tbody>" 																																																		& vbCrlf &_
"		<tr><td width='666'><div align='right'>Corte na linha pontilhada</div></td></tr>" 																									& vbCrlf &_
"		<tr><td width='666'><img src='../img/boleto_6.gif' width='665' height='1'></td></tr>" 																								& vbCrlf &_
"	</tbody>" 																																																		& vbCrlf &_
"</table>" 																																																			& vbCrlf &_
"</body>" 																																																			& vbCrlf &_
"</html>"

set objFileSystemObject = CreateObject("Scripting.FileSystemObject")
'------------------------------------------------------------------------------------------------------------
' Salva arquivo do boleto na pasta FIN_Boletos, que encontra-se dentro da pasta upload do cliente, 
' no seguinte formato: "codigo da conta a pagar/receber"_"numero de impressoes do boleto".htm
'------------------------------------------------------------------------------------------------------------
strFilePath = Server.MapPath(strUploadPath & "FIN_Boletos/Boleto_" & intCOD_CONTA_PAGAR_RECEBER & "_" & CInt("0" & strNUM_IMPRESSOES)+1 & ".htm")
set objArquivo = objFileSystemObject.CreateTextFile(strFilePath, true)   

strHTML = Replace(Replace(strHTML,"src='../img/","src='../../../img/"),"src='../upload/" & Request.Cookies("VBOSS")("CLINAME") & "/","src='../")

objArquivo.Write(strHTML)
objArquivo.Close

set objArquivo = Nothing
'------------------------------------------------------------------------------------------------------------
' Faz a leitura dos dados no arquivo gravado anteriormente
'------------------------------------------------------------------------------------------------------------
set objArquivo = objFileSystemObject.OpenTextFile(strFilePath)

strHTML = objArquivo.ReadAll
objArquivo.Close

set objArquivo 			= Nothing
set objFileSystemObject = Nothing

'------------------------------------------------------------------------------------------------------------
' Altera o caminho das imagens para poder exibir corretamente a partir do modulo atual
'------------------------------------------------------------------------------------------------------------
strHTML = Replace(strHTML,"src='../../../img/", "src='./img/")
strHTML = Replace(strHTML,"src='../"			 , "src='../upload/" & Request.Cookies("VBOSS")("CLINAME") & "/")
strHTML = Replace(strHTML,"src='./img/"		 , "src='../img/")
'------------------------------------------------------------------------------------------------------------

if Err.Number <> 0 then
	strMSG = "Não foi possível exibir boleto.<br>"
	strMSG = strMSG & Err.Number & " - " & Err.Description
	Mensagem strMSG, "", 1 
elseif intCOD_CONTA_PAGAR_RECEBER<>"" and IsNumeric(intCOD_CONTA_PAGAR_RECEBER) then
	AbreDBConn objConn, CFG_DB
	objConn.Execute("UPDATE FIN_CONTA_PAGAR_RECEBER SET NUM_IMPRESSOES=NUM_IMPRESSOES+1 WHERE COD_CONTA_PAGAR_RECEBER=" & intCOD_CONTA_PAGAR_RECEBER)
	FechaDBConn objConn
end if

Response.Write(strHTML)
%>