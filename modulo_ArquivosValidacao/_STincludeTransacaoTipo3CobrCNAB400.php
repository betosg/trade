<!-- INCLUDE PARA A TRANSAÇÃO TIPO '1' //-->
<!-- Criado Por: Márcio Padilha //-->
<!-- Update Por: Leandro e Alessander //-->
<!-- Adaptado para Cobrança CNAB400 por: Cleverson //-->
<thead><tr><th width="40%"></th><th width="60%"></th></tr></thead>
<?php for($auxContador = 1; $auxContador <= $_SESSION['ArqValida_key']; $auxContador++){ ?>		
	<tr><td colspan="2" ><br />
		<div align="left" alstyle="padding-left:130px;" class="destaque_gde"><strong>Transação Tipo 3</strong></div>
	</td></tr>
	<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
	<tr><td height="10" colspan="2"></td></tr>			
	<tr bgcolor="#CCCCCC"><td colspan="2" align="left"><b><?php  echo $_SESSION['ArqValida_LinhaTransT3'.$auxContador]; ?></b></td></tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Identificação do Registro</b></td>
		<td ><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_IdentificacaoRegistroT1'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FAFAFA">
		<td align="right"><b>Tipo de Inscrição Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_TipoInscrEmpresaT3'.$auxContador]; ?></div></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="right"><b>Nº Inscrição da Empresa</b></td>
		<td><div style="padding-left:10px"><?php echo $_SESSION['ArqValida_NumInscrEmpresaT3'.$auxContador]; ?></div></td>
	</tr>
	<tr><td></td></tr>
<?php
	}
	$_SESSION['ArqValida_key'] = $_SESSION['ArqValida_key']-$_SESSION['ArqValida_key'];
?>