<?php
	// REQUEST DE LIMITS
	$intLimitCards = (request("var_limit_cards") == "") ? 25 : request("var_limit_cards");
	
	// Busca todas as CARTEIRINHAS
	// RECENTEMENTE SOLICITADAS E QUE 
	// ESTEJAM COM NR_IMPRESSOES == 0
	try {
		$strSQL = " 
			SELECT	 
				  sd_credencial.cod_credencial
				, sd_credencial.cod_pedido
				, sd_credencial.cod_pf
				, sd_credencial.cod_pj
				, sd_credencial.pf_matricula
				, cad_pj.cod_pj||' - '||cad_pj.razao_social AS pf_empresa
				, sd_credencial.pf_nome
				, sd_credencial.pf_cpf
				, sd_credencial.pf_funcao	
			FROM sd_credencial 
			INNER JOIN cad_pj ON (cad_pj.cod_pj = sd_credencial.cod_pj)
			WHERE sd_credencial.dtt_inativo IS NULL
			AND ((sd_credencial.qtde_impresso IS NULL) OR (sd_credencial.qtde_impresso = 0))
			ORDER BY sd_credencial.sys_dtt_ins DESC LIMIT ".$intLimitCards." OFFSET 0";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// MONTA HTML DO SELECT A SER COLOCADO NO CABEÇALHO
	$strHTML  = '<div style="float:right;vertical-align:top;margin:0px 5px 0px 2px;"><form name="formcards" action="'.$_SERVER['PHP_SELF'].'"><span style="float:left;margin-top:5px;">'.getTText("qtde_registros",C_NONE).'</span><select name="var_limit_cards" style="width:50px;" onchange="document.formcards.submit();">';
	for($auxCounter = 25; $auxCounter <= 150; $auxCounter++){
		$strHTML .= (($auxCounter % 25) == 0) ? '<option value="'.$auxCounter.'" '.(($auxCounter == $intLimitCards) ? 'selected="selected"' : "").'>'.$auxCounter.'</option>' : '';
	}
	$strHTML .= '</select></form></div>';
	
	// Inicializa o box ao redor da tabela
	athBeginFloatingBox("100%","",$strHTML."<a href=\"../modulo_SdCredencial/\"><b>".getTText("cred_ainda_nao_impressas",C_UCWORDS)."</b></a><div style=\"width:100%;height:5px;background-color:#".CL_CORBAR_GLASS_2.";\"></div>",CL_CORBAR_GLASS_2);
?>
	<table style="width:100%;margin-bottom:0px;background-color:#FFFFFF;" class="tablesort">
		<?php if($objResult->rowCount() > 0){?>
			<thead>
			<tr>
				<!--<th width="1%"></th> <!-- DEL -->
				<th width="1%"></th>     <!-- VIE -->
				<th width="1%"></th>     <!-- CARD -->
				<th width="06%" class="sortable"><?php echo(getTText("cod_credencial",C_UCWORDS));?></th>
				<th width="30%" class="sortable"><?php echo(getTText("razao_social",C_UCWORDS));?></th>
				<th width="26%" class="sortable"><?php echo(getTText("nome",C_UCWORDS));?></th>
				<th width="08%" class="sortable"><?php echo(getTText("num_matricula",C_UCWORDS));?></th>
				<th width="08%" class="sortable"><?php echo(getTText("pf_cpf",C_UCWORDS));?></th>
				<th width="28%" class="sortable-date-dmy"><?php echo(getTText("funcao",C_UCWORDS));?></th>
				<th width="04%" class="sortable"><?php echo(getTText("pedido",C_UCWORDS));?></th>
				<!--<th width="04%" class="sortable"><?php echo(getTText("titulo",C_UCWORDS));?></th>-->
			</tr>
			</thead>
        	<tbody>
			<?php 
			foreach($objResult as $objRS) {
				// Busca a situação do título vinculado a credencial
				/*
				$strSituacaoCobranca = "";
				$intCodAgrupador = getValue($objRS, "cod_agrupador");
				$strSituacaoCobranca = getValue($objRS,"situacao");
				
				// Se título tiver sido agrupado então verifica situação do título agrupador
				// Se próprio título agrupador tiver sido agrupado então verifica o novo agrupador e assim por diante
				
				// Não se pode agrupar um título que foi gerado pelo agrupamento de outros títulos
				// Ou seja, fazer um segundo nível de agrupamento. Logo não precisa fazer o laço de WHILE abaixo:
				// while (($strSituacaoCobranca == "agrupado") && ($intCodAgrupador != "")) {
				if(($strSituacaoCobranca == "agrupado") && ($intCodAgrupador != "")){
					try {
						$strSQL = "
							SELECT situacao, cod_agrupador FROM fin_conta_pagar_receber 
							WHERE cod_conta_pagar_receber = " . $intCodAgrupador;
						$objResultAux = $objConn->query($strSQL);
						$objRSAux = $objResultAux->fetch();
					}
					catch(PDOException $e) {
						mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
						die();
					}
					$strSituacaoCobranca = getValue($objRSAux,"situacao");
					$intCodAgrupador = getValue($objRSAux, "cod_agrupador");
					$objResultAux->closeCursor();
				}
				*/
				?>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="center" style="vertical-align:middle;">
					 <a href='../_fontes/insupddelmastereditor.php?var_basename=modulo_PrdPedido&var_chavereg=<?php echo(getValue($objRS,"cod_pedido")) ?>&var_oper=VIE'>
					 	<img src='../img/icon_zoom.gif' title='<?php echo(getTText("visualizar",C_UCWORDS)); ?>'>
					 </a>
					</td>
					<td align="center" style="vertical-align:middle;">
					 <span onClick="AbreJanelaPAGE('STGeraCard.php?var_chavereg=<?php echo(getValue($objRS,"cod_credencial")); ?>&var_populate=yes',670,500);" style='cursor:pointer;'>
					 	<img src='../img/icon_impr_carteirinha.gif' title='<?php echo(getTText("gerar_cred",C_UCWORDS)); ?>'> 
					 </span>
					</td>
					<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_credencial")); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(substr(getValue($objRS,"pf_empresa"),0,23)); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(substr(getValue($objRS,"pf_nome"),0,24)); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"pf_matricula")); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"pf_cpf")); ?></td>
					<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"pf_funcao")); ?></td>
					<td align="left"><?php echo(getValue($objRS,"cod_pedido")); ?></td>
				</tr>
				<?php }?>
        	</tbody>
			<?php } else{?>
			<tbody style="border:none;">
				<tbody><tr><td colspan="11" style="border:1px dashed #CCC;color:#999;font-style:italic;text-align:center;"><?php echo getTText("nenhuma_cred_nova",C_UCWORDS); ?></td></tr></tbody>
			</tr>
			</tbody>
		<?php }?>
	</table>
<?php
	athEndFloatingBox();
	$objResult->closeCursor();
?>
