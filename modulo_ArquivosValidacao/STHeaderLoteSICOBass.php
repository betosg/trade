	<tr>
		<td colspan="2">
		<br /><div align="left" alstyle="padding-left:130px;" class="destaque_gde"><strong>Registro Header de Lote</strong></div>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="2" background="../img/line_dialog.jpg"></td>
	</tr>
	<tr>
		<td height="10" colspan="2"></td>
	</tr>			
	<tr bgcolor="#CCCCCC">
		<td colspan="2" align="left"><b><?php echo $_SESSION['ArqValida_linhaHeaderL']; ?></b></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--1--><td  align="right"><b>C�digo do Banco na Compensa��o</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codBanHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--2--><td align="right"><b>Lote de Servi�o</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_lotSerHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--3--><td align="right"><b>Tipo de Registro</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipRegHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--4--><td align="right"><b>Tipo de Opera��o</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAtipOperHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--5--><td align="right"><b>Tipo de Servi�o</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipServHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--6--><td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo$_SESSION['ArqValida_AFAusoExcFeb1HL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--7--><td align="right"><b>N� da Vers�o do Layout do Lote</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numVerLayLotHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--8--><td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcFebHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--9--><td align="right"><b>Tipo de Inscri��o da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipInsEmpHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--10--><td align="right"><b>N� de Inscri��o da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_insEmpHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--11--><td align="right"><b>C�digo do Conv�nio do Banco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAcodConBanHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--12--><td align="right"><b>Uso Exclusivo da CAIXA</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcCaiHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--13--><td align="right"><b>Agencia Mantenedora da Conta</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ageManConHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--14--><td align="right"><b>Digito Verificador da Conta</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--15--><td align="right"><b>N�mero da Conta Corrente</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numConCorrHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--16--><td align="right"><b>D�gito Verificador da Conta</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerConHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--17--><td align="right"><b>D�gito Verif. Ag./Ced (sem opera��o)</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_digVerAgCedHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--18--><td align="right"><b>Nome da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAnomEmpHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--19--><td align="right"><b>Mensagem 1</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAmens1HL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--20--><td align="right"><b>Mensagem 2</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAmens2pHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--21--><td align="right"><b>Numero Remessa/Retorno</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numRemRetHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--22--><td align="right"><b>Data de Grava��o Remessa/Retorno</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtRemRetHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
<!--23--><td align="right"><b>Data do Cr�dito</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtCreHL']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
<!--24--><td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_AFAusoExcFeb2pHL']; ?></div></td>
	</tr>
	<tr><td></td></tr>