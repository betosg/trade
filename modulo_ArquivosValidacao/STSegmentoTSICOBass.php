<?php 
	for($x=1; $x <= $_SESSION['ArqValida_key']; $x++){
?>		
		<tr>
			<td colspan="2" >
				<br /><div align="left" alstyle="padding-left:130px;" class="destaque_gde"><strong>Segmento T</strong></div>
			</td>
		</tr>
		<tr>
			<td colspan="2" height="2" background="../img/line_dialog.jpg"></td>
		</tr>
		<tr>
			<td height="10" colspan="2"></td>
		</tr>			
		<tr bgcolor="#CCCCCC">
			<td colspan="2" align="left"><b><?php  echo $_SESSION['ArqValida_linhaSegmT'.$x]; ?></b></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--1-->	<td align="right"><b>Código do Banco na Compensação</b></td>
			<td ><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codBanComST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--2-->	<td align="right"><b>Lote de Serviço</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_lotSerST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--3-->	<td align="right"><b>Tipo de Registro</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipRegST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--4-->	<td align="right"><b>Nº Sequencial Registro de Lote</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numSecRegLotST'.$x] ; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--5-->	<td align="right"><b>Código Segmento do Registro Detalhe</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAcodSegRegDetST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--6-->	<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcFeb1ST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--7-->	<td align="right"><b>Código de Movimento Retorno</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codMovRetST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--8-->	<td align="right"><b>Agência Mantenedora da Conta</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_agMantCon'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--9-->	<td align="right"><b>Dígito Verificador da Agência</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVeriAge'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--10-->	<td align="right"><b>Código do Cedente (sem operação)</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codCede'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--11-->	<td align="right"><b>Dig. Verif. Cedente (sem operação)</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerCedeST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--12-->	<td align="right"><b>Dig. Verif. Ag./Ced (sem operação)</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgeCed'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--13-->	<td align="right"><b>Uso Exclusivo da CAIXA</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcCai2ST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--14-->	<td align="right"><b>Identificação do Título</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ideTitST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--17-->	<td align="right"><b>Código  da Carteira</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codCartST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--18-->	<td align="right"><b>Nº do Documento de Cobrança</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAnumDocCobST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--19-->	<td align="right"><b>Uso Exclusivo da CAIXA</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExclCaiST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--20-->	<td align="right"><b>Data de Vencimento do Título</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtVenTituST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--21-->	<td align="right"><b>Valor Nominal do Título</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valNomTitST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--22-->	<td align="right"><b>Numero do Banco</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numBanST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--23-->	<td align="right"><b>Agência Cobradora/Recebedora</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AgeCobRebST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--24-->	<td align="right"><b>Dígito Verificador da Agência</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgeST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--25-->	<td align="right"><b>Identificação do Título na Empresa / Nosso Número SR com 16 posições</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAidenTituEmpST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--26-->	<td align="right"><b>Código da Moeda</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codMoeST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--27-->	<td align="right"><b>Tipo de Inscrição</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipInsST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--28-->	<td align="right"><b>Número de Inscrição</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numInsST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--29-->	<td align="right"><b>Nome do Sacado</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAnomeSacST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--30-->	<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcFeb2ST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--31-->	<td align="right"><b>Valor da tarifa/Custas</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valTarCusST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--32-->	<td align="right"><b>Identificação para Rejeições, Tarifas, Custas, Liquidação e Baixas</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ideRejTafCusLiqBaiST'.$x]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--33-->	<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcFeb3ST'.$x]; ?></div></td>
		</tr>
		<tr><td></td></tr>
<?php
}
$_SESSION['ArqValida_key'] = $_SESSION['ArqValida_key']-$_SESSION['ArqValida_key'];

?>