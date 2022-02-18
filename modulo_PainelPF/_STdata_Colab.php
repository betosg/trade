<?php
if ($intCodDado != ""){
	try {
	//BUSCA COLABORADORES
	$strSQL  = " SELECT t2.cod_pf, t2.nome, t2.cpf, t2.ctps, t3.dt_admissao ";
	$strSQL .= "      , t3.tipo, t3.funcao, t4.nome AS cargo ";
	$strSQL .= " FROM cad_pj t1, cad_pf t2, relac_pj_pf t3, cad_cargo t4 ";
	$strSQL .= " WHERE t1.cod_pj = " . $intCodDado;
	$strSQL .= " AND t1.cod_pj = t3.cod_pj ";
	$strSQL .= " AND t2.cod_pf = t3.cod_pf ";
	$strSQL .= " AND t3.dt_demissao IS NULL ";
	$strSQL .= " AND t3.cod_cargo = t4.cod_cargo ";
	$strSQL .= " ORDER BY t2.nome ";
	
	$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	?>
 <tr>
   <td align="left" valign="top" height="98%">
	<?php athBeginWhiteBox("725","none","",CL_CORBAR_GLASS_1); ?>
	<table width="100%" cellpadding="4" cellspacing="0" border="0" bgcolor="#FFFFFF">
	<tr>
		<td align="left" width="400"><strong>Colaboradores</strong></td>
		<td align="right"><img src="../img/icon_trash.gif" alt="inserir"></td>
	</tr>
	<tr>
		<td colspan="2">
		<!-- -->
		<table width="100%" cellpadding="2" cellspacing="0" border="0" class="tablesort">
		<thead>
		<tr>
			<th></th>
			<th></th>
			<th class="sortable-numeric">cod</th>
			<th>cpf</th>
			<th>nome</th>
			<th>tipo</th>
			<th>departamento</th>
			<th>cargo</th>
			<th>função</th>
			<th>ctps</th>
			<th class="sortable-date-dmy">dt admissão</th>
			<th class="sortable-date-dmy">dt demissão</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if($objResult->rowCount() > 0) {
			foreach($objResult as $objRS){
			?>
			<tr>
				<td><img src="../img/icon_trash.gif" alt="???"></td>
				<td><img src="../img/icon_trash.gif" alt="???"></td>
				<td><?php echo(getValue($objRS,"cod_pf")); ?></td>
				<td><?php echo(getValue($objRS,"cpf")); ?></td>
				<td><?php echo(getValue($objRS,"nome")); ?></td>
				<td><?php echo(getValue($objRS,"tipo")); ?></td>
				<td><?php echo(getValue($objRS,"departamento")); ?></td>
				<td><?php echo(getValue($objRS,"cargo")); ?></td>
				<td><?php echo(getValue($objRS,"funcao")); ?></td>
				<td><?php echo(getValue($objRS,"ctps")); ?></td>
				<td><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_admissao"), false)); ?></td>
				<td><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_demissao"), false)); ?></td>
			</tr>
			<?php
			}
		}
		else {
			?>
			<tr>
				<td colspan="12" align="center"><div style="padding-top:15px; padding-bottom:15px;">
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
	<br>
   </td>
 </tr>
	<?php
	$objResult->closeCursor();
}
?>
