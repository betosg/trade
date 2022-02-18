<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// ABERTURA DE CONEXÃO COM BANCO
$objConn = abreDBConn(CFG_DB);


$strUsuario       = getsession(CFG_SYSTEM_NAME."_id_usuario");
$strCodNotaDebitoGerada = -1;
$intCodDado	      = request("var_chavereg");
$intCodEntidade   = request("var_cod_ent");
$strTipoEntidade  = request("var_tipo_ent");
$dateDtEmissao    = request("var_dt_emissao_nota_deb");
$dateDtVcto       = request("var_dt_vcto_nota_deb");
$strHistorico     = request("var_historico");
$strObs           = request("var_obs");
$dblVlrConta      = request("var_vlr_titulo");
$strDescrDespesas = request("var_descricao_despesas_nota");

$strObs = utf8_decode("Observações: (1) A presente Nota de Débito não está sujeita a retenção de Imposto de Renda na Fonte.<br>" . $strObs);

try {
	/*Busca os dados da PJ ou PF*/	
	if($strTipoEntidade == "cad_pj") {
		$strSQL = "SELECT cad_pj.razao_social as razao
		                 ,cad_pj.endprin_cep
						 ,cad_pj.endprin_logradouro
						 ,cad_pj.endprin_numero
						 ,cad_pj.endprin_complemento
						 ,cad_pj.endprin_bairro
						 ,cad_pj.endprin_cidade
						 ,cad_pj.endprin_estado
						 ,cad_pj.endprin_pais
						 ,cad_pj.cnpj as cnpj_cpf						 						 
					FROM cad_pj WHERE cad_pj.cod_pj = '".$intCodEntidade."'";
	}else if($strTipoEntidade == "cad_pf") {
		$strSQL = "SELECT cad_pf.nome as razao
		                 ,cad_pf.endprin_cep
						 ,cad_pf.endprin_logradouro
						 ,cad_pf.endprin_numero
						 ,cad_pf.endprin_complemento
						 ,cad_pf.endprin_bairro
						 ,cad_pf.endprin_cidade
						 ,cad_pf.endprin_estado
						 ,cad_pf.endprin_pais
						 ,cad_pf.cpf  as cnpj_cpf						 
					FROM cad_pf WHERE cad_pf.cod_pf = '".$intCodEntidade."';";		
	}
				//echo $strSQL;
	//die($strSQL);	
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

if($objRS = $objResult->fetch()){

	$objConn->beginTransaction();
	try{
		//Insere nota_debito
		$strSQL =	"INSERT INTO fin_nota_debito
					(
					  codigo,
					  tipo,
					  razao_social,
					  cnpj_cpf,
					  cod_conta_pagar_receber,
					  dt_vcto,
					  dt_emissao,
					  descricao_despesas,
					  end_cep,
					  end_logradouro,
					  end_numero,
					  end_complemento,
					  end_bairro,
					  end_cidade,
					  end_estado,
					  end_pais,
					  vlr_total,
					  obs,
					  sys_dtt_ins,
					  sys_usr_ins
					) 
					VALUES (
					  " . $intCodEntidade . ",
					  '" . $strTipoEntidade . "',
					  '" . getValue($objRS,"razao") . "',
					  '" . getValue($objRS,"cnpj_cpf") . "',
					  " . $intCodDado . ",
					  '" . cDate("PTB", $dateDtVcto, false) . "',
					  '" . cDate("PTB", $dateDtEmissao, false) . "',
					  '" . $strDescrDespesas . "',
					  '" . getValue($objRS,"endprin_cep") . "',
					  '" . getValue($objRS,"endprin_logradouro") . "',
					  '" . getValue($objRS,"endprin_numero") . "',
					  '" . getValue($objRS,"endprin_complemento") . "',
					  '" . getValue($objRS,"endprin_bairro") . "',
					  '" . getValue($objRS,"endprin_cidade") . "',
					  '" . getValue($objRS,"endprin_estado") . "',
					  '" . getValue($objRS,"endprin_pais") . "',
					  '" . $dblVlrConta . "',
					  '" . $strObs . "',
					  "  . "CURRENT_TIMESTAMP" . ",
					  '" . $strUsuario . "');";
        //    die($strSQL);	
		$objConn->query($strSQL);
		
		//ATENÇÃO - Esta consulta tem que ficar aqui... antes do commit da transação, pois para pegar o valor
		//do sequence por currval tem que estar na mesma transação que fez o insert.
		//Busca o código da nota de debito gerada para abrir a nota no popup
		$strSQL = "SELECT currval('fin_nota_debito_cod_nota_debito_seq') as valor";
		$objResult = $objConn->query($strSQL);
		if($objRS = $objResult->fetch()){
			$strCodNotaDebitoGerada = getValue($objRS, "valor");
		}
        //commit no insert da nota de débito
		$objConn->commit();
	}catch(PDOException $e) {
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
}
?>
<script language="javascript">
	//abre a nota em um popup e fecha este popup
	AbreJanelaPAGE('../modulo_FinNotaDebito/STnotadebitoavulsa.php?var_chavereg=<?php echo($strCodNotaDebitoGerada); ?>', '700', '800');
	window.close();
</script>
