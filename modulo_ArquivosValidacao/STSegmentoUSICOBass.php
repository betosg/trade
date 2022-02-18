<?php 
for($cont=1; $cont <= $_SESSION['ArqValida_chave']; $cont++){
//segmento U Obrigatório
//--------------------------------------------------------------------------------------------------------									
?>
		<tr>
			<td colspan="2">
			<br /><div align="left" alstyle="padding-left:130px;" class="destaque_gde"><strong>Segmento U</strong></div>
			</td>
		</tr>
		<tr>
			<td colspan="2" height="2" background="../img/line_dialog.jpg"></td>
		</tr>
		<tr>
			<td height="10" colspan="2"></td>
		</tr>			
		<tr bgcolor="#CCCCCC">
			<td colspan="2" align="left"><b><?php echo $_SESSION['ArqValida_linhaSegmU'.$cont]; ?></b></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--1--><td align="right"><b>Código do Banco na Compensação</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codBanComSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--2--><td nowrap="nowrap" align="right"><b>Lote de Serviço</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_lotSerSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--3--><td align="right"><b>Tipo de Registro</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipRegSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--4--><td align="right"><b>Nº Sequencial Registro de Lote</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numSeqRegDetSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--5--><td align="right"><b>Código Segmento do Registro Detalhe</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ALFAcodSegRegDetSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--6--><td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ALFAusoExcFebSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--7--><td align="right"><b>Código de Movimento Retorno</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codMovRetSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--8--><td align="right"><b>Juros/Multa/Encargos</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_jurMulEncSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--9--><td align="right"><b>Valor do Desconto Concedido</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valDesConSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--10--><td align="right"><b>Valor do Abat. Concedido/Cancel.</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valAbtConCanSU'.$cont];?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--11--><td align="right"><b>Valor do IOF Recolhido</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valiofRecSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--12--><td align="right"><b>Valor Pago Pelo Sacado</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valpagSacSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--13--><td align="right"><b>Valor Líquido a ser Creditado</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valliqCreSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--14--><td align="right"><b>Valor de Outras Despesas</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valOutDesSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--15--><td align="right"><b>Valor de Outros Créditos</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valOutCreSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--16--><td align="right"><b>Data da Ocorrência</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtOcoSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--17--><td align="right"><b>Data da Efetivação do Crédito</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtEfeCreSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FAFAFA">
<!--23--><td align="right"><b>Data do Débito da Tarifa</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtDebTarSU'.$cont]; ?></div></td>
		</tr>
		<tr bgcolor="#FFFFFF">
<!--24--><td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
			<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_usoExcFebSU'.$cont]; ?></div></td>
		</tr>
		<tr><td></td></tr>	
<?php		
}
$_SESSION['ArqValida_chave'] = $_SESSION['ArqValida_chave'] - $_SESSION['ArqValida_chave'];
?>