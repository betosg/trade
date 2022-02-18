<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// HEADERS
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodTitulo  			 = request("var_chavereg");  	// cod_conta_pagar_receber
	$strNomeCampoChaveDetail = request("var_field_detail"); // 
	
	// Abertura de Conexão com o DB
	$objConn = abreDBConn(CFG_DB);
	
	try {
		// Localiza todos os LCTOS referentes
		// ao COD TITULO encaminhado para aqui
		$strSQL = "	
			SELECT 
				  fin_lcto_ordinario.cod_lcto_ordinario
				, fin_lcto_ordinario.cod_conta_pagar_receber
				, fin_lcto_ordinario.vlr_lcto
				, fin_lcto_ordinario.vlr_multa
				, fin_lcto_ordinario.vlr_juros
				, fin_lcto_ordinario.vlr_desc
				, fin_lcto_ordinario.dt_lcto
				, fin_lcto_ordinario.historico
				, fin_lcto_ordinario.obs
				, fin_lcto_ordinario.sys_dtt_ins
				, fin_lcto_ordinario.sys_usr_ins
				, fin_lcto_ordinario.tipo_documento
				, fin_lcto_ordinario.extra_documento
				, fin_plano_conta.cod_reduzido
				, fin_plano_conta.nome as plano_conta
				, fin_conta.nome AS conta
			FROM fin_lcto_ordinario
			INNER JOIN fin_conta_pagar_receber
			ON ( fin_lcto_ordinario.cod_conta_pagar_receber = fin_conta_pagar_receber.cod_conta_pagar_receber 
			AND fin_conta_pagar_receber.cod_conta_pagar_receber = ".$intCodTitulo.")
			LEFT JOIN fin_plano_conta ON ( fin_lcto_ordinario.cod_plano_conta = fin_plano_conta.cod_plano_conta )
			LEFT JOIN fin_conta 	  ON ( fin_lcto_ordinario.cod_conta = fin_conta.cod_conta ) 
			ORDER BY fin_lcto_ordinario.dt_lcto DESC, fin_conta_pagar_receber.cod_conta_pagar_receber DESC";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"not html");
		die();
	}
	
	// inicializa variavel para pintar linha
	$strColor = "#F5FAFA";
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? "#F5FAFA" : CL_CORLINHA_1;
		echo($prColor);
	}
