<?php
	include_once("../_database/athdbconn.php");
	//include_once("../_database/athtranslate.php");
	//include_once("../_database/athkernelfunc.php");
	//include_once("../_scripts/scripts.js");
	//include_once("../_scripts/STscripts.js");

	// Cуdigo chave da pбgina, cod_pedido
	$intCodDado   = request("var_chavereg");  
	$intCodPai    = request("var_cod_pai");
	
	// redirect para onde a pбgina serб enviada
	$strRedirect  = request("var_redirect"); 	
	// Operaзгo a ser realizada
	$strOperacao  = request("var_oper");		
	// Executor externo (fora do kernel)
	$strExec      = request("var_exec");		
	// Flag para necessidade de popular o session ou nгo
	$strPopulate  = request("var_populate");	
	// Indicativo para qual formato que a grade deve ser exportada. Caso vazio campo, a grade exibida normalmente.
	$strAcao   	  = request("var_acao");		
	
	//Popula o session para fazer a abertura dos нtens do mуdulo
	if($strPopulate == "yes") { initModuloParams(basename(getcwd())); }

	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

	//Inicia objeto para manipulaзгo do banco
	$objConn = abreDBConn(CFG_DB);

	// Sу й feito a busca e exibiзгo dos dados 
	// seja enviado como parametro para este script.
	if($intCodDado == ""){
		mensagem("err_sql_titulo","err_sql_desc_card",getTText("cod_ped_invalido",C_NONE),"","aviso",1);
		die();
	}

	// consulta dados do pedido - atй aqui sabe-se que
	// cod_pedido foi encaminhado corretamente. buscan
	// do todos os dados da tabela de pedidos
	try{ 
		$strSQL = "
			SELECT	
				  prd_pedido.cod_pedido
				, prd_pedido.cod_pf 
				, prd_pedido.cod_ficha 
				, cad_pf.cpf
				, prd_pedido.situacao 
				, prd_pedido.obs 
				, prd_pedido.valor
				, prd_pedido.dtt_inativo 
  				, prd_pedido.it_cod_produto
				, prd_pedido.it_tipo
				, prd_pedido.it_descricao
				, prd_pedido.it_valor
				, prd_pedido.it_obs
				, prd_pedido.it_exibe_ficha_tipo
				, prd_pedido.it_deve_gerar
				, prd_pedido.it_qtde_vigencia_meses
				, prd_pedido.sys_usr_ins
				, prd_pedido.sys_dtt_ins
				, prd_pedido.sys_usr_upd
				, prd_pedido.sys_dtt_upd
				, prd_produto.rotulo
				, prd_produto.descricao
			FROM 
				prd_pedido
			LEFT OUTER JOIN prd_produto ON (prd_produto.cod_produto = prd_pedido.it_cod_produto)
			LEFT OUTER JOIN cad_pf ON (prd_pedido.cod_pf = cad_pf.cod_pf)
			WHERE
				prd_pedido.cod_pedido = ".$intCodDado;
			//LEFT OUTER JOIN cad_pf ON (prd_pedido.cod_pf = cad_pf.cod_pf)
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	// fetch dos dados para exibiзгo em tela
	$objRS = $objResult->fetch();
	
	// otimizaзгo para redirect e fechamento
	// de conexгo com o banco de dados
	$strLink = '';
	
	// para quando existe ficha e o produto
	// do pedido estб marcado p gerar ficha
	if((getValue($objRS,"it_tipo") == "prod") && (getValue($objRS,"it_deve_gerar") == "ficha")){
		$strLink = "STgeratituloficha.php?var_chavereg=".$intCodDado."&var_cod_pai=".$intCodPai."&var_redirect=".$strRedirect;
	}
	// para pedidos de troca de local 
	elseif(getValue($objRS,"it_deve_gerar") == "troca_local"){ 
		$strLink = "STgeratitulotroca.php?var_chavereg=".$intCodDado."&var_cod_pai=".$intCodPai."&var_redirect=".$strRedirect;
	} 
	// para pedidos de troca de sepultado
	elseif(getValue($objRS,"it_deve_gerar") == "troca_sepultado"){ 
		$strLink = "STgeratitulosepultado.php?var_chavereg=".$intCodDado."&var_cod_pai=".$intCodPai."&var_redirect=".$strRedirect;
	} 
	// para pedidos de sepultamento
	elseif((getValue($objRS,"it_deve_gerar") == "sepult") && (getValue($objRS,"it_tipo")=="serv")){ 
		$strLink = "STgeratitulosepultamento.php?var_chavereg=".$intCodDado."&var_cod_pai=".$intCodPai."&var_redirect=".$strRedirect;
	} 
	// para pedidos de translado
	elseif((getValue($objRS,"it_deve_gerar") == "transl") && (getValue($objRS,"it_tipo")=="serv")){ 
		$strLink = "STgeratitulotranslado.php?var_chavereg=".$intCodDado."&var_cod_pai=".$intCodPai."&var_redirect=".$strRedirect;
	} 
	// para pedido em geral
	else{ 
		$strLink = "STgeratitulobasica.php?var_chavereg=".$intCodDado."&var_cod_pai=".$intCodPai."&var_redirect=".$strRedirect;
	}
	
	//die($strRedirect);
	
	// fechamento de conexгo com o banco e REDIRECT
	$objResult->closeCursor();
	$objConn = NULL;
	redirect($strLink);
?>