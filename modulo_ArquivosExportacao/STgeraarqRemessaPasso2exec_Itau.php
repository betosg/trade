<?php 
/***           		   INCLUDES                   ***/
/****************************************************/
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$dateDtEmissaoIni = request("var_dt_emissao_ini");
$dateDtEmissaoFim = request("var_dt_emissao_fim");
$dateDtVctoIni = request("var_dt_vcto_ini");
$dateDtVctoFim = request("var_dt_vcto_fim");
$strHistorico = request("var_historico");
$strTipoDocumento = request("var_tipo_documento");
$dblValor		  = request("var_valor");
function modulo_11($num, $base=9, $r=0)  {
    /**
     *   Autor:
     *           Pablo Costa <pablo@users.sourceforge.net>
     *
     *   Função:
     *    Calculo do Modulo 11 para geracao do digito verificador 
     *    de boletos bancarios conforme documentos obtidos 
     *    da Febraban - www.febraban.org.br 
     *
     *   Entrada:
     *     $num: string numérica para a qual se deseja calcularo digito verificador;
     *     $base: valor maximo de multiplicacao [2-$base]
     *     $r: quando especificado um devolve somente o resto
     *
     *   Saída:
     *     Retorna o Digito verificador.
     *
     *   Observações:
     *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
     *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
     */                                        

    $soma = 0;
    $fator = 2;

    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {
        // pega cada numero isoladamente
        $numeros[$i] = substr($num,$i-1,1);
        // Efetua multiplicacao do numero pelo falor
        $parcial[$i] = $numeros[$i] * $fator;
        // Soma dos digitos
        $soma += $parcial[$i];
        if ($fator == $base) {
            // restaura fator de multiplicacao para 2 
            $fator = 1;
        }
        $fator++;
    }

    /* Calculo do modulo 11 */
    if ($r == 0) {
        $soma *= 10;
        $digito = $soma % 11;
        if ($digito == 10) {
            $digito = 0;
        }
        return $digito;
    } elseif ($r == 1){
        $resto = $soma % 11;
        return $resto;
    }
}

function digitoVerificador_nossonumero($numero) {
	$resto2 = modulo_11($numero, 7, 1);
    $digito = 11 - $resto2;
    if ($digito == 10) {
        $dv = "P";
    } elseif($digito == 11) {
     	$dv = 0;
	} else {
        $dv = $digito;
     	}
	return $dv;
}

function GeraLinhaHeader($prCont, $prCodEmpresa, $prNomeEmpresa, $prNumBanco, $prNomeBanco, $prDia, $prMes, $prAno, $prNumSeqRemessa,$prAgencia,$prConta) {
	$strLinha = "";

	$strLinha .= "01";
	$strLinha .= "REMESSA01";
	$strLinha .= "COBRANCA";
	$strLinha .= str_pad("", 7);
	$strLinha .= str_pad($prAgencia,4,"0",STR_PAD_LEFT);
	$strLinha .= "00";
	$strLinha .= str_pad($prConta,6,"0",STR_PAD_LEFT);  //conta + digito
	$strLinha .= str_pad("", 8);		
	$strLinha .= str_pad(substr(removeAcento($prNomeEmpresa), 0, 30), 30);
	$strLinha .= str_pad($prNumBanco, 3, "0", STR_PAD_LEFT);
	$strLinha .= str_pad(substr($prNomeBanco, 0, 15), 15);
	$strLinha .= str_pad($prDia, 2, "0", STR_PAD_LEFT) . str_pad($prMes, 2, "0", STR_PAD_LEFT) . str_pad($prAno, 2, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 294);	
	$strLinha .= str_pad($prCont, 6, "0", STR_PAD_LEFT);
	
	
	

	
	return $strLinha;
}

