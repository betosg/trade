<!-- INCLUDE PARA O HEADER DE ARQUIVO //-->
<!-- Criado Por: Márcio Padilha //-->
<!-- Update Por: Leandro e Alessander //-->
<!-- Adaptado para Cobrança CNAB400 por: Cleverson //-->
	<thead><tr><th width="40%"></th><th width="60%"></th></tr></thead>
	<tr><td colspan="2" ><br />
		<span style="color:#D1343B;float:right; font-size:18px; font-weight:bold">Cobrança CNAB400</span>
		<span style="float:left;" class="destaque_gde"><strong>Registro Header de Arquivo</strong></span>
	</td></tr>
	<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
	<tr><td height="10" colspan="2"></td></tr>			
	<tr bgcolor="#CCCCCC"><td colspan="2" align="left"><b><?php echo $_SESSION['ArqValida_LinhaHeaderHA'];?></b></td></tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Identificação do Registro</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IdentificacaoRegistroHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Identificação do Arquivo Retorno</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IdentificacaoArqRetornoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Literal Retorno</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_LiteralRetornoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Código do Serviço</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CodigoServicoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Literal Serviço</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_LiteralServicoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Código da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CodigoEmpresaHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nome da Empresa por Extenso</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NomeEmpresaHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nº do Bradesco na Câmara de Compensação</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumBancoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Nome do Banco por Extenso</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NomeBancoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Data da Gravação do Arquivo</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DataGravacaoArqHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Densidade de Gravação</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DensidadeGravacaoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nº Aviso Bancário</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumAvisoBancarioHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Branco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS1HA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Data do Crédito</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_DataCreditoHA']; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Branco</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_BRANCOS2HA']; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nº Seqüencial de Registro</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumSeqRegistroHA']; ?></div></td>
	</tr>
	<tr><td></td></tr>