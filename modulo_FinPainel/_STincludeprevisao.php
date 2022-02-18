<?php 

$strCorHeader = CL_CORBAR_GLASS_2; //"#D1D1D1"; //"#999999";
$strCorLinha1 = "#FFFFFF";
$strCorLinha2 = "#F2F2F2";
$strBgColor = $strCorLinha1;

// CARDS JÁ VENCIDOS
try{
	$strSQL = "	SELECT SUM(t1.vlr_conta) AS vlr_total, COUNT(t1.cod_conta_pagar_receber) AS qtde_total, t2.it_tipo
            FROM fin_conta_pagar_receber t1 INNER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido AND (t2.it_tipo = 'card' OR t2.it_tipo = 'homo'))
			WHERE t1.dt_vcto < CURRENT_DATE
            AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial')
            AND t1.pagar_receber = FALSE
            GROUP BY t2.it_tipo
            ORDER BY t2.it_tipo DESC ";
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
// CARDS VENCEM HOJE
try{
	$strSQL = "	SELECT SUM(t1.vlr_conta) AS vlr_total ,COUNT(t1.cod_conta_pagar_receber) AS qtde_total ,t2.it_tipo
            FROM fin_conta_pagar_receber t1 INNER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido AND (t2.it_tipo = 'card' OR t2.it_tipo = 'homo'))
            WHERE t1.dt_vcto = CURRENT_DATE
            AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial')
            AND t1.pagar_receber = FALSE
            GROUP BY t2.it_tipo
            ORDER BY t2.it_tipo DESC ";
	$objResult2 = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
// CARDS AINDA NAO VENCIDAS
try{
	$strSQL = "	SELECT SUM(t1.vlr_conta) AS vlr_total, COUNT(t1.cod_conta_pagar_receber) AS qtde_total ,t2.it_tipo
            FROM fin_conta_pagar_receber t1 INNER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido AND (t2.it_tipo = 'card' OR t2.it_tipo = 'homo'))
            WHERE t1.dt_vcto > CURRENT_DATE
            AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial')
            AND t1.pagar_receber = FALSE
            GROUP BY t2.it_tipo
            ORDER BY t2.it_tipo DESC ";
	$objResult3 = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}


// TITULOS BASEADOS POR HISTORICO
// JA VENCIDOS
try{
	$strSQL = " SELECT SUM(t1.vlr_conta) AS vlr_total, COUNT(t1.cod_conta_pagar_receber) AS qtde_total,t1.historico
			FROM fin_conta_pagar_receber t1	LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido)
			WHERE t1.dt_vcto < CURRENT_DATE
			AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial')
			AND t1.pagar_receber = FALSE
			AND (t2.it_tipo IS NULL OR t2.it_tipo = '')
			GROUP BY t1.historico
			ORDER BY t1.historico DESC";
	$objResult4 = $objConn->query($strSQL);
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
// TITULOS BASEADOR POR HISTORICO VENCENDO HOJE
try{
	$strSQL = " SELECT SUM(t1.vlr_conta) AS vlr_total,COUNT(t1.cod_conta_pagar_receber) AS qtde_total ,t1.historico
			FROM fin_conta_pagar_receber t1
			LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido)
			WHERE t1.dt_vcto = CURRENT_DATE
			AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial')
			AND t1.pagar_receber = FALSE
			AND (t2.it_tipo IS NULL OR t2.it_tipo = '')
			GROUP BY t1.historico
			ORDER BY t1.historico DESC";
	$objResult5 = $objConn->query($strSQL);
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
// TITULOS BASEADOS POR HISTORICO
// QUE AINDA NAO VENCERAM
try{
	$strSQL = " SELECT SUM(t1.vlr_conta) AS vlr_total,COUNT(t1.cod_conta_pagar_receber) AS qtde_total,t1.historico 			
			FROM fin_conta_pagar_receber t1
			LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido)
			WHERE t1.dt_vcto > CURRENT_DATE
			AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial')
			AND t1.pagar_receber = FALSE
			AND (t2.it_tipo IS NULL OR t2.it_tipo = '')
			GROUP BY t1.historico
			ORDER BY t1.historico DESC";
	$objResult6 = $objConn->query($strSQL);
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}		

