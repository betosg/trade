<?php
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intVlrCampoChaveDetail  = request("var_chavereg");
$strNomeCampoChaveDetail = request("var_field_detail");

$objConn = abreDBConn(CFG_DB);

try {

	$strSQL = "	SELECT 
					lct.cod_lcto_ordinario,
					lct.cod_conta_pagar_receber,
					lct.vlr_lcto,
					lct.dt_lcto,
					lct.historico,
					pcont.cod_reduzido,
					lct.obs,
					lct.sys_dtt_ins,
					lct.sys_usr_ins,
					lct.tipo_documento,
					lct.extra_documento
				FROM 
					fin_lcto_ordinario lct
				JOIN fin_conta_pagar_receber cont ON lct.cod_conta_pagar_receber = cont.cod_conta_pagar_receber AND cont.cod_conta_pagar_receber = ".$intVlrCampoChaveDetail."
				LEFT JOIN fin_plano_conta pcont ON lct.cod_plano_conta = pcont.cod_plano_conta
				ORDER BY cod_conta_pagar_receber DESC, dt_lcto DESC";

				
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	?>
		<script>
			window.onload = function(){
				window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intVlrCampoChaveDetail); ?>').style.height = 0;
				window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intVlrCampoChaveDetail); ?>').style.height = document.body.scrollHeight;
			
				if(window.parent.document.frmSizeBody){
					var codAvo = window.parent.document.frmSizeBody.codAvo.value;
					window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_'+codAvo).style.height = 0;
					window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_'+codAvo).style.height = window.parent.document.body.scrollHeight;
				}

			}
		</script>
	<?php
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

if($objResult->rowCount() == 0) {
	?>
		<script>
			window.onload = function(){
				window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intVlrCampoChaveDetail); ?>').style.height = 0;
				window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intVlrCampoChaveDetail); ?>').style.height = document.body.scrollHeight;
			if(window.parent.document.frmSizeBody){	
				var codAvo = window.parent.document.frmSizeBody.codAvo.value;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_'+codAvo).style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_'+codAvo).style.height = window.parent.document.body.scrollHeight;
			}

				
			}
		</script>
	<?php
	mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc", "", "","aviso",1);
	die();
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
		ul{ margin-top: 0px; margin-bottom: 0px; }
		li{ margin-left: 0px; }
	 </style>
	 <script language="javascript">
		function removeLancamento(prCodLancamento){
			if(confirm("Tem certeza que deseja remover este lançamento?")){
				window.location = 'STremovelancamento.php?var_chavereg=' + <?php echo($intVlrCampoChaveDetail); ?> + '&var_cod_lcto_ordinario=' + prCodLancamento;
			}
		}
		
		function gerarBoleto(){
			var form = document.formboleto;
			var submeter = false;
			for(var i = 0; i < form.elements.length ; i++){
				if(form.elements[i].type == 'checkbox' && form.elements[i].disabled == false){
					if(form.elements[i].checked == true){
						submeter = true;
					}
				}
			}
			if(submeter){
				form.submit();
			} else {
				alert('Por Favor selecione algum pedido!');
			}
		}
		window.onload = function(){
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intVlrCampoChaveDetail); ?>').style.height = 0;
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intVlrCampoChaveDetail); ?>').style.height = document.body.scrollHeight;
			
			if(window.parent.document.frmSizeBody){	
				var codAvo = window.parent.document.frmSizeBody.codAvo.value;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_'+codAvo).style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_'+codAvo).style.height = window.parent.document.body.scrollHeight;
			}
		}
	 </script>
</head>
<body style="margin:0px 0px 10px 20px;" bgcolor="#CFCFCF">
<table align="center" cellpadding="0" cellspacing="1" style="width:100%">
	<thead>
		<tr>
			<!--th width="1%"></th-->
			<th width="18%" nowrap>Plano de Conta</th>
			<th width="10%" nowrap>Lançamento</th>
			<th width="10%" nowrap>Data</th>
			<th width="15%" nowrap>Ocorrência</th>
			<th width="10%" nowrap>Usuario</th>
			<th width="10%" nowrap>Tipo</th>
			<th width="5%"  nowrap>Histórico</th>
			<th width="20%" nowrap>Info Extra</th>
			<th width="1%"></th>
		</tr>
	</thead>
	<tbody>
	<?php
		$Ct=1;
		$dblValotTotal = 0;
		$strCOLOR = "";
		$strTituloAnt = "";
		$boolShowResult = true;

		foreach($objResult as $objRS){
		$strCOLOR = (($Ct++%2)==0)?"#FFFFFF":"#F5FAFA";
		
	?>
		<tr bgcolor=<?php echo($strCOLOR) ?>>	
				<!--td style="vertical-align:middle;" align="center"><img src="../img/icon_trash.gif" alt="deletar" border="0" style="cursor: pointer;" onClick="removeLancamento(<?php echo(getValue($objRS,"cod_lcto_ordinario")); ?>)"></td-->
				<td style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_reduzido")); ?></td>
				<td style="vertical-align:middle;"><?php echo(number_format((double) getValue($objRS,"vlr_lcto"),2,",","")); ?></td>
				<td style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_lcto"),false)); ?></td>
				<td style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),true)); ?></td>
				<td style="vertical-align:middle;" align="left"><?php echo(getValue($objRS,"sys_usr_ins")); ?></td>
				<td style="vertical-align:middle;" align="left"><?php echo(getValue($objRS,"tipo_documento")); ?></td>
				<td style="vertical-align:middle;" align="left"><?php echo(getValue($objRS,"historico")); ?></td>
				<td style="vertical-align:middle;" align="left"><?php echo(getValue($objRS,"extra_documento")); ?></td>
				<td style="vertical-align:middle;" align="center">
					<?php if(getValue($objRS,"obs") != ''){ ?>
						<img src="../img/icon_obs.gif" alt="<?php echo(getValue($objRS,"obs")); ?>" border="0" style="cursor: pointer;">
					<?php } ?>
				</td>
		</tr>
			<?php
		}
		?>
	</tbody>
</table>
</body>
</html>
<?php
	$objResult->closeCursor();
?>