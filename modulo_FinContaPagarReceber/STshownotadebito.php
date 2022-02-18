<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<?php
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// INI: INCLUDE requests ORDIÀRIOS -------------------------------------------------------------------------------------
/*
 Por definição esses são os parâmetros que a página anterior de preparação (execaslw.php) manda para os executores.
 Cada executor pode utilizar os parâmetros que achar necessário, mas por definição queremos que todos façam os
 requests de todos os parâmetros enviados, como no caso abaixo:
 Variáveis e Carga:
	 -----------------------------------------------------------------------------
	 variável          | "alimentação"
	 -----------------------------------------------------------------------------
	 $data_ini         | DataHora início do relatório
	 $intRelCod		   | Código do relatórioRodapé do relatório
	 $strRelASL		   | ASL - Conulta com parâmetros processados, mas TAGs e Modificadores 
	 $strRelSQL		   | SQL - Consulta no formato SQL (com parâmetros processados e "limpa" de TAGs e Modificadores)
	 $strRelTit		   | Nome/Título do relatório
	 $strRelDesc	   | Descrição do relatório	
	 $strRelHead	   | Cabeçalho do relatório
	 $strRelFoot	   | Rodapé do relatório		
	 $strRelInpts	   | Usado apenas para o log
	 $strDBCampoRet	   | O nome do campo na consulta que deve ser retornado
	 $strDBCampoRet    | **Usado no repasse entre ralatórios - sem o nome da tabela do campo que será retornado
	 -----------------------------------------------------------------------------  */
include_once("../modulo_ASLWRelatorio/_include_aslRunRequest.php");
// FIM: INCLUDE requests ORDIÀRIOS -------------------------------------------------------------------------------------


// INI: INCLUDE funcionalideds BÁSICAS ---------------------------------------------------------------------------------
/* Funções
	 filtraAlias($prValue)
	 ShowDebugConsuta($prA,$prB)
	 ShowCR("CABECALHO/RODAPE",str)
  Ações:
  	 SEGURANÇA: Faz verificação se existe usuário logado no sistema
  Variáveis e Carga:
	 -----------------------------------------------------------------------------
	 variável          | "alimentação"
	 -----------------------------------------------------------------------------
	 $strDIR           | Pega o diretporio corrente (usado na exportação) 
	 $arrModificadores | Array contendo os modificadores ([! ], [$ ], ...) do ASL
	 $strSQL           | SQL PURO, ou seja, SEM os MODIFICADORES, TAGS, etc...
	 -----------------------------------------------------------------------------  */
include_once("../modulo_ASLWRelatorio/_include_aslRunBase.php");
// FIM: INCLUDE funcionalideds BÁSICAS ---------------------------------------------------------------------------------

$strDirCliente = getsession(CFG_SYSTEM_NAME . "_dir_cliente");

//Se não veio nada no código de conta pagar/receber então é para 
//gerar para vários porque vai estar sendo executado via relatórios ASL.
$intCodContaPR = request("var_chavereg");

$objConn = abreDBConn(CFG_DB);

try{
	if ($intCodContaPR != "") {
		$strSQL = "select
                       pr.cod_conta_pagar_receber
                      ,pr.tipo
					  ,pr.codigo
                      ,pr.vlr_conta
                      ,pr.dt_vcto
                      ,pr.historico
                      ,pr.obs     
                   from fin_conta_pagar_receber pr
                   where pr.situacao not ilike 'cancelado'  /*retira contas canceladas*/
                      and pr.pagar_receber = false /*somente contas a receber*/
                      and pr.cod_conta_pagar_receber =" . $intCodContaPR;
	}
	else {
		$strSQL = $strRelSQL; //SQL do relatório já sem as tags
	}
	
	$objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}

