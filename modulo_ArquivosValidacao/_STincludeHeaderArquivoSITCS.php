<!-- INCLUDE PARA O HEADER DE ARQUIVO -->
<!-- Criado Por: M�rcio Padilha -->
<!-- Update Por: Leandro e Alessander -->
	<thead><tr><th width="40%"></th><th width="60%"></th></tr></thead>
	<tr><td colspan="2" ><br /><span style="color:#009966;float:right; font-size:18px; font-weight:bold" >SITCS - Sindicais</span><span style="float:left;" class="destaque_gde"><strong>Registro Header de Arquivo</strong></span></td></tr>
	<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
	<tr><td height="10" colspan="2"></td></tr>			
	<tr bgcolor="#CCCCCC"><td colspan="2" align="left"><b><?php echo $_SESSION['ArqValida_linhaHeaderHA'];?></b></td></tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>C�digo do Banco na Compensa��o</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codBancoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Lote de Servi�o</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_loteServicoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de Registro</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipoRegistroHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExclFeb1HA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de inscri��o da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipoIncEmpHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>N� de Inscri��o da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numIncEmpHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>C�d do Conv�nio no Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codConvBancoHA']; ?></div></td>
	</tr>
	<!--tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo CAIXA</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoCAIXA1HA']; ?></div></td>
	</tr-->
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Ag�ncia Mantenedora da Conta</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ageMantContaHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Digito Verificador da Agencia</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgeHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>C�d do Cedente (sem opera��o)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codCedenteHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>D�g. Verif. Cedente (sem opera��o)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerCedHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>D�g. Verif. Ag/Ced. (sem opera��o)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgCedHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nome da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAnomEmpHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nome do Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAnomBanHA']; ?></div></td>
	</tr>
	<!--tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExclFeb2HA']; ?></div></td>
	</tr-->
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>C�digo Remessa/Retorno</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codRemRetHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Data de Gera��o do Arquivo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtGerArqHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Hora de Gera��o do Arquivo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_hsGerArqHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Numero Sequencial do Arquivo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numSeqArqHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>N� da Vers�o do Layout do Arquivo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numVerLayArqHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Densidade de Grava��o do Arquivo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_denGerArqHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Para uso Reservado do Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoResBanHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Para uso Reservado da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoResEmpHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExclFeb3HA']; ?></div></td>
	</tr>
	<tr><td></td></tr>