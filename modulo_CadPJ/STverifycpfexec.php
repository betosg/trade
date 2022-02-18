<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// TRATAMENTO PARA C�DIGO DA PJ
	$intCodPJ   = request("var_chavereg"); 		// COD_PJ
	$intCPF		= request("var_cpf");			// CPF a ser verificado
	$flagInsert = request("var_flag_inserir");	// FLAG PARA TIPO DE INSER��O DE PF
	
	$strSesPfx 	= strtolower(str_replace("modulo_","",basename(getcwd())));
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "VIE");
	
	// ABERTURA DE CONEX�O NO BANCO
	$objConn   	= abreDBConn(CFG_DB);
	
	// TRATAMENTO PARA FLAG DE INSER��O
	if($flagInsert == "INS_HOMO"){
		try {
			$strSQL = "
				SELECT cod_pj_pf, dt_demissao FROM relac_pj_pf 
				INNER JOIN cad_pj ON (cad_pj.cod_pj = relac_pj_pf.cod_pj) 
				INNER JOIN cad_pf ON (cad_pf.cod_pf = relac_pj_pf.cod_pf) 
				WHERE 
				cad_pf.cpf = '".$intCPF."' AND 
				cad_pj.cod_pj = ".$intCodPJ;
			// die($strSQL);
			$objResult  = $objConn->query($strSQL);
			$objResult2 = $objConn->query($strSQL);
			$objRS	    = $objResult->fetch();
			
			// VERIFICA SE COLAB EXISTE MAS EST� DEMITIDO PARA ESTA EMPRESA J�
			if($objResult2->rowCount() > 0){
				if(getValue($objRS,"dt_demissao") != ""){
					mensagem("err_sql_titulo","err_sql_desc_card",getTText("colaborador_ja_homologado",C_NONE),"STverifycpf.php?var_chavereg=".$intCodPJ."&var_flag_inserir=".$flagInsert,"aviso",1,"a");
					include_once("../_scripts/scripts.js");
					echo('
					<script type="text/javascript">
  					  // Quando esta p�gina for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
					  resizeIframeParent(\''.CFG_SYSTEM_NAME.'_detailiframe_'.request("var_chavereg").'\',20);
					  // ----------------------------------------------------------------------------------------------------------
					</script>');
					die();
				} else{
					redirect("STGeraHomoFast.php?var_chavereg=".getValue($objRS,"cod_pj_pf")."&var_cod_pj=".$intCodPJ);
				}
			} else{
				redirect("STinscolabhomo.php?var_cpf=".$intCPF."&var_cod_pj=".$intCodPJ);
			}
		}catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			
			die();
		}
	} else{
		// INSER��O DE PF NO MODO CREDENCIAL
		try {
			//SQL alterado para passar a considerar a dt_demissao - by Vini - 12.11.2012
			$strSQL = "
				SELECT cod_pj_pf FROM relac_pj_pf 
				INNER JOIN cad_pj ON (cad_pj.cod_pj = relac_pj_pf.cod_pj) 
				INNER JOIN cad_pf ON (cad_pf.cod_pf = relac_pj_pf.cod_pf) 
				WHERE 
				dt_demissao is null AND 
				cad_pf.cpf = '".$intCPF."' AND 
				cad_pj.cod_pj = ".$intCodPJ;
			// die($strSQL);
			$objResult  = $objConn->query($strSQL);
			$objResult2 = $objConn->query($strSQL);
			$objRS	    = $objResult->fetch();
			
			// VERIFICA SE COLAB EXISTE MAS EST� DEMITIDO PARA ESTA EMPRESA J�
			//if($objResult2->rowCount() > 0){
			if(getValue($objRS,"cod_pj_pf") != ""){
				redirect("STgeracardfast.php?var_chavereg=".getValue($objRS,"cod_pj_pf")."&var_cod_pj=".$intCodPJ);
			} else{
				redirect("STinscolabcard.php?var_cpf=".$intCPF."&var_cod_pj=".$intCodPJ);
			}
		}catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
	}
	$strTitle  	= ($flagInsert == "INS_HOMO") ? getTText("insercao_de_colab_homologando",C_NONE) : getTText("insercao_de_colab_credencial",C_NONE);
	
	$objConn = NULL; 
?>