function GeraLinhaTipo1($prCont, $prIdentEmpresaCedente, $prCarteira, $prNumBanco, $prNossoNumero, $prOcorrencia, $prNumDocumento, $prDiaVcto, $prMesVcto, $prAnoVcto, $prDiaEmissao, $prMesEmissao, $prAnoEmissao, $prVlrTitulo, $prPrimMensagem, $prSacadoCNPJ, $prSacadoNome, $prSacadoEndereco, $prSacadoCEP,$prCNPJCedente,$prAgencia,$prConta,$prSacadoBairro,$prSacadoCidade,$prSacadoEstado) {
	$strLinha = "";
	
	$strLinha .= "1";
	$strLinha .= "02"; //codigo de inscricao(2)
	$strLinha .= $prCNPJCedente; // cnpj (14)
	$strLinha .= str_pad($prAgencia,4,"0",STR_PAD_LEFT); //agencia
	$strLinha .= "00";
	$strLinha .= str_pad($prConta,6,"0",STR_PAD_LEFT);  //conta + digito
	$strLinha .= str_pad("", 4);
	$strLinha .= "0000";
	$strLinha .= str_pad($prNumDocumento, 25); //controle participante
	$strLinha .= str_pad(substr($prNossoNumero, 0, 8), 8, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 13, "0", STR_PAD_LEFT);
	$strLinha .= str_pad($prCarteira, 3, "0", STR_PAD_LEFT);

	$strLinha .= str_pad("", 21); // uso do banco, nao tem nota explicativa, deixei como brancos
	$strLinha .= "I"; //codigo da carteira
	$strLinha .= str_pad("01", 2); //cod_ocorrencia
	$strLinha .= str_pad($prNumDocumento, 10); //nro documento
	$strLinha .= str_pad($prDiaVcto, 2, "0", STR_PAD_LEFT) . str_pad($prMesVcto, 2, "0", STR_PAD_LEFT) . str_pad($prAnoVcto, 2, "0", STR_PAD_LEFT);
	$strLinha .= str_pad(str_replace(".", "", MoedaToFloat(FloatToMoeda($prVlrTitulo))), 13, "0", STR_PAD_LEFT);
	$strLinha .= str_pad($prNumBanco, 3, "0", STR_PAD_LEFT);
	$strLinha .= "00000"; //ag cobradora
	$strLinha .= "01"; //especie
	$strLinha .= "N"; //aceite
	$strLinha .= str_pad($prDiaEmissao, 2, "0", STR_PAD_LEFT) . str_pad($prMesEmissao, 2, "0", STR_PAD_LEFT) . str_pad($prAnoEmissao, 2, "0", STR_PAD_LEFT);
	$strLinha .= "00"; //instrução 1a
	$strLinha .= "00"; //instrução 2a
	$strLinha .= str_pad("", 13, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 6, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 13, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 13, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 13, "0", STR_PAD_LEFT);
	$strLinha .= "01";
	$strLinha .= str_pad($prSacadoCNPJ, 14, "0", STR_PAD_LEFT);
	$strLinha .= str_pad(substr(removeAcento($prSacadoNome), 0, 30), 30);
	$strLinha .= str_pad("", 10);
	$strLinha .= str_pad(substr(removeAcento($prSacadoEndereco), 0, 40), 40);
	$strLinha .= str_pad(substr(removeAcento($prSacadoBairro),0,12),12);
	$strLinha .= str_pad(substr($prSacadoCEP, 0, 8), 8, "0", STR_PAD_LEFT);
	$strLinha .= str_pad(substr(removeAcento($prSacadoCidade),0,15),15);
	$strLinha .= str_pad(substr(removeAcento($prSacadoEstado),0,2),2);
	$strLinha .= str_pad("",30);
	$strLinha .= str_pad("",4);
	$strLinha .= str_pad("", 6, "0", STR_PAD_LEFT); //data mora
	$strLinha .= "00";
	$strLinha .= str_pad("",1);
	$strLinha .= str_pad($prCont, 6, "0", STR_PAD_LEFT);


/*
	$strLinha .= str_pad("", 5, "0", STR_PAD_LEFT);
	$strLinha .= "0";
	$strLinha .= str_pad("", 5, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 7, "0", STR_PAD_LEFT);
	$strLinha .= "0";
	$strLinha .= str_pad($prIdentEmpresaCedente, 17);
	$strLinha .= str_pad($prNumDocumento, 25); //controle participante
	//$strLinha .= str_pad($prNumBanco, 3, "0", STR_PAD_LEFT);
	$strLinha .= "000";
	$strLinha .= "0";
	$strLinha .= "0000";
	$strLinha .= str_pad(substr($prNossoNumero, 0, 11), 11, "0", STR_PAD_LEFT);
	//$strLinha .= str_pad(substr($prNossoNumero, 11, 1), 1);
	$strAux = $prCarteira.str_pad(substr($prNossoNumero, 0, 11), 11, "0", STR_PAD_LEFT);
	$strLinha .= digitoVerificador_nossonumero($strAux);
	$strLinha .= str_pad("", 10, "0", STR_PAD_LEFT);
	$strLinha .= "2";
	$strLinha .= "N";
	$strLinha .= str_pad("", 10);
	$strLinha .= " ";
	$strLinha .= "0";
	$strLinha .= "  ";
	$strLinha .= str_pad($prOcorrencia, 2, "0", STR_PAD_LEFT);
	$strLinha .= str_pad(substr($prNumDocumento, 0, 10), 10);
	$strLinha .= str_pad($prDiaVcto, 2, "0", STR_PAD_LEFT) . str_pad($prMesVcto, 2, "0", STR_PAD_LEFT) . str_pad($prAnoVcto, 2, "0", STR_PAD_LEFT);
	$strLinha .= str_pad(str_replace(".", "", MoedaToFloat(FloatToMoeda($prVlrTitulo))), 13, "0", STR_PAD_LEFT);
	$strLinha .= "000";
	$strLinha .= "00000";
	$strLinha .= "01";
	$strLinha .= "N";
	$strLinha .= str_pad($prDiaEmissao, 2, "0", STR_PAD_LEFT) . str_pad($prMesEmissao, 2, "0", STR_PAD_LEFT) . str_pad($prAnoEmissao, 2, "0", STR_PAD_LEFT);
	$strLinha .= "00"; //instrução 1a
	$strLinha .= "00"; //instrução 2a
	$strLinha .= str_pad("", 13, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 6, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 13, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 13, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 13, "0", STR_PAD_LEFT);
	$strLinha .= "02"; //tipo de num inscricao do sacado
	$strLinha .= str_pad($prSacadoCNPJ, 14, "0", STR_PAD_LEFT);
	$strLinha .= str_pad(substr(removeAcento($prSacadoNome), 0, 40), 40);
	$strLinha .= str_pad(substr(removeAcento($prSacadoEndereco), 0, 40), 40);
	$strLinha .= str_pad(substr(removeAcento($prPrimMensagem), 0, 12), 12); //mensagem do emissor que pode ser impressa no boleto
	$strLinha .= str_pad(substr($prSacadoCEP, 0, 8), 8, "0", STR_PAD_LEFT);
	$strLinha .= str_pad("", 60);
	$strLinha .= str_pad($prCont, 6, "0", STR_PAD_LEFT);
*/	
	return $strLinha;
}

