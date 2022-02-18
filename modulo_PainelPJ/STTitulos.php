<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");


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

$strExibir = request("var_exibir");
if($strExibir == ""){$strExibir = 'abertos';}


// SÓ ABRE TELA DOS TÍTULOS | COBRANÇAS SE HOUVER UMA
	// PJ LOGADA NA SESSÃO CORRENTE, ANTI PROBLEMAS ;-)
$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
	//getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo")
	$intQtdeTitulos = 0;


/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
/****************************************************/
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo



$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
if(getsession(CFG_SYSTEM_NAME."_id_usuario") != "athenas"){
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente
}


/***           DEFINIÇÃO DE CONSTANTES             ***/
/*****************************************************/
define("ICONES_NUM"        ,2);     // NÚMERO DE ÍCONES DA GRADE
define("ICONES_WIDTH"      ,20);    // LARGURA DOS ÍCONES DA GRADE
define("GRADE_NUM_ITENS"   ,getsession($strSesPfx . "_num_per_page"));    // NÚMERO DE ITENS DA GRADE (PAGINAÇÃO)
define("GRADE_ACAO_DEFAULT","");    // AÇÃO PADRÃO DA TECLA ENTER NA GRADE
define("ARQUIVO_LEITURA"   ,"STconfiginc.php"); // AÇÃO PADRÃO DA TECLA ENTER NA GRADE




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
} 

$objConn = abreDBConn(CFG_DB); // Abertura de banco