?>	
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_NAME);?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="_css/default.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style>
			/* suas adaptações CSS aqui */
			.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
			body{ margin: 10px; background-color:#FFFFFF; } 
			ul{ margin-top: 0px; margin-bottom: 0px; }
			li{ margin-left: 0px; }
		</style>
	 	<script language="javascript">
			/* seu conteudo JAVASCRIPT aqui */
			function removeLancamento(prCodLancamento){
				if(confirm("Tem certeza que deseja remover este lançamento?")){
					window.location = 'STremovelancamento.php?var_chavereg=' + <?php echo($intCodTitulo); ?> + '&var_cod_lcto_ordinario=' + prCodLancamento;
				}
			}

			function linkPage(prLink){
				// esta função redireciona a página
				// atual para a pagina informada em
				// prLink
				var strLink = (prLink == "") ? "#" : prLink;
				location.href = strLink;
			}

		</script>
	</head>
<body bgcolor="#FFFFFF">

	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td align="left">
				<?php
					// Menu para lançamentos
					athBeginCssMenu();
						athCssMenuAddItem("","_self",getTText("lancamentos",C_TOUPPER),1);
						athBeginCssSubMenu();
							athCssMenuAddItem("../modulo_FinContaPagarReceber/STinslctoordinarioSinog.php?var_chavereg=".$intCodTitulo,
											  "_self",getTText("lcto_inserir",C_NONE));
						athEndCssSubMenu();
					athEndCssMenu();		
				?>
			</td>
		</tr>
	</table>
	
	<?php
		// Caso nenhum registro tenha retornado no
		// Resultset, entao exibe msg de consulta
		// vazia
		if($objResult->rowCount() <= 0){
			mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",
			getTText("nenhum_lcto_ins",C_NONE),"","aviso",1,"not_html");
		} else{
	?>
	
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%" class="tablesort">
		<thead>
			<tr>
				<th width="1%"></th><!-- DEL -->
				<th width="1%"></th><!-- RECIBO -->	
				<th width="1%"></th><!-- UPD -->				
				<th width="15%" nowrap><?php echo(getTText("plano_conta",C_NONE));?></th>
				<th width="06%" nowrap><?php echo(getTText("conta",C_NONE));?></th>
				<th width="10%" nowrap><?php echo(getTText("data",C_NONE));?></th>
				<th width="06%" nowrap><?php echo(getTText("lcto",C_NONE));?></th>
				<th width="06%" nowrap><?php echo(getTText("juros",C_NONE));?></th>
				<th width="06%" nowrap><?php echo(getTText("desc",C_NONE));?></th>
				<th width="15%" nowrap><?php echo(getTText("ocorrencia",C_NONE));?></th>
				<!--th width="10%" nowrap><php echo(getTText("usuario",C_NONE));?></th-->
				<th width="10%" nowrap><?php echo(getTText("tipo",C_NONE));?></th>
				<th width="1%"></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$Ct=1;
			$dblValotTotal = 0;
			$strTituloAnt = "";
			$boolShowResult = true;
			foreach($objResult as $objRS){
		?>
			<tr bgcolor="<?php echo(getLineColor($strColor)); ?>">	
				<td style="vertical-align:middle;" align="center">
					<img src="../img/icon_trash.gif" alt="<?php echo(getTText("remover",C_NONE));?>" 
					 title="<?php echo(getTText("remover",C_NONE));?>" border="0" style="cursor:pointer;" 
					 onClick="removeLancamento(<?php echo(getValue($objRS,"cod_lcto_ordinario"));?>)">
				</td>
				<td style="vertical-align:middle;" align="center">
					<img src="../img/icon_recibo_lcto.gif" alt="Recibo" title="Recibo" border="0" style="cursor:pointer;" 
					onClick="AbreJanelaPAGE('STshowrecibolcto.php?var_chavereg=<?php echo(getValue($objRS,"cod_lcto_ordinario"));?>','800','800');" />
				</td>
				
				<td align="center" style="vertical-align:middle;">
					<img src="../img/icon_write.gif" alt="editar" 
						 title="editar"
						 border="0" style="cursor:pointer;" 
						 onClick="linkPage('STupdlctoordinarioSinog.php?var_chavereg=<?php echo $intCodTitulo; ?>&var_cod_lcto_ordinario=<?php echo(getValue($objRS,"cod_lcto_ordinario"));?>&var_cod_cad=<?PHP echo $intCodCadastro; ?>');" />
				</td>
				
				<td style="vertical-align:middle;">
					<?php echo(getValue($objRS,"cod_reduzido")." ".getValue($objRS,"plano_conta"));?>
				</td>
				<td style="vertical-align:middle;text-align:center;">
					<?php echo(getValue($objRS,"conta"));?>
				</td>
				<td style="vertical-align:middle;" align="center">
					<?php echo(dDate(CFG_LANG,getValue($objRS,"dt_lcto"),false));?>
				</td>
				<td style="vertical-align:middle;" align="right">
					<?php echo(number_format((double) getValue($objRS,"vlr_lcto"),2,",",""));?>
				</td>
				<td style="vertical-align:middle;" align="right">
					<?php echo(number_format((double) getValue($objRS,"vlr_juros"),2,",",""));?>
				</td>
				<td style="vertical-align:middle;" align="right">
					<?php echo(number_format((double) getValue($objRS,"vlr_desc"),2,",",""));?>
				</td>
				<td style="vertical-align:middle;" align="right">
					<?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),true));?>
				</td>
				<!--td style="vertical-align:middle;" align="center">
					<span class="comment_peq"><?php echo(getValue($objRS,"sys_usr_ins"));?></span>
				</td-->
				<td style="vertical-align:middle;" align="center">
					<?php echo(getValue($objRS,"tipo_documento"));?>
				</td>
				<td style="vertical-align:middle;" align="center">
					<?php 
						if((getValue($objRS,"obs")!="") || (getValue($objRS,"obs")!="") || (getValue($objRS,"obs")!="")){
						$strObsFinal  = "";
						$strObsFinal .= (getValue($objRS,"obs")!="") ? "OBS: ".getValue($objRS,"obs") : "";
						$strObsFinal .= (getValue($objRS,"extra_documento")!="") ? getValue($objRS,"extra_documento") : "";
					?>
					<img src="../img/icon_obs.gif" alt="<?php echo($strObsFinal);?>" border="0" 
					 style="cursor: pointer;" title="<?php echo($strObsFinal);?>">
						<?php } ?>
				</td>
			</tr>
			<?php }?>
		</tbody>
	</table>
	<?php }?>
</body>
<script type="text/javascript">
// Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
// ----------------------------------------------------------------------------------------------------------
</script>
</html>
<?php
	// Quitando Conexão com DB
	$objResult->closeCursor();
	$objConn = NULL;
?>