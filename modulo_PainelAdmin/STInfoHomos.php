<?php

	// LISTA TODAS AS ENTRADAS GERADAS NA TABELA DE 'HOMOLOGAÇÕES', ONDE A DATA SEJA DIFERENTE DE NULL
	try {
		$strSQL = "
			SELECT
				  sd_homologacao.cod_homologacao
				, sd_homologacao.cod_pedido
				, sd_homologacao.cod_pf
				, sd_homologacao.cod_pj
				, sd_homologacao.pf_matricula
				, sd_homologacao.pf_empresa
				, sd_homologacao.pf_nome
				, sd_homologacao.pf_rg
				, sd_homologacao.pf_cpf
				, sd_homologacao.pf_funcao	
			FROM sd_homologacao
			WHERE sd_homologacao.dtt_inativo IS NULL
			AND sd_homologacao.dtt_homologacao IS NULL ";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Inicializa a box ao redor da table
	athBeginFloatingBox("100%","","<b>".getTText("homo_ainda_nao_confirmada",C_UCWORDS)."</b>",CL_CORBAR_GLASS_2);
?>
	<table style="width:100%;margin-bottom:0px;background-color:#FFFFFF;" class="tablesort">
		<?php if($objResult->rowCount() > 0){?>
			<thead>
			<tr>
				<th width="1%"></th> <!-- deleção -->
				<th width="1%"></th> <!-- visualizar -->
				<th width="1%"></th> <!-- confirma homo -->
				<th width="05%" class="sortable"><?php echo(getTText("cod_homologacao",C_UCWORDS));?></th>
				<th width="25%" class="sortable"><?php echo(getTText("razao_social",C_UCWORDS));?></th>
				<th width="20%" class="sortable"><?php echo(getTText("nome",C_UCWORDS));?></th>
				<th width="05%" class="sortable"><?php echo(getTText("num_matricula",C_UCWORDS));?></th>
				<th width="05%" class="sortable-currency"><?php echo(getTText("pf_rg",C_UCWORDS));?></th>
				<th width="05%" class="sortable"><?php echo(getTText("pf_cpf",C_UCWORDS));?></th>
				<th width="25%" class="sortable-date-dmy"><?php echo(getTText("funcao",C_UCWORDS));?></th>
				<th width="07%" class="sortable"><?php echo(getTText("pedido",C_UCWORDS));?></th>
			</tr>
			</thead>
        	<tbody>
			<?php foreach($objResult as $objRS) {?>
				<tr>
					<td align="center" style="vertical-align:middle;"><?php echo("<a href='../modulo_SdHomologacao/index.php?var_redirect=insupddelmastereditor.php<PARAM_QM>var_chavereg=".getValue($objRS,"cod_homologacao")."<PARAM_EC>var_oper=DEL'><img src='../img/icon_trash.gif' alt='".getTText("remover",C_UCWORDS)."' title='".getTText("remover",C_UCWORDS)."'></a>"); ?></td>
					<td align="center" style="vertical-align:middle;"><?php echo("<a href='../modulo_PrdPedido/index.php?var_redirect=insupddelmastereditor.php<PARAM_QM>var_chavereg=".getValue($objRS,"cod_pedido")."<PARAM_EC>var_oper=VIE'><img src='../img/icon_zoom.gif' alt='".getTText("visualizar",C_UCWORDS)."' title='".getTText("visualizar",C_UCWORDS)."'></a>"); ?></td>
					<td align="center" style="vertical-align:middle;"><?php echo("<a href='STConfirmaHomo.php?var_chavereg=".getValue($objRS,"cod_homologacao")."'><img src='../img/icon_confirm_homo.gif' alt='".getTText("confirmar_homo",C_UCWORDS)."' title='".getTText("confirmar_homo",C_UCWORDS)."'></a>"); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_homologacao")); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(substr(getValue($objRS,"pf_empresa"),0,23)); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(substr(getValue($objRS,"pf_nome"),0,24)); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"pf_matricula")); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"pf_rg")); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"pf_cpf")); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"pf_funcao")); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_pedido")); ?></td>
				</tr>
				<?php }?>
        	</tbody>
			<?php } else{?>
			<tbody style="border:none;">
				<tbody><tr><td colspan="11" style="border:1px dashed #CCC;color:#999;font-style:italic;text-align:center;"><?php echo getTText("nenhuma_homo",C_UCWORDS); ?></td></tr></tbody>
			</tbody>
		<?php }?>
	</table>
<?php
	athEndFloatingBox();
	$objResult->closeCursor();
?>
