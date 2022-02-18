<!-- INCLUDE PARA O HEADER LOTE -->
<!-- Criado Por: Márcio Padilha -->
<!-- Update Por: Leandro e Alessander -->
	<thead><tr><th width="40%"></th><th width="60%"></th></tr></thead>
	<tr><td colspan="2"><br /><div align="left" alstyle="padding-left:130px;" class="destaque_gde"><strong>Registro Header de Lote</strong></div></td></tr>
	<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
	<tr><td height="10" colspan="2"></td></tr>			
	<tr bgcolor="#CCCCCC"><td colspan="2" align="left"><b><?php echo $_SESSION['ArqValida_linhaHeaderL']; ?></b></td></tr>
	<tr bgcolor="#FFFFFF">
		<td  align="right"><b>Código do Banco na Compensação</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codBanHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Lote de Serviço</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_lotSerHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de Registro</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipRegHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Tipo de Operação</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAtipOperHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de Serviço</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipServHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo$_SESSION['ArqValida_AFAusoExcFeb1HL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nº da Versão do Layout do Lote</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numVerLayLotHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcFebHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de Inscrição da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipInsEmpHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nº de Inscrição da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_insEmpHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Código do Convênio do Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAcodConBanHL']; ?></div></td>
	</tr>
	<!--tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo da CAIXA</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcCaiHL']; ?></div></td>
	</tr-->
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Agencia Mantenedora da Conta</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ageManConHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Digito Verificador da Conta</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Número da Conta Corrente</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numConCorrHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Dígito Verificador da Conta</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerConHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Dígito Verif. Ag./Ced (sem operação)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgCedHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nome da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAnomEmpHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Mensagem 1</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAmens1HL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Mensagem 2</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAmens2pHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Numero Remessa/Retorno</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numRemRetHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Data de Gravação Remessa/Retorno</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtRemRetHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Data do Crédito</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtCreHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcFeb2pHL']; ?></div></td>
	</tr>
	<tr><td></td></tr>