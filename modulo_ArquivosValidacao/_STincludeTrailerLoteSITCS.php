<!-- INCLUDE PARA O TRAILER DE LOTE -->
<!-- Criado Por: Márcio Padilha -->
<!-- Update Por: Leandro e Alessander -->
	<thead><tr><th width="40%"></th><th width="60%"></th></tr></thead>
	<tr><td colspan="2"><br /><div align="left" alstyle="padding-left:130px;" class="destaque_gde"><strong>Registro Trailer de Lote</strong></div></td></tr>
	<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
	<tr><td height="10" colspan="2"></td></tr>			
	<tr bgcolor="#CCCCCC"><td colspan="2" align="left"><b><?php echo $_SESSION['ArqValida_linhaTrailerL'];?></b></td></tr>
	
    
<!--1	$_SESSION['ArqValida_codBanTL'] 		  = isNumber($traileL, 1, 3, "Cód do Banco na Compensação","104");
2	$_SESSION['ArqValida_lotSerTL'] 		  = isNumber($traileL, 4, 7, "Lote de Serviço","1");
3	$_SESSION['ArqValida_tipSerTL'] 		  = isNumber($traileL, 8, 8, "Tipo de registro","5");
4	$_SESSION['ArqValida_AFAusoExclFeb1TL']   = isAlfa($traileL, 9, 17);
5	$_SESSION['ArqValida_quanRegLotTL'] 	  = isNumber($traileL, 18, 23, "Quantidade de Registros no Lote");
6	$_SESSION['ArqValida_quanTCobr1TL'] 	  = isNumber($traileL, 24, 29, "Quantidade de Titulos em Cobrança Simples","0");
7	$_SESSION['ArqValida_valTotTitCarTL'] 	  = isNumber($traileL, 30, 46, "Valor Total dos Títulos em Carteiras Simples","0");
8	$_SESSION['ArqValida_quanTitCobr2TL'] 	  = isNumber($traileL, 47, 52, "Quantidade de Titulos em Cobrança Caucionada","0");
9	$_SESSION['ArqValida_valTotTitCar2TL'] 	  = isNumber($traileL, 53, 69, "Valor Total dos Títulos em Carteiras Caucionada");
10	$_SESSION['ArqValida_valTotTitCar3TL'] 	  = isNumber($traileL, 70, 75, "Quantidade de Titulos em Cobrança Descontada","0");
11	$_SESSION['ArqValida_valTotTitCobr3TL']   = isNumber($traileL, 76, 92, "Quantidade de Titulos em Carteiras","0");-->
    
    <tr bgcolor="#FFFFFF">
		<td align="right"><b>Código do Banco na Compensação</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codBanTL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Lote de Serviço</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_lotSerTL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de Registro</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipSerTL']; ?></div></td>
	</tr>
    <tr bgcolor="#FFFFFF">
		<td align="right"><b>Quantidade de Registros no Lote</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_quanRegLotTL']; ?></div></td>
	</tr>
    
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Quantidade de Titulos em Cobrança Simples</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_quanTCobr1TL']; ?></div></td>
	</tr>
    <tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor Total dos Títulos em Carteiras Simples</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valTotTitCarTL']; ?></div></td>
	</tr>
    <tr bgcolor="#FAFAFA">
		<td align="right"><b>Quantidade de Titulos em Cobrança Caucionada</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_quanTitCobr2TL']; ?></div></td>
	</tr>
   <tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor Total dos Títulos em Carteiras Caucionada</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valTotTitCar2TL']; ?></div></td>
	</tr>
    
    <tr bgcolor="#FFFFFF">
		<td align="right"><b>Quantidade de Títulos em Carteiras</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valTotTitCar3TL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Quantidade de Titulos em Carteira Descontada</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valTotTitCobr3TL']; ?></div></td>
	</tr>
    
    <?php /*?><tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExclFeb1TL']; ?></div></td>
	</tr>	
	
	
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Quantidade de Titulos em Cobrança Vinculado</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_quanTCobr1VincTL']; ?></div></td>
	</tr>
	<!--tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor Total dos Títulos em Carteiras Vinculado</b></td>
		<td><div style="padding-left:10px"><?php //echo $_SESSION['ArqValida_valTotTitCarVincTL']; ?></div></td>
	</tr-->
	
    
	
	
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Num. Aviso LCTO</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numAvisoLctoTL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExclFeb3TL']; ?></div></td>
	</tr><?php */?>
	<tr><td></td></tr>