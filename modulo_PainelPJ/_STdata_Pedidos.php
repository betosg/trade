<?php
if ($intCodDado != ""){
	try {
	//BUSCA PEDIDOS
	$strSQL  = " SELECT t1.cod_pedido, t1.valor, t1.obs, t1.sys_dtt_ins, t1.it_descricao ";
	$strSQL .= "      , t2.nome, t2.sexo, t2.cpf ";
	$strSQL .= " FROM prd_pedido t1, cad_pf t2 ";
	$strSQL .= " WHERE t1.cod_pj = " . $intCodDado;
	$strSQL .= " AND t1.situacao = 'aberto' ";
	$strSQL .= " AND t1.it_cod_pf = t2.cod_pf ";
	$strSQL .= " ORDER BY t2.nome ";
	
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
		<td align="left" width="400"><strong>Pedidos</strong></td>
		<td align="right"></td>
	</tr>
	<tr>
		<td colspan="2">
		<!-- -->
		<table width="100%" cellpadding="2" cellspacing="0" border="0" class="tablesort">
		<thead>
		<tr>
			<th class="sortable-numeric">cod</th>
			<th>pedido</th>
			<th align="right">valor</th>
			<th align="left">colaborador</th>
			<th>cpf</th>
			<th class="sortable-date-dmy">solicitação</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if($objResult->rowCount() > 0) {
			foreach($objResult as $objRS){
			?>
			<tr>
				<td><?php echo(getValue($objRS,"cod_pedido")); ?></td>
				<td><?php echo(getValue($objRS,"it_descricao")); ?></td>
				<td align="right"><?php echo(number_format((double) getValue($objRS,"valor"),2,",","")); ?></td>
				<td><?php echo(getValue($objRS,"nome")); ?></td>
				<td><?php echo(getValue($objRS,"cpf")); ?></td>
				<td><?php echo(dDate(CFG_LANG, getValue($objRS,"sys_dtt_ins"), true)); ?></td>
			</tr>
			<?php
			}
		}
		else {
			?>
			<tr>
				<td colspan="6" align="center"><div style="padding-top:15px; padding-bottom:15px;">
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
