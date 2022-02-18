<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	//Caminho logico para salvar gravar as imagens do certificado no arquivo
	$strCaminhoLogico = findLogicalPath();
	
	//REQUESTS
	$intCodCertificado 		= request("var_chavereg");
	
	
	//Inicia conexão com banco
	$objConn = abreDBConn(CFG_DB);
	try {
	$strSQL = "SELECT 
					sd_certificado.cod_pedido
				  , sd_certificado.cod_pj
				  , cad_pj.razao_social  
				  , sd_certificado.dtt_pedido
				  , cad_pj.cnpj 				    AS cnpj
				  , to_char(dtt_pedido,'DD') 		AS dia_compra
				  , CASE WHEN to_char(dtt_pedido,'MM') = '01' THEN 'janeiro'
				  		 WHEN to_char(dtt_pedido,'MM') = '02' THEN 'fevareiro'
						 WHEN to_char(dtt_pedido,'MM') = '03' THEN 'marco'
						 WHEN to_char(dtt_pedido,'MM') = '04' THEN 'abril'
						 WHEN to_char(dtt_pedido,'MM') = '05' THEN 'maio'
						 WHEN to_char(dtt_pedido,'MM') = '06' THEN 'junho'
						 WHEN to_char(dtt_pedido,'MM') = '07' THEN 'julho'
						 WHEN to_char(dtt_pedido,'MM') = '08' THEN 'agosto'
						 WHEN to_char(dtt_pedido,'MM') = '09' THEN 'setembro'
						 WHEN to_char(dtt_pedido,'MM') = '10' THEN 'outubro'
						 WHEN to_char(dtt_pedido,'MM') = '11' THEN 'novembro'
						 WHEN to_char(dtt_pedido,'MM') = '12' THEN 'dezembro'
						 END AS mes_compra
				  , to_char(dtt_pedido,'YYYY') 		AS ano_compra
				  , to_char(dt_validade,'DD')	 	AS dia_validade
  				  , CASE WHEN to_char(dt_validade,'MM') = '01' THEN 'janeiro'
				  		 WHEN to_char(dt_validade,'MM') = '02' THEN 'fevareiro'
						 WHEN to_char(dt_validade,'MM') = '03' THEN 'marco'
						 WHEN to_char(dt_validade,'MM') = '04' THEN 'abril'
						 WHEN to_char(dt_validade,'MM') = '05' THEN 'maio'
						 WHEN to_char(dt_validade,'MM') = '06' THEN 'junho'
						 WHEN to_char(dt_validade,'MM') = '07' THEN 'julho'
						 WHEN to_char(dt_validade,'MM') = '08' THEN 'agosto'
						 WHEN to_char(dt_validade,'MM') = '09' THEN 'setembro'
						 WHEN to_char(dt_validade,'MM') = '10' THEN 'outubro'
						 WHEN to_char(dt_validade,'MM') = '11' THEN 'novembro'
						 WHEN to_char(dt_validade,'MM') = '12' THEN 'dezembro'
						 END AS mes_validade

				  , to_char(dt_validade,'YYYY') 	AS ano_validade
				  , sd_certificado.dt_validade
				  , sd_certificado.sys_dtt_ins
				  , prd_pedido.it_arq_modelo
				FROM sd_certificado
				INNER JOIN cad_pj ON sd_certificado.cod_pj = cad_pj.cod_pj 
				INNER JOIN prd_pedido ON sd_certificado.cod_pedido = prd_pedido.cod_pedido
				WHERE sd_certificado.cod_certificado =" .$intCodCertificado.";";
	$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	//Cria array associativo para o sql gerado acima
	$objRS = $objResult->fetch();
	
	//Leitura do HTML do modelo de CARD padrão
	//$strStreamHTML 	 	= file_get_contents($strCardPadrao);
	$strStreamHTML 	 		= file_get_contents("../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/modelos/certificado/".getValue($objRS,"it_arq_modelo"));
	$strStreamHTML	   		= preg_replace("/\<TAG_NOME_CLIENTE\>/",$strCaminhoLogico,$strStreamHTML);
	//Insere RAZAO SOCIAL na tag especificada
	//$strStreamHTML	   	= preg_replace("/\<TAG_RAZAO_SOCIAL\>/","Gabriel Schunck",$strStreamHTML);




	//Troca a string '<TAG_' de Todo stream do 
	//modelo [cód. html], por um código php que
	//faz a busca dos dados no $objRS, em fetch
	preg_match_all("/\<TAG_[A-Za-z0-9_]+\>/",$strStreamHTML,$arrMatches);
	foreach($arrMatches[0] as $strMatch){
		$strParse 		= preg_replace("/\<TAG_|\>/","",$strMatch);
		$strStreamHTML	= str_replace($strMatch,getValue($objRS,strtolower($strParse)),$strStreamHTML);
	}
	
	
	// Prefixo FILE
	$strPrefixFile 	= date("Y").date("m").date("d").date("H").date("i").date("s");
	$strFileName	= "certificado_".$strPrefixFile."_".$intCodCertificado.".html";
	$strStreamFile 	= $strStreamHTML;
	
	// Feito o Stream do Arquivo, guarda-o em um html
	$strFileNew = "../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/certificado/".$strFileName;
	file_put_contents($strFileNew,$strStreamFile);
	//redirect($strFileNew);
	//echo($strStreamHTML);
	
	if(file_exists($strFileNew)){
		try {
			$strSQL = " UPDATE sd_certificado 
						SET arquivo = '".$strFileName."' 
						  , sys_usr_upd = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
						  , sys_dtt_upd = CURRENT_TIMESTAMP
						WHERE cod_certificado = ".$intCodCertificado;
			$objResult = $objConn->query($strSQL);
		}
		catch(PDOException $e) {
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
	}
	
?>
<script type="text/javascript">
<!--
window.location = "<?php echo($strFileNew);?>"
//-->
</script>