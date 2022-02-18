<?php

$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");

if ($intCodDado != ""){
	try {
	//BUSCA COLABORADORES
	$strSQL  = " SELECT t2.cod_pf, t2.nome, t2.matricula, t2.cpf, t2.ctps, t2.sys_dtt_ins, t3.dt_admissao ";
	$strSQL .= "      , t3.dt_demissao, t3.tipo, t3.funcao, t3.departamento, t4.nome AS cargo ";
	$strSQL .= "      , (CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' AS mais_de_uma_hora ";
	$strSQL .= " FROM cad_pj t1 ";
	$strSQL .= " INNER JOIN relac_pj_pf t3 ON (t1.cod_pj = t3.cod_pj) ";
	$strSQL .= " INNER JOIN cad_pf t2 ON (t2.cod_pf = t3.cod_pf) ";
	$strSQL .= " LEFT OUTER JOIN cad_cargo t4 ON (t3.cod_cargo = t4.cod_cargo) ";
	$strSQL .= " WHERE t1.cod_pj = " . $intCodDado;
	$strSQL .= " AND t3.dt_demissao IS NULL";
	$strSQL .= " ORDER BY t2.nome ";
	
	$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	$strModo = "";
	if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "sindieventos") $strModo = "Modo1";
	if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "sindiprom")    $strModo = "Modo2";
	if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "ubrafe")       $strModo = "Modo3";
	
	athBeginFloatingBox("100%","","<b>".getTText("colaboradores",C_UCWORDS)."</b>&nbsp;&nbsp;&nbsp;<a href='../modulo_PainelPJ/STinsColab".$strModo."_Passo1.php?var_cod_pj=" . $intCodDado . "'>inserir</a>",CL_CORBAR_GLASS_2);
	?>
	<table bgcolor="#FFFFFF" style="border:none; width:100%; margin-bottom:0px;" class="tablesort">
		<?php
		if($objResult->rowCount() > 0) {
			?>
			<thead>
			<tr>
				<th></th>
				<th></th>
				<th class="sortable-numeric"><?php echo getTText("cod",C_UCNONE); ?></th>
				<th class="sortable-numeric"><?php echo getTText("matr",C_UCNONE); ?></th>
				<th class="sortable"><?php echo getTText("cpf",C_UCNONE); ?></th>
				<th class="sortable"><?php echo getTText("nome",C_UCNONE); ?></th>
				<th class="sortable"><?php echo getTText("tipo",C_UCNONE); ?></th>
				<th class="sortable"><?php echo getTText("departamento",C_UCNONE); ?></th>
				<th class="sortable"><?php echo getTText("cargo",C_UCNONE); ?></th>
				<th class="sortable"><?php echo getTText("funcao",C_UCNONE); ?></th>
				<th class="sortable"><?php echo getTText("ctps",C_UCNONE); ?></th>
				<th class="sortable-date-dmy"><?php echo getTText("admissao",C_UCNONE); ?></th>
				<th class="sortable-date-dmy"><?php echo getTText("demissao",C_UCNONE); ?></th>
			</tr>
			</thead>
        	<tbody>
			<?php
			foreach($objResult as $objRS){
				?>
				<tr>
					<td align="center"><?php if (getValue($objRS,"mais_de_uma_hora") == false) echo("<img src='../img/icon_colab_del.gif' alt='".getTText("remover",C_UCWORDS)."'>"); ?></td>
					<td align="center"><?php if ((getValue($objRS,"mais_de_uma_hora") == true) && (getValue($objRS,"dt_demissao") == "")) echo("<img src='../img/icon_colab_homologa.gif' alt='".getTText("homologar",C_UCWORDS)."'>"); ?></td>
					<td><?php echo(getValue($objRS,"cod_pf")); ?></td>
					<td><?php echo(getValue($objRS,"matricula")); ?></td>
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
			?>
        	</tbody>
			<?php 
		}
		else {
			?>
			<tbody>
			<tr>
				<td colspan="12" align="center"><div style="padding-top:15px; padding-bottom:15px;"><?php echo(getTText("alert_consulta_vazia_titulo",C_NONE)); ?></div></td>
			</tr>
			</tbody>
			<?php
		}
		?>
	</table>
	<?php
	athEndFloatingBox();
	$objResult->closeCursor();
}
?>
