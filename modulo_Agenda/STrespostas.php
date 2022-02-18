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
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
	
	// REQUESTS
	$intCodAgenda   = request("var_chavereg"); 		// cod_agenda
	$strNameDetail  = request("var_field_detail");	
	$strRedirect 	= request("var_redirect");		// redirect da pagina [provavel que venha null nesta pag]

	// abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);

	// faz busca de respostas com 
	// base no cod agenda enviado
	try{
		$strSQL = "
			SELECT 
  				  cod_resposta
				, cod_agenda  
				, id_usuario
  				, resposta
 				, dtt_resposta
  				, sys_usr_ins
  				, sys_dtt_ins
			FROM ag_resposta 
			WHERE cod_agenda = ".$intCodAgenda."
			ORDER BY sys_dtt_ins DESC";
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
		<title><?php echo(strtoupper(CFG_SYSTEM_NAME));?></title>
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
						athCssMenuAddItem("","_self",getTText("respostas",C_TOUPPER),1);
						athBeginCssSubMenu();
							athCssMenuAddItem("STinsresposta.php?var_chavereg=".$intCodAgenda,
											  "_self",getTText("resposta_inserir",C_NONE));
						athEndCssSubMenu();
					athEndCssMenu();		
				?>
			</td>
		</tr>
	</table>
	
	<?php
	// Testa se existe alguma resposta inserida
	// caso contrário, exibe mensagem de vazio
	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("nenhuma_resp_pub",C_NONE),"","aviso",1,"","");
	} else{
	?>
	
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="1%"></th> 		<!-- DELETE -->
				<!--<th width="1%"></th>--> <!-- EDIT -->
				<th width="05%" class="sortable" nowrap><?php echo(getTText("cod_resposta",C_TOUPPER));?></th>
				<th width="15%" class="sortable" nowrap><?php echo(getTText("id_usuario_resp",C_TOUPPER));?></th>
				<th width="10%" class="sortable-date-dmy" nowrap><?php echo(getTText("dtt_resposta",C_TOUPPER));?></th>
				<th width="10%" class="sortable" nowrap><?php echo(getTText("sys_usr_ins_resp",C_TOUPPER));?></th>
				<th width="10%" class="sortable-date-dmy" nowrap><?php echo(getTText("sys_dtt_ins_resp",C_TOUPPER));?></th>
				<th width="50%" class="sortable" nowrap><?php echo(getTText("resposta",C_TOUPPER));?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($objResult as $objRS){?>
			<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td width="1%" align="center" style="vertical-align:middle;">
					<?php if(getValue($objRS,"sys_usr_ins") == getsession(CFG_SYSTEM_NAME."_id_usuario")){?>
						<a href="STdelresposta.php?var_chavereg=<?php echo(getValue($objRS,"cod_resposta"));?>
								 &var_redirect=<?php echo("STrespostas.php?var_chavereg=".$intCodAgenda);?>">
							<img src="../img/icon_trash.gif" alt="<?php echo(getTText("remover",C_NONE));?>" 
						     title="<?php echo(getTText("remover",C_NONE));?>" border="0" style="cursor:pointer;"/>
						</a>
					<?php }else{ ?>
							<img src="../img/icon_trash_off.gif" alt="<?php echo(getTText("remover",C_NONE));?>" 
						     title="<?php echo(getTText("remover",C_NONE));?>" border="0"/>
					<?php } ?>
				</td>
				<td width="05%" align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_resposta"));?></td>
				<td width="15%" align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"id_usuario"));?></td>
				<td width="10%" align="center" style="vertical-align:middle;">
					<?php echo(dDate(CFG_LANG,getValue($objRS,"dtt_resposta"),false));?>
				</td>
				<td width="10%" align="center" style="vertical-align:middle;">
					<?php echo("<span class='comment_peq' style='padding-top:3px;'>".
									getValue($objRS,"sys_usr_ins").
							   "</span>");?>
				</td>
				<td width="10%" align="center" style="vertical-align:middle;">
					<?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),false));?>
				</td>
				<td width="50%" align="justify">
					<?php echo("<span title='".getValue($objRS,"resposta")."' 
								 alt='".getValue($objRS,"resposta")."' 
								 style='cursor:default;'>".getValue($objRS,"resposta")."
								</span>");
					?>
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