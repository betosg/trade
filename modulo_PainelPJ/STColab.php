<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página
//$intCodPedido   = request("var_cod_pedido");   // Código do pedido, caso exista
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");      // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente


/***           DEFINIÇÃO DE CONSTANTES             ***/
/*****************************************************/
define("ICONES_NUM"        ,2);     // NÚMERO DE ÍCONES DA GRADE
define("ICONES_WIDTH"      ,20);    // LARGURA DOS ÍCONES DA GRADE
define("GRADE_NUM_ITENS"   ,getsession($strSesPfx . "_num_per_page"));    // NÚMERO DE ITENS DA GRADE (PAGINAÇÃO)
define("GRADE_ACAO_DEFAULT","");    // AÇÃO PADRÃO DA TECLA ENTER NA GRADE
define("ARQUIVO_LEITURA"   ,"STconfiginc.php"); // AÇÃO PADRÃO DA TECLA ENTER NA GRADE


/***           DEFINIÇÃO DE PARÂMETROS            ***/
/****************************************************/
$strOrderCol      = request("var_order_column");   // Índice da coluna para ordenação
$strOrderDir      = request("var_order_direct");   // Direção da ordenação (ASC ou DESC)
$intNumCurPage    = request("var_curpage");        // Página corrente
$strAcao   	      = request("var_acao");           // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$strSQLParam      = request("var_sql_param");      // Parâmetro com o SQL vindo do bookmark
$strPopulate      = request("var_populate");       // Flag de verificação se necessita popular o session ou não
$strIndice        = request("var_indice");         // Campo de filtro
$strValor         = request("var_valor");          // Campo de filtro


/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
/****************************************************/
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

/***        FUNÇÕES AUXILIARES - OPCIONAL         ***/
/****************************************************/
function filtro($prValue) {
	global $strIndice, $strValor;
	
	$strLine = trim($prValue);
	
	$strLine = preg_replace("/define\(|\)\;(.*)/i","",$strLine);
	$arrLine = explode(",",$strLine);
	
	$arrLine[0] = str_replace("\"","",$arrLine[0]);
	$strLineValor = (isset($arrLine[1])) ? $arrLine[1] : "";
	
	if(($strIndice == "" || strpos($arrLine[0],$strIndice) !== false) 
	   && ($strValor == "" || strpos($strLineValor,$strValor) !== false) 	
	   && (trim($strLine) != "" && trim($strLine) != "<?php" && trim($strLine) != "?>")
	   && (strpos($strLine,"//") === false)) {
	    return($prValue);
	}
}

/***        AÇÃO DE EXPORTAÇÃO DA GRADE          ***/
/***************************************************/
//Define uma variável booleana afim de verificar se é um tipo de exportação ou não
$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc") || ($strAcao == ".pdf");

//Exportação para excel, word e adobe reader
if($boolIsExportation) {
	if($strAcao == ".pdf") {
		redirect("exportpdf.php"); //Redireciona para página que faz a exportação para adode reader
	}
	else{
		//Coloca o cabeçalho de download do arquivo no formato especificado de exportação
		header("Content-type: application/force-download"); 
		header("Content-Disposition: attachment; filename=Modulo_" . getTText(getsession($strSesPfx . "_titulo"),C_NONE) . "_". time() . $strAcao);
	}
	
	$strLimitOffSet = "";
} else{
	include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
} 

$objConn = abreDBConn(CFG_DB); // Abertura de banco

