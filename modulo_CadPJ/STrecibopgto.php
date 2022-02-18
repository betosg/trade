<?php
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intCodTit    = request("var_chavereg");
//$strValor     = request("var_valor");
//$strValorPago = request("var_valor_pago");

$objConn = abreDBConn(CFG_DB);
// busca os dados da entidade para
// preenchimento do cabecalho da nota
try{
	$strSQL = "
			SELECT 
				id_var, valor 
			FROM 
				sys_var_entidade
			WHERE
				sys_var_entidade.id_var = 'razao_social'
			OR
				sys_var_entidade.id_var = 'lc_logradouro'
			OR
				sys_var_entidade.id_var = 'lc_num'
			OR
				sys_var_entidade.id_var = 'lc_comp'
			OR
				sys_var_entidade.id_var = 'lc_cidade'
			OR
				sys_var_entidade.id_var = 'lc_estado'
			OR
				sys_var_entidade.id_var = 'lc_cep'
			OR
				sys_var_entidade.id_var = 'cnpj'
			ORDER BY ordem";
	
	$objResultEnt = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}


// busca produto e cod_pedido conforme
// cod_conta_pagar_receber informado
/*try{
	$strSQL = "
			SELECT  
				prd_pedido.cod_pedido,
				prd_produto.rotulo
			FROM
				prd_pedido,
				prd_produto,
				fin_conta_pagar_receber
			WHERE	
				fin_conta_pagar_receber.cod_pedido = prd_pedido.cod_pedido
			AND
				prd_produto.cod_produto = prd_pedido.it_cod_produto
			AND
				fin_conta_pagar_receber.cod_conta_pagar_receber = '" . $intCodTit . "'";
   
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}*/


try{
	$strSQL = "
			SELECT  
				fin_lcto_ordinario.cod_lcto_ordinario,
				fin_lcto_ordinario.vlr_lcto,
				fin_lcto_ordinario.vlr_desc,
				fin_lcto_ordinario.vlr_juros,
				fin_lcto_ordinario.vlr_multa,
				fin_lcto_ordinario.dt_lcto,
				fin_conta_pagar_receber.cod_conta_pagar_receber,
				fin_lcto_ordinario.tipo_documento,
				fin_conta_pagar_receber.vlr_desc AS desc,
				fin_conta_pagar_receber.vlr_saldo,
				fin_conta_pagar_receber.vlr_pago,
				fin_conta_pagar_receber.vlr_conta
			FROM
				fin_lcto_ordinario,
				fin_conta_pagar_receber
			WHERE	
				fin_conta_pagar_receber.cod_conta_pagar_receber = fin_lcto_ordinario.cod_conta_pagar_receber
			AND
				fin_conta_pagar_receber.cod_conta_pagar_receber = " . $intCodTit;
   
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}


// após buscar dados do pedido e produto
// fetch e passagem dos dados para var
/*if(($objResult->rowCount()) > 0){
	$intQtdeItens = $objResult->rowCount();
	$objRS = $objResult->fetch();
	$intCodPedido = (getValue($objRS,"cod_pedido") != "") ? getValue($objRS,"cod_pedido") : "";
	$strNomeProd  = (getValue($objRS,"rotulo") != "") ? getValue($objRS,"rotulo") : "";
}
else{
	mensagem("err_sql_titulo","err_sql_desc","Código inválido. Tente novamente.","","erro",1);
	die();
}*/
?>
<html>
<head>
	 <title><?php echo(CFG_SYSTEM_NAME);?></title>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	 <link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	 <script>
		window.onload = function (){
			self.resizeTo(400,600);
			window.print(); 
		}
	 </script>
	<style>
		td{	font-family:Arial Narrow; padding-left:5px;	}
		.localidade{ font-size:13px; }
		.texto{	font-size:13px;	}
		.texto_ft{font-size:10px;	}
	</style>
