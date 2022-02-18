<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// Recebe o COD_PF para BUSCA DE RELAÇÕES ATIVAS
$intVlrCampoChaveDetail  = request("var_chavereg");
$strNomeCampoChaveDetail = request("var_field_detail");
//$intQtdeTitulos 		 = 0;

$objConn = abreDBConn(CFG_DB);

try{
	// Busca todas as relações possíveis
	// em que a determinada PF faça parte
	// LISTAGEM EM TABLESORT ABAIXO
	$strSQL  = "
			SELECT
				  cad_pf.cod_pf
				, cad_pj.cod_pj
				, cad_pf.nome
				, cad_pj.razao_social
				, cad_pf.cpf
				, cad_pj.cnpj
				, relac_pj_pf.funcao
				, relac_pj_pf.categoria
				, relac_pj_pf.departamento
				, relac_pj_pf.dt_admissao
				, relac_pj_pf.dt_demissao
				, relac_pj_pf.obs
			FROM
				relac_pj_pf
			LEFT JOIN cad_pf ON (relac_pj_pf.cod_pf = cad_pf.cod_pf)
			INNER JOIN cad_pj ON (relac_pj_pf.cod_pj = cad_pj.cod_pj)
			WHERE
				cad_pf.cod_pf = ".$intVlrCampoChaveDetail;
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}

// CASO NENHUM resultado seja encontrado
// entao exibe mensagem de consulta vazia
if($objResult->rowCount() == 0) {
	mensagem("alert_consulta_vazia_titulo",
	"alert_consulta_vazia_desc", "", "","aviso",1,"","");
	die();
}

// TRATAMENTO das cores de linhas do fundo
// da grade, para alteração de COR caso a
// linha corrente seja a RELAÇÃO ATIVA
/* bgcolor = <?php echo(getValue($objRS,"dt_demissao") == "") ? CL_CORLINHA_2 : CL_CORLINHA_1 );?> */

$boolVerifyChecks = false;

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
    body{ padding:10px; }
    ul{ margin-top: 0px; margin-bottom: 0px; }
    li{ margin-left: 0px; }
