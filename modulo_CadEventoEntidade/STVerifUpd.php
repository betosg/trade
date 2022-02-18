<?php
	// HEADERS ANTI-CACHE
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
		
	// REQUESTS
	$intCodEvento = request("var_chavereg");
	$strOper      = request("var_oper");
	$strBasename  = request("var_basename");
	$strRedirect  = request("var_redirect");
	
	// Verificação de ACESSO
     	
	// Verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_FAST");
	
	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// SQL
	try {
		/*
		$strSQL = "SELECT cad_evento_entidade.sys_dtt_ins
	                    ,(SELECT to_date(COALESCE(valor,'01/01/2013'),'DD/MM/YYYY') FROM sys_var_entidade WHERE id_var = 'limite_catalogo_ini') AS valor_ini
     					,(SELECT to_date(COALESCE(valor,'01/09/2013'),'DD/MM/YYYY') FROM sys_var_entidade WHERE id_var = 'limite_catalogo_fim') AS valor_fim  
                   FROM cad_evento_entidade
                   WHERE cad_evento_entidade.cod_evento = ".$intCodEvento; */

		$strSQL = "SELECT cad_evento_entidade.sys_dtt_ins
					 	  ,(SELECT to_date(COALESCE(valor,'01/01/2013'),'DD/MM/YYYY') FROM sys_var_entidade WHERE id_var = 'limite_catalogo_ini') AS valor_ini
					      ,(SELECT to_date(COALESCE(valor,'01/09/2013'),'DD/MM/YYYY') FROM sys_var_entidade WHERE id_var = 'limite_catalogo_fim') AS valor_fim  
					      ,cad_pj.razao_social
					      ,cad_pj.tp_edevento
				    FROM cad_evento_entidade, cad_pj
    	           WHERE cad_evento_entidade.tipo       = 'cad_pj'
			         AND cad_evento_entidade.codigo     = cad_pj.cod_pj 
				 	 AND cad_evento_entidade.cod_evento = " . $intCodEvento;  

		//echo("Debug: <br>" . $strSQL . "<br>");
		$objResult  = $objConn->query($strSQL);
		$objRS	    = $objResult->fetch();
	}catch(PDOException $e){
        messageDlg(C_MSG_ERRO ,getTText("err_sql_titulo" ,C_NONE),getTText("err_sql_desc",C_NONE)." ","$e->getMessage()","javascript:history.back();");				
		die();
	}
	$strDtIns  = getValue($objRS,"sys_dtt_ins");
	$strLimIni = getValue($objRS,"valor_ini"  );
	$strLimFim = getValue($objRS,"valor_fim"  );	
	$strTpEdEv = getValue($objRS,"tp_edevento");	
?>
<html>
<head>
	<title><?php echo(strtoupper(CFG_SYSTEM_NAME))?></title>
	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
	<link href='../_css/<?php echo CFG_SYSTEM_NAME ?>.css' rel='stylesheet' type='text/css'>
</head>    
<body>
<?php		
    //echo ("Debug: strLimite = " . $strLimite. "<br>");
    //$arrLimit = explode('/', $strLimite);

    //Monta a data intervalo limite que permite edição (inicio e fim)
    //neste momento a variável da entidade contem apenas o mês e dia limite, 
	//logo fica variando entre o ano anterior e o atual neste mesmo dia e mês
	//$strLimIni = (date("Y")-1) ."-". $arrLimit[1] ."-". $arrLimit[0];
    //$strLimFim = date("Y")     ."-". $arrLimit[1] ."-". $arrLimit[0]; 

	//Converte para o formato timestamp(dateime) para fazder a comparação logo abaixo
    $DtLimIni = strtotime($strLimIni); 
    $DtLimFim = strtotime($strLimFim); 
	$DtIns    = strtotime($strDtIns); 
	//echo ( "Debug:<br>" . (date('d/m/Y', $DtLimIni)) . "<br>" . date('d/m/Y', $DtLimFim) . "<br>" .  date('d/m/Y',$DtIns)  );
	//Solicitado por Cabrera a edição de eventos para grupo de usuário ADMIN. By Lumertz - 03.06.2013	
	if ((($DtIns > $DtLimIni) && ($DtIns < $DtLimFim)) 
	      || (strtoupper(getsession(CFG_SYSTEM_NAME . "_grp_user"))== "SU") 
		  || (strtoupper(getsession(CFG_SYSTEM_NAME . "_grp_user"))== "ADMIN") 
		  || (strtoupper($strTpEdEv) == "LIVRE") ) { 
	  //die("<br><br>debug: liberado");	
	  redirect("../_fontes/insupddelmastereditor.php?var_oper=".$strOper."&var_chavereg=".$intCodEvento."&var_basename=".$strBasename);			 	
	}else{
	  //die("<br><br>debug: bloqueado");	
      messageDlg(C_MSG_AVISO ,getTText("titulo_aviso_limite_dt_alt_evento" ,C_NONE),getTText("msg_aviso_limite_dt_alt_evento",C_NONE)." ","","javascript:history.back();");		       
	}
	
?>
</body>
</html>