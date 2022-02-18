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
	
	/***           DEFINIÇÃO DE PARÂMETROS            ***/
	/****************************************************/

	$strNameDetail   = request("var_field_detail");	
	$strRedirect 	 = request("var_redirect");		// redirect da pagina
    $strPopulate 	 = (request("var_populate") == "") ? "yes" : request("var_populate");
	
	
	//************************* ---------- TRATAMENTO VAR_CHAVEREG ---------- *************************//
	//************************* - Quando recebe inicialmente ou pelo update - *************************//
	$intCodCadastro  = (request("var_cod_cadastro") != "") ? request("var_cod_cadastro") : request("var_chavereg") ; 


    // Inicializa as variaveis de sessao do modulo
	if($strPopulate == "yes") { initModuloParams(basename(getcwd())); }      
	// verificação de ACESSO
	/*if(!verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession(basename(getcwd()) . "_chave_app"),"VIE","not die")){
		mensagem("err_acesso_titulo","err_acesso_desc","Ação a ser realizada:&nbsp;VIE","","erro",1,"not html");
		$strScript  = "";
		$strScript .= "<script type=\"text/javascript\">";
		$strScript .= "/* usado para redimensionar o IFRAME ";
		$strScript .= "resizeIframeParent('" . CFG_SYSTEM_NAME . "_detailiframe_" . $var_cod_cad ."',05)";
		$strScript .=" </script>";
		echo($strScript);die();
	}*/
	

	/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
	/****************************************************/

	// CASO SEJA NECESSÁRIO UTILIZAR OS LIMITS, 
	// DESCOMENTAR E ALTERAR O SQL PADRÃO
	// 	$strLimit       = request("var_limit"); // request do LIMIT
	// 	if(($strLimit=="-1")){ 
	// 		$strLimit = ""; 
	// 	} else {
	// 		if (($strLimit == "")){ $strLimit = 10; }
	//  	$strLimit = "LIMIT " . $strLimit;
	// 	}

	// abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);

	// SQL PADRÃO DA LISTAGEM - BREVE DESCRIÇÃO
	try{
		// seleciona todos os contatos do fornecedor
		// com cod_cadastro enviado para este script
	$strSQL = "	select t2.cod_marca as codigo, t2.marca
				from cad_pj t1
					INNER JOIN cad_pj_marcas t2 on t1.cod_pj = t2.cod_pj					
				WHERE t1.cod_pj = " . $intCodCadastro . "				
				ORDER BY t2.marca ;";

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
					athCssMenuAddItem("","_self","Marcas",1);
					athBeginCssSubMenu();								
						athCssMenuAddItem("STinsMarca.php?var_codigo=".$intCodCadastro,"_self","Inserir");
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
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc","Não há marcas cadastradas.","","aviso",1,"","","");
	} else {
	?>
	
	<!-- TABLESORT DA MINI APP . INICIO -->
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="1%"></th><!-- DEL -->
				<th width="1%"></th><!-- UPD -->
				<!--th width="1%"></th>< VIE -->				
				<th width="12%" class="sortable" nowrap>Cód</th>
				<th width="20%" class="sortable" nowrap>Marca</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($objResult as $objRS){ ?>
        	<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td align="center" style="vertical-align:middle;">
					<img src="../img/icon_trash.gif" alt="deletar" 
						 title="deletar"
						 border="0" style="cursor:pointer;"
						 onClick="linkPage('STdelMarca.php?var_cod_dado=<?php echo(getValue($objRS,"codigo"));?>&var_cod_cad=<?PHP echo $intCodCadastro; ?>');" />				</td>
				<td align="center" style="vertical-align:middle;">
					<img src="../img/icon_write.gif" alt="editar" 
						 title="editar"
						 border="0" style="cursor:pointer;" 
						 onClick="linkPage('STupdMarca.php?var_cod_dado=<?php echo(getValue($objRS,"codigo"));?>&var_cod_cad=<?PHP echo $intCodCadastro; ?>');" />				</td>
				<!--td align="left" style="vertical-align:middle;">
					<img src="../img/icon_zoom.gif" alt="<?php echo(getTText("visualizar",C_NONE));?>" 
						 title="<?php echo(getTText("visualizar",C_NONE));?>"
						 border="0" style="cursor:pointer;" 
						 onClick="linkPage('STvieMarca.php?var_cod_dado=<?php echo(getValue($objRS,"codigo"));?>&var_cod_cad=<?PHP echo $intCodCadastro; ?>');" />				</td//-->		
				<td align="left"><?php echo(getValue($objRS,"codigo")); ?></td>
				<td align="left"><?php echo(strtoupper(getValue($objRS,"marca"))); ?></td>							
			</tr>
			<!-- # EXEMPLO DE UTILIZAÇÃO DE IFRAME DETAIL NO FINAL DE CADA TR # 
				 CASO VOCÊ PRECISE UTILIZAR UM DETAIL DENTRO DE OUTRO DETAIL 
				 VOCÊ TERÁ QUE DESCOMENTAR AS LINHAS ABAIXO. CADA IFRAME POR
				 VOLTA DO LAÇO DO FOREACH RECEBE COMUMENTE O NOME/ID COMO
				 "|nome_do_sistema|_detailiframe_|request[VAR_CHAVEREG]|"
				 E PARA O SEU SOURCE SER REDIMENSIONADO, O PRÓPRIO SOURCE DE-
				 VERÁ CONTER NO FINAL DE SEU CÓDIGO A CHAMADA DA FUNÇÃO JS
				 resizeIFrameParent('id_iframe',margem_adicional) - ONDE O
				 'ID_IFRAME' É O ID DO FRAME DA PÁGINA PAI, OU SEJA, ESTE SC-
				 RIPT QUE VOCÊ ESTÁ UTILIZANDO.	
			  <tr bgcolor="#DDDDDD">
			  	<td colspan="6">
					<iframe name="|?php echo(CFG_SYSTEM_NAME."_detailiframe_".getValue($objRS,"cod_pk"));?|" 
							id="|?php echo(CFG_SYSTEM_NAME."_detailiframe_".getValue($objRS,"cod_pk"));?|" 
							width="100%" src="" frameborder="0" scrolling="no">
					</iframe>
				</td>
			  </tr>
			  -->
		<?php } ?>
		</tbody>		
	</table>
	<!-- TABLESORT DA MINI APP . FIM -->

	<?php } ?>
</body>
	<script type="text/javascript">
	  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodCadastro); ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php
	// SETA O OBJETO DE CONEXÃO COM BANCO PARA NULO
	// ALÉM DISSO, FECHA O CURSOR DO RESULTSET
	$objConn = NULL;
	$objResult->closeCursor();
?>