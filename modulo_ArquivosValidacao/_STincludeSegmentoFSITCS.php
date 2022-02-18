<!-- INCLUDE PARA O SEGMENTO 'F' -->
<!-- Criado Por: Leandro Antunes -->
<?php if(isset($_SESSION['ArqValida_chaveSF'])){?>
<thead><tr><th width="40%"></th><th width="60%"></th></tr></thead>
<?php for($auxContador = 1; $auxContador <= $_SESSION['ArqValida_chaveSF']; $auxContador++){ ?>	
	<tr><td colspan="2"><br /><div align="left" alstyle="padding-left:130px;" class="destaque_gde"><strong>Segmento F</strong></div></td></tr>
	<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
	<tr><td height="10" colspan="2"></td></tr>			
	<tr bgcolor="#CCCCCC"><td colspan="2" align="left"><b><?php echo $_SESSION['ArqValida_linhaSegmU'.$auxContador]; ?></b></td></tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Código do Banco na Compensação</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codBanComSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td nowrap="nowrap" align="right"><b>Lote de Serviço</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_lotSerSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de Registro</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipRegSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nº Sequencial Registro de Lote</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numSeqRegDetSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Código Segmento do Registro Detalhe</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ALFAcodSegRegDetSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ALFAusoExcFebSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Horário da Transação</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_hrTransSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Natureza do Lançamento</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_natLctoSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo do Complemento do LCTO</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tpCompLctoSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Complemento do LANÇAMENTO</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_compLctoSF'.$auxContador];?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Identificação de Isenção de CPMF</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_isenCpmfSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Data Contábil</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtauxContadorabilSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Data do Lançamento</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtLctoSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Valor do Lançamento</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_vlrLctoSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de Lançamento</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tpLctoSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Categoria do Lançamento</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_catLctoSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Código Hist Lcto. Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codHistLctoSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Descr. do Serviço Origem na Tarifa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_descHistLctoSF'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Númerdo do Documento / Complemento</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numDocComplSF'.$auxContador]; ?></div></td>
	</tr>
	<tr><td></td></tr>
<?php		
	}
	$_SESSION['ArqValida_chaveSF'] = $_SESSION['ArqValida_chaveSF'] - $_SESSION['ArqValida_chaveSF'];
 	}
?>