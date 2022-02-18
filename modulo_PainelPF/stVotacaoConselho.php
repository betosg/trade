<?php
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");



$strDirCliente = getsession(CFG_SYSTEM_NAME . "_dir_cliente");
$objConn = abreDBConn(CFG_DB);



$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
$intAberto = 0;

$strSQL = "SELECT 
                out_cod_pf
              , out_nome
              , out_cpf
              , out_dt_admissao_socio
              , out_nro_socio
              , out_adimplente
              , out_codbarra
              FROM public.sp_socio_adimplente_inadimplente() where out_cod_pf = $intCodDado ";
try{
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}

	foreach ($objResult as $objRS){
        $strAdimplente     = getValue($objRS,"out_adimplente");
        $strNome           = getValue($objRS,"out_nome") ;          
        $strCpf            = getValue($objRS,"out_cpf") ;
        $strDtAdmissao     = getValue($objRS,"out_dt_admissao_socio") ;       
        $numSocio          = getValue($objRS,"out_nro_socio")   ;
        $strCodbarra       = getValue($objRS,"out_codbarra");
      
      }


    if ($strAdimplente != "adimplente"){
    //mensagem("err_sql_titulo"   ,"err_sql_desc",      ,""              , "erro"                 ,1)            ;
    mensagem("Pendências" , "Identificamos que você possui pendências financeiras junto à ABFM.<br> Verifique suas pendências através do menu FINANCEIRO > Abertos, ou entre em contato com nossa administração.", ""   , ""  , "standardinfo", 1);
    die();
}else{
    if ($strCodbarra == ''){$strCodbarra = '123456010';}
    
    mensagem("Votação" , "<a href='https://pvista.proevento.com.br/abfm/modulo_questionario/pesquisa.asp?var_chavereg=5&var_codigos=".$strCodbarra."' target='_blank'>Clique <u>aqui</u> para a acessar a CÉDULA DE VOTAÇÃO</a><br><br><p>Clique nos links abaixo para conhecer os candidatos:</p><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/1_Diretoria_Presidente.pdf' target='_blank'>Diretoria - Presidente</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/1_Diretoria_Secretaria.pdf' target='_blank'>Diretoria - Secretaria</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/1_Diretoria_Tesoureira.pdf' target='_blank'>Diretoria - Tesoureira</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/1_Diretoria_VicePresidente.pdf' target='_blank'>Diretoria - Vice Presidente</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/1_Plano_Acao_ABFM_2022_2023_FINAL.pdf' target='_blank'>Plano de Ação ABFM 2022-2023</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/2_ComiteDeliberativo_AdrianoLegnani.pdf' target='_blank'>Comitê Deliberativo - Adriano Legnani</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/2_ComiteDeliberativo_CeciliaMariaKalilHaddad.pdf' target='_blank'>Comitê Deliberativo - Cecilia Maria Kalil Haddad</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/2_ComiteDeliberativo_DanielCoirodaSilva.pdf' target='_blank'>Comitê Deliberativo - Daniel Coiroda Silva</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/2_ComiteDeliberativo_GabrielaReisdosSantosdeJesus.pdf' target='_blank'>Comitê Deliberativo - Gabriela Reis dos Santos de Jesus</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/2_ComiteDeliberativo_PedroVitorBerchiolIwai.pdf' target='_blank'>Comitê Deliberativo - Pedro Vitor Berchiol Iwai</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/3_ComiteEtica_CinthiaKotzianPereiraBenavides.pdf' target='_blank'>Comitê Ética - Cinthia Kotzian Pereira Benavides</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/3_ComiteEtica_FelipeSimasdosSantos.pdf' target='_blank'>Comitê Ética - Felipe Simas dos Santos</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/3_ComiteEtica_GiselaMenegussi.pdf' target='_blank'>Comitê Ética - Gisela Menegussi</a><li><a href='https://tradeunion.proevento.com.br/abfm/upload/imgdin/3_ComiteEtica_IvanPagotto.pdf' target='_blank'>Comitê Ética - Ivan Pagotto</a>", ""   , ""  , "standardinfo", 1);    
}
 




?>

<?php


$objResult->closeCursor();

	$objConn = NULL;
?>
