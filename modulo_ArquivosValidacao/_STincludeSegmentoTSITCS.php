<!-- INCLUDE PARA O SEGMENTO 'T' -->
<!-- Criado Por: Márcio Padilha -->
<!-- Update Por: Leandro e Alessander -->
<thead><tr><th width="40%"></th><th width="60%"></th></tr></thead>
<?php for($auxContador = 1; $auxContador <= $_SESSION['ArqValida_key']; $auxContador++){ ?>		
	<tr><td colspan="2" ><br /><div align="left" alstyle="padding-left:130px;" class="destaque_gde"><strong>Segmento T</strong></div></td></tr>
	<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
	<tr><td height="10" colspan="2"></td></tr>			
	<tr bgcolor="#CCCCCC"><td colspan="2" align="left"><b><?php  echo $_SESSION['ArqValida_linhaSegmT'.$auxContador]; ?></b></td></tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Código do Banco na Compensação</b></td>
		<td ><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codBanComST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Lote de Serviço</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_lotSerST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de Registro</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipRegST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nº Sequencial Registro de Lote</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numSecRegLotST'.$auxContador] ; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Código Segmento do Registro Detalhe</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAcodSegRegDetST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcFeb1ST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Numero Sequencial de REGISTRO DE LOTE</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numSecRegLotST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Código de Movimento Retorno</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codMovRetST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Agência Mantenedora da Conta</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_agMantCon'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Dígito Verificador da Agência</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVeriAge'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Código do Cedente (sem operação)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codCede'.$auxContador]; ?></div></td>
	</tr>
	<!--tr bgcolor="#FFFFFF">
		<td align="right"><b>Dig. Verif. Cedente (sem operação)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerCedeST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Dig. Verif. Ag./Ced (sem operação)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgeCed'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Uso Exclusivo da CAIXA</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcCai2ST'.$auxContador]; ?></div></td>
	</tr-->
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Identificação do Título</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ideTitST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Código  da Carteira</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codCartST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nº do Documento de Cobrança</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAnumDocCobST'.$auxContador]; ?></div></td>
	</tr>
	<!--tr bgcolor="#FFFFFF">
		<td align="right"><b>Uso Exclusivo da CAIXA</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExclCaiST'.$auxContador]; ?></div></td>
	</tr-->
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Data de Vencimento do Título</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtVenTituST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor Nominal do Título</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valNomTitST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Numero do Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numBanST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Agência Cobradora/Recebedora</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AgeCobRebST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Dígito Verificador da Agência</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgeST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Identificação do Título na Empresa / Nosso Número SR com 16 posições</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAidenTituEmpST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Código da Moeda</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codMoeST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de Inscrição</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipInsST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Número de Inscrição</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numInsST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nome do Sacado</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAnomeSacST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Número do Contrato de Crédito</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numContrCredST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor da tarifa/Custas</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valTarCusST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Identificação para Rejeições, Tarifas, Custas, Liquidação e Baixas</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ideRejTafCusLiqBaiST'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcFeb3ST'.$auxContador]; ?></div></td>
	</tr>
	<tr><td></td></tr>
<?php
	}
	$_SESSION['ArqValida_key'] = $_SESSION['ArqValida_key']-$_SESSION['ArqValida_key'];
?>