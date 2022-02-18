<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	
	/***           		   INCLUDES                   ***/
	/****************************************************/
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
		
	/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
	/****************************************************/
	// verificação de ACESSO
	//$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");
	
	
	/***           DEFINIÇÃO DE PARÂMETROS            ***/
	/****************************************************/
	$intCodDado		 = request("var_codnfpai");
	
	
	// abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);
	
	
	
	// SQL PADRÃO DA LISTAGEM - BREVE DESCRIÇÃO
	try{
		// seleciona todos os contatos do fornecedor
		// com cod_cadastro enviado para este script
		 $strSQL = "SELECT 
						  cod_nf_entrada_item,
						  cod_nf_entrada,
						  descr_produto,
						  cod_cfop,
						  cod_sit_trib,
						  quantidade,
						  unidade,
						  vlr_unitario,
						  vlr_total,
						  vlr_icms_aliq,
						  vlr_icms_base,
						  vlr_icms,
						  vlr_ipi_aliq,
						  vlr_ipi,
						  obs,
						  dtt_inativo,
						  sys_usr_ins,
						  sys_dtt_ins,
						  sys_dtt_upd,
						  sys_usr_upd
						FROM fin_nf_entrada_item
						WHERE cod_nf_entrada = ".$intCodDado.";
		 			";
					
		$objResult = $objConn->query($strSQL);
		
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
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
		<title><?php echo(CFG_SYSTEM_TITLE);?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="_css/default.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style>
			/* suas adaptações css aqui */
			.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
			body{ margin:10px;background-color:#FFFFFF; } ul{ margin-top:0px;margin-bottom:0px; } li{ margin-left:0px; }
		</style>
		
		<script type="text/javascript">
			/* seu código javascript aqui */
			
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
	
	<!-- MENU PURE CSS SUPERIOR . COMENTÁRIOS DE UTILIZAÇÃO NO INTERIOR DO CONJUNTO DE FUNÇÕES MENU CSS -->
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td align="left">			
			<?php
			athBeginCssMenu();
				athCssMenuAddItem("","_self",getTText("itens",C_TOUPPER),1);
				athBeginCssSubMenu();
					athCssMenuAddItem("STinsItemNF.php?var_codnfpai=".$intCodDado,"_self",getTText("inserir_item",C_UCWORDS));				
				athEndCssSubMenu();
			athEndCssMenu();		
			?>
			</td>
		</tr>
	</table>
	<!-- MENU PURE CSS SUPERIOR . FIM -->
	
	<?php
	// TESTA SE CONSULTA ESTÁ VAZIA - NÃO REMOVER
	// Neste caso apenas mostra mensagem de consulta
	// vazia, para que seja carregado o resize do
	// frame pai - para quando este script é utilizado
	// como detail.
	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("no_contato",C_NONE),"","aviso",1,"","","");
	} else {
	?>
	
	<!-- TABLESORT DA MINI APP . INICIO -->

		<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="1%"></th><!-- DEL -->
				<th width="1%"></th><!-- UPD -->
				<th width="1%"></th><!-- VIE -->
				<th width="10%" class="sortable" nowrap colspan="2"><?php echo(getTText("codigo",C_TOUPPER));?></th>
				<th width="36%" class="sortable" nowrap  colspan="2"><?php echo(getTText("descricao",C_TOUPPER));?></th>
				<th width="8%" class="sortable" nowrap><?php echo(getTText("qtde",C_TOUPPER));?></th>
				<th width="7%" class="sortable" nowrap><?php echo(getTText("unidade",C_TOUPPER));?></th>
  				<th width="7%" class="sortable" nowrap><?php echo(getTText("unitario",C_TOUPPER));?></th>
				<th width="7%" class="sortable" nowrap><?php echo(getTText("total",C_TOUPPER));?></th>
			</tr>
		</thead>
		<tbody>
		
		
		<?php 
			$soma =0;
			foreach($objResult as $objRS){ 
		?>
		
		<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td  align="center" style="vertical-align:middle;">				
				<img src="../img/icon_trash.gif" alt="<?php echo(getTText("remover",C_NONE));?>" 
						 title="<?php echo(getTText("remover",C_NONE));?>"
						 border="0" style="cursor:pointer;"
						 onClick="linkPage('STdelItemNF.php?var_chavereg=<?php echo(getValue($objRS,"cod_nf_entrada_item"));?>&var_codnfpai=<?php echo($intCodDado);?>');" />				
				</td>					
				<td align="center" style="vertical-align:middle;">				
					<img src="../img/icon_write.gif" alt="<?php echo(getTText("editar",C_NONE));?>" 
						 title="<?php echo(getTText("editar",C_NONE));?>"
						 border="0" style="cursor:pointer;" 
						 onClick="linkPage('STupdItemNF.php?var_chavereg=<?php echo(getValue($objRS,"cod_nf_entrada_item"));?>&var_codnfpai=<?php echo($intCodDado);?>');" />			
				</td>
				<td align="center" style="vertical-align:middle;">					
					<img src="../img/icon_zoom.gif" alt="<?php echo(getTText("visualizar",C_NONE));?>" 
						 title="<?php echo(getTText("visualizar",C_NONE));?>"
						 border="0" style="cursor:pointer;" 
						 onClick="linkPage('STvieItemNF.php?var_chavereg=<?php echo(getValue($objRS,"cod_nf_entrada_item"));?>&var_codnfpai=<?php echo($intCodDado);?>');" />	
				</td>				
				<td align="left" colspan="2"><?php echo(getValue($objRS,"cod_nf_entrada_item"));?></td>
				<td align="left" colspan="2" >
				  <?php echo(strtoupper(getValue($objRS,"descr_produto")));?><br />	
           		</td>
				<td align="center"><?php echo(number_format((double) getValue($objRS,"quantidade"), 2, ',', '.'));?></td>
                <td align="center"><?php echo(getValue($objRS,"unidade"));?></td>
                <td align="center"><?php echo number_format((double) getValue($objRS,"vlr_unitario"), 2, ',', '.'); ?></td>
                <td align="center"><?php  echo number_format((double) getValue($objRS,"vlr_total"), 2, ',', '.'); ?></td>
				<?php // $somatotal = $somatotal + getValue($objRS,"subtotalo"); ?> 				
			</tr>		
		
		
				
		<?php 	
				$soma = $soma + getValue($objRS,"vlr_total");
				} //fecha for each
		?>
		</tbody>
		 <tfoot>
			<tr bgcolor="#F8F8F8">				
				<td align="right" class="total" colspan="10">Valor Total Produtos</td>
				<td align="right" class="total"><?php  echo number_format((double) $soma, 2, ',', '.'); ?></td>		
			</tr>
		</tfoot>
	</table>
	<!-- TABLESORT DA MINI APP . FIM -->
	<?php } //if count?>	
</body>
	<script type="text/javascript">
	  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodDado); ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php
	// SETA O OBJETO DE CONEXÃO COM BANCO PARA NULO
	// ALÉM DISSO, FECHA O CURSOR DO RESULTSET
	$objConn = NULL;
	$objResult->closeCursor();
?>