if (getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") != "") {
?>
<script>
	var allTrTags = new Array();
	var detailTrFrameAnt = '';
	var moduloDetailAnt = '';
	function showDetailGrid(prChave_reg,prLink, prField){

		if(prLink.indexOf("?") == -1){
			strConcactQueryString = "?"
		}else{
			strConcactQueryString = "&"
		}
		var detailTr = document.getElementById("detailtr_"+prChave_reg).style.display;
		if(detailTr == 'none'){
			 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo CFG_SYSTEM_NAME ?>_detailiframe_"+prChave_reg);
	
			var allTrTags  = document.getElementsByTagName("tr");
			for( i=0; i < allTrTags.length; i++){
				if(allTrTags[i].className == 'iframe_detail'){
					allTrTags[i].style.display = 'none';
				}
			}
			document.getElementById("detailtr_"+prChave_reg).style.display = '';
		}else{
			if( moduloDetailAnt == prLink){
					document.getElementById("detailtr_"+prChave_reg).style.display = 'none';
			}else{
				if(detailTrFrameAnt != "detailtr_"+prChave_reg ){
					 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo CFG_SYSTEM_NAME ?>_detailiframe_"+prChave_reg);
				}
			}
		}
		moduloDetailAnt = prLink;
	}
	
	function SetIFrameSource(prPage,prId) {
		document.getElementById(prId).src = prPage;
	}
	
	<?php if(getsession($strSesPfx . "_field_detail") != '') { 	?>
			window.onload = function(){
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = document.body.scrollHeight + 15;
			}
	<?php }	?>
	
</script>
<html>
  <head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<?php 
		if(!$boolIsExportation || $strAcao == "print"){
			echo("
				  <link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\">
			      <link href='../_css/tablesort.css' rel='stylesheet' type='text/css'>
			      <script type='text/javascript' src='../_scripts/tablesort.js'></script>
			      <style>
			  	    ul{ margin-top: 0px; margin-bottom: 0px; }
				    li{ margin-left: 0px; }
			      </style>
			      <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
				");
		}
	?>
	<script language="JavaScript" type="text/javascript">
		function switchColor(prObj, prColor){
			prObj.style.backgroundColor = prColor;
		}
	</script>
  </head>
<body style="margin:10px 0px 10px 0px;" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table cellpadding="0" cellspacing="0" style="border:none;width:100%">
 <tr>
 	<td width="100%" align="left" style="border:none; background:none; padding-left: 15px;">
<?php
$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");

if ($intCodDado != ""){
	try{
		/// verifica se a card do colaborador corrente
		// expirou a validade, para solicitacao de nova
		$strSQL  = " SELECT count(t7.cod_pedido)     AS qtde_ped_homo ";
		$strSQL .= "      , t1.cod_pj, t2.cod_pf, t2.nome, t2.cpf, t2.ctps, t2.sys_dtt_ins  ";
		$strSQL .= "      , t3.cod_pj_pf, t3.dt_admissao, t4.nome AS cargo  ";
		$strSQL .= "      , t3.dt_demissao, t3.tipo, t3.funcao, t3.obs, t3.departamento ";
		$strSQL .= "      , (CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' AS mais_de_uma_hora  ";
		//$strSQL .= "      , (t5.dt_validade -CURRENT_DATE ) AS vencida";
		$strSQL .= " FROM cad_pj t1  ";
		$strSQL .= "  	  INNER JOIN relac_pj_pf t3        ON (t1.cod_pj = t3.cod_pj) ";
		$strSQL .= "  	  INNER JOIN cad_pf t2             ON (t2.cod_pf = t3.cod_pf) ";
		$strSQL .= "      LEFT OUTER JOIN cad_cargo t4     ON (t3.cod_cargo = t4.cod_cargo) ";
		$strSQL .= " 	  LEFT OUTER JOIN prd_pedido t7    ON (t7.situacao <> 'cancelado' ";
		$strSQL .= "                                   AND t7.it_tipo = 'homo'  ";
		$strSQL .= "                                   AND t7.it_cod_pj_pf = t3.cod_pj_pf  ";
		$strSQL .= "                                   AND t3.dt_demissao IS NULL  ";
		$strSQL .= "                                   AND t7.dtt_inativo IS NULL  )";
		
		$strSQL .= " WHERE t1.cod_pj = " . $intCodDado; 
//		$strSQL .= " AND t6.cod_pedido = t5.cod_pedido "; 
		$strSQL .= " GROUP BY ";
		$strSQL .= "        t1.cod_pj, t2.cod_pf, t2.nome, t2.cpf, t2.ctps, t2.sys_dtt_ins  ";
		$strSQL .= "      , t3.cod_pj_pf, t3.dt_admissao, t4.nome";
		$strSQL .= "      , t3.dt_demissao, t3.tipo, t3.funcao, t3.obs, t3.departamento ";
		$strSQL .= "      , (CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour'  ";
		$strSQL .= " ORDER BY t2.nome";
		//echo $strSQL;
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	
	
	
	/*/BUSCA COLABORADORES
	try {
	$strSQL  = " SELECT t1.cod_pj, t2.cod_pf, t2.nome, t2.cpf, t2.ctps, t2.sys_dtt_ins ";
	$strSQL .= "      , t3.cod_pj_pf, t3.dt_admissao, t4.nome AS cargo ";
	$strSQL .= "      , t3.dt_demissao, t3.tipo, t3.funcao, t3.obs, t3.departamento ";
	$strSQL .= "      , (CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' AS mais_de_uma_hora ";
	$strSQL .= " FROM cad_pj t1 ";
	$strSQL .= " INNER JOIN relac_pj_pf t3 ON (t1.cod_pj = t3.cod_pj) ";
	$strSQL .= " INNER JOIN cad_pf t2 ON (t2.cod_pf = t3.cod_pf) ";
	$strSQL .= " LEFT OUTER JOIN cad_cargo t4 ON (t3.cod_cargo = t4.cod_cargo) ";
	$strSQL .= " WHERE t1.cod_pj = " . $intCodDado;
	$strSQL .= " ORDER BY t2.nome ";
	//die($strSQL);
	$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}*/
	athBeginFloatingBox("100%","","<b>Colaboradores</b>",CL_CORBAR_GLASS_2);
?>
	<table bgcolor="#FFFFFF" style="width:100%;  margin-bottom:0px;" class="tablesort">
		<?php
		if($objResult->rowCount() > 0) {
			?>
			<thead>
			<tr>
				<th width="1%"></th><!-- PARA EDITAR COLABS HOMOLOGADOS -->
				<th width="5%" class="sortable-numeric"><?php echo getTText("cod",C_NONE); ?></th>
				<th width="10%" class="sortable"><?php echo getTText("cpf",C_NONE); ?></th>
				<th width="27%" class="sortable"><?php echo getTText("nome",C_NONE); ?></th>
				<th width="27%" class="sortable"><?php echo getTText("funcao",C_NONE); ?></th>
				<th width="14%" class="sortable-date-dmy"><?php echo getTText("admissao",C_NONE); ?></th>
				<th width="14%" class="sortable-date-dmy"><?php echo getTText("demissao",C_NONE); ?></th>
				<th width="14%" class="sortable-date-dmy"><?php echo getTText("validade",C_NONE); ?></th>
				<th width="1%"><?php echo getTText("obs",C_NONE); ?></th>
				<th width="1%"></th>
				<th width="1%"></th>
			</tr>
			</thead>
        	<tbody bgcolor="<?php echo($strCor);?>">
			<?php
			foreach($objResult as $objRS){
			
				try{
					// SQL para listagem de CARDS
					// BUSCA a qtde de PEDIDOS DO
					// Tipo CARD e CARDS ativa
					$strSQL = " 
							SELECT 
								  count(t5.cod_credencial) AS qtde_credencial
								, count(t6.cod_pedido)     AS qtde_ped_card
								, t2.nome, t5.qtde_impresso, t5.dt_validade
								, t6.it_cod_produto
							FROM cad_pf t2 
							LEFT OUTER JOIN 
								sd_credencial t5 ON ((t5.cod_pf = t2.cod_pf)
								AND (t5.dtt_inativo IS NULL) 
								AND (CURRENT_DATE <= t5.dt_validade))
							LEFT OUTER JOIN 
								prd_pedido t6 ON ((t6.situacao <> 'cancelado') 
								AND (t6.it_tipo = 'card') 
								AND (CURRENT_DATE <= t6.it_dt_fim_val_produto) 
								AND (t6.it_cod_pf = t2.cod_pf))
							WHERE t2.cod_pf = ".getValue($objRS,"cod_pf")." 
							GROUP BY t2.nome, t5.qtde_impresso, t5.dt_validade, t6.it_cod_produto";
					$objResultPF = $objConn->query($strSQL);
				}catch(PDOException $e){
					mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
					die();
				}
				// fetch em DADOS
				if($objResultPF->rowCount() > 0){
					$objRSPF = $objResultPF->fetch();
				}
				
				if (getValue($objRS,"dt_demissao") == "")
					$strCor = CL_CORLINHA_1;
				else 
					$strCor = "#E9E9E9";
					
			?>
				<tr bgcolor="<?php echo($strCor);?>">
					<td align="center" valign="top">
					<?php if(getValue($objRS,"dt_demissao") != ""){?>
							<a href="STupdcolabhomologado.php?var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_cod_pj=<?php echo($intCodDado);?>">
							<img src="../img/icon_write.gif" title="<?php echo getTText("editar_colab",C_NONE); ?>" border="0">
							</a>
					<?php } else{?>
							<img src="../img/icon_write_off.gif" title="<?php echo getTText("editar_colab",C_NONE); ?>" border="0">
					<?php }?>
					</td>
					<td bgcolor="<?php echo($strCor);?>"><?php echo(getValue($objRS,"cod_pf")); ?></td>
					<td><?php echo(getValue($objRS,"cpf")); ?></td>
					<td><?php echo(getValue($objRS,"nome")); ?></td>
					<td><?php echo(getValue($objRS,"funcao")); ?></td>
					<td><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_admissao"), false)); ?></td>
					<td><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_demissao"), false)); ?></td>
					<td><?php echo(dDate(CFG_LANG, getValue($objRSPF,"dt_validade"), false)); ?></td>
					<td align="center"></td>
					<td align="center" valign="middle">
					<?php
					// DECLARAÇÃO DE STATUS
					
					// colab com credencial vencida
					if((getValue($objRSPF,"qtde_credencial") < 1)&&(getValue($objRSPF,"qtde_ped_card") < 1)&&(getValue($objRS,"dt_demissao") == "")){
						echo("<img src='../img/icon_sit_vencida.gif' alt='" . getTText("colab_card_vencido",C_TOUPPER) . "' title='" . getTText("colab_card_vencido",C_TOUPPER) . "' />&nbsp;"); 
					} 
					
					// colab com credencial ok, porém sem nenhuma impressão
					if((getValue($objRSPF,"qtde_credencial") >= 1)&&(getValue($objRSPF,"qtde_ped_card") >= 1)&&(getValue($objRSPF,"qtde_impresso") == 0)&&(getValue($objRS,"dt_demissao") == "")){
						echo("<img src='../img/icon_sit_zero.gif' alt='" . getTText("colab_card_ativo",C_TOUPPER) . "' title='" . getTText("colab_card_impr_zero",C_TOUPPER) . "' />&nbsp;");
					} 
					
					// card ativa
					else if((getValue($objRSPF,"qtde_credencial") >= 1)&&(getValue($objRSPF,"qtde_ped_card") >= 1)&&(getValue($objRS,"dt_demissao") == "")){
						echo("<img src='../img/icon_sit_normal.gif' alt='" . getTText("colab_card_ativo",C_TOUPPER) . "' title='" . getTText("colab_card_ativo",C_TOUPPER) . "' />&nbsp;");
					} 
					
					// card solicitada
					else if((getValue($objRSPF,"qtde_ped_card") >= 1)&&(getValue($objRSPF,"qtde_credencial") < 1)&&(getValue($objRS,"dt_demissao") == "")){
						echo("<img src='../img/icon_sit_solicitacao.gif' alt='" . getTText("colab_card_solic",C_TOUPPER) . "' title='" . getTText("colab_card_solic",C_TOUPPER) . "' />&nbsp;");
					}
					?>
					</td>
					<td align="center" valign="middle">
					<?php  
					// em processo de homologação
					if(getValue($objRS,"dt_demissao") != ""){
						echo("<img src='../img/icon_sit_invalido.gif' alt='" . getTText("colab_proc_homo",C_TOUPPER) . "' title='" . getTText("colab_homologado",C_TOUPPER) . "' />&nbsp;");
					}
					if((getValue($objRS,"qtde_ped_homo") > 0) &&(getValue($objRS,"dt_demissao") == "")){
						echo("<img src='../img/icon_sit_saindo.gif' alt='" . getTText("colab_proc_homo",C_TOUPPER) . "' title='" . getTText("colab_proc_homo",C_TOUPPER) . "' />&nbsp;");
					}
					?>
					</td>
				</tr>
				<?php 
			}
			?>
        	</tbody>
			<?php 
		}
		else {
			?>
			<tbody style="border:none;">
			<tr style="border:none;">
				<td colspan="12" align="center" style="border:none;"><div style="padding-top:2px; padding-bottom:2px;">
				<?php echo("&nbsp;"); //echo(getTText("alert_consulta_vazia_titulo",C_NONE)); ?>
				</div></td>
			</tr>
			</tbody>
			<?php
		}
		?>
	</table>
	<?php
	athEndFloatingBox();
	$objResult->closeCursor();
}
?>
<br>
	</td>
 </tr>
</table>
</body>
</html>
<?php
}
else {
	echo(mensagem("err_selec_empresa_titulo","err_selec_empresa_desc","","","erro",1));
}
$objConn = NULL;
?>