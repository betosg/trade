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

$strMsg = "";

$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
$intAberto = 0;

$strSQL = " SELECT
                t1.out_cod_pf as cod_pf
              , t1.out_nome as nome
              , t1.out_cpf as cpf
              , t1.out_nro_socio as matricula
              , to_char(t2.data_nasc,'dd/mm/yyyy') as data_nasc
              , t2.rg
              , t2.foto
              , t3.graducao_curso
              , t1.out_adimplente
              , t4.nome as categoria
              FROM public.sp_socio_adimplente_inadimplente() t1
              inner join cad_pf t2 on t2.cod_pf = t1.out_cod_pf
              left join cad_categoria t4 on t2.cod_categoria = t4.cod_categoria
              left join cad_pf_curriculo t3 on t3.cod_pf = t1.out_cod_pf
              where
              t1.out_cod_pf = ". $intCodDado ."
              AND T2.dtt_inativo IS NULL
            /*  AND t1.out_adimplente LIKE 'adimplente'*/
            LIMIT 1";

try{
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}
$strEspecialidade = "";

foreach ($objResult as $objRS){
    $strAdimplente      = getValue($objRS,"out_adimplente");
    $strNome            = getValue($objRS,"nome") ;          
    $strCpf             = getValue($objRS,"cpf") ;
    $strDtNasc          = getValue($objRS,"data_nasc") ;       
    $strFoto            = getValue($objRS,"foto");    
    $strRg              = getValue($objRS,"rg");  
    $numSocio           = getValue($objRS,"matricula");  
    $strGraduacao       = getValue($objRS,"graducao_curso");
    $strCategoria       = getValue($objRS,"categoria");
  
  $strSQlEsp = "SELECT DISTINCT
                    T2.NOME AS especialidade  
                FROM CAD_PF_ESPECIALIDADE T1  
                INNER JOIN CAD_ESPECIALIDADE T2 ON T2.cod_especialidade = t1.cod_especialidade
                WHERE t1.cod_pf = ". $intCodDado ;

        try{
            $objResultEsp = $objConn->query($strSQlEsp);
        }catch(PDOException $e){
            mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
            die();	
        }

        foreach ($objResultEsp as $objRSEsp){
            $strEspecialidade   .= getValue($objRSEsp,"especialidade")."<br>";
        }
        if ($strEspecialidade != ""){
            $strEspecialidade = substr($strEspecialidade, 0, -4);  
        }
  $strChave          = base64_encode(CFG_DB."|".$strCpf."|".$intCodDado ."|".now());
  

}

if ($strAdimplente != "adimplente"){
    //mensagem("err_sql_titulo"   ,"err_sql_desc",      ,""              , "erro"                 ,1)            ;
    mensagem("Pendências" , "Identificamos que você possui pendências financeiras junto à ABFM.<br> Verifique suas pendências através do menu FINANCEIRO > Abertos, ou entre em contato com nossa administração.", ""   , ""  , "standardinfo", 1);
    die();
}else{
    if ($strFoto == ""){
           $strMsg .= " - Foto<br>";
    }
    if ($strNome == ""){
        $strMsg .= " - Nome<br>";
    }    
    if ($strDtNasc == ""){
        $strMsg .= " - Data de Nascimento<br>";
    }
    if ($strRg == ""){
        $strMsg .= " - RG<br>";
    }
    if ($strCpf == ""){
        $strMsg .= " - CPF<br>";
    }
    if ($numSocio == ""){
        $strMsg .= " - Matrícula<br>";
    }
    if ($strMsg != ""){
        $strMsg = "Complete seu cadastro através do menu <strong>Dados Pessoais</strong> ou entre em contato com a ABFM para atualizá-lo <br>" . $strMsg;
        mensagem("Pendências" , $strMsg, ""   , ""  , "standardinfo", 1);
        die();
    }

 $strCarta = getVarEntidade($objConn, "layout_carteirinha");
//die();
//$strDataExtenso = date("d") ." de ". getMesExtensoFromMes(date("m")) . " de " . date("Y") ;

  
        $strCarta = str_replace("[foto]"             ,  $strFoto                                     , $strCarta);
        $strCarta = str_replace("[especialidade]"    ,  $strEspecialidade                            , $strCarta);
        $strCarta = str_replace("[nome]"             ,  $strNome                                     , $strCarta);
        $strCarta = str_replace("[cpf]"              ,  $strCpf                                      , $strCarta);
        $strCarta = str_replace("[rg]"               ,  $strRg                                       , $strCarta);
        $strCarta = str_replace("[dt_nasc]"          ,  $strDtNasc                                   , $strCarta);
        $strCarta = str_replace("[matricula]"        ,  $numSocio                                    , $strCarta);
        $strCarta = str_replace("[chave]"            ,  $strChave                                    , $strCarta);
        $strCarta = str_replace("[ano_validade]"     ,  date('Y')                                    , $strCarta);
        $strCarta = str_replace("[graduacao]"        ,  $strGraduacao                                , $strCarta);
        $strCarta = str_replace("[categoria]"        ,  $strCategoria                                , $strCarta);
        //[foto] [nome] [especialidade] [rg] [cpf] [dt_nasc] [matricula] [ano_validade] [chave]
  
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
$strSqlLog .= "      'carteirinha',          ";          
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
