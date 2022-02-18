<!-- INCLUDE PARA A TRANSAÇÃO TIPO '1' //-->
<!-- Criado Por: Márcio Padilha //-->
<!-- Update Por: Leandro e Alessander //-->
<!-- Adaptado para Cobrança CNAB400 por: Cleverson //-->
<thead><tr><th width="40%"></th><th width="60%"></th></tr></thead>
<?php for($auxContador = 1; $auxContador <= $_SESSION['ArqValida_key']; $auxContador++){ ?>		
	<tr><td colspan="2" ><br />
		<div align="left" alstyle="padding-left:130px;" class="destaque_gde"><strong>Transação Tipo 1</strong></div>
	</td></tr>
	<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
	<tr><td height="10" colspan="2"></td></tr>			
	<tr bgcolor="#CCCCCC"><td colspan="2" align="left"><b><?php  echo $_SESSION['ArqValida_LinhaTransT1'.$auxContador]; ?></b></td></tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Identificação do Registro</b></td>
		<td ><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IdentificacaoRegistroT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Tipo de Inscrição Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_TipoInscrEmpresaT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nº Inscrição da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumInscrEmpresaT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Zeros</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ZEROS1T1'.$auxContador] ; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Identificação da Empresa Cedente no Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IdentificacaoEmpresaCedenteT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nº Controle do Participante</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumControleParticipanteT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Zeros</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ZEROS2T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Identificação do Título no Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IdentificacaoTituloT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso do Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_UsoBanco1T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Uso do Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_UsoBanco2T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Indicador de Rateio Crédito</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IndicadorRateioCreditoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Zeros</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ZEROS3T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Carteira</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CarteiraT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Identificação de Ocorrência</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IdentificacaoOcorrenciaT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Data Ocorrência no Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DataOcorrenciaT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Número do Documento</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumDocumentoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Identificação do Título no Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IdentificacaoTituloT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Data Vencimento do Título</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DataVctoTituloT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Valor do Título</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ValorTituloT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Banco Cobrador</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BancoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Agência Cobradora</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AgenciaT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Espécie do Título</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_EspecieTituloT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Despesas de cobrança para os Códigos de Ocorrência 02 - Entrada Confirmada 28 - Débito de Tarifas</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DespesasCobrCodigosOcorr02e28T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Outras Despesas / Custas de Protesto</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_OutrasDespesasCustasProtestoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Juros Operação em Atraso</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_JurosOperacaoAtrasoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>IOF Devido</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IOFDevidoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Abatimento Concedido sobre o Título</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AbatimentoConcedidoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Desconto Concedido</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DescontoConcedidoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Valor Pago</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ValorPagoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Juros de Mora</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_JurosMoraT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Outros Créditos</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_OutrosCreditosT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Brancos</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS1T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Motivo do Código de Ocorrência 25 (Confirmação de Instrução de Protesto Falimentar e do Código de Ocorrência 19 Confirmação de Instrução de Protesto)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_MotivoCodigoOcorrencia25T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Data do Crédito</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DataCreditoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Origem Pagamento</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_OrigemPagamentoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Brancos</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS2T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Quando cheque Bradesco informe 0237</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CodigoBancoChequeBradescoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Motivos das Rejeições para os Códigos de Ocorrência da Posição 109 a 110</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_MotivosRejeicoesCodOcorrPos109a110T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Brancos</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS3T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Número do Cartório</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumCartorioT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Número do Protocolo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumeroProtocoloT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Brancos</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS4T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nº Seqüencial de Registro</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumSeqRegistroT1'.$auxContador]; ?></div></td>
	</tr>
	<tr><td></td></tr>
<?php
	}
	$_SESSION['ArqValida_key'] = $_SESSION['ArqValida_key']-$_SESSION['ArqValida_key'];
?>