</head>
<body bgcolor="#FFFFFF" style='margin:0'>
	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
		<tr>
			<td align="left" class='localidade'>
				<?php
					// popula o cabecalho do recibo
					foreach($objResultEnt as $objRSEnt){
						$strVarEntidade = getValue($objRSEnt,"id_var");
						switch($strVarEntidade){
							case("razao_social"):
								$strRazaoSocial = getValue($objRSEnt,"valor");
								break;
							case('cnpj'):
								$intCnpj = getValue($objRSEnt,"valor");
								break;
							case('lc_logradouro'):
								$strLogradouro = getValue($objRSEnt,"valor");
								break;
							case('lc_num'):
								$intNum = getValue($objRSEnt,"valor");
								break;
							case('lc_comp'):
								$strComp = getValue($objRSEnt,"valor");
								break;
							case('lc_cidade'):
								$strCidade = getValue($objRSEnt,"valor");
								break;
							case('lc_estado'):
								$strEstado = getValue($objRSEnt,"valor");
								break;
							case('lc_cep'):
								$intCep = getValue($objRSEnt,"valor");
								break;
							default: break;
						}
					}
					echo substr($strRazaoSocial,0,50)."<br />";
					echo $strLogradouro.", ".$intNum." - ".$strComp."  ".$strCidade." / ".$strEstado."<br/>CEP ".$intCep." - CNPJ ".$intCnpj;
				?>
            </td>
		</tr>
        <!-- <tr><td height="1" bgcolor="#666666"></td></tr> -->
		<tr><td height="10"></td></tr>
		<tr>
		<td colspan='2' align="left" valign='top' class='localidade'>
		RECIBO<br><?php echo(date("d/m/y H:i",strtotime(now())));?>
		</td>
		</tr>
		<tr><td height="20"></td></tr>
        <tr>
			<td>
				<table width="300"  cellpadding="0" cellspacing="0" border="0">
                	<tr><td colspan="5" height="1" bgcolor="#666666"></td></tr>
					<tr><td height="20">Lançamentos</td></tr>
			        <tr><td colspan="5" height="1" bgcolor="#666666"></td></tr>
				</table>
			</td>
		</tr>
		<tr>
            <td colspan="2" align="left">
                <table width="300"  cellpadding="0" cellspacing="0" border="0">
                   <?php 
						// calcula os lancamentos, efetua as
						// somatorias adequadas e foreach dos
						// resultados em tela
						foreach($objResult as $objRS){
							$intVlrLcto    = (getValue($objRS,"vlr_lcto") == "") ? 0 : getValue($objRS,"vlr_lcto");
							$intVlrJuros   = (getValue($objRS,"vlr_juros") == "") ? 0 : getValue($objRS,"vlr_juros");
							$intVlrMulta   = (getValue($objRS,"vlr_multa") == "") ? 0 : getValue($objRS,"vlr_multa");
							$intVlrDesc    = (getValue($objRS,"vlr_desc") == "") ? 0 : getValue($objRS,"vlr_desc");
							//calcula o valor final que será exibido ao final da linha
							$intValorFinal = (($intVlrLcto + $intVlrJuros + $intVlrMulta) - $intVlrDesc);
					?>
                    <tr>
                      <td width="10%"  align="left"   valign="top" class="texto" nowrap="nowrap"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_lcto"),false));?>&nbsp;</td>
					  <td width="10%" align="center" valign="top" class="texto" nowrap="nowrap">-</td>
					  <td width="10%"  align="center" valign="top" class="texto" nowrap="nowrap"><?php echo(getValue($objRS,"cod_lcto_ordinario"));?>&nbsp;</td>
					  <td width="10%" align="center"   valign="top" class="texto" nowrap="nowrap">-</td>
					  <td width="14%"  align="center"   valign="top" class="texto" nowrap="nowrap"><?php echo(getValue($objRS,"tipo_documento"));?>&nbsp;</td>
					  <td width="10%" align="center"   valign="top" class="texto" nowrap="nowrap">-</td>
                      <td width="1%"  align="right"  valign="top" class="texto" nowrap="nowrap">R$&nbsp;<?php echo(number_format((double) $intValorFinal,2,',','.')); ?></td></tr>
		            <tr><td colspan="10" height="1" bgcolor="#666666"></td></tr>
  		<?php } ?>
					<tr><td>&nbsp;</td></tr>				
				</table>
            </td>
        </tr>
	    <tr>
			<td colspan="2" align="left">
				<table cellpadding="0" cellspacing="0" width="300" border="0">
				<tr><td colspan="5" height="1" bgcolor="#666666"></td></tr>
                    <tr><td colspan="2" height="3"></td></tr>
					<tr>
					  <td width="20%" align="left"  valign='top' class='texto' nowrap="nowrap"><?php echo(getTText("total_lancado",C_TOUPPER));?></td>
				      <td width="70%"  align="right"  valign='top' class='texto' nowrap="nowrap"> &nbsp;<b>R$ <?php echo(number_format((double) getValue($objRS,"vlr_pago"), 2, ',', '')); ?></b></td>
				  </tr>
					<tr>
					  <td width="10%" align="left"  valign='top' class='texto' nowrap="nowrap"><?php echo(getTText("total_desc",C_TOUPPER));?></td>
				      <td width="90%"  align="right"  valign='top' class='texto' nowrap="nowrap"> &nbsp;R$ <?php echo(number_format((double) getValue($objRS,"desc"), 2, ',', '')); ?></td>
				    </tr>
				  <tr>
					  	<td width="10%" align="left"  valign='top' class='texto' nowrap="nowrap">
						<?php 
							echo(getTText("total_titulo",C_TOUPPER));
						?>
						</td>
				      	<td width="90%"  align="right"  valign='top' class='texto' nowrap="nowrap">
					  	&nbsp;R$ 
						<?php 
					  		$strValor = number_format((double) getValue($objRS,"vlr_conta"), 2);
							$strValor = str_replace(",", "", $strValor);
							$strValor = str_replace(".", ",", $strValor);
							echo($strValor);
						?>
						</td>
				    </tr>
                  <tr><td colspan="2" height="10"></td></tr>
				</table>
			</td>
		</tr>
        <!-- <tr><td height="1" bgcolor="#666666"></td></tr> -->
		<tr>
			<td colspan="2" align="left">
				<table cellpadding="0" cellspacing="0" width="300" border="0">
                    <tr><td height="1" bgcolor="#666666"></td></tr>
					<tr>
					  <td align="right"  valign='top' class='texto_ft'>
					  <?php
						$objResult->closeCursor();

						try{
							/* BUSCA os dados do último lançamento de pagamento */
							$strSQL  = " SELECT sys_usr_ins, sys_dtt_ins
										   FROM fin_lcto_ordinario 
										  WHERE cod_conta_pagar_receber = " . $intCodTit . " ORDER BY sys_dtt_ins DESC ";
							   
							$objResult = $objConn->query($strSQL);
                    		$objRS = $objResult->fetch();
							$strFT = strtoupper (str_pad($intCodTit, 5, "0", STR_PAD_LEFT) . " - " . getValue($objRS,"sys_usr_ins") . " - " . date("d/m/y H:i",strtotime(getValue($objRS,"sys_dtt_ins"))) . " - pgto" );
						} catch(PDOException $e) {
							mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
							die();
						}
						$objResult->closeCursor();
						echo($strFT);
					  ?>
                      </td>
				    </tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>