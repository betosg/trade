<?php
if ($intCodDado != ""){
	try {
	//BUSCA TITULOS
	$strSQL  = " SELECT t1.cod_conta_pagar_receber, t1.situacao, t1.vlr_conta, t1.vlr_saldo, t1.vlr_pago ";
	$strSQL .= "      , t1.dt_emissao, t1.dt_vcto, t1.num_documento, t1.historico, t2.cod_pedido ";
	$strSQL .= " FROM fin_conta_pagar_receber t1 ";
	$strSQL .= " LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido) ";
	$strSQL .= " WHERE t1.tipo = 'cad_pj' AND t1.codigo = " . $intCodDado;
	$strSQL .= " AND t1.pagar_receber = FALSE ";
	$strSQL .= " AND (t1.situacao = 'aberta' OR t1.situacao = 'lcto_parcial') ";
	$strSQL .= " ORDER BY t1.dt_emissao ";
	
	$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	?>
 <tr>
   <td align="left" valign="top" height="1%">
	<?php athBeginWhiteBox("725","none","",CL_CORBAR_GLASS_1); ?>
	<table width="100%" cellpadding="4" cellspacing="0" border="0" bgcolor="#FFFFFF">
	<tr>
		<td align="left" width="400"><strong>Títulos</strong></td>
		<td align="right">
			<select name="" size="1" style="width:90px;">
				<option value="nao_pago" selected="selected">em aberto</option>
				<option value="pago">pagos</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<!-- -->
		<table width="100%" cellpadding="2" cellspacing="0" border="0" class="tablesort">
		<!-- Possibilidades de tipo de sort...
			class="sortable-date-dmy"
			class="sortable-currency"
			class="sortable-numeric"
			class="sortable"
		-->
		<thead>
		<tr>
			<th></th>
			<th>num_documento</th>
			<th>situacao</th>
			<th class="sortable-currency">vlr orig</th>
			<th class="sortable-currency">vlr pago</th>
			<th class="sortable-currency">vlr saldo</th>
			<th class="sortable-date-dmy">dt emissao</th>
			<th class="sortable-date-dmy">dt vcto</th>
			<th class="sortable-date-dmy">pedido</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if($objResult->rowCount() > 0) {
			foreach($objResult as $objRS){
			?>
			<tr>
				<td><img src="../img/icon_trash.gif" alt="emitir boleto"></td>
				<td><?php echo(getValue($objRS,"num_documento")); ?></td>
				<td><?php echo(getValue($objRS,"situacao")); ?></td>
				<td align="right"><?php echo(number_format((double) getValue($objRS,"vlr_conta"),2,",","")); ?></td>
				<td align="right"><?php echo(number_format((double) getValue($objRS,"vlr_pago"),2,",","")); ?></td>
				<td align="right"><?php echo(number_format((double) getValue($objRS,"vlr_saldo"),2,",","")); ?></td>
				<td><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false)); ?></td>
				<td><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false)); ?></td>
				<td><?php echo(getValue($objRS,"cod_pedido")); ?></td>
			</tr>
			<?php
			}
		}
		else {
			?>
			<tr>
				<td colspan="9" align="center"><div style="padding-top:15px; padding-bottom:15px;">
				<?php echo(getTText("alert_consulta_vazia_titulo",C_NONE)); ?>
				</div></td>
			</tr>
			<?php
		}
		?>
		</tbody>
		</table>
		<!-- -->
		</td>
	</tr>
	</table>
	<?php athEndWhiteBox(); ?>
	<br />
   </td>
 </tr>
	<?php
	$objResult->closeCursor();
}
?>