if (getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") != "") {
?>
<script>
	<!--alert('<?php //echo(getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo")) ?>');-->
	
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
<body style="margin:10px;" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<?php athBeginFloatingBox("100%","","<b>Títulos (Contas a Pagar / Receber)</b>",CL_CORBAR_GLASS_2);?>
<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td align="left">			
			<?php
				// concatenamos o link corretamente para os casos
				// onde o redirect tenha sido informado ou não
	
					athBeginCssMenu();
						athCssMenuAddItem("","_self","Conta Pagar/Receber" ,1);
							athBeginCssSubMenu();								
							athCssMenuAddItem("","_self","Tipo Titulo",1);
							athBeginCssSubMenu();
								athCssMenuAddItem("STTitulos.php?var_chavereg=".$intCodDado."&var_exibir=abertos"  ,"_self","Abertos");
								athCssMenuAddItem("STTitulos.php?var_chavereg=".$intCodDado."&var_exibir=a_vencer"  ,"_self","A Vencer");
								athCssMenuAddItem("STTitulos.php?var_chavereg=".$intCodDado."&var_exibir=vencidos"  ,"_self","Vencidos");
								athCssMenuAddItem("STTitulos.php?var_chavereg=".$intCodDado."&var_exibir=fechados" ,"_self","Fechados");
								//athCssMenuAddItem("STTitulos.php?var_chavereg=".$intCodDado."&var_exibir=homo" ,"_self","Homo/Carteirinha");
								athCssMenuAddItem("STTitulos.php?var_chavereg=".$intCodDado."&var_exibir=full" ,"_self","Todos");
							athEndCssSubMenu();
					athEndCssMenu();		
				?>
			</td>
		</tr>
</table>
<table cellpadding="0" cellspacing="0" style="border:none;width:100%">
 <tr>
 	<td width="100%" align="left" style="border:none; background:none; padding-left:15px;">
<?php
	// SÓ ABRE TELA DOS TÍTULOS | COBRANÇAS SE HOUVER UMA
	// PJ LOGADA NA SESSÃO CORRENTE, ANTI PROBLEMAS ;-)
$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
	//getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo")
	$intQtdeTitulos = 0;
	if ($intCodDado != ""){
		//BUSCA TITULOS
		try {
			$strSQL  = " 
				SELECT 
					  t1.cod_conta_pagar_receber
					, t1.situacao
					, t1.vlr_conta
					, t1.vlr_saldo
					, t1.vlr_pago 
					, t1.vlr_mora_multa
					, t1.vlr_outros_acresc
					, t1.dt_emissao
					, t1.dt_vcto
					, t1.num_documento
					, t1.historico
					, t1.tipo_documento
					, t2.cod_pedido 
					, t3.razao_social
					, t3.cnpj
				FROM fin_conta_pagar_receber t1 ";
			if ($strExibir == "abertos") {
				$strSQL .= " LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido) ";
				$strSQL .= " INNER JOIN cad_pj t3 ON (t3.cod_pj = t1.codigo) ";
				$strSQL .= " WHERE t1.tipo = 'cad_pj' AND t1.codigo = ".$intCodDado;
				$strSQL .= " AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial') ";
			}
			elseif ($strExibir == "vencidos") {
				$strSQL .= " LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido) ";
				$strSQL .= " INNER JOIN cad_pj t3 ON (t3.cod_pj = t1.codigo) ";
				$strSQL .= " WHERE t1.tipo = 'cad_pj' AND t1.codigo = ".$intCodDado;
				$strSQL .= " AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial') ";
				$strSQL .= " AND (t1.dt_vcto < CURRENT_DATE) ";
			}
			elseif ($strExibir == "a_vencer") {
				$strSQL .= " LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido) ";
				$strSQL .= " INNER JOIN cad_pj t3 ON (t3.cod_pj = t1.codigo) ";
				$strSQL .= " WHERE t1.tipo = 'cad_pj' AND t1.codigo = ".$intCodDado;
				$strSQL .= " AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial') ";
				$strSQL .= " AND (t1.dt_vcto >= CURRENT_DATE) ";
			}
			elseif ($strExibir == "fechados") {
				$strSQL .= " LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido) ";
				$strSQL .= " INNER JOIN cad_pj t3 ON (t3.cod_pj = t1.codigo) ";
				$strSQL .= " WHERE t1.tipo = 'cad_pj' AND t1.codigo = ".$intCodDado;
				$strSQL .= " AND (t1.situacao = 'lcto_total') ";
			}
			elseif (($strExibir == "homo") || ($strExibir == "card")) {
				$strSQL .= " INNER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido AND t2.it_tipo = '".$strExibir."') ";
				$strSQL .= " INNER JOIN cad_pj t3 ON (t3.cod_pj = t1.codigo) ";
				$strSQL .= " WHERE t1.tipo = 'cad_pj' AND t1.codigo = ".$intCodDado;
				$strSQL .= " AND (t1.situacao <> 'agrupado') ";
			}
			elseif (($strExibir == "full")) {
				$strSQL .= " LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido) ";
				$strSQL .= " INNER JOIN cad_pj t3 ON (t3.cod_pj = t1.codigo) ";
				$strSQL .= " WHERE t1.tipo = 'cad_pj' AND t1.codigo = ".$intCodDado;
				$strSQL .= " AND (t1.situacao <> 'agrupado') ";
			}
			$strSQL .= " ORDER BY t1.situacao, t1.dt_emissao ";
			
			$objResult = $objConn->query($strSQL);
		}
		catch(PDOException $e) {
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
	
	
?>
<script type="text/javascript" language="javascript">
	function agruparTitulos(){
		var form = document.formboleto;
		var submeter = false;
		var count = 0;
		for(var i = 0; i < form.elements.length ; i++){
			if(form.elements[i].type == 'checkbox' && form.elements[i].disabled == false){
				if(form.elements[i].checked == true){
					count++;
					submeter = true;
				}
			}
		}
		if(submeter){
			if(count > 1){
				form.submit();
			}
			else if(count <= 1) {
				alert('Não é possível agrupar somente uma cobrança');
				return;
			}
		}
		else{
			alert('Selecione alguma cobrança');
		}
	}
</script>
<form name="formboleto" action="../modulo_PainelPJ/STagrupatitulo.php" method="post" style="margin:0px;">
<input type="hidden" name="var_chavereg" value="<?php echo($intVlrCampoChaveDetail); ?>">
<input type="hidden" name="var_url_retorno" value="../modulo_PainelPJ/STTitulos.php?var_exibir=<?php echo $strExibir; ?>">	
<br />
<table bgcolor="#FFFFFF" style="width:100%;margin-bottom:0px;border-bottom:1px solid #CCC;" class="tablesort">
<?php if($objResult->rowCount() > 0){?>
	<thead>
	<tr>
		<th width="01%"></th> <!-- BOLETO -->
		<th width="01%"></th> <!-- DESAGRUPAR -->
		<th width="01%">
		<div id="detail_desagrupa" style="display:none;">
			<img src='../img/icon_agrupa_titulos.gif' onClick="agruparTitulos()" style="cursor:pointer;" alt='<?php echo(getTText("agrupar_titulo",C_NONE))?>' title='<?php echo(getTText("agrupar_titulo",C_NONE))?>' border="0">
		</div>
		</th> <!-- AGRUPAR -->
		<th width="01%"></th> <!-- RECIBO -->
		<th width="01%"></th> <!-- LANÇAMENTOS -->
		<th width="01%"></th> <!-- CALC JUROS -->
		<th width="04%"><?php echo(getTText("cod",C_NONE))?></th>
		<th width="05%" class="sortable"><?php echo(getTText("documento",C_NONE))?></th>
		<th width="10%" class="sortable"><?php echo(getTText("situacao",C_NONE))?></th>
		<th width="08%" class="sortable-currency"><?php echo(getTText("valor",C_NONE))?></th>
		<th width="08%" class="sortable-currency"><?php echo(getTText("pago",C_NONE))?></th>
		<th width="08%" class="sortable-currency"><?php echo(getTText("saldo",C_NONE))?></th>
		<th width="10%" class="sortable-date-dmy"><?php echo(getTText("emissao",C_NONE))?></th>
		<th width="10%" class="sortable-date-dmy"><?php echo(getTText("vcto",C_NONE))?></th>
		<th width="10%" class="sortable"><?php echo(getTText("pedido",C_NONE))?></th>
		<th width="30%" class="sortable"><?php echo(getTText("historico",C_NONE))?></th>
	</tr>
	</thead>
   	<tbody>
	<?php foreach($objResult as $objRS){?>
	<?php 
		// PARA CADA LINHA, MONTA O NOME DO SACADO
		$intQtdeTitulos++;
		$strSACADO  = "";
		$strSACADO .= getValue($objRS,"razao_social");
		$strSACADO .= (getValue($objRS,"cnpj") != "") ? " (CNPJ: ".getValue($objRS,"cnpj").")" : "";
	?>
		<tr>
			<td align="center"><a href='javascript:void(0);' onClick="AbreJanelaPAGE('../modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','750','580');" style="cursor:pointer"><img src='../img/icon_boleto.gif' alt='ver boleto' border='0'></a></td>
			<td align="center">
				<?php 
				// Busca ao menos uma ocorrencia de que o titulo corrente 
				// é tido como referencia como agrupador para outro titulo 
				// qualquer. Caso ele seja agrupador, significa que é um 
				// titulo novo de uma agrupamento.
				try{
					$strSQL = "	SELECT fin.cod_agrupador FROM fin_conta_pagar_receber as fin WHERE fin.cod_agrupador = ".getValue($objRS,"cod_conta_pagar_receber");
					$objResult2 = $objConn->query($strSQL);
				}
				catch(PDOException $e){
					mensagem("err_sql_titulo","err_sql_desc",$e->getMessage,"erro",1,"");
					die();
				}
				
				$boolAgrupador = (($objResult2->rowCount()) > 0)&&(getValue($objRS,"situacao") == "aberto")&&((getValue($objRS,"vlr_pago") == "")||(getValue($objRS,"vlr_pago") == 0));
				// Conta o número de linhas obtidas > 0 - é um titulo resultante de um agrupamento
				
				if($boolAgrupador){?>
					<img src="../img/icon_desagrupar.gif" title="Desagrupar" border="0" style="cursor:pointer;" onClick="location.href='STdesagrupatitulo.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>&var_cod_pj=<?php echo($intCodDado);?>&var_location=../modulo_PainelPJ/STTitulos.php';">
				<?php }	else{?>
					<img src="../img/icon_desagrupar_off.gif" title="Desagrupar" border="0">
				<?php }?>
			</td>
			<td align="center">
			<?php if((getValue($objRS,"situacao") == "aberto") && (getValue($objRS,"vlr_conta") > 0)){ $intQtdeTitulos++;?>
				<input type="checkbox" name="var_cod_conta_pagar_receber[]" value="<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>" class="inputclean" style="margin:0px;">
			<?php } else{?>
				<input type="checkbox" class="inputclean" style="margin:0px;" disabled="disabled" />
			<?php }?>
			</td>
			<td align="center">
			<?php if(((getValue($objRS,"situacao")) == "lcto_total")||((getValue($objRS,"situacao")) == "lcto_parcial")){ ?>
				<img src="../img/icon_recibo.gif" title="Recibo" onClick="showDetailGrid('<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','../modulo_FinContaPagarReceber/STifrrecibos.php?var_cod_resize=<?php echo(request("var_chavereg"));?>&var_cod_conta_pagar_receber=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','cod_conta_pagar_receber');" border="0" style="cursor:pointer;">
				<!--a href='javascript:void(0);' onClick="AbreJanelaPAGE('../modulo_FinContaPagarReceber/STshowrecibonormal.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>&var_nome_sacado=<?php echo($strSACADO);?>','750','580');"><img src='../img/icon_recibo.gif' alt='RECIBO' border='0'></a-->
			<?php } else{ ?>
				<img src="../img/icon_recibo_off.gif" border="0" />
			<?php } ?>
			</td>
			<td align="center"><a onClick="showDetailGrid('<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>','STifrlancamento.php','cod_conta_pagar_receber')" style="cursor:pointer"><img src="../img/icon_ver_lancamento.gif" alt="ver lançamentos" border="0"></a></td>
			<td align="center">
			<?php
				if (!$boolAgrupador) {
					$strIcone = "../img/icon_calc_juros_outro.gif";
					//adicionado ao IF a liberação das guias assistencias, tendo em vista o andamento do chamado nro 24314
					//no o aless informou que estavam equiparadas as telas internas do contas a pagar e do painel pj no que se refere aos titulos.
					//by GS - 22/07/2014
					if ((stripos(getValue($objRS,"historico"), "sindica") !== false) || (stripos(getValue($objRS,"historico"), "guias sindicais") !== false) || (stripos(getValue($objRS,"historico"), "assistenc") !== false)) $strIcone = "../img/icon_calc_juros.gif";
					
					//Só exibe se for para realmente uma SINDICAL
					if ($strIcone != "../img/icon_calc_juros_outro.gif") {
						?>
						<a href="../modulo_PainelPJ/STCalcJurosTituloGuia.php?var_chavereg=<?php echo(getValue($objRS,"cod_conta_pagar_receber"));?>">
						<img src="<?php echo($strIcone);?>" title="<?php echo(getTText("calc_juros_guia",C_NONE));?>" alt="<?php echo(getTText("calc_juros_guia",C_NONE));?>" border="0" style="cursor:pointer;">						</a>
						<?php
					}
				}
				else {
					//$strIcone = "../img/icon_calc_juros_off.gif";
					?>
					<!--<img src="<? //=$strIcone;?>" title="<? //=getTText("calc_juros_guia",C_NONE);?>" alt="<? //=getTText("calc_juros_guia",C_NONE);?>" border="0" style="cursor:pointer;">//-->
					<?php
				}
			?>
			</td>
			<td style="text-align:center;"><?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?></td>
			<td style="text-align:center;"><?php echo(getValue($objRS,"num_documento")); ?></td>
			<td style="text-align:center;text-transform:uppercase;"><?php echo(getValue($objRS,"situacao")); ?></td>
			<td style="text-align:right;"><?php echo(number_format((double) getValue($objRS,"vlr_conta"),2,",","")); ?></td>
			<td style="text-align:right;"><?php echo(number_format((double) getValue($objRS,"vlr_pago"),2,",","")); ?></td>
			<td style="text-align:right;"><?php echo(number_format((double) getValue($objRS,"vlr_saldo"),2,",","")); ?></td>
			<td style="text-align:center;"><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false)); ?></td>
			<td style="text-align:center;"><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false)); ?></td>
			<td style="text-align:center;"><?php echo(getValue($objRS,"cod_pedido"));?></td>
			<td style="text-align:left;"><?php echo(getValue($objRS,"tipo_documento").": ".getValue($objRS,"historico"));?></td>
		</tr>
		<tr id="detailtr_<?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?>" bgColor="#FFFFFF" style="display:none;" class="iframe_detail">
			<td colspan="16"><iframe name="<?php echo getsession(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?>" id="<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?>" width="99%" src="" frameborder="0" scrolling="no"></iframe></td>
		</tr>
	<?php }?>
    </tbody>
	<?php if($intQtdeTitulos > 1) {?>
			<script type="text/javascript" language="javascript">
				document.getElementById("detail_desagrupa").style.display = "block";
			</script>
			<tfoot style="background-color:<?php echo(CL_CORBAR_GLASS_2);?>;" >
			<tr>
				<td colspan="2" style="background-color:<?php echo(CL_CORBAR_GLASS_2);?>;"></td>
				<td align="center" valign="middle" style="background-color:<?php echo(CL_CORBAR_GLASS_2);?>;">
					<img src='../img/icon_agrupa_titulos.gif' onClick="agruparTitulos()" style="cursor:pointer;" alt='<?php echo(getTText("agrupar_titulo",C_NONE))?>' title='<?php echo(getTText("agrupar_titulo",C_NONE))?>' border="0">
				</td>
				<td colspan="13" style="background-color:<?php echo(CL_CORBAR_GLASS_2);?>;"></td>
			</tr>
			</tfoot>
	<?php }?>
<?php } else{?>
    <tbody>
		<tr><td colspan="16" style="text-align:center;"><div style="padding-top:2px;padding-bottom:2px;font-style:color:#CCC;"><?php echo(getTText("msg_sem_titulos_para_exibir",C_NONE)); ?></div></td></tr>
   	</tbody>
<?php }?>
</table>
</form>
<?php
	$objResult->closeCursor();
}
		?><br>
	</td>
 </tr>
</table>
<?php athEndFloatingBox(); ?>
</body>
</html>
<?php
}
else {
	echo(mensagem("err_selec_empresa_titulo","err_selec_empresa_desc","","","erro",1));
}
$objConn = NULL;
?>
