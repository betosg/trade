<?php
	// REQUEST DE LIMITS
	$intLimitEmpresas = (request("var_limit_empresas") == "") ? 25 : request("var_limit_empresas");

	// BUSCA DAS PJS COM SITUACAO DE 
	// USUARIO = PRE_CADASTRO	
	try {
		$strSQL  = " SELECT 
						  cad_pj.cod_pj
						, cad_pj.razao_social
						, cad_pj.cnpj
						, cad_pj.arquivo_1
						, cad_pj.email
						, cad_pj.endprin_fone1
						, cad_pj.sys_dtt_ins
						, sys_usuario.id_usuario
					FROM cad_pj
					INNER JOIN sys_usuario 
					ON (sys_usuario.codigo = cad_pj.cod_pj AND sys_usuario.grp_user <=> 'PRE_CADASTRO')
					ORDER BY sys_dtt_ins DESC LIMIT ".$intLimitEmpresas." OFFSET 0 ";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_2;
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	// MONTA HTML DO SELECT A SER COLOCADO NO CABEÇALHO
	$strHTML  = '<div style="float:right;vertical-align:top;margin:0px 5px 0px 2px;"><form name="formempresas" action="'.$_SERVER['PHP_SELF'].'"><span style="float:left;margin-top:5px;">'.getTText("qtde_registros",C_NONE).'</span><select name="var_limit_empresas" style="width:50px;" onchange="document.formempresas.submit();">';
	for($auxCounter = 25; $auxCounter <= 150; $auxCounter++){
		$strHTML .= (($auxCounter % 25) == 0) ? '<option value="'.$auxCounter.'" '.(($auxCounter == $intLimitEmpresas) ? 'selected="selected"' : "").'>'.$auxCounter.'</option>' : '';
	}
	$strHTML .= '</select></form></div>';
	
	// Inicializa o box ao redor da tabela
	athBeginFloatingBox("100%","",$strHTML."<a href=\"../modulo_CadPJ/\"><b>".getTText("novas_empresas",C_UCWORDS)."</b></a><div style=\"width:100%;height:5px;background-color:#".CL_CORBAR_GLASS_2.";\"></div>",CL_CORBAR_GLASS_2);
?>
	<table style="width:100%;margin-bottom:0px;background-color:#FFFFFF;" class="tablesort">
		<?php if($objResult->rowCount() > 0){?>
			<thead>
			<tr>
				<th width="01%"></th> <!-- DEL -->
				<th width="01%"></th> <!-- UPD -->
				<th width="01%"></th> <!-- VIEW -->
				<th width="01%"></th> <!-- HOMO -->
				<th width="05%" class="sortable-numeric"><?php echo getTText("cod_pj",C_UCWORDS); ?></th>
				<th width="12%" class="sortable"><?php echo getTText("cnpj",C_UCWORDS); ?></th>
				<th width="30%" class="sortable"><?php echo getTText("razao_social",C_UCWORDS); ?></th>
				<th width="13%" class="sortable"><?php echo getTText("fone",C_UCWORDS); ?></th>
				<th width="10%" class="sortable"><?php echo getTText("usuario",C_UCWORDS); ?></th>
				<th width="10%" class="sortable-date-dmy"><?php echo getTText("sys_dtt_ins",C_UCWORDS); ?></th>
			</tr>
			</thead>
        	<tbody>
			<?php foreach($objResult as $objRS) { ?>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="center" style="vertical-align:middle;"><a href="STcancelacadnovo.php?var_chavereg=<?php echo(getValue($objRS,"cod_pj"));?>"><img src='../img/icon_trash.gif' title='<?php echo getTText("remover_canc_cadastro",C_NONE); ?>' border="0"></a></td>
					<td align="center" style="vertical-align:middle;"><a href="../modulo_CadPJ/index.php?var_redirect=insupddelmastereditor.php<PARAM_QM>var_chavereg=<?php echo(getValue($objRS,"cod_pj")); ?><PARAM_EC>var_oper=UPD"><img src='../img/icon_write.gif' title='<?php echo getTText("editar",C_UCWORDS); ?>'     border="0"></a></td>
					<td align="center" style="vertical-align:middle;"><a href="../modulo_CadPJ/index.php?var_redirect=insupddelmastereditor.php<PARAM_QM>var_chavereg=<?php echo(getValue($objRS,"cod_pj")); ?><PARAM_EC>var_oper=VIE"><img src='../img/icon_zoom.gif'  title='<?php echo getTText("visualizar",C_UCWORDS); ?>' border="0"></a></td>
					<td align="center" style="vertical-align:middle;"><a href="STliberacadnovo.php?var_chavereg=<?php echo(getValue($objRS,"cod_pj"));?>"><img src="../img/icon_ativar.gif" title="<?php echo getTText("liberar_novo_cadastro",C_NONE); ?>" border="0"></a></td>
					<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_pj")); ?></td>
					<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"cnpj")); ?></td>
					<td align="center" style="vertical-align:middle;text-align:left;"><?php echo(substr(getValue($objRS,"razao_social"),0,45));?></td>
					<td align="center" style="vertical-align:middle;text-align:left;"><?php echo(getValue($objRS,"endprin_fone1")); ?></td>
					<td align="center" style="vertical-align:middle;text-align:center;font-size:10px;color:#999;"><?php echo(getValue($objRS,"id_usuario")); ?></td>
					<td align="center" style="vertical-align:middle;text-align:center;"><?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),false));?></td>
				</tr>
			<?php }?>
        	</tbody>
			<?php }else{?>
			<tbody><tr><td colspan="12" style="border:1px dashed #CCC;color:#999;font-style:italic;text-align:center;"><?php echo getTText("msg_nenhum_cadastro_novo",C_NONE); ?></td></tr></tbody>
		<?php }?>
	</table>
<?php
	athEndFloatingBox();
	$objResult->closeCursor();
?>