</style>
</head>
<body style="margin:0px 0px 10px 0px;" bgcolor="<?php echo(CL_CORLINHA_1);?>">
	<form name='frmSizeBody'>
		<input type='hidden' value='' name='sizeBody'>
		<input type='hidden' value='<?php echo($intVlrCampoChaveDetail); ?>' name='codAvo'>
	</form>
	<form name="formboleto" action="STAgrupaTitulo.php" method="post" style="margin:0px;">
		<input type="hidden" name="var_chavereg" value="<?php echo($intVlrCampoChaveDetail); ?>">
		<input type="hidden" name="var_url_retorno" value="STifrfinanceiro.php">
		<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td align="left">
			<?php	
				athBeginCssMenu();
					athCssMenuAddItem("","_self",getTText("empresas",C_TOUPPER),0);
					// athBeginCssSubMenu();
						// athCssMenuAddItem("STverifycpf.php?var_flag_inserir=INS_CARD&var_chavereg=".$intCodDado,"_self",getTText("ins_colab_card",C_NONE));
						// athCssMenuAddItem("STverifycpf.php?var_flag_inserir=INS_HOMO&var_chavereg=".$intCodDado,"_self",getTText("ins_colab_homo",C_NONE));
						// athCssMenuAddItem("STverifycpf.php?var_flag_inserir=INS_HOMO&var_chavereg=".$intCodDado,"_self",getTText("ins_colab_homo_fast_bla bla bla bla bla bla",C_NONE));
					// athEndCssSubMenu();
				athEndCssMenu();		
			?>
			</td>
		</tr>
		</table>
		<table bgcolor="<?php echo(CL_CORLINHA_1);?>" style="width:100%;  margin-bottom:0px;" class="tablesort">
			<!-- 
				POSSIBILIDADES DE TIPOS DE SORT
				class="sortable-date-dmy"
				class="sortable-currency"
				class="sortable-numeric"
				class="sortable" 
			-->
			<thead>
				<tr>
					<th width="1%"></th><!-- EDIT -->
					<th width="1%"></th><!-- VIEW -->
					<th width="1%"></th><!-- PAINEL -->
					<th width="8%"class="sortable-numeric" nowrap><?php echo(getTText("cod_pj",C_TOUPPER));?></th>
					<th width="27%"class="sortable" nowrap><?php echo(getTText("razao_social",C_TOUPPER));?></th>
					<th width="12%"class="sortable-numeric" nowrap><?php echo(getTText("cnpj",C_TOUPPER));?></th>
					<th width="20%"class="sortable" nowrap><?php echo(getTText("funcao",C_TOUPPER));?></th>
					<th width="14%"class="sortable" nowrap><?php echo(getTText("categoria",C_TOUPPER));?></th>
					<th width="8%"class="sortable-date-dmy" nowrap><?php echo(getTText("dt_admissao",C_TOUPPER));?></th>
					<th width="8%" class="sortable-date-dmy" nowrap><?php echo(getTText("dt_demissao",C_TOUPPER));?></th>
					<th width="1%" nowrap></th>
				</tr>
			</thead>
			<tbody>
	<?php
		/*$Ct=1;
		$dblValotTotal = 0;
		$intCodContaPagarReceber = "";
		$strCOLOR = CL_CORLINHA_2;
		$boolAgrupar = true;
		$strDescricao = '';
		$strDescricaoAux = '';*/

		// START da listagem de PJs RELACIONADAS
		// com aquele respectivo COD_PF relação
		foreach($objResult as $objRS){
			$boolAgrupar = true;
			$strIdFrame = CFG_SYSTEM_NAME."_detailiframe_".getValue($objRS,"cod_conta_pagar_receber");
		?>
			<tr bgcolor=<?php echo((getValue($objRS,"dt_demissao") == "") ? CL_CORLINHA_2 : "'".CL_CORLINHA_1."'style='color:".CL_CORLINHA_2."'");?>>
					<td width="1%" align="center" valign="middle" style="vertical-align:middle;">
						<a href="../modulo_CadPJ/index.php?var_redirect=insupddelmastereditor.php<PARAM_QM>var_oper=UPD<PARAM_EC>var_chavereg=<?php echo(getValue($objRS,"cod_pj"));?>" target="<?php echo(CFG_SYSTEM_NAME."_frmain");?>" style="border:none;"><img src="../img/icon_write.gif" alt="<?php echo(getTText("editar",C_NONE));?>" title="<?php echo(getTText("editar",C_NONE));?>" /></a>
					</td>
					<td width="1%" align="center" valign="middle" style="vertical-align:middle;">
						<a href="../modulo_CadPJ/index.php?var_redirect=insupddelmastereditor.php<PARAM_QM>var_oper=VIE<PARAM_EC>var_chavereg=<?php echo(getValue($objRS,"cod_pj"));?>" target="<?php echo(CFG_SYSTEM_NAME."_frmain");?>"><img src="../img/icon_zoom.gif" alt="<?php echo(getTText("visualizar",C_NONE));?>" title="<?php echo(getTText("visualizar",C_NONE));?>" /></a>
					</td>
					<td width="1%" align="center" valign="middle" style="vertical-align:middle;">
						<a href="../modulo_CadPJ/STactivate.php?var_chavereg=<?php echo(getValue($objRS,"cod_pj"));?>" target="<?php echo(CFG_SYSTEM_NAME."_frmain");?>"><img src="../img/icon_painel.gif" alt="<?php echo(getTText("painel_pj",C_NONE));?>" title="<?php echo(getTText("painel_pj",C_NONE));?>" /></a>
					</td>
					<td style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_pj")); ?></td>
					<td style="vertical-align:middle;"><?php echo(getValue($objRS,"razao_social")); ?></td>
					<td style="vertical-align:middle;"><?php echo(getValue($objRS,"cnpj")); ?></td>
					<td style="vertical-align:middle;"><?php echo(getValue($objRS,"funcao")); ?></td>
					<td style="vertical-align:middle;"><?php echo(getValue($objRS,"categoria")); ?></td>
					<td style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_admissao"),false)); ?></td>
					<td style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_demissao"),false)); ?></td>
					<td style="vertical-align:middle;"><?php if(getValue($objRS,"obs") != "") {echo("<img src='../img/icon_obs.gif' title='".getValue($objRS,"obs")."' alt='" . getValue($objRS,"obs") . "' />");}?></td>
				</tr>
		<?php }	?>
			</tbody>
		</table>
	</form>
	<br/>
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
