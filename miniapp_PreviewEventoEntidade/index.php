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
	
	/***           DEFINI��O DE PAR�METROS            ***/
	/****************************************************/

	$strNameDetail   = request("var_field_detail");	
	$strRedirect 	 = request("var_redirect");		// redirect da pagina
    $strPopulate 	 = (request("var_populate") == "") ? "yes" : request("var_populate");
	$id_empresa      = getsession("id_empresa");
	
	//************************* ---------- TRATAMENTO VAR_CHAVEREG ---------- *************************//
	//************************* - Quando recebe inicialmente ou pelo update - *************************//
	$intCodDado  = (request("var_cod_cadastro") != "") ? request("var_cod_cadastro") : request("var_chavereg") ; 


    // Inicializa as variaveis de sessao do modulo
	if($strPopulate == "yes") { initModuloParams(basename(getcwd())); }      
	// verifica��o de ACESSO
	if(!verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession(basename(getcwd()) . "_chave_app"),"VIE","not die")){
		mensagem("err_acesso_titulo","err_acesso_desc","A��o a ser realizada:&nbsp;VIE","","erro",1,"not html");
		$strScript  = "";
		$strScript .= "<script type=\"text/javascript\">";
		$strScript .= "/* usado para redimensionar o IFRAME */";
		$strScript .= "resizeIframeParent('" . CFG_SYSTEM_NAME . "_detailiframe_" . $var_cod_cad ."',05)";
		$strScript .=" </script>";
		echo($strScript);die();
	}
	

	/***    A��O DE PREPARA��O DA GRADE - OPCIONAL    ***/
	/****************************************************/

	// CASO SEJA NECESS�RIO UTILIZAR OS LIMITS, 
	// DESCOMENTAR E ALTERAR O SQL PADR�O
	// 	$strLimit       = request("var_limit"); // request do LIMIT
	// 	if(($strLimit=="-1")){ 
	// 		$strLimit = ""; 
	// 	} else {
	// 		if (($strLimit == "")){ $strLimit = 10; }
	//  	$strLimit = "LIMIT " . $strLimit;
	// 	}

	// abre conex�o com o banco de dados
	$objConn = abreDBConn(CFG_DB);

	// SQL PADR�O DA LISTAGEM - BREVE DESCRI��O
	try{
		// seleciona todos os contatos do fornecedor
		// com cod_cadastro enviado para este script
		$strSQL = "SELECT cad_funcionario_dependente.cod_dependente ";
        $strSQL = $strSQL . ", cad_funcionario_dependente.nome ";
		$strSQL = $strSQL . ", TO_CHAR(cad_funcionario_dependente.data_nasc, 'DD/MM/YYYY') AS dt_nasc ";
		$strSQL = $strSQL . ", cad_parentesco.parentesco ";
		$strSQL = $strSQL . "FROM cad_funcionario_dependente ";
		$strSQL = $strSQL . "LEFT JOIN cad_parentesco ON cad_funcionario_dependente.cod_parentesco = cad_parentesco.cod_parentesco ";
		$strSQL = $strSQL . "WHERE cad_funcionario_dependente.cod_funcionario = " . $intCodDado ;
		$strSQL = $strSQL . " ORDER BY nome;";
		$objResult = $objConn->query($strSQL);
//die($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// inicializa variavel para pintar linha
	$strColor = "#F5FAFA";
	// fun��o para cores de linhas
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
			/* suas adapta��es css aqui */
			.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
			body{ margin:10px;background-color:#FFFFFF; } ul{ margin-top:0px;margin-bottom:0px; } li{ margin-left:0px; }
		</style>
		
		<script type="text/javascript">
			
		
			/* seu c�digo javascript aqui */
			
			function linkPage(prLink){
				// esta fun��o redireciona a p�gina
				// atual para a pagina informada em
				// prLink
				var strLink = (prLink == "") ? "#" : prLink;
				location.href = strLink;
			}
		</script>
		
	</head>
<body bgcolor="#FFFFFF">
	
	<!-- MENU PURE CSS SUPERIOR . COMENT�RIOS DE UTILIZA��O NO INTERIOR DO CONJUNTO DE FUN��ES MENU CSS -->
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td align="left">		
			<?php
				athBeginCssMenu();
					athCssMenuAddItem("","_self",getTText("rotulo_dialog",C_TOUPPER),1);
					athBeginCssSubMenu();								
						athCssMenuAddItem("STinsdependente.php?var_codigo=".$intCodDado,
										  "_self",getTText("inserir_dependente",C_UCWORDS));
					athEndCssSubMenu();
				athEndCssMenu();		
			?>
			</td>
		</tr>
	</table>
	<!-- MENU PURE CSS SUPERIOR . FIM -->
	
	<?php
	// TESTA SE CONSULTA EST� VAZIA - N�O REMOVER
	// Neste caso apenas mostra mensagem de consulta
	// vazia, para que seja carregado o resize do
	// frame pai - para quando este script � utilizado
	// como detail.
	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("no_dependente",C_NONE),"","aviso",1,"","","");
	} else {
	?>
	
	<!-- TABLESORT DA MINI APP . INICIO -->
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="1%"></th><!-- DEL -->
				<th width="1%"></th><!-- UPD -->
				<th width="1%"></th><!-- VIE -->
				<th width="10%" class="sortable" nowrap><?php echo(getTText("codigo",C_TOUPPER));?></th>
				<th width="60%" class="sortable" nowrap><?php echo(getTText("dependente",C_TOUPPER));?></th>
				<th width="20%" class="sortable" nowrap><?php echo(getTText("parentesco",C_TOUPPER));?></th>
				<th width="7%" class="sortable" nowrap><?php echo(getTText("aniversario",C_TOUPPER));?></th>
			</tr>
		</thead>
		<tbody>
		
		
		<?php foreach($objResult as $objRS){ ?>
        	<tr bgcolor="<?php echo(getLineColor($strColor));?>">
				<td style="vertical-align:middle;">
					<img src="../img/icon_trash.gif" alt="<?php echo(getTText("remover",C_NONE));?>" 
						 title="<?php echo(getTText("remover",C_NONE));?>"
						 border="0" style="cursor:pointer;"
						 onClick="linkPage('STdeldependente.php?var_chavereg=<?php echo(getValue($objRS,"cod_dependente"));?>&var_cod_cad=<?PHP echo $intCodDado; ?>');" />
				</td>
				<td style="vertical-align:middle;">
					<img src="../img/icon_write.gif" alt="<?php echo(getTText("editar",C_NONE));?>" 
						 title="<?php echo(getTText("editar",C_NONE));?>"
						 border="0" style="cursor:pointer;" 
						 onClick="linkPage('STupddependente.php?var_chavereg=<?php echo(getValue($objRS,"cod_dependente"));?>&var_cod_cad=<?PHP echo $intCodDado; ?>');" />
				</td>
				<td style="vertical-align:middle;">
					<img src="../img/icon_zoom.gif" alt="<?php echo(getTText("visualizar",C_NONE));?>" 
						 title="<?php echo(getTText("visualizar",C_NONE));?>"
						 border="0" style="cursor:pointer;" 
						 onClick="linkPage('STviedependente.php?var_chavereg=<?php echo(getValue($objRS,"cod_dependente"));?>&var_cod_cad=<?PHP echo $intCodDado; ?>');" />
				</td>				
				<td style="left;"><?php echo(getValue($objRS,"cod_dependente"));?></td>
				<td style="left;"><?php echo(strtoupper(getValue($objRS,"nome")));?></td>
				<td style="left;"><?php echo(getValue($objRS,"parentesco"));?></td>
                <td style="left;"><?php echo(getValue($objRS,"dt_nasc"));?></td>												
			</tr>
			<!-- # EXEMPLO DE UTILIZA��O DE IFRAME DETAIL NO FINAL DE CADA TR # 
				 CASO VOC� PRECISE UTILIZAR UM DETAIL DENTRO DE OUTRO DETAIL 
				 VOC� TER� QUE DESCOMENTAR AS LINHAS ABAIXO. CADA IFRAME POR
				 VOLTA DO LA�O DO FOREACH RECEBE COMUMENTE O NOME/ID COMO
				 "|nome_do_sistema|_detailiframe_|request[VAR_CHAVEREG]|"
				 E PARA O SEU SOURCE SER REDIMENSIONADO, O PR�PRIO SOURCE DE-
				 VER� CONTER NO FINAL DE SEU C�DIGO A CHAMADA DA FUN��O JS
				 resizeIFrameParent('id_iframe',margem_adicional) - ONDE O
				 'ID_IFRAME' � O ID DO FRAME DA P�GINA PAI, OU SEJA, ESTE SC-
				 RIPT QUE VOC� EST� UTILIZANDO.	
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
		 <!--<tfoot>
			<tr bgcolor="#DDDDDD">				
				<td align="center" colspan="11">
					<?php echo(getTText("leg_di",C_NONE));?>&nbsp;&nbsp;
					<?php echo(getTText("leg_mv",C_NONE));?>&nbsp;&nbsp;
					<?php echo(getTText("leg_cp",C_NONE));?>&nbsp;&nbsp;
					<?php echo(getTText("leg_ic",C_NONE));?>&nbsp;&nbsp;
					<?php echo(getTText("leg_ex",C_NONE));?>&nbsp;&nbsp;
					<?php echo(getTText("leg_re",C_NONE));?>
				
				</td>
			</tr>
		</tfoot>-->
		</tbody>		
	</table>
	<!-- TABLESORT DA MINI APP . FIM -->
	
	

	
		
	<?php } ?>
</body>
	<script type="text/javascript">
	  // Quando esta p�gina for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodDado); ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php
	// SETA O OBJETO DE CONEX�O COM BANCO PARA NULO
	// AL�M DISSO, FECHA O CURSOR DO RESULTSET
	$objConn = NULL;
	$objResult->closeCursor();
?>