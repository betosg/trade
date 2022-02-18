<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");
	
	// REQUESTS
	$intCodDado = request("var_chavereg"); 		// cod_agenda

	// abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);

	// faz busca de respostas com 
	// base no cod agenda enviado
	try{
		$strSQL = "
			SELECT 
				  fin_saldo_ac.cod_conta
				, fin_conta.nome
			  	, fin_saldo_ac.mes
  				, fin_saldo_ac.ano
  				, fin_saldo_ac.valor
  				, fin_saldo_ac.recalculado
			FROM 
				fin_saldo_ac
			INNER JOIN fin_conta ON (fin_conta.cod_conta = fin_saldo_ac.cod_conta)
			WHERE
				fin_saldo_ac.cod_conta = ".$intCodDado."
			ORDER BY fin_saldo_ac.ano DESC, fin_saldo_ac.mes DESC";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	$boolVerifyChecks = false;
	
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
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="_css/default.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style>
			.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
			body{ margin: 10px; background-color:#FFFFFF; } 
			ul{ margin-top: 0px; margin-bottom: 0px; }
			li{ margin-left: 0px; }
		</style>
	</head>
<body bgcolor="#FFFFFF">
	
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td align="left">
				<?php
					// concatenamos o link corretamente para os casos
					// onde o redirect tenha sido informado ou não
					athBeginCssMenu();
						athCssMenuAddItem("","_self",getTText("saldo_acumulado",C_TOUPPER),1);
						//athBeginCssSubMenu();
							//athCssMenuAddItem("",
											  //"_self",getTText("recalcular_acumulado",C_NONE));
						//athEndCssSubMenu();
					athEndCssMenu();		
				?>
			</td>
		</tr>
	</table>
	
	<?php
	// Testa se existe alguma resposta inserida
	// caso contrário, exibe mensagem de vazio
	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("sem_saldo_acumulado",C_NONE),"","aviso",1,"","");
	} else{
	?>
	
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<!--th width="1%"></th--> 		<!-- DELETE -->
				<!--<th width="1%"></th>--> <!-- EDIT -->
				<th width="5%" class="sortable" nowrap><?php echo(getTText("cod_conta_banco",C_TOUPPER));?></th>
				<th width="54%" class="sortable" nowrap><?php echo(getTText("nome_conta",C_TOUPPER));?></th>
				<th width="10%" class="sortable-date-dmy" nowrap><?php echo(getTText("mes",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("ano",C_TOUPPER));?></th>
				<th width="15%" class="sortable-numeric" nowrap><?php echo(getTText("valor_saldo",C_TOUPPER));?></th>
				<th width="1%" nowrap></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($objResult as $objRS){?>
			<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_conta"));?></td>
				<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"nome"));?></td>
				<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"mes"));?></td>
				<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"ano"));?></td>
				<td align="right" style="vertical-align:middle;"><?php echo(number_format((double) getValue($objRS,"valor"),2,',','.'));?></td>
				<td align="center" style="vertical-align:middle;">
				<?php if(getValue($objRS,"recalculado") == TRUE){?>
					<img src="../img/icon_status_ativo.gif" title="<?php echo(getTText("recalculado",C_TOUPPER));?>" />
				<?php }?>
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
	$objConn = NULL;
	$objResult->closeCursor();
?>