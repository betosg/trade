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


$id_evento		= getsession(CFG_SYSTEM_NAME."_id_evento");
$stridmercado 	= getsession("id_empresa");
$dateDtFat 		= request("var_dataemi");
$dateDtIni 		= request("var_dtinicio");
$dateDtFim 		= request("var_dtfim");
$strPedIni      = request("var_ped_ini");
$strPedFim      = request("var_ped_fim");

$objConn = abreDBConn(CFG_DB);


$strPopulate = "yes";
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE"); //Verificação de acesso do usuário corrente
	
	// SQL PADRÃO DA LISTAGEM - DAS CREDENCIAIS
	try{
		$strSQL = "SELECT
				  	 ped_pedidos.idpedido
					,ped_pedidos.cgcmfpe                                  
					,tmp_faturamento.vencimentoped  AS vencimento    
					,ped_pedidos.codigope 
					,ped_pedidos.razaope as razaonf 
					,tmp_faturamento.valorpar 
					,tmp_faturamento.nroduplicata 
					,ped_nota_fiscal.valornf 
                  FROM
					 tmp_faturamento LEFT JOIN 	ped_pedidos 	ON  (tmp_faturamento.idpedido 	= ped_pedidos.idpedido 		AND tmp_faturamento.idmercado = ped_pedidos.idmercado)
									 LEFT JOIN  cad_cadastro    ON  (ped_pedidos.idmercado = cad_cadastro.idmercado  AND ped_pedidos.codigope  = cad_cadastro.codigo)			
									 LEFT JOIN  cad_empresa 	ON (ped_pedidos.idmercado 		= cad_empresa.idmercado)
									 LEFT JOIN  ped_nota_fiscal ON (tmp_faturamento.idmercado = ped_nota_fiscal.idmercado AND tmp_faturamento.nronf = ped_nota_fiscal.idnotafiscal) 
				WHERE ped_pedidos.razaope IS NOT NULL
			UNION
				  SELECT ped_servico.idservico
					  ,ped_servico.cgcmfse					
					  ,tmp_faturamento.vencimentoped 
					  ,ped_servico.idmontse 
					  ,ped_servico.razaose as razaonf 
					  ,tmp_faturamento.valorpar 
					  ,tmp_faturamento.nroduplicata 
					  ,ped_nota_fiscal.valornf 				
				   FROM tmp_faturamento LEFT JOIN ped_servico ON (tmp_faturamento.idpedido	= ped_servico.idservico AND tmp_faturamento.idmercado = ped_servico.idmercado)
									 LEFT JOIN  ped_nota_fiscal ON (tmp_faturamento.idmercado = ped_nota_fiscal.idmercado AND tmp_faturamento.nronf = ped_nota_fiscal.idnotafiscal) 
					WHERE ped_servico.razaose IS NOT NULL";
		//echo ($strSQL);
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
<style type="text/css">
	/* suas adaptações css aqui */
	.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
	body{ margin:10px;background-color:#FFFFFF; } ul{ margin-top:0px;margin-bottom:0px; } li{ margin-left:0px; }
</style>

<script type="text/javascript">
function linkPage(prLink){
	var strLink = (prLink == "") ? "#" : prLink;
	location.href = strLink;
}

function imprime(prPedido){						
	strLink = '../modulo_ASLWRelatorio/STcarta_fechamento_faturamento.php?var_dtinicio=<?php echo $dateDtIni; ?>&var_dtfim=<?php echo $dateDtFim; ?>&var_duplicata='+prPedido;	
	location.href = strLink;	
}

function imprimeTodas(){	
	strLink = '../modulo_ASLWRelatorio/STcarta_fechamento_faturamento.php?var_dtinicio=<?php echo $dateDtIni; ?>&var_dtfim=<?php echo $dateDtFim; ?>';	
	location.href = strLink;	
}	
</script>
</head>
<body bgcolor="#FFFFFF">
	<!-- MENU PURE CSS SUPERIOR . COMENTÁRIOS DE UTILIZAÇÃO NO INTERIOR DO CONJUNTO DE FUNÇÕES MENU CSS -->
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td width="19%" align="left">			
			<?php
				athBeginCssMenu();
					athCssMenuAddItem("","_self",getTText("carta",C_TOUPPER),1);
					athBeginCssSubMenu();	
						athCssMenuAddItem("javascript:imprimeTodas()",
										  "_self",getTText("imprimir_todas",C_UCWORDS));						
					athEndCssSubMenu();
				athEndCssMenu();		
			?>			</td>
			<td width="81%">
		</td>
		</tr>
	</table>
	<!-- MENU PURE CSS SUPERIOR . FIM -->
	
	<?php
	if(($objResult->rowCount() == 0) and ($objResult->rowCount() == 0) ){
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("no_contato",C_NONE),"","aviso",1,"","","");
	} else {
	?>
	
	<!-- TABLESORT DA MINI APP ink
	. INICIO -->
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
      <thead>
        <tr>
          <th width="1%"></th>
          <th width="11%" class="sortable" nowrap><?php echo(getTText("nroduplicata",C_TOUPPER));?></th>
          <th width="48%" class="sortable" nowrap><?php echo(getTText("razaonf",C_TOUPPER));?></th>
          <th width="20%" class="sortable" nowrap><?php echo(getTText("cgcmfnf",C_TOUPPER));?></th>
          <th width="10%" class="sortable" nowrap><?php echo(getTText("vlr",C_TOUPPER));?></th>
		  <th width="10%" class="sortable" nowrap><?php echo(getTText("venc",C_TOUPPER));?></th>
        </tr>
      </thead>
      <tbody>
        <?php 	 
				 $int_quant = 0;
				 foreach($objResult as $objRS){   
				 $int_quant++;	
		?>
        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
          <td align="center" style="vertical-align:middle;">
		  				<img src="../img/icon_impressao_darf.gif" alt="<?php echo(getTText("imprimir",C_NONE));?>" 
						 title="<?php echo(getTText("imprimir",C_NONE));?>"
						 border="0" style="cursor:pointer;"
						 onclick="imprime('<?php echo getValue($objRS,"nroduplicata"); ?>');" /> </td>
          <td align="left"><?php echo(getValue($objRS,"nroduplicata"));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRS,"razaonf")));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRS,"cgcmfpe")));?></td>
          <td align="right"><?php echo(number_format((double) getValue($objRS,"valorpar"),2,',','.'));?></td>
		  <td align="left"><?php echo(Ddate("PTB",getValue($objRS,"vencimento"),""));?></td>
		</tr>
		<?php } ?>
		
		       
      </tbody>
      <!-- TFOOT TABLESORT . INICIO #CASO SEJA NECESSÁRIO UTILIZAR ALGUM TIPO DE AÇÃO NO FOOTER, DESCOMENTAR -->
      <tfoot>
        <tr bgcolor="#DDDDDD">
          <td colspan="8" align="right">Total: <?php echo $int_quant;?></td>
        </tr>
      </tfoot>
      <!-- TFOOT TABLESORT . FIM -->
    </table>
	<!-- TABLESORT DA MINI APP . FIM -->
    <?php } ?>
</body>
	
</html>
<?php
	// SETA O OBJETO DE CONEXÃO COM BANCO PARA NULO
	// ALÉM DISSO, FECHA O CURSOR DO RESULTSET
	$objConn = NULL;
	$objResult->closeCursor();
?>