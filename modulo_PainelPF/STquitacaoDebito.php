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
              , to_char(out_dt_admissao_socio,'dd/mm/yyyy') as out_dt_admissao_socio
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
  $strChave          = base64_encode(CFG_DB."|".$strCpf."|".$intCodDado ."|".now());
}

if ($strAdimplente != "adimplente"){
    //mensagem("err_sql_titulo"   ,"err_sql_desc",      ,""              , "erro"                 ,1)            ;
    mensagem("Pendências" , "Identificamos que você possui pendências financeiras junto à ABFM.<br> Verifique suas pendências através do menu FINANCEIRO > Abertos, ou entre em contato com nossa administração.", ""   , ""  , "standardinfo", 1);
    die();
}else{
     

$strCarta = getVarEntidade($objConn, "cartaQuitacaoDebito");
$strDataExtenso = date("d") ." de ". getMesExtensoFromMes(date("m")) . " de " . date("Y") ;

  
        $strCarta = str_replace("[LOGOMARCA]"        ,  getVarEntidade($objConn, "logotipo_empresa") , $strCarta);
        $strCarta = str_replace("[DATA_EXTENSO]"     ,  $strDataExtenso                              , $strCarta);
        $strCarta = str_replace("[NOME]"             ,  $strNome                                     , $strCarta);
        $strCarta = str_replace("[CPF]"              ,  $strCpf                                      , $strCarta);
        $strCarta = str_replace("[DT_ADMISSAO_SOCIO]",  $strDtAdmissao                               , $strCarta);
        $strCarta = str_replace("[NUMERO_SOCIO]"     ,  $numSocio                                    , $strCarta);
        $strCarta = str_replace("[CHAVE]"            ,  $strChave                                    , $strCarta);
  
print($strCarta);
$strSqlLog  = " INSERT INTO                               ";     
$strSqlLog .= "  log_gera_documento                       ";             
$strSqlLog .= "      (                                    ";
$strSqlLog .= "      categoria,                           ";         
$strSqlLog .= "      codigo,                              ";      
$strSqlLog .= "      tipo,                                ";    
$strSqlLog .= "      html,                                ";    
$strSqlLog .= "      chave_verificacao,                   ";                 
$strSqlLog .= "      sys_dtt_ins,                         ";           
$strSqlLog .= "      sys_usr_ins                          ";          
$strSqlLog .= "      )                                    ";
$strSqlLog .= "      VALUES (                             ";                   
$strSqlLog .= "      'CartaQuitacaoDebito',               ";          
$strSqlLog .= "      ".$intCodDado.",                     ";       
$strSqlLog .= "      'cad_pf',                            ";     
$strSqlLog .= "      '".str_replace("'","\"",$strCarta).  "',";     
$strSqlLog .= "      '".$strChave.  "',                   ";                  
$strSqlLog .= "      '".now()."',                         ";            
$strSqlLog .= "      '".$strCpf."'                        ";           
 $strSqlLog .= "      );                                  "; 
try{
$objConn->query($strSqlLog);
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}

?>
<script language="javascript">
window.print();
</script>
<?php
}
die();

$objResult->closeCursor();

	$objConn = NULL;
?>
