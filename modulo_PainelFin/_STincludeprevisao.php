<?php 
$intNumLctos = request("var_limite_titulos");

$intNumLctos = ($intNumLctos != "") ? $intNumLctos : "100";

$strCorHeader = CL_CORBAR_GLASS_2; //"#D1D1D1"; //"#999999";

$strCorLinha1 = "#F2F2F2";
$strCorLinha2 = "#FFFFFF";

$strSQL = "	SELECT t2.nome
                 , t1.vlr_conta
                 , t1.historico
				 , current_date - t1.dt_vcto AS dias
				 , t1.dt_vcto 
            FROM fin_conta_pagar_receber t1
            INNER JOIN cad_pf t2 ON (t1.codigo = t2.cod_pf AND t1.tipo = 'cad_pf')
            WHERE t1.situacao = 'aberto'
			  AND dt_vcto < current_date
            ORDER BY t1.dt_vcto DESC, t2.nome ASC
			LIMIT " . $intNumLctos;
			
try{
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}		

if ($objResult->rowCount() > 0) {
	?>
	<form name="formstatic" method="post">
	<?php athBeginFloatingBox("100%","","
		<strong>
			Previsão de recebimentos e pagamentos - Últimos 
			<select name=\"var_limite_titulos\" onChange=\"document.formstatic.submit();\" style=\"width:50px; margin:0px; padding:0px;\">
				<option value=\"50\"" . (($intNumLctos == "50") ? " selected" : "") . ">50</option>
				<option value=\"100\"" . (($intNumLctos == "100") ? " selected" : "") . ">100</option>
				<option value=\"200\"" . (($intNumLctos == "200") ? " selected" : "") . ">200</option>
				<option value=\"500\"" . (($intNumLctos == "500") ? " selected" : "") . ">500</option>
			</select>
		</strong>",CL_CORBAR_GLASS_2); ?>
	</form>
	<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
	<tr bgcolor="<?php echo($strCorHeader)?>">
		<td colspan="6" align="left" class="texto_contraste_mdo"><div style="padding-top:1px; padding-bottom:1px; padding-left:10px; padding-right:4px;"><strong></strong></div></td>
	</tr>
	<tr bgcolor="<?php echo($strCorLinha1); ?>">
		<td align="left" style="padding:0px 4px; font-weight:bold;">Data vcto</td>
		<td align="left" style="padding:0px 4px 0px 10px; font-weight:bold;">Nome</td>
		<td align="left" style="padding:0px 4px; font-weight:bold;">Histórico</td>
		<td align="right" style="padding:0px 4px; font-weight:bold;">Valor</td>
		<td align="right" style="padding:0px 4px; font-weight:bold;">Dias</td>
		<td align="right"></td>
	</tr>
	<?php
	$strBgColor = $strCorLinha2;
	foreach($objResult as $objRS) {

		if(getValue($objRS, "pagar_receber") == "1") { 
			$strICONE = "<img src='../img/icon_fincontapagar.gif' alt='Conta a Pagar'>";
		} else {
			$strICONE = "<img src='../img/icon_fincontareceber.gif' alt='Conta a Receber'>";
		}
	?>		
	<tr bgcolor="<?php echo($strBgColor); ?>">
		<td width="9%" align="right" class="texto_corpo_mdo">
			<div style="padding-left:4px; padding-right:4px;"><?php echo(dDate(CFG_LANG,getValue($objRS,'dt_vcto'),false)); ?></div>
		</td>
		<td width="21%" align="left" class="texto_corpo_mdo" nowrap>
			<div style="padding-left:10px; padding-right:4px;"><?php echo(getValue($objRS,"nome")); ?></div>
		</td>
		<td width="51%" align="left" class="texto_corpo_mdo">
			<div style="padding-left:4px; padding-right:4px;"><?php echo(getValue($objRS,"historico")); ?></div>
		</td>
		<td width="9%" align="right" class="texto_corpo_mdo">
			<div style="padding-left:4px; padding-right:4px;"><?php echo(number_format((double) getValue($objRS,"vlr_conta"),2,",","")); ?></div>
		</td>
		<td width="9%" align="right" class="texto_corpo_mdo">
			<div style="padding-left:4px; padding-right:4px;"><?php echo(getValue($objRS,"dias") . " " . getTText("dia_s_",C_NONE)); ?></div>
		</td>
		<td width="1%" align="center">
			<div style="padding-left:4px; padding-right:4px;"><?php echo($strICONE); ?></div>
		</td>
	</tr>
	<?php
		if($strBgColor == $strCorLinha2)
			$strBgColor = $strCorLinha1;
		else
			$strBgColor = $strCorLinha2;
	}
	$objResult->closeCursor();
	?>
	<tr><td colspan="4" height="20"></td></tr>
	<?php
	///...
	?>
	<tr><td colspan="6" height="20"></td></tr>
	</table>
	<?php
	athEndFloatingBox();
}
$objConn = NULL;
?>