athBeginFloatingBox("100%","","<b>Previsão de entrada</b>",CL_CORBAR_GLASS_2); 
?>
	<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
	<tr bgcolor="<?php echo($strCorHeader)?>">
		<td colspan="4" align="left" class="texto_contraste_mdo"><div style="padding-top:1px; padding-bottom:1px; padding-left:10px; padding-right:4px;"><strong></strong></div></td>
	</tr>
	<tr bgcolor="<?php echo($strCorLinha2); ?>">
		<td align="left" class="texto_corpo_mdo"><div style="padding-left:4px; padding-right:4px;"><strong>Tipo</strong></div></td>
		<td align="left" class="texto_corpo_mdo"><div style="padding-left:4px; padding-right:4px;"><strong>QTDE</strong></div></td>
		<td align="left" class="texto_corpo_mdo"><div style="padding-left:4px; padding-right:4px;"><strong>Valor</strong></div></td>
		<td align="right" class="texto_corpo_mdo"><div style="padding-left:4px; padding-right:4px;"><strong></strong></div></td>
	</tr>
	
	
	<tr><td>&nbsp;</td></tr>
	
	
	<!-- LISTAGEM DE TÍTULOS JÁ VENCIDOS -->
	<tr>
		<td>
		<div style="padding-left:4px; padding-right:4px;">
			<strong><?php echo(getTText("titulo_vencido",C_NONE));?></strong>
		</div>
		</td>
	</tr>
	<?php
	// TITULOS CARDS / HOMOS JÁ VENCIDOS
	if ($objResult->rowCount() > 0) {
	$strBgColor = $strCorLinha2;
	foreach($objResult as $objRS){
	// Alteração nos ícones - PAGAR / RECEBER
	if(getValue($objRS,"pagar_receber")=="1"){$strICONE="<img src='../img/icon_fincontapagar.gif'alt='Conta a Pagar'>";}
	else{$strICONE = "<img src='../img/icon_fincontareceber.gif' alt='Conta a Receber'>";}
	?>		
	<tr bgcolor="<?php echo($strBgColor);?>">
		<td width="99%" align="left" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:10px; padding-right:4px;"><?php echo(getValue($objRS,"it_tipo")); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:4px; padding-right:4px;"><?php echo(getValue($objRS,'qtde_total')); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo">
			<div style="padding-left:4px; padding-right:4px;"><?php echo(number_format((double) getValue($objRS,'vlr_total'),2,",","")); ?></div>
		</td>
		<td width="1%" align="center">
			<div style="padding-left:4px; padding-right:4px;"><?php echo($strICONE); ?></div>
		</td>
	</tr>
	<?php
		// Alteração na cor de FUNDO DE LINHA
		if($strBgColor == $strCorLinha2){$strBgColor = $strCorLinha1;}
		else{$strBgColor = $strCorLinha2;}
	} // FIM foreach
	$objResult->closeCursor();
	} 
	// END TITULOS CARDS / HOMOS
	// TITULOS OUTROS JA VENCIDOS 
	if ($objResult4->rowCount() > 0){
	//$strBgColor = $strCorLinha2;
	foreach($objResult4 as $objRS){
	// Alteração nos ícones - PAGAR / RECEBER
	if(getValue($objRS,"pagar_receber")=="1"){$strICONE="<img src='../img/icon_fincontapagar.gif'alt='Conta a Pagar'>";}
	else{$strICONE = "<img src='../img/icon_fincontareceber.gif' alt='Conta a Receber'>";}
	?>		
	<tr bgcolor="<?php echo($strBgColor);?>">
		<td width="99%" align="left" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:10px; padding-right:4px;"><?php echo(getValue($objRS,"historico")); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:4px; padding-right:4px;"><?php echo(getValue($objRS,'qtde_total')); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo">
			<div style="padding-left:4px; padding-right:4px;"><?php echo(number_format((double) getValue($objRS,'vlr_total'),2,",","")); ?></div>
		</td>
		<td width="1%" align="center">
			<div style="padding-left:4px; padding-right:4px;"><?php echo($strICONE); ?></div>
		</td>
	</tr>
	<?php
		// Alteração na cor de FUNDO DE LINHA
		if($strBgColor == $strCorLinha2){$strBgColor = $strCorLinha1;}
		else{$strBgColor = $strCorLinha2;}
	} // FIM foreach
	$objResult4->closeCursor();
	}// FIM titulos OUTROS
	if(($objResult4->rowCount() <= 0) && ($objResult->rowCount() <= 0)){ ?>
	<tr>
		<td width="99%" align="left" class="texto_corpo_mdo" colspan="6" height="20">
		<div style="padding-left:10px; padding-right:4px;">
			<?php echo(getTText("nenhum_tit_encontrado",C_NONE));?>
		</div>
		</td>
	</tr>
	<?php }?>
	<!-- FIM LISTAGEM DE TITULOS JÁ VENCIDOS -->
	
	
	
	<tr><td>&nbsp;</td></tr>
	
	
	
	<!-- LISTAGEM DE TÍTULOS VENCENDO HOJE -->
	<tr>
		<td>
		<div style="padding-left:4px; padding-right:4px;">
			<strong><?php echo(getTText("titulo_vence_hoje",C_NONE));?></strong>
		</div>
		</td>
	</tr>
	<?php
	// TITULOS CARDS / HOMOS VENCENDO HOJE
	if ($objResult2->rowCount() > 0) {
	$strBgColor = $strCorLinha2;
	foreach($objResult2 as $objRS){
	// Alteração nos ícones - PAGAR / RECEBER
	if(getValue($objRS,"pagar_receber")=="1"){$strICONE="<img src='../img/icon_fincontapagar.gif'alt='Conta a Pagar'>";}
	else{$strICONE = "<img src='../img/icon_fincontareceber.gif' alt='Conta a Receber'>";}
	?>		
	<tr bgcolor="<?php echo($strBgColor);?>">
		<td width="99%" align="left" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:10px; padding-right:4px;"><?php echo(getValue($objRS,"it_tipo")); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:4px; padding-right:4px;"><?php echo(getValue($objRS,'qtde_total')); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo">
			<div style="padding-left:4px; padding-right:4px;"><?php echo(number_format((double) getValue($objRS,'vlr_total'),2,",","")); ?></div>
		</td>
		<td width="1%" align="center">
			<div style="padding-left:4px; padding-right:4px;"><?php echo($strICONE); ?></div>
		</td>
	</tr>
	<?php
		// Alteração na cor de FUNDO DE LINHA
		if($strBgColor == $strCorLinha2){$strBgColor = $strCorLinha1;}
		else{$strBgColor = $strCorLinha2;}
	} // FIM foreach
	$objResult2->closeCursor();
	} 
	// END TITULOS CARDS / HOMOS
	// TITULOS OUTROS VENCENDO HOJE
	if ($objResult5->rowCount() > 0){
	//$strBgColor = $strCorLinha2;
	foreach($objResult5 as $objRS){
	// Alteração nos ícones - PAGAR / RECEBER
	if(getValue($objRS,"pagar_receber")=="1"){$strICONE="<img src='../img/icon_fincontapagar.gif'alt='Conta a Pagar'>";}
	else{$strICONE = "<img src='../img/icon_fincontareceber.gif' alt='Conta a Receber'>";}
	?>		
	<tr bgcolor="<?php echo($strBgColor);?>">
		<td width="99%" align="left" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:10px; padding-right:4px;"><?php echo(getValue($objRS,"historico")); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:4px; padding-right:4px;"><?php echo(getValue($objRS,'qtde_total')); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo">
			<div style="padding-left:4px; padding-right:4px;"><?php echo(number_format((double) getValue($objRS,'vlr_total'),2,",","")); ?></div>
		</td>
		<td width="1%" align="center">
			<div style="padding-left:4px; padding-right:4px;"><?php echo($strICONE); ?></div>
		</td>
	</tr>
	<?php
		// Alteração na cor de FUNDO DE LINHA
		if($strBgColor == $strCorLinha2){$strBgColor = $strCorLinha1;}
		else{$strBgColor = $strCorLinha2;}
	} // FIM foreach
	$objResult5->closeCursor();
	}// FIM titulos OUTROS
	if(($objResult5->rowCount() <= 0) && ($objResult2->rowCount() <= 0)){ ?>
	<tr>
		<td width="99%" align="left" class="texto_corpo_mdo" colspan="6" height="20">
		<div style="padding-left:10px; padding-right:4px;">
			<?php echo(getTText("nenhum_tit_encontrado",C_NONE));?>
		</div>
		</td>
	</tr>
	<?php }?>
	<!-- FIM LISTAGEM DE TITULOS VENCENDO HOJE -->
	
	
	
	<tr><td>&nbsp;</td></tr>
	
	
	
	<!-- LISTAGEM DE TÍTULOS AINDA NAO VENCIDOS -->
	<tr>
		<td>
		<div style="padding-left:4px; padding-right:4px;">
			<strong><?php echo(getTText("titulo_ainda_nao_vencidos",C_NONE));?></strong>
		</div>
		</td>
	</tr>
	<?php
	// TITULOS CARDS / HOMOS AINDA NAO VENCIDOS
	if ($objResult3->rowCount() > 0) {
	$strBgColor = $strCorLinha2;
	foreach($objResult3 as $objRS){
	// Alteração nos ícones - PAGAR / RECEBER
	if(getValue($objRS,"pagar_receber")=="1"){$strICONE="<img src='../img/icon_fincontapagar.gif'alt='Conta a Pagar'>";}
	else{$strICONE = "<img src='../img/icon_fincontareceber.gif' alt='Conta a Receber'>";}
	?>		
	<tr bgcolor="<?php echo($strBgColor);?>">
		<td width="99%" align="left" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:10px; padding-right:4px;"><?php echo(getValue($objRS,"it_tipo")); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:4px; padding-right:4px;"><?php echo(getValue($objRS,'qtde_total')); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo">
			<div style="padding-left:4px; padding-right:4px;"><?php echo(number_format((double) getValue($objRS,'vlr_total'),2,",","")); ?></div>
		</td>
		<td width="1%" align="center">
			<div style="padding-left:4px; padding-right:4px;"><?php echo($strICONE); ?></div>
		</td>
	</tr>
	<?php
		// Alteração na cor de FUNDO DE LINHA
		if($strBgColor == $strCorLinha2){$strBgColor = $strCorLinha1;}
		else{$strBgColor = $strCorLinha2;}
	} // FIM foreach
	$objResult3->closeCursor();
	} 
	// END TITULOS CARDS / HOMOS
	// TITULOS OUTROS AINDA NAO VENCIDOS
	if ($objResult6->rowCount() > 0){
	//$strBgColor = $strCorLinha2;
	foreach($objResult6 as $objRS){
	// Alteração nos ícones - PAGAR / RECEBER
	if(getValue($objRS,"pagar_receber")=="1"){$strICONE="<img src='../img/icon_fincontapagar.gif'alt='Conta a Pagar'>";}
	else{$strICONE = "<img src='../img/icon_fincontareceber.gif' alt='Conta a Receber'>";}
	?>		
	<tr bgcolor="<?php echo($strBgColor);?>">
		<td width="99%" align="left" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:10px; padding-right:4px;"><?php echo(getValue($objRS,"historico")); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:4px; padding-right:4px;"><?php echo(getValue($objRS,'qtde_total')); ?></div>
		</td>
		<td width="10%" align="right" class="texto_corpo_mdo">
			<div style="padding-left:4px; padding-right:4px;"><?php echo(number_format((double) getValue($objRS,'vlr_total'),2,",","")); ?></div>
		</td>
		<td width="1%" align="center">
			<div style="padding-left:4px; padding-right:4px;"><?php echo($strICONE); ?></div>
		</td>
	</tr>
	<?php
		// Alteração na cor de FUNDO DE LINHA
		if($strBgColor == $strCorLinha2){$strBgColor = $strCorLinha1;}
		else{$strBgColor = $strCorLinha2;}
	} // FIM foreach
	$objResult6->closeCursor();
	}// FIM titulos OUTROS
	if(($objResult6->rowCount() <= 0) && ($objResult3->rowCount() <= 0)){ ?>
	<tr>
		<td width="99%" align="left" class="texto_corpo_mdo" colspan="6" height="20">
		<div style="padding-left:10px; padding-right:4px;">
			<?php echo(getTText("nenhum_tit_encontrado",C_NONE));?>
		</div>
		</td>
	</tr>
	<tr><td colspan="6" height="20">&nbsp;</td></tr>
	<?php }?>
	<!-- FIM LISTAGEM DE TITULOS AINDA NAO VENCIDOS -->
	
	<tr><td colspan="6" height="20">&nbsp;</td></tr>
	
	</table>

<?php
athEndFloatingBox();
$objConn = NULL;
?>