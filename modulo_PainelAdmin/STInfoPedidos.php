<?php
	// REQUEST DE LIMITS
	$intLimitPedidos = (request("var_limit_pedidos") == "") ? 25 : request("var_limit_pedidos");
	
	// LOCALIZA TODOS OS PEDIDOS ABERTOS
	// ORDENADOS POR ÚLTIMO INSERIDO P CIMA
	try {
		$strSQL = "
			SELECT
				  cad_pj.razao_social
				, cad_pj.cod_pj
				, cad_pf.nome
				, prd_pedido.it_cod_pf
				, prd_pedido.cod_pedido
				, prd_pedido.valor
				, prd_pedido.obs
				, prd_pedido.situacao
				, prd_pedido.it_arquivo
				, prd_produto.rotulo
				, prd_produto.tipo
				, prd_pedido.sys_dtt_ins
				, prd_pedido.sys_usr_ins
				, (CURRENT_TIMESTAMP - prd_pedido.sys_dtt_ins) > '1 hour' AS mais_de_uma_hora
			FROM prd_pedido 
			LEFT JOIN cad_pf      ON (prd_pedido.it_cod_pf 		= cad_pf.cod_pf)
			LEFT JOIN cad_pj      ON (prd_pedido.cod_pj    		= cad_pj.cod_pj)
			LEFT JOIN prd_produto ON (prd_pedido.it_cod_produto = prd_produto.cod_produto)
			WHERE prd_pedido.situacao ILIKE 'aberto' 
			ORDER BY sys_dtt_ins DESC LIMIT ".$intLimitPedidos." OFFSET 0";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// MONTA HTML DO SELECT A SER COLOCADO NO CABEÇALHO
	$strHTML  = '<div style="float:right;vertical-align:top;margin:0px 5px 0px 2px;"><form name="formpedidos" action="'.$_SERVER['PHP_SELF'].'"><span style="float:left;margin-top:5px;">'.getTText("qtde_registros",C_NONE).'</span><select name="var_limit_pedidos" style="width:50px;" onchange="document.formpedidos.submit();">';
	for($auxCounter = 25; $auxCounter <= 150; $auxCounter++){
		$strHTML .= (($auxCounter % 25) == 0) ? '<option value="'.$auxCounter.'" '.(($auxCounter == $intLimitPedidos) ? 'selected="selected"' : "").'>'.$auxCounter.'</option>' : '';
	}
	$strHTML .= '</select></form></div>';
	
	athBeginFloatingBox("100%","",$strHTML."<a href=\"../modulo_PrdPedido/\"><b>".getTText("pedidos_abertos",C_UCWORDS)."</b></a><div style=\"width:100%;height:5px;background-color:#".CL_CORBAR_GLASS_2.";\"></div>",CL_CORBAR_GLASS_2);
?>
<table bgcolor="#FFFFFF" style="width:100%; margin-bottom:0px;" class="tablesort">
	<?php if($objResult->rowCount() > 0) {?>
	<thead>
		<tr>
			<th width="01%"></th> <!-- DEL -->
			<th width="01%"></th> <!-- VIE -->
			<th width="01%"></th> <!-- FAT -->
			<th width="06%" class="sortable"><?php echo getTText("cod_pedido",C_UCWORDS);?></th>
			<th width="27%" class="sortable"><?php echo getTText("razao_social",C_UCWORDS);?></th>
			<th width="27%" class="sortable"><?php echo getTText("p_fisica",C_UCWORDS);?></th>
			<th width="08%" class="sortable"><?php echo getTText("tipo",C_UCWORDS);?></th>
			<th width="11%" class="sortable"><?php echo getTText("produto",C_UCWORDS);?></th>
			<th width="06%" class="sortable-currency"><?php echo getTText("valor",C_UCWORDS);?></th>
			<th width="10%" class="sortable-date-dmy"><?php echo getTText("sys_dtt_ins",C_UCWORDS);?></th>
			<th width="01%"></th>
			<th width="01%"></th>
		</tr>
	</thead>
    <tbody>
	<?php 
	foreach($objResult as $objRS){
		// Testa grupo de usuario para exibicao do
		// icone de gerar titulo para pedidos de
		// HOMO gerados pelo próprio sindicato
		if(getValue($objRS,"sys_usr_ins")!=""){
			try{
				$strSQL 	  = "SELECT grp_user FROM sys_usuario WHERE id_usuario = '".getValue($objRS,"sys_usr_ins")."'";
				$objResultUsr = $objConn->query($strSQL);
				$objRSUsr 	  = $objResultUsr->fetch();
			}catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();	
			}
		}
		
		// Verifica se o pedido de tipo card foi inserido por
		// um usuario do grupo admin ou su, para liberar o fa
		// turamento antes de uma hora - para isto testa a re
		// lacao correspondente do pedido foi cadastrada por su
		// ou admin
		if((getValue($objRS,"situacao")=="aberto") && ((getValue($objRS,"tipo")=="card")||((getValue($objRS,"tipo")=="homo"))) && (getValue($objRS,"it_cod_pf")!="") && (getValue($objRS,"cod_pj")!="")){
			try{
				$strSQL = "	SELECT sys_usuario.grp_user, relac_pj_pf.sys_usr_ins 
							FROM relac_pj_pf
							INNER JOIN sys_usuario ON (relac_pj_pf.sys_usr_ins = sys_usuario.id_usuario)
						    WHERE relac_pj_pf.cod_pj = ".getValue($objRS,"cod_pj")."
							AND relac_pj_pf.cod_pf = ".getValue($objRS,"it_cod_pf")."
							AND relac_pj_pf.dt_demissao IS NULL";
				$objResultPF = $objConn->query($strSQL);
				$objRSPF     = $objResultPF->fetch();
			}catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();
			}
		}
		?>
		<tr bgcolor="<?php echo(getLineColor($strColor));?>">
			<td align="center" width="<?php echo(CL_LINK_WIDTH);?>">
				<?php echo("<a href='../modulo_PrdPedido/STDelPedido.php?var_chavereg=".getValue($objRS,"cod_pedido")."'><img src='../img/icon_trash.gif' alt='".getTText("remover",C_UCWORDS)."' title='".getTText("remover",C_UCWORDS)."' border='0'></a>");?>
			</td>
			<td align="center" width="<?php echo(CL_LINK_WIDTH);?>"><a href="javascript:AbreJanelaPAGE('../_fontes/insupddelmastereditor.php?var_chavereg=<?php echo(getValue($objRS,"cod_pedido"));?>&var_populate=yes&var_oper=UPD&var_basename=modulo_PrdPedido','750','480');"><img src='../img/icon_write.gif' alt='<?php echo getTText("editar",C_UCWORDS); ?>' title='<?php echo getTText("editar",C_UCWORDS); ?>' border="0"></a></td>
		<!--	<a href="../modulo_PrdPedido/index.php?var_redirect=insupddelmastereditor.php<PARAM_QM>var_chavereg=<?php //echo(getValue($objRS,"cod_pedido"));?><PARAM_EC>var_oper=UPD"><img src='../img/icon_zoom.gif' alt='<?php //echo getTText("visualizar",C_UCWORDS); ?>' title='<?php //echo getTText("visualizar",C_UCWORDS); ?>' border="0"></a></td> //-->
			<td align="center" width="<?php echo(CL_LINK_WIDTH);?>">
			<?php 
			// MONTA O LINK PARA GERAR TÍTULO
			// CASO PEDIDO NÃO SEJA NEM DE HOMOLOGAÇÃO NEM CARD
			if((getValue($objRS,"tipo") != "card") && (getValue($objRS,"tipo") != "homo")){
				echo("<a href=\"STgeraTitulo.php?var_chavereg=" . getValue($objRS,"cod_pedido") . "&var_populate=yes\"><img src='../img/icon_gerar_titulo.gif' alt='".getTText("gerar_titulo",C_UCWORDS)."' title='".getTText("gerar_titulo",C_UCWORDS)."' border='0'></a>"); 
			} 
			elseif((getValue($objRS,"mais_de_uma_hora") == true) || 
					((getValue($objRS,"tipo") == "homo") && ((getValue($objRSUsr,"grp_user") == "ADMIN")||(getValue($objRSUsr,"grp_user") == "SU"))) ||
					((getValue($objRSUsr,"grp_user") == 'ADMIN')||(getValue($objRSUsr,"grp_user") == 'SU') && (getValue($objRS,"tipo") == "card"))){
				echo("<a href=\"STgeraTitulo.php?var_chavereg=".getValue($objRS,"cod_pedido")."&var_populate=yes\">
					  <img src='../img/icon_gerar_titulo.gif' alt='".getTText("gerar_titulo",C_UCWORDS)."' title='".getTText("gerar_titulo",C_UCWORDS)."' border='0'>
					  </a>");
			}
			?>
			</td>
			<td align="left"><?php echo(getValue($objRS,"cod_pedido")); ?></td>
			<td align="left"><?php echo (substr((getValue($objRS,"cod_pj")) ." - ". (getValue($objRS,"razao_social")),0,25)); ?></td>
			<td align="left"><?php echo(substr(getValue($objRS,"nome"),0,24)); ?></td>
			<td align="center"><?php echo(getValue($objRS,"tipo")); ?></td>
			<td align="center"><?php echo(substr(getValue($objRS,"rotulo"),0,15)); ?></td>
			<td align="right"><?php echo (getValue($objRS,"valor") == 0) ? "" : (number_format((double) getValue($objRS,"valor"),2,",","")); ?></td>
			<td align="center"><?php echo(dDate(CFG_LANG, getValue($objRS,"sys_dtt_ins"), false)); ?></td>
			<td align="center"><?php if (getValue($objRS,"obs") != "") echo("<img src='../img/icon_obs.gif' title='".getValue($objRS,"obs")."' alt='".getValue($objRS,"obs")."'>"); ?></td>
			<td align="center"><?php if (getValue($objRS,"it_arquivo") != "") echo("<a href='../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/".getValue($objRS,"it_arquivo")."' target='_blank'><img src='../img/icon_anexo.gif' border='0'></a>"); ?></td>
		</tr>
				<?php 
			}
			?>
        	</tbody>
			<?php 
		}
		else {
			?>
			<tbody><tr><td colspan="12" style="border:1px dashed #CCC;color:#999;font-style:italic;text-align:center;"><?php echo getTText("nenhum_pedido_aberto",C_UCWORDS); ?></td></tr></tbody>
			<?php
		}
		?>
	</table>
	<?php
	athEndFloatingBox();
	$objResult->closeCursor();
?>
