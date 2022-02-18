<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);
$strMsg = "";

/*** RECEBE PARAMETROS ***/

  $intCodPF	    = request("var_chavereg");
  $intCodPJ     = request("var_cod_pj");

  $strFuncao  	= strtoupper(request("var_funcao"));
  $strAction    = strtoupper(request("var_action"));
//----------------------------------
// Insere dados da PESSOA FISICA 
//----------------------------------
$objConn->beginTransaction();
if ($strAction == "INS"){
	try {

		$strSQL  = " INSERT INTO cad_pf_funcao (cod_pf, funcao, cod_pj) ";
		$strSQL .= " VALUES ( " . $intCodPF . ", '" . $strFuncao . "',".$intCodPJ.") ";
		//die($strSQL);
		$objConn->query($strSQL);

		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	$objConn = NULL;
}

if ($strAction == "VIE"){
	try {
		
			$strSQL  = " SELECT cod_pf_funcao, cod_pf, funcao FROM cad_pf_funcao  ";
			$strSQL .= " where cod_pf = " . $intCodPF ." order by 3" ;
			//die($strSQL);
			$objConn->query($strSQL);

				$comando = $objConn->prepare($strSQL);
				$comando->execute();
					
				while($row = $comando->fetch(PDO::FETCH_OBJ)){
					$funcoes .= $row->cod_pf_funcao."|".$row->cod_pf."|".$row->funcao."//";
				}
				echo($funcoes);
				

			
		}catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			
			die();
		}
		$objConn = NULL;
}

if ($strAction == "DEL"){
	try {

		
		
		$strSQL  = " delete from cad_pf_funcao where cod_pf_funcao = ". $intCodPF ;
		//die($strSQL);
		$objConn->query($strSQL);

		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	$objConn = NULL;
}



//redirect($strLocation);
?>