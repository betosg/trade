<!-- INCLUDE PARA O SEGMENTO 'U' -->
<!-- Criado Por: M�rcio Padilha -->
<!-- Update Por: Leandro e Alessander -->
<thead><tr><th width="40%"></th><th width="60%"></th></tr></thead>
<?php for($auxContador = 1; $auxContador <= $_SESSION['ArqValida_chave']; $auxContador++){ ?>		
	<tr><td colspan="2"><br /><div align="left" style="padding-left:130px;" class="destaque_gde"><strong>Segmento U</strong></div></td></tr>
	<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
	<tr><td height="10" colspan="2"></td></tr>
	<tr bgcolor="#CCCCCC"><td colspan="2" align="left"><b><?php echo $_SESSION['ArqValida_linhaSegmU'.$auxContador]; ?></b></td></tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>C�digo do Banco na Compensa��o</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codBanComSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td nowrap="nowrap" align="right"><b>Lote de Servi�o</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_lotSerSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Tipo de Registro</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_tipRegSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>N� Sequencial Registro de Lote</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_numSeqRegDetSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>C�digo Segmento do Registro Detalhe</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ALFAcodSegRegDetSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo FEBRABAN/CNAB</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_ALFAusoExcFebSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>C�digo de Movimento Retorno</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_codMovRetSU'.$auxContador]; ?></div></td>
	</tr>
    <tr bgcolor="#FFFFFF">
		<td align="right"><b>Capital Social da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CapitalSocialEmpSU'.$auxContador]; ?></div></td>
	</tr>
    <tr bgcolor="#FFFFFF">
		<td align="right"><b>Capital social estabelecimento</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_CapitalSocialEstSU'.$auxContador]; ?></div></td>
	</tr>
    <tr bgcolor="#FFFFFF">
		<td align="right"><b>N�mero de empregados contribuintes</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumEmpregadosContribSU'.$auxContador]; ?></div></td>
	</tr>
   	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Total da remunera��o contribuintes</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_RemuneracaoContribSU'.$auxContador]; ?></div></td>
	</tr>
    <tr bgcolor="#FFFFFF">
		<td align="right"><b>N�mero de empregados</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumEmpregadosSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>CNAE</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_cnaeSU'.$auxContador]; ?></div></td>
	</tr>
    
    <tr bgcolor="#FAFAFA">
		<td align="right"><b>Codigo sindical da entidade sindical</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqCodigoEntidadeSU'.$auxContador]; ?></div></td>
	</tr>
    <tr bgcolor="#FAFAFA">
		<td align="right"><b>Tipo Entidade Sindical</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqTipoEntidadeSU'.$auxContador]; ?></div></td>
	</tr>
    <tr bgcolor="#FAFAFA">
		<td align="right"><b>Tipo de Arrecada��o</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqTipoArecadacaoSU'.$auxContador]; ?></div></td>
	</tr>
    
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Juros/Multa/Encargos</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_jurMulEncSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor do DesauxContadoro Concedido</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valDesConSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Valor do Abat. Concedido/Cancel.</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valAbtConCanSU'.$auxContador];?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor do IOF Recolhido</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valiofRecSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Valor Pago Pelo Sacado</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valpagSacSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor L�quido a ser Creditado</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valliqCreSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Valor de Outras Despesas</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valOutDesSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Valor de Outros Cr�ditos</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_valOutCreSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Data da Ocorr�ncia</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtOcoSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Data da Efetiva��o do Cr�dito</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_dtEfeCreSU'.$auxContador]; ?></div></td>
	</tr>
	
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Complemento OCORR�NCIA</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_complOcorrSU'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Uso Exclusivo Febraban</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_usoExcFebSU'.$auxContador]; ?></div></td>
	</tr>
	<tr><td></td></tr>	
<?php 
	}
	$_SESSION['ArqValida_chave'] = $_SESSION['ArqValida_chave'] - $_SESSION['ArqValida_chave'];
?>