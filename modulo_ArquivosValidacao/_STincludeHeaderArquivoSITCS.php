<!-- INCLUDE PARA O HEADER DE ARQUIVO -->
<!-- Criado Por: Márcio Padilha -->
<!-- Update Por: Leandro e Alessander -->
	<thead><tr><th width="40%"></th><th width="60%"></th></tr></thead>
	<tr><td colspan="2" ><br /><span style="color:#009966;float:right; font-size:18px; font-weight:bold" >SITCS - Sindicais</span><span style="float:left;" class="destaque_gde"><strong>Registro Header de Arquivo</strong></span></td></tr>
	<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
	<tr><td height="10" colspan="2"></td></tr>			
	<tr bgcolor="#CCCCCC"><td colspan="2" align="left"><b><?php echo $_SESSION['ArqValida_linhaHeaderHA'];?></b></td></tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Código do Banco na Compensação</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codBancoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Lote de Serviço</b></td>
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
		<td align="right"><b>Tipo de inscrição da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipoIncEmpHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nº de Inscrição da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numIncEmpHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Cód do Convênio no Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codConvBancoHA']; ?></div></td>
	</tr>
	<!--tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo CAIXA</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoCAIXA1HA']; ?></div></td>
	</tr-->
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Agência Mantenedora da Conta</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ageMantContaHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Digito Verificador da Agencia</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgeHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Cód do Cedente (sem operação)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codCedenteHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Díg. Verif. Cedente (sem operação)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerCedHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Díg. Verif. Ag/Ced. (sem operação)</b></td>
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
		<td align="right"><b>Código Remessa/Retorno</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codRemRetHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Data de Geração do Arquivo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtGerArqHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Hora de Geração do Arquivo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_hsGerArqHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Numero Sequencial do Arquivo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numSeqArqHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nº da Versão do Layout do Arquivo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numVerLayArqHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Densidade de Gravação do Arquivo</b></td>
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