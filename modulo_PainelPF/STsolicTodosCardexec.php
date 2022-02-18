<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");

$objConn = abreDBConn(CFG_DB);

/*** RECEBE PARAMETROS ***/
$strOpcao 			= request("var_opcao");
$intCodPJ 			= request("var_cod_pj");
$dblValor 			= request("var_valor");
$strObs  			= request("var_obs");
$intCodProduto  	= request("var_cod_produto");
$intCodCFGBoleto 	= request("var_data_vcto");
$user 				= getSession(CFG_SYSTEM_NAME . "_id_usuario");

/*try{
	$strSQL2 = "
			SELECT 	t2.cod_pf, t2.nome, t2.cpf, t1.cod_pj, t1.cnpj,t1.razao_social,
					count(t5.cod_credencial) AS qtde_credencial,
					count(t6.cod_pedido)     AS qtde_ped_card,
        			(CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' AS mais_de_uma_hora,
					(t5.dt_validade -CURRENT_DATE ) AS vencida
			FROM cad_pj t1 
			INNER JOIN relac_pj_pf t3 ON (t1.cod_pj = t3.cod_pj AND t3.dt_demissao IS NULL) 
			INNER JOIN cad_pf t2 ON (t2.cod_pf = t3.cod_pf) 
			LEFT OUTER JOIN cad_cargo t4 ON (t3.cod_cargo = t4.cod_cargo) 
			LEFT OUTER JOIN sd_credencial t5 ON (t5.dtt_inativo is NULL AND t5.cod_pf = t2.cod_pf 
									 AND CURRENT_DATE <= dt_validade) 
			LEFT OUTER JOIN prd_pedido t6 ON (t6.situacao <> 'cancelado' AND t6.it_tipo = 'card' 
										AND t6.it_cod_pf = t2.cod_pf AND t6.cod_pj = t3.cod_pj 
										AND CURRENT_DATE <= t6.it_dt_fim_val_produto ) 
			WHERE t1.cod_pj =".$intCodPJ." 
			GROUP BY t1.cod_pj, t2.cod_pf, t2.nome, t2.cpf, t1.cod_pj, t1.cnpj, t1.razao_social,
					(CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour', vencida
			ORDER BY t2.cod_pf";
	$objResult2 = $objConn->query($strSQL2);	
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
*/
if($strOpcao == 0){
	$strOpcao = "uma_empresa";
}else{
	$strOpcao = "agrupar";
}

if ($intCodCFGBoleto == "") $intCodCFGBoleto = NULL;

if ($dblValor == "") $dblValor = "0";
$dblValor = str_replace(".","",$dblValor);
$dblValor = str_replace(",",".",$dblValor);		
	/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
$strMsg = '';

if($strOpcao == "") $strMsg .= "Selecionar para quem será gerado pedido<br>";
if(($intCodPJ == "") && ($strOpcao == "uma_empresa")) $strMsg .= "Informar empresa<br>";
if($intCodProduto == "") $strMsg .= "Informar produto<br>";
if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}


$strSQL ="SELECT sp_gera_cred(".$intCodPJ.",'".$strOpcao."','".$user."',
							  '".$dblValor."',".$intCodProduto.",'".$strObs."')";
					  
$objConn->query($strSQL);
$objConn = NULL;
redirect("STindex.php");
?>