<!-- INCLUDE PARA A TRANSAÇÃO TIPO '1' //-->
<!-- Criado Por: Márcio Padilha //-->
<!-- Update Por: Leandro e Alessander //-->
<!-- Adaptado para Cobrança CNAB400 por: Cleverson //-->
<!-- Adaptado para Cobrança CNAB400 Itau por: Gabriel //-->
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
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Agencia Mantenedora</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AgenciaMantenedoraT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Zeros</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ZEROS1T1'.$auxContador] ; ?></div></td>
	</tr>	
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Conta Corrente</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ContaCorrenteT1'.$auxContador] ; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>DAC Ag/Conta</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DACT1'.$auxContador]; ?></div></td>
	</tr>	
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Brancos 1</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS1T1'.$auxContador]; ?></div></td>
	</tr>	
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Identificação do Título no Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IdentificacaoTituloT1'.$auxContador]; ?></div></td>
	</tr>	
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nosso Número</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NossoNumeroT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Brancos 2</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS2T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Carteira</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CarteiraT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nosso Número 2</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NossoNumero2T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>DAC Nosso Número</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DacNossoNumeroT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Brancos 3</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS3T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Cód. Carteira</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CodCarteiraT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Cód. Ocorrencia</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CodOcorrenciaT1'.$auxContador]; ?></div></td>
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
		<td align="right"><b>Nosso Número 3</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NossoNumero3T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Brancos 4</b></td>	
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS4T1'.$auxContador]; ?></div></td>
	</tr>		
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Data Vencimento do Título</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DataVctoTituloT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor do Título</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ValorTituloT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Banco Cobrador</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BancoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Agência Cobradora</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AgenciaT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>DAC Agência Cobradora</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DacAgenciaT1'.$auxContador]; ?></div></td>
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
		<td align="right"><b>Brancos 5</b></td>	
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS4T1'.$auxContador]; ?></div></td>
	</tr>	

	<tr bgcolor="#FAFAFA">
		<td align="right"><b>IOF Devido</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IOFDevidoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Abatimento Concedido sobre o Título</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AbatimentoConcedidoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Desconto Concedido</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DescontoConcedidoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor Pago</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ValorPagoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Juros de Mora</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_JurosMoraT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Outros Créditos</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_OutrosCreditosT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Indicador DDA</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IndicadorDDAT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Brancos 6</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS6T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Data do Crédito</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DataCreditoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Cód. Cancelamento</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CodCancelamentoT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Brancos 7</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS7T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Zeros</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ZEROS2T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nome Pagador</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NomePagadorT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Brancos 8</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS8T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Mensagens/Erros</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_MsgErrosT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Brancos 9</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS9T1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Cód. Liquidação</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CodLiquidacaoT1'.$auxContador]; ?></div></td>
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