function GeraLinhaFooter($prCont) {
	$strLinha = "";
	
	$strLinha .= "9";
	$strLinha .= str_pad("", 393);
	$strLinha .= str_pad($prCont, 6, "0", STR_PAD_LEFT);
	
	return $strLinha;
}

$strPopulate = request("var_populate");   // Flag para necessidade de popular o session ou não

if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "VIE");

// abre conexão com o banco de dados
$objConn = abreDBConn(CFG_DB);

$strNomeArquivo = "remessa_cnab400_".date("Y").date("m").date("d")."_".date("H").date("i").date("s").".txt";
$fArquivo = fopen("../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/arqbanco/cobrcnab400/remessa/".$strNomeArquivo, "w");

$iCont = 1; //Contador geral

// -------------------------------------
// gera linha de header
// -------------------------------------
$iCodEmpresa = getVarEntidade($objConn, "remessa_itau_400pos_cod_empresa");
$strNomeEmpresa = getVarEntidade($objConn, "nome_fan");
$strNumBanco = "341";
$strNomeBanco = "BANCO ITAU SA";
$iDia = date("d");
$iMes = date("m");
$iAno = date("y");
$iNumSeqRemessa = getVarEntidade($objConn, "remessa_itau_400pos_num_seq_arquivo");
$strCarteira = getVarEntidade($objConn, "remessa_itau_codigo_carteira"); //"009";  //TEMPORARIO


$strAgencia = getVarEntidade($objConn, "remessa_itau_agencia");
$strConta   = getVarEntidade($objConn, "remessa_itau_conta");


$strLinha = GeraLinhaHeader($iCont, $iCodEmpresa, $strNomeEmpresa, $strNumBanco, $strNomeBanco, $iDia, $iMes, $iAno, $iNumSeqRemessa,$strAgencia,$strConta);
fwrite($fArquivo, $strLinha . chr(13) . chr(10));
$iCont++;

$iNumSeqRemessa = (int)$iNumSeqRemessa+1;
try{
	$strSQL = " UPDATE sys_var_entidade
				SET valor = '".$iNumSeqRemessa."'
				WHERE id_var ILIKE 'remessa_itau_400pos_num_seq_arquivo' ";
	$objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}

// -------------------------------------
// gera linhas de dados
// -------------------------------------
try{
	$strSQL = " SELECT t1.nosso_numero, t1.num_documento, t1.dt_vcto, t1.dt_emissao, t1.vlr_conta
	                 , t2.cnpj AS sacado_cnpj, t2.razao_social AS sacado_nome, t2.endprin_logradouro
				     , t2.endprin_numero, t2.endprin_complemento, t2.endprin_bairro, t2.endprin_cidade
				     , t2.endprin_estado, t2.endprin_cep AS sacado_cep
				FROM fin_conta_pagar_receber t1, cad_pj t2, fin_conta t3, fin_banco t4
				WHERE t1.pagar_receber = FALSE
				AND t1.situacao ILIKE 'aberto'
				AND t1.codigo = t2.cod_pj
				AND t1.tipo = 'cad_pj' 
				AND t1.cod_conta = t3.cod_conta
				AND t3.cod_banco = t4.cod_banco
				AND t4.num_banco = '341' "; //FIXO POR ENQUANTO
	if (($dateDtEmissaoIni != "") && ($dateDtEmissaoFim != "")) $strSQL .= " AND t1.dt_emissao BETWEEN TO_TIMESTAMP('".$dateDtEmissaoIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtEmissaoFim."', 'DD/MM/YYYY') ";
	if (($dateDtVctoIni != "") && ($dateDtVctoFim != "")) $strSQL .= " AND t1.dt_vcto BETWEEN TO_TIMESTAMP('".$dateDtVctoIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtVctoFim."', 'DD/MM/YYYY') ";
	if ($strHistorico != "") $strSQL .= " AND t1.historico ILIKE '".$strHistorico."%' ";
	if ($strTipoDocumento != "") $strSQL .= " AND t1.tipo_documento ILIKE '".$strTipoDocumento."' ";
	if ($dblValor != "") $strSQL .= " AND t1.vlr_conta = ".MoedaToFloat($dblValor) ;
	$strSQL .= " ORDER BY t2.razao_social, t1.dt_emissao, t1.num_documento ";
	//die($strSQL);
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}

$strNumBanco = "341";
$strOcorrencia = "01"; //remessa
$strIdentEmpresaCedente = getVarEntidade($objConn, "remessa_itau_400pos_id_empresa_cedente");
$strCNPJCedente = getVarEntidade($objConn,"remessa_itau_cnpj");

foreach($objResult as $objRS){
	$strNossoNumero = substr(getValue($objRS,"nosso_numero"),-8);
	$strNumDocumento = getValue($objRS,"num_documento");
	$iDiaVcto = substr(dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false),0,2);
	$iMesVcto = substr(dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false),3,2);
	$iAnoVcto = substr(dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false),8,2);
	$iDiaEmissao = substr(dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false),0,2);
	$iMesEmissao = substr(dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false),3,2);
	$iAnoEmissao = substr(dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false),8,2);
	$dblVlrTitulo = getValue($objRS,"vlr_conta");
	$strSacadoCNPJ = getValue($objRS,"sacado_cnpj");
	$strSacadoNome = getValue($objRS,"sacado_nome");
	$strSacadoCEP = getValue($objRS,"sacado_cep");
	
	$strSacadoEndereco = getValue($objRS,"endprin_logradouro");
	if (getValue($objRS,"endprin_numero") != "")      $strSacadoEndereco .= ",".getValue($objRS,"endprin_numero");
	if (getValue($objRS,"endprin_complemento") != "") $strSacadoEndereco .= " ".getValue($objRS,"endprin_complemento");
	//if (getValue($objRS,"endprin_bairro") != "")      $strSacadoEndereco .= " ".getValue($objRS,"endprin_bairro");
	//$strSacadoEndereco .= " ".getValue($objRS,"endprin_cidade")."/".getValue($objRS,"endprin_estado");
	$strSacadoBairro = getValue($objRS,"endprin_bairro");
	$strSacadoCidade = getValue($objRS,"endprin_cidade");
	$strSacadoEstado = getValue($objRS,"endprin_estado");

	$strLinha = GeraLinhaTipo1($iCont, $strIdentEmpresaCedente, $strCarteira, $strNumBanco, $strNossoNumero, $strOcorrencia, $strNumDocumento, $iDiaVcto, $iMesVcto, $iAnoVcto, $iDiaEmissao, $iMesEmissao, $iAnoEmissao, $dblVlrTitulo, "", $strSacadoCNPJ, $strSacadoNome, $strSacadoEndereco, $strSacadoCEP,$strCNPJCedente,$strAgencia,$strConta,$strSacadoBairro,$strSacadoCidade,$strSacadoEstado);
	fwrite($fArquivo, $strLinha . chr(13) . chr(10));
	$iCont++;
}
$objResult->closeCursor();

// -------------------------------------
// gera linha de footer
// -------------------------------------
$strLinha = GeraLinhaFooter($iCont);
fwrite($fArquivo, $strLinha . chr(13) . chr(10));
$iCont++;

fclose($fArquivo);

?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../_scripts/tablesort.js"></script>
<style>
	.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
	body{ margin: 0px; background-color:#FFFFFF; } 
	ul{ margin-top: 0px; margin-bottom: 0px; }
	li{ margin-left: 0px; }
</style>
<script language="javascript" type="text/javascript">
function reiniciar() {
	document.location.href = "STgeraarqRemessaPasso1.php";	
}

</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","<b>".getTText("titulo_gerar_remessa",C_NONE)."</b>",CL_CORBAR_GLASS_1); ?>
      <table id="var_dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6; display:block;">
		<tr><td height="22" colspan="2"></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="550" border="0" cellspacing="0" cellpadding="4">
					<tr><td width="30%"></td><td width="70%"></td></tr>
					<tr><td align="left" style="padding-left:5px;" colspan="2"><img src="../img/remessa_passo03.gif"></td></tr>
					<tr>
						<td colspan="2" height="40"><?php echo(getTText("arquivo_gerado",C_NONE)); ?>:&nbsp;<a href="../../<?php echo getsession(CFG_SYSTEM_NAME."_dir_cliente");?>/upload/arqbanco/cobrcnab400/remessa/<?php echo $strNomeArquivo; ?>" target="_blank"><u><?php echo $strNomeArquivo; ?></u></a></td>
					</tr>
					<tr><td height="10" colspan="2"></td></tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
					<tr>
						<td colspan="2">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
							<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
								<button onClick="reiniciar();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
							</td>
							</tr>
						</table>
						</td>
					</tr> 
				</table>
			</td>
		</tr>
      </table>
      <?php athEndFloatingBox(); ?>
   </td>
  </tr>
</table>
</body>
</html>
<?php
$objConn = NULL;
?>