if($objRS = $objResult->fetch()) {

 //Obtem dados especificos
 $strTipoCliente = getValue($objRS,"tipo");
 $strCodigoEnt   = getValue($objRS,"codigo");
 $dblVlrConta    = getValue($objRS,"vlr_conta");
 $dateDtVcto     = getValue($objRS,"dt_vcto");
 $strHistorico   = getValue($objRS,"historico");
 $strObs         = getValue($objRS,"obs");
 $objResult->closeCursor();

 $strVlrContaExtenso = valorPorExtenso($dblVlrConta);
		
 try{
	$strSQL = "";
	//Monta consulta para cad_pj
	if(strtolower($strTipoCliente) == "cad_pj"){
		$strSQL = "select 
					cp.razao_social as nome
				   ,cast(case when ((cp.endprin_numero is not null) and (cp.endprin_numero <> ''))
						   then coalesce(cp.endprin_logradouro,'') || coalesce(cp.endprin_numero, '')
						   else coalesce(cp.endprin_logradouro,'')
					end  as varchar) as endereco
				   ,cp.endprin_numero
				   ,cp.endprin_cidade
				   ,cp.endprin_estado
				   ,cp.cnpj as cnpj_cpf
				   ,cp.insc_est
				from cad_pj cp
				where cod_pj = " . $strCodigoEnt;
	//Monta consulta para cad_pf
	}else if(strtolower($strTipoCliente) == "cad_pf"){
		$strSQL = "select 
					cp.nome
				   ,cast(case when ((cp.endprin_numero is not null) and (cp.endprin_numero <> ''))
						   then coalesce(cp.endprin_logradouro,'') || ' ' || coalesce(cp.endprin_numero, '')
						   else coalesce(cp.endprin_logradouro,'')
					end  as varchar) as endereco
				   ,cp.endprin_numero
				   ,cp.endprin_cidade
				   ,cp.endprin_estado
				   ,cp.cpf as cnpj_cpf
				   ,'' as insc_est
				from cad_pf cp
				where cod_pf = " . $strCodigoEnt;			
	//Monta consulta para cad_pj_fornec
	}else if(strtolower($strTipoCliente) == "cad_pj_fornec"){
	$strSQL = "select 
				cp.razao_social as nome
			   ,cast(case when ((cp.end_numero is not null) and (cp.end_numero <> ''))
					   then coalesce(cp.end_logradouro,'') || ' ' || coalesce(cp.end_numero, '')
					   else coalesce(cp.end_logradouro,'')
				end  as varchar) as endereco
			   ,cp.end_numero as endprin_numero
			   ,cp.end_cidade as endprin_cidade
			   ,cp.end_estado as endprin_estado
			   ,cp.cnpj as cnpj_cpf
			   ,cp.insc_est 
			from cad_pj_fornec cp
			where cod_pj = " . $strCodigoEnt;						
		
	}else{
  		mensagem("Cliente não identificado","Entidade do tipo " . $strTipoCliente . " e código " . $strCodigoEnt . " não foi localizada.",$e->getMessage(),"","erro",1);
		die();			
	}	
	
	$objResult = $objConn->query($strSQL);
 }
 catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
 }
 if($objRS = $objResult->fetch()){ 
   //Obtem dados do cliente 
   $strCliente_nome      = utf8_encode(getValue($objRS,"nome"));
   $strCliente_ender     = utf8_encode(getValue($objRS,"endereco"));
   $strCliente_numero    = getValue($objRS,"endprin_numero");   
   $strCliente_cidade    = utf8_encode(getValue($objRS,"endprin_cidade"));
   $strCliente_uf        = getValue($objRS,"endprin_estado");
   $strCliente_cnpj_cpf  = getValue($objRS,"cnpj_cpf");
   $strCliente_ie        = getValue($objRS,"insc_est");
   //Busca dados da empresa
   $strEmpresa_nome     = utf8_encode(getVarEntidade($objConn,"razao_social"));
   $strEmpresa_img_logo = getVarEntidade($objConn,"logotipo_empresa");
   $strEmpresa_cnpj     = getVarEntidade($objConn,"cnpj");
   $strEmpresa_insc_est = getVarEntidade($objConn,"insc_est");  
 }
 $objResult->closeCursor(); 
}
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo(CFG_SYSTEM_NAME);?></title>
    <link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
  </head>
  <body bgcolor="#FFFFFF">
  <?php
    //Leitura do HTML do modelo de NOTA DE DEBITO padrão
    $strStreamHTML = file_get_contents(getVarEntidade($objConn, "modelo_nota_debito"));
    //Substituir as TAGs do html
    $strStreamHTML = str_replace("<TAG_EMPRESA_SRC_IMG_LOGO>", $strEmpresa_img_logo,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_EMPRESA_CNPJ>"        , $strEmpresa_cnpj,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_EMPRESA_IE>"          , $strEmpresa_insc_est,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_EMPRESA_NOME>"        , $strEmpresa_nome,$strStreamHTML);

    $strStreamHTML = str_replace("<TAG_NF_NUMERO>"        , $intCodContaPR,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_NF_VENCIMENTO>"    , dDate("PTB", $dateDtVcto, false),$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_NF_VALOR>"         , FloatToMoeda($dblVlrConta),$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_NF_VALOR_EXTENSO>" , $strVlrContaExtenso,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_NF_DESCR_DESPESAS>", $strObs,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_NF_OBSERVACAO>"    , $strHistorico,$strStreamHTML);

    $strStreamHTML = str_replace("<TAG_CLIENTE_NOME>"    , $strCliente_nome,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_CLIENTE_ENDERECO>", $strCliente_ender,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_CLIENTE_CIDADE>"  , $strCliente_cidade,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_CLIENTE_UF>"      , $strCliente_uf,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_CLIENTE_CNPJ_CPF>", $strCliente_cnpj_cpf,$strStreamHTML);
    $strStreamHTML = str_replace("<TAG_CLIENTE_IE>"      , $strCliente_ie,$strStreamHTML);
  
	echo($strStreamHTML);
  ?>
  </body>
</html>
<?php
$objConn = NULL;
?>
