<?php
session_start();               // Inicia o session
session_cache_limiter("none"); // ATENÇÃO!!! Esta linha estipula o tipo de cache que as páginas terão. Está "none" por causa das exportações que estouram o cache.
set_time_limit(600); 		   // Limite de tempo para execução do script em si (página php)

include_once("athutils.php");
include_once("STathutils.php");
include_once("../_class/multi-language/multilang.php");
include_once("../_class/multi-language/functions.inc.php");

include_once("STconfiginc.php"); // São as constantes de configuração básica, tais como cores, banco e etc...

function abreDBConn($prDBName){
	if($prDBName != "") {
		try{
			$objConn = new PDO("pgsql:host=" . CFG_DB_HOST . ";port=" . CFG_DB_PORT . ";dbname=" . $prDBName . ";user=" . CFG_DB_USER . ";password=" . CFG_DB_PASS);
			$objConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Força para que sejam mostrado erros, se existirem.
			return($objConn);
		} catch(PDOException $e) {
			mensagem("Aviso: Não foi possível se conectar ao banco","
			      		O sistema encontra-se em manutenção.<br>
			      		Aguarde alguns instantes e tente novamente, ou entre em contato com o administrador.<br><br>
			    	", $e->getMessage(),"","standarderro",1);
			die();
	    }
	}
	/*else {
		echo("<script>top.location.href = 'http://" . (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") ? $_SERVER["SERVER_NAME"] : $_SERVER["SERVER_NAME"] . "/" . CFG_SYSTEM_NAME) . "'</script>");
	}*/
}


function mensagem($prTitulo, $prAviso, $prAdText="", $prHyperlink="", $prAcao="standardinfo", $prFlagHTML=0, $prBackground="default"){
 
  if(strpos(strtolower($prAcao),"standard") === false){
	$objLangLocal = new phpMultiLang("../_database/errlang","../_database/errlang");
	if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
		$locale = CFG_LANG;
	}
	else{
		switch(CFG_LANG){
	        case "ptb": $locale = "pt_BR"; break;
	        case "en":  $locale = "en_US"; break;
	        case "es":  $locale = "es_ES"; break;
	    }
	}
	
	$objLangLocal->AssignLanguage(CFG_LANG,NULL,array("LC_ALL",$locale));
	$objLangLocal->AssignLanguageSource(CFG_LANG,CFG_LANG . ".lang",3600);
		
	$objLangLocal->SetLanguage(CFG_LANG,false);

	$strAcao   = $prAcao;
	$strTitulo = $objLangLocal->GetString($prTitulo);
	$strAviso  = $objLangLocal->GetString($prAviso);
	
	$objLang = NULL;
  }
  else{
	$strAcao   = str_replace("standard","",strtolower($prAcao));
	($strAcao == "") ? $strAcao = "aviso" : NULL;
    $strTitulo = $prTitulo;
	$strAviso  = $prAviso;
  }
  
  if($prFlagHTML != 0){ 
    echo("<html>
		  	<head>
				<title></title>
				<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
				<link href=\"../_css/" . CFG_SYSTEM_NAME . ".css\" rel=\"stylesheet\" type=\"text/css\">
			</head>");
	echo("<body style=\"margin:8px;\" text=\"#000000\" bgcolor=\"#FFFFFF\" ");		
	if ($prBackground == "default") {
		echo("background=\"../img/bgFrame_" . CFG_SYSTEM_THEME . "_main.jpg\"");
	}
	else {
		echo("background=\"" . $prBackground . "\"");
	}
	echo(" >");
  }
  echo("<center>");
  athBeginWhiteBox("100%"); //450
  echo("
		<table width=\"100%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
		  <tr>
			<td valign=\"top\" width=\"1%\"><img src=\"../img/mensagem_" .  $strAcao . ".gif\" hspace=\"5\"></td>
			<td width=\"99%\">
  				<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
					<tr><td style=\"font-size:14px;padding-left:5px;padding-bottom:5px;\"><b>" . $strTitulo . "</b></td></tr>
					<tr><td class=\"padrao_gde\" style=\"padding:10px 0px 10px 5px;\"><b>" . $strAviso . "</b></td></tr>
					<tr><td height=\"1\" bgcolor=\"#BDBDBF\"></td></tr>
					<tr><td style=\"padding:10px 0px 10px 5px;\">" . $prAdText . "</td></tr>
					<tr><td align=\"right\" class=\"comment_peq\">" . basename($_SERVER["PHP_SELF"]) . "</td></tr>
				</table>
			</td>
		  </tr>
	  ");
  
  if($prHyperlink != ""){
	echo("
		  <tr>
			<td align=\"right\" colspan=\"2\">
				<button onClick=\"location.href='" . $prHyperlink . "'\">Ok</button>
			</td>
		  </tr>
		"); 
  }  
  echo("</table>");
  athEndWhiteBox();
  echo("</center><br>");

  if($prFlagHTML != 0){ 
    echo("	
			</body>
		  </html>
		");
  }
  
}

function montaSiteAreaSQL($prCodSiteArea, $prTipoCons, $prLocalID = "") {
    $strSQL = " SELECT
				  lj_revista.titulo  AS revista_titulo, 
				  lj_exemplar.titulo AS exemplar_titulo , 
				  lj_secao.titulo    AS secao_titulo , 
				  lj_materia.titulo  AS materia_titulo, 
				  
				  lj_revista.rotulo_menu  AS revista_rotulo, 
				  lj_exemplar.rotulo_menu AS exemplar_rotulo , 
				  lj_secao.rotulo_menu    AS secao_rotulo , 
				  lj_materia.rotulo_menu  AS materia_rotulo, 
				  
 		          lj_revista.cod_revista   AS cod_revista, 
    		      lj_exemplar.cod_exemplar AS cod_exemplar, 
    		      lj_secao.cod_secao       AS cod_secao, 
    		      lj_materia.cod_materia   AS cod_materia, 

                  lj_revista.texto  AS revista_texto, 
                  lj_exemplar.texto AS exemplar_texto , 
                  lj_secao.texto    AS secao_texto , 
                  lj_materia.texto  AS materia_texto, 

                  lj_revista.descricao  AS revista_descricao, 
                  lj_exemplar.descricao AS exemplar_descricao, 
                  lj_secao.descricao    AS secao_descricao, 
                  lj_materia.descricao  AS materia_descricao, 

                  lj_revista.cod_revista  AS revista_cod_pai, 
                  lj_exemplar.cod_revista AS exemplar_cod_pai,  
                  lj_secao.cod_exemplar   AS secao_cod_pai, 
                  lj_materia.cod_secao    AS materia_cod_pai, 

                  lj_revista.img          AS revista_img, 
                  lj_exemplar.img         AS exemplar_img, 
                  lj_secao.img            AS secao_img, 
                  lj_materia.img          AS materia_img, 

                  lj_revista.img_thumb    AS revista_img_thumb, 
                  lj_exemplar.img_thumb   AS exemplar_img_thumb, 
                  lj_secao.img_thumb      AS secao_img_thumb, 
                  lj_materia.img_thumb    AS materia_img_thumb, 

                  lj_revista.img_thumb_over    AS revista_img_thumb_over, 
                  lj_exemplar.img_thumb_over   AS exemplar_img_thumb_over, 
                  lj_secao.img_thumb_over      AS secao_img_thumb_over, 
                  lj_materia.img_thumb_over    AS materia_img_thumb_over, 

                  lj_revista.img_descricao    AS revista_img_descricao, 
                  lj_exemplar.img_descricao   AS exemplar_img_descricao, 
                  lj_secao.img_descricao      AS secao_img_descricao, 
                  lj_materia.img_descricao    AS materia_img_descricao, 

                  lj_site_area.tipo as tipo, lj_site_area.cod_site_area, lj_site_area.cod, 
                  lj_site_area.bloqueado, lj_site_area.ordem, lj_site_area.cod_revista ";

	$strSOLFrom = " FROM ((( (lj_site_area  LEFT  JOIN lj_revista ON lj_site_area.cod_revista=lj_revista.cod_revista) 
                      LEFT JOIN lj_exemplar ON lj_site_area.cod_revista = lj_exemplar.cod_exemplar)
                      LEFT JOIN lj_secao ON lj_site_area.cod_revista = lj_secao.cod_secao)
                      LEFT JOIN lj_materia ON lj_site_area.cod_revista = lj_materia.cod_materia) ";

    switch($prTipoCons) {
		case "JOIN-ALL":
			if(!empty($prLocalID)){
				$strSQL .= $strSOLFrom . " WHERE lj_revista.local_id = '" . $prLocalID . "' AND lj_site_area.cod_site_area = '" . $prCodSiteArea . "' AND lj_site_area.bloqueado = false ORDER BY lj_site_area.ordem ";
			}
			else {
				$strSQL .= $strSOLFrom . " WHERE lj_site_area.cod_site_area = '" . $prCodSiteArea . "' AND lj_site_area.bloqueado = false ORDER BY lj_site_area.ordem ";
			}
			break;
		case "JOIN-ALLIMAGES": 
			$strSQL .= " ,rv_images.img, rv_images.img_thumb " . $strSOLFrom . "
	                       LEFT JOIN rv_images ON (rv_site_area.tipo = rv_images.tipo 
						   AND lj_site_area.cod_revista = rv_images.codigo)
                           WHERE lj_site_area.cod_site_area = '" . $prCodSiteArea . "' AND lj_site_area.bloqueado = false
                           ORDER BY lj_site_area.ordem, RV_IMAGES.ordem ";
			break;
    }

    return($strSQL);
}

//-------------------------------------------------------------------------------------
// Facilita a montagem do SQL de cada Show: RV, EX, SE e MA
//------------------------------------------------------------------------- by Aless --
function montaLogicaRevistaSQL($prTipo, $prCodigo) {	

	$strSQL = " SELECT lj_" . $prtipo . ".cod_" . $prtipo .
			  "       ,lj_" . $prtipo . ".texto 
					  ,lj_" . $prtipo . ".img 
					  ,lj_" . $prtipo . ".img_thumb 
					  ,lj_" . $prtipo . ".img_thumb_over 
			      FROM lj_" . $prTipo . " WHERE cod_" . $prTipo . " = " . $prCodigo;
	return($strSQL);
}

//------------------------------------------------------------------------
// Retorna o tipo do pai de EX, SE, MA
//----------------------------------------------------- by Aless & Davi --
function retTipoPai($prTipo) {
	switch(strtolower($prTipo)) {
		Case "revista":	 
			$strRetTipoPai = "revista";
			break;
		Case "exemplar":
			$strRetTipoPai = "revista";
			break;
		Case "secao":
			$strRetTipoPai = "exemplar";
			break;
		Case "materia":
			$strRetTipoPai = "secao";
			break;
	}
	
	return($strRetTipoPai);
}

//------------------------------------------------------------------------
// Retorna o tipo do filho de RV, EX, SE
//----------------------------------------------------- by Aless & Davi --
function retTipoFilho($prTipoPai) {
	switch(strtolower($prTipoPai)) {
		Case "revista":	
			$strRetTipoFilho = "exemplar";
			break;
		Case "exemplar":
			$strRetTipoFilho = "secao";
			break;
		Case "secao":
			$strRetTipoFilho = "materia";
			break;
		Case "materia":
			$strRetTipoFilho = "materia";
			break;
	}
	
	return($strRetTipoFilho);
}

//-- NOVA ----------------------------------------------------------------------
// Facilita a montagem dos filhos de RV, EX, SE e MA (com LEFT OUTER JOIN)
//----------------------------------------------------------- by Aless & Davi --
function montaChildsSQL($prTipo, $prCodigo, $prArea, $prOrdenacao1, $prOrdenacao2) {
	$strTipoFilho = retTipoFilho($prTipo);
	
	//if($strTipoFilho == $prTipo){ $strTipoFilho = $strTipoFilho . " AS t1"; }

	$strSQL = " SELECT lj_" . $strTipoFilho . ".cod_" . $strTipoFilho . "
				      ,lj_" . $strTipoFilho . ".titulo
				      ,lj_" . $strTipoFilho . ".texto
				      ,lj_" . $strTipoFilho . ".descricao
				      ,lj_" . $strTipoFilho . ".rotulo_menu AS rotulo
				      ,lj_" . $strTipoFilho . ".img
				      ,lj_" . $strTipoFilho . ".img_thumb
				      ,lj_" . $strTipoFilho . ".img_thumb_over
				      ,lj_" . $strTipoFilho . ".img_descricao
				      ,lj_" . $strTipoFilho . ".dtt_publicacao AS dtt_pub
				      ,lj_" . $prTipo . ".cod_"                 . $prTipo . "
				      ,lj_" . $prTipo . ".titulo AS "           . $prTipo . "_titulo 
				      ,lj_" . $prTipo . ".texto AS "            . $prTipo . "_texto
				      ,lj_" . $prTipo . ".descricao AS "        . $prTipo . "_descricao
				      ,lj_" . $prTipo . ".rotulo_menu AS "      . $prTipo . "_rotulo
				      ,lj_" . $prTipo . ".img AS "              . $prTipo . "_img
				      ,lj_" . $prTipo . ".img_thumb AS "        . $prTipo . "_img_thumb
				      ,lj_" . $prTipo . ".img_thumb_over AS "   . $prTipo . "_img_thumb_over
				      ,lj_" . $prTipo . ".img_descricao AS "    . $prTipo . "_img_descricao
				      ,lj_" . $prTipo . ".dtt_publicacao AS "   . $prTipo . "_dtt_pub ";

	$strSQL .= "   FROM lj_" . $prTipo;
	if($prTipo != $strTipoFilho) {
		$strSQL .=  " LEFT OUTER JOIN 
					    lj_" . $strTipoFilho . " ON lj_" . $prTipo . ".cod_" . $prTipo . " = lj_" . $strTipoFilho . ".cod_" . $prTipo;
	}
	//Caso deseje pesquisar incluíndo o parâmetro área
	If(trim($prArea) != "") {
 		$strSQL .= " , lj_site_area ";
	}

	$strSQL .= "  WHERE lj_" . $prTipo . ".cod_" . $prTipo . " = " . $prCodigo . "
				  AND lj_" . $strTipoFilho . ".dtt_inativo IS NULL ";

	//Caso deseje pesquisar incluíndo o parâmetro área
	if(trim($prArea) != "") {
		$strSQL .= "   AND lj_site_area.tipo = '" . $prTipo . "' 
					   AND lj_site_area.cod_site_area = '" . $prArea . "' 
					   AND lj_" . $strTipoFilho . ".cod_revista = lj_site_area.cod_revista ";
	}
	
	$strSQL .= " ORDER BY lj_" . $strTipoFilho . ".dtt_publicacao" . " " . $prOrdenacao1 . ", lj_" . $strTipoFilho . ".ordem  " . $prOrdenacao2;
	
	return($strSQL);
}

function printBanner($prCodBanner, $prArquivo, $prLargura, $prAltura, $prBorda, $prTipo, $objConn){ 
	$strAux = "";
	
	if($prLargura != "") { $strAux .= " width =\"" . $prLargura . "\""; }
	if($prAltura  != "") { $strAux .= " height =\"" . $prAltura . "\""; }
	if($prBorda   != "") { 
		$strAux .= " border=\"" . $prBorda . "\"";
	} else {
		$strAux .= " border=\"0\"";
	}
	
	$prArquivo = strtolower(trim($prArquivo));
	
	switch($prTipo){
		case "IMG": 
			echo("<img src='" . $prArquivo . "'" . $strAux . " alt=\"\">");
			break;
		case "FLASH":
			echo("<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' " . $strAux. ">
					<param name='movie' value='" . $prArquivo . "'>
					<param name='quality' value='high'>
					<embed src='" . $prArquivo . "' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' " . $strAux . "></embed>
				  </object>");
			break;
		case "IFRAME":
			echo("<iframe src=\"" . arquivo . "\" width=\"" . largura . "\" height=\"" . altura . "\" frameborder=\"0\" scrolling=\"no\"></iframe>");
			break;
	}
	
   // ----------------------------------------------------------------------	
   if ($prCodBanner>0) {
   
	   //$objConn = abreDBConn("prostudio_apas"); // ** PROVISÓRIO **
	   //$objConn = abreDBConn(CFG_DB);
	   
	   $objConn->beginTransaction();
	   try{
			$strSQL  = "INSERT INTO stats_bannerlog (cod_banner, bl_tipo, bl_sessionid, bl_ipaddress, sys_usr_ins, sys_dtt_ins) ";
			$strSQL .= " VALUES ('".$prCodBanner."','visit','".session_id()."','".$_SERVER["REMOTE_ADDR"]."','".getsession(CFG_SYSTEM_NAME."_id_usuario")."',CURRENT_TIMESTAMP)";
			$objConn->query($strSQL);
			$objConn->commit();
	   }
		catch(PDOException $e){
			$objConn->rollBack();
			//ATENÇÃO
			// Colocamos o campo bl_unique_computed como UNIQUE, então gravamos as visitas por sessão, e no atualizar (F5) simplemente 
			//dentro de uma mesma sessão o sistema não contará, gerando esta exception e abortando a inserção.
			// ------------------------------------------------------------------------------------------------------------------
			//mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			//die();
	   }
	  //$objConn = NULL;
  }
  // ----------------------------------------------------------------------
}
?>