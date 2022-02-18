<?php 

$strSQL = "	SELECT nome, vlr_saldo, dtt_inativo
			  FROM fin_conta 
			ORDER BY dtt_inativo DESC, ordem, nome ";
			
try{
	$objResult = $objConn->query($strSQL);

} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}		

$strVlrSaldoGeral = 0;

athBeginFloatingBox("100%","","<b>Saldo das Contas</b>",CL_CORBAR_GLASS_2); 
?>
<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
<?php
	foreach($objResult as $objRS){
		$strVlrSaldo = getValue($objRS,'vlr_saldo');
		if($strVlrSaldo == '' || !is_numeric($strVlrSaldo)) $strVlrSaldo = 0;
		
		$strVlrSaldoGeral += $strVlrSaldo;
		
		$strColorSaldo   = ($strVlrSaldo >= 0)                  ? "#000099" : "#990000";
		$strColorInativo = (getValue($objRS,"dtt_inativo") == "") ? "#000000" : "#BBBBBB";
		?>		
		<tr>
			<td width="99%" align="left" class="texto_corpo_mdo">
				<div style="padding-left:10px; padding-right:4px; color:<?php echo($strColorInativo); ?>"><?php echo(getValue($objRS,'nome'));?></div>
			</td>
			<td width="1%" align="right" class="texto_corpo_mdo" nowrap>
				<div style="padding-left:10px; padding-right:4px;color:<?php echo($strColorSaldo)?>"><?php echo(number_format((double) $strVlrSaldo,2,",","."));?></div>
			</td>
		</tr>
		<?php
	}
	?>
		<tr>
			<td colspan="2" align="right" class="texto_corpo_mdo" bgcolor="#F2F2F2">
			  <div style="padding-left:10px; padding-right:4px;">
			  <strong>Total:&nbsp;<?php echo(number_format((double) $strVlrSaldoGeral,2,",","."));?></strong></div>
			</td>
			<td></td>
		</tr>
		<!-- <tr><td colspan="2" height="10"></td></tr> -->
	</table>
<?php
athEndFloatingBox();
$objResult->closeCursor();
?>
<br/>