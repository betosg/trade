<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athutils.php");
erroReport();
$db1 = "tradeunion_abramge";
$db2 = "tradeunion_sinog";
$db3 = "tradeunion_sinamge";
$db4 = "tradeunion_uca";

$objConnAbramge = abreDBConn($db1);
$objConnSinog = abreDBConn($db2);
$objConnSinamge = abreDBConn($db3);
$objConnUca = abreDBConn($db4);
$strMsg = "";
$intCodPjBuscaSinog = "";
$intCodPjBuscaUca = "";
$intCodPjBuscaSinamge = "";

/*** RECEBE PARAMETROS ***/

echo $intCodPJ	    = request("var_chavereg");


try {
		
	$strSQL  = " SELECT   
					cod_tipo_normal,
					cod_tipo_prest,
					cod_categoria,
					cod_segmento,
					cod_atividade,
					cod_atuacao,
					cod_cnae_n1,
					cod_cnae_n2,
					cod_cnae_n3,
					cod_cnae_n4,
					razao_social,
					nome_fantasia,
					nome_comercial,
					cnpj,
					insc_est,
					insc_munic,
					email,
					email_extra,
					website,
					contato,
					arquivo_1,
					arquivo_2,
					arquivo_3,
					endprin_cep,
					endprin_logradouro,
					endprin_numero,
					endprin_complemento,
					endprin_bairro,
					endprin_cidade,
					endprin_estado,
					endprin_pais,
					endprin_fone1,
					endprin_fone2,
					endprin_fone3,
					endprin_fone4,
					endprin_fone5,
					endprin_fone6,
					endcobr_cep,
					endcobr_rotulo,
					endcobr_logradouro,
					endcobr_numero,
					endcobr_complemento,
					endcobr_bairro,
					endcobr_cidade,
					endcobr_estado,
					endcobr_pais,
					endcobr_fone1,
					endcobr_fone2,
					endcobr_fone3,
					endcobr_fone4,
					endcobr_fone5,
					endcobr_fone6,
					dtt_inativo,
					sys_usr_ins,
					sys_dtt_ins,
					sys_dtt_upd,
					sys_usr_upd,
					num_funcionarios,
					dtt_fundacao,
					capital,
					obs,
					tmp_cod_associado,
					old_num_funcionarios,
					endcobr_email,
					endcobr_contato,
					matricula,
					cod_pj_contabil,
					cod_cnae_n5,
					porte,
					categoria,
					id_sussys,
					dt_socio,
					socio,
					img_logo,
					tp_edevento,
					descr_portal,
					arquivo_4,
					dt_alteracao_contrato_social,
					dt_associacao,
					dt_ingresso_quadro_associativo,
					codigo_ans,
					num_beneficiarios_medica,
					num_beneficiarios_odonto,
					vinculo_abramge,
					vinculo_sinamge,
					vinculo_uca,
					vinculo_sinog
				FROM 
					cad_pj where cod_pj = " . $intCodPJ;


	
	//die($strSQL);
	$objConnAbramge->query($strSQL);

		$comando = $objConnAbramge->prepare($strSQL);
		$comando->execute();
			
		while($row = $comando->fetch(PDO::FETCH_OBJ)){			
			$cod_tipo_normal			     =   $row->cod_tipo_normal;
			$cod_tipo_prest				     =   $row->cod_tipo_prest;
			$cod_categoria				     =   $row->cod_categoria;
			$cod_segmento				     =   $row->cod_segmento;
			$cod_atividade				     =   $row->cod_atividade;
			$cod_atuacao				     =   $row->cod_atuacao;
			$cod_cnae_n1				     =   $row->cod_cnae_n1;
			$cod_cnae_n2				     =   $row->cod_cnae_n2;
			$cod_cnae_n3				     =   $row->cod_cnae_n3;
			$cod_cnae_n4				     =   $row->cod_cnae_n4;
			$razao_social				     =   $row->razao_social;
			$nome_fantasia				     =   $row->nome_fantasia;
			$nome_comercial				     =   $row->nome_comercial;
			$cnpj				             =   $row->cnpj;
			$insc_est				         =   $row->insc_est;
			$insc_munic				         =   $row->insc_munic;
			$email				             =   $row->email;
			$email_extra				     =   $row->email_extra;
			$website				         =   $row->website;
			$contato				         =   $row->contato;
			$arquivo_1				         =   $row->arquivo_1;
			$arquivo_2				         =   $row->arquivo_2;
			$arquivo_3				         =   $row->arquivo_3;
			$endprin_cep				     =   $row->endprin_cep;
			$endprin_logradouro			     =   $row->endprin_logradouro;
			$endprin_numero				     =   $row->endprin_numero;
			$endprin_complemento		     =   $row->endprin_complemento;
			$endprin_bairro				     =   $row->endprin_bairro;
			$endprin_cidade				     =   $row->endprin_cidade;
			$endprin_estado				     =   $row->endprin_estado;
			$endprin_pais				     =   $row->endprin_pais;
			$endprin_fone1				     =   $row->endprin_fone1;
			$endprin_fone2				     =   $row->endprin_fone2;
			$endprin_fone3				     =   $row->endprin_fone3;
			$endprin_fone4				     =   $row->endprin_fone4;
			$endprin_fone5				     =   $row->endprin_fone5;
			$endprin_fone6				     =   $row->endprin_fone6;
			$endcobr_cep				     =   $row->endcobr_cep;
			$endcobr_rotulo				     =   $row->endcobr_rotulo;
			$endcobr_logradouro				 =   $row->endcobr_logradouro;
			$endcobr_numero				     =   $row->endcobr_numero;
			$endcobr_complemento			 =   $row->endcobr_complemento;
			$endcobr_bairro				     =   $row->endcobr_bairro;
			$endcobr_cidade				     =   $row->endcobr_cidade;
			$endcobr_estado				     =   $row->endcobr_estado;
			$endcobr_pais				     =   $row->endcobr_pais;
			$endcobr_fone1				     =   $row->endcobr_fone1;
			$endcobr_fone2				     =   $row->endcobr_fone2;
			$endcobr_fone3				     =   $row->endcobr_fone3;
			$endcobr_fone4				     =   $row->endcobr_fone4;
			$endcobr_fone5				     =   $row->endcobr_fone5;
			$endcobr_fone6				     =   $row->endcobr_fone6;
			$dtt_inativo				     =   $row->dtt_inativo;
			$sys_usr_ins				     =   $row->sys_usr_ins;
			$sys_dtt_ins				     =   $row->sys_dtt_ins;
			$sys_dtt_upd				     =   $row->sys_dtt_upd;
			$sys_usr_upd				     =   $row->sys_usr_upd;
			$num_funcionarios				 =   $row->num_funcionarios;
			$dtt_fundacao				     =   $row->dtt_fundacao;
			$capital				         =   $row->capital;
			$obs				             =   $row->obs;
			$tmp_cod_associado				 =   $row->tmp_cod_associado;
			$old_num_funcionarios			 =   $row->old_num_funcionarios;
			$endcobr_email				     =   $row->endcobr_email;
			$endcobr_contato				 =   $row->endcobr_contato;
			$matricula				         =   $row->matricula;
			$cod_pj_contabil				 =   $row->cod_pj_contabil;
			$cod_cnae_n5				     =   $row->cod_cnae_n5;
			$porte				             =   $row->porte;
			$categoria				         =   $row->categoria;
			$id_sussys				         =   $row->id_sussys;
			$dt_socio				         =   $row->dt_socio;
			$socio				             =   $row->socio;
			$img_logo			             =	 $row->img_logo;
			$tp_edevento				     =   $row->tp_edevento;
			$descr_portal				     =   $row->descr_portal;
			$arquivo_4				         =   $row->arquivo_4;
			$dt_alteracao_contrato_social	 =	 $row->dt_alteracao_contrato_social;
			$dt_associacao				     =   $row->dt_associacao;
			$dt_ingresso_quadro_associativo	 =   $row->dt_ingresso_quadro_associativo;
			$codigo_ans				         =   $row->codigo_ans;
			$num_beneficiarios_medica	     =   $row->num_beneficiarios_medica;
			$num_beneficiarios_odonto	     =   $row->num_beneficiarios_odonto;
			$vinculo_abramge			     =   $row->vinculo_abramge;
			$vinculo_sinamge			     =   $row->vinculo_sinamge;
			$vinculo_uca				     =   $row->vinculo_uca;
			$vinculo_sinog				     =   $row->vinculo_sinog;

		}
		
	
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	
	die();
}

$comando = "";
try {
		
echo "<br>busca: ".	$strSQL  = " SELECT cod_pj FROM cad_pj where cnpj = '". $cnpj ."'";
	
	
	    $objConnSinog->query($strSQL);

		$comando = $objConnSinog->prepare($strSQL);
		$comando->execute();
			
		while($row = $comando->fetch(PDO::FETCH_OBJ)){
			$intCodPjBuscaSinog .= $row->cod_pj;
		}
		echo("<br> Achou ou nao? ".$intCodPjBuscaSinog);


		$objConnSinamge->query($strSQL);

		$comando = $objConnSinamge->prepare($strSQL);
		$comando->execute();
			
		while($row = $comando->fetch(PDO::FETCH_OBJ)){
			$intCodPjBuscaSinamge .= $row->cod_pj;
		}
		echo("<br> Achou ou nao? ".$intCodPjBuscaSinamge);


		$objConnUca->query($strSQL);

		$comando = $objConnUca->prepare($strSQL);
		$comando->execute();
			
		while($row = $comando->fetch(PDO::FETCH_OBJ)){
			$intCodPjBuscaUca .= $row->cod_pj;
		}
		echo("<br> Achou ou nao? ".$intCodBuscaUca);
		

	
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	
	die();
}


$cod_tipo_normal                  == "" ?   $cod_tipo_normal                = 'NULL'  : $cod_tipo_normal                 = $cod_tipo_normal ;
$cod_tipo_prest                   == "" ?   $cod_tipo_prest                 = 'NULL'  : $cod_tipo_prest                  = $cod_tipo_prest ;		     
$cod_categoria                    == "" ?   $cod_categoria                  = 'NULL'  : $cod_categoria                   = $cod_categoria ;  		     
$cod_segmento                     == "" ?   $cod_segmento                   = 'NULL'  : $cod_segmento                    = $cod_segmento  ; 	     
$cod_atividade                    == "" ?   $cod_atividade                  = 'NULL'  : $cod_atividade                   = $cod_atividade ; 	     
$cod_atuacao                      == "" ?   $cod_atuacao                    = 'NULL'  : $cod_atuacao                     = $cod_atuacao   ; 	     
$cod_cnae_n1                      == "" ?   $cod_cnae_n1                    = 'NULL'  : $cod_cnae_n1                     = $cod_cnae_n1   ; 	     
$cod_cnae_n2                      == "" ?   $cod_cnae_n2                    = 'NULL'  : $cod_cnae_n2                     = $cod_cnae_n2   ; 	     
$cod_cnae_n3                      == "" ?   $cod_cnae_n3                    = 'NULL'  : $cod_cnae_n3                     = $cod_cnae_n3   ; 	     
$cod_cnae_n4                      == "" ?   $cod_cnae_n4                    = 'NULL'  : $cod_cnae_n4                     = $cod_cnae_n4   ; 	     
$razao_social                     == "" ?   $razao_social                   = 'NULL'  : $razao_social	                 = "'".$razao_social."'"	;	     
$nome_fantasia                    == "" ?   $nome_fantasia                  = 'NULL'  : $nome_fantasia                   = "'".$nome_fantasia."'";		     
$nome_comercial                   == "" ?   $nome_comercial                 = 'NULL'  : $nome_comercial                  = "'".$nome_comercial."'";		     
$cnpj			                  == "" ?   $cnpj			                = 'NULL'  : $cnpj			                 = "'".$cnpj."'"			;            
$insc_est		                  == "" ?   $insc_est		                = 'NULL'  : $insc_est		                 = "'".$insc_est."'"		;         
$insc_munic		                  == "" ?   $insc_munic		                = 'NULL'  : $insc_munic	                     = "'".$insc_munic."'"		;         
$email			                  == "" ?   $email			                = 'NULL'  : $email			                 = "'".$email."'"			;            
$email_extra	                  == "" ?   $email_extra	                = 'NULL'  : $email_extra	                 = "'".$email_extra."'"	;
           
           
$website			              == "" ? $website			 	            = 'NULL'  :  $website			             = "'". $website			    ."'";	         
$contato			              == "" ? $contato			 	            = 'NULL'  :  $contato			             = "'". $contato			    ."'";	         
$arquivo_1			              == "" ? $arquivo_1			            = 'NULL'  :  $arquivo_1			             = "'". $arquivo_1			."'";	         
$arquivo_2			              == "" ? $arquivo_2			            = 'NULL'  :  $arquivo_2			             = "'". $arquivo_2			."'";	         
$arquivo_3			              == "" ? $arquivo_3			            = 'NULL'  :  $arquivo_3			             = "'". $arquivo_3			."'";	         
$endprin_cep		              == "" ? $endprin_cep		 	            = 'NULL'  :  $endprin_cep		             = "'". $endprin_cep		    ."'";		     
$endprin_logradouro	              == "" ? $endprin_logradouro	            = 'NULL'  :  $endprin_logradouro	         = "'". $endprin_logradouro	."'";		     
$endprin_numero		              == "" ? $endprin_numero		            = 'NULL'  :  $endprin_numero		         = "'". $endprin_numero		."'";		     
$endprin_complemento              == "" ? $endprin_complemento              = 'NULL'  :  $endprin_complemento            = "'". $endprin_complemento  ."'";		     
$endprin_bairro		              == "" ? $endprin_bairro		            = 'NULL'  :  $endprin_bairro		         = "'". $endprin_bairro		."'";		     
$endprin_cidade		              == "" ? $endprin_cidade		            = 'NULL'  :  $endprin_cidade		         = "'". $endprin_cidade		."'";		     
$endprin_estado		              == "" ? $endprin_estado		            = 'NULL'  :  $endprin_estado		         = "'". $endprin_estado		."'";		     
$endprin_pais		              == "" ? $endprin_pais		                = 'NULL'  :  $endprin_pais		             = "'". $endprin_pais		    ."'";		     
$endprin_fone1		              == "" ? $endprin_fone1		            = 'NULL'  :  $endprin_fone1		             = "'". $endprin_fone1		."'";		     
$endprin_fone2		              == "" ? $endprin_fone2		            = 'NULL'  :  $endprin_fone2		             = "'". $endprin_fone2		."'";		     
$endprin_fone3		              == "" ? $endprin_fone3		            = 'NULL'  :  $endprin_fone3		             = "'". $endprin_fone3		."'";		     
$endprin_fone4		              == "" ? $endprin_fone4		            = 'NULL'  :  $endprin_fone4		             = "'". $endprin_fone4		."'";		     
$endprin_fone5		              == "" ? $endprin_fone5		            = 'NULL'  :  $endprin_fone5		             = "'". $endprin_fone5		."'";		     
$endprin_fone6		              == "" ? $endprin_fone6		            = 'NULL'  :  $endprin_fone6		             = "'". $endprin_fone6		."'";		     
$endcobr_cep		              == "" ? $endcobr_cep		                = 'NULL'  :  $endcobr_cep		             = "'". $endcobr_cep		    ."'";		     
$endcobr_rotulo		              == "" ? $endcobr_rotulo		            = 'NULL'  :  $endcobr_rotulo		         = "'". $endcobr_rotulo		."'";		     
$endcobr_logradouro	              == "" ? $endcobr_logradouro	            = 'NULL'  :  $endcobr_logradouro	         = "'". $endcobr_logradouro	."'";			 
$endcobr_numero		              == "" ? $endcobr_numero		            = 'NULL'  :  $endcobr_numero		         = "'". $endcobr_numero		."'";		     
$endcobr_complemento              == "" ? $endcobr_complemento              = 'NULL'  :  $endcobr_complemento            = "'". $endcobr_complemento  ."'";			 
$endcobr_bairro		              == "" ? $endcobr_bairro		            = 'NULL'  :  $endcobr_bairro		         = "'". $endcobr_bairro		."'";		     
$endcobr_cidade		              == "" ? $endcobr_cidade		            = 'NULL'  :  $endcobr_cidade		         = "'". $endcobr_cidade		."'";		     
$endcobr_estado		              == "" ? $endcobr_estado		            = 'NULL'  :  $endcobr_estado		         = "'". $endcobr_estado		."'";		     
$endcobr_pais		              == "" ? $endcobr_pais		                = 'NULL'  :  $endcobr_pais		             = "'". $endcobr_pais		    ."'";		     
$endcobr_fone1		              == "" ? $endcobr_fone1		            = 'NULL'  :  $endcobr_fone1		             = "'". $endcobr_fone1		."'";		     
$endcobr_fone2		              == "" ? $endcobr_fone2		            = 'NULL'  :  $endcobr_fone2		             = "'". $endcobr_fone2		."'";		     
$endcobr_fone3		              == "" ? $endcobr_fone3		            = 'NULL'  :  $endcobr_fone3		             = "'". $endcobr_fone3		."'";		     
$endcobr_fone4		              == "" ? $endcobr_fone4		            = 'NULL'  :  $endcobr_fone4		             = "'". $endcobr_fone4		."'";		     
$endcobr_fone5		              == "" ? $endcobr_fone5		            = 'NULL'  :  $endcobr_fone5		             = "'". $endcobr_fone5		."'";		     
$endcobr_fone6		              == "" ? $endcobr_fone6		            = 'NULL'  :  $endcobr_fone6		             = "'". $endcobr_fone6		."'";		
$dtt_inativo                      == "" ? $dtt_inativo                      = 'NULL'  :  $dtt_inativo                    = "'". $dtt_inativo          ."'";				     				   
$sys_usr_ins                      == "" ? $sys_usr_ins                      = 'NULL'  :  $sys_usr_ins                    = "'". $sys_usr_ins          ."'";				     				   
$sys_dtt_ins                      == "" ? $sys_dtt_ins                      = 'NULL'  :  $sys_dtt_ins                    = "'". $sys_dtt_ins          ."'";				     				   
$sys_dtt_upd                      == "" ? $sys_dtt_upd                      = 'NULL'  :  $sys_dtt_upd                    = "'". $sys_dtt_upd          ."'";				     				   
$sys_usr_upd                      == "" ? $sys_usr_upd                      = 'NULL'  :  $sys_usr_upd                    = "'". $sys_usr_upd          ."'";
$num_funcionarios                 == "" ? $num_funcionarios		            = 'NULL'  :  $num_funcionarios               = "'". $num_funcionarios     ."'";
$dtt_fundacao	                  == "" ? $dtt_fundacao			            = 'NULL'  :  $dtt_fundacao	                 = "'". $dtt_fundacao         ."'";
$capital		                  == "" ? $capital				            = 'NULL'  :  $capital		                 = "'". $capital              ."'";
$obs			                  == "" ? $obs				                = 'NULL'  :  $obs			                 = "'". $obs                  ."'";
$tmp_cod_associado                == "" ? $tmp_cod_associado                = 'NULL'  :  $tmp_cod_associado              = $tmp_cod_associado             ;
$num_funcionarios                 == "" ? $old_num_funcionarios             = 'NULL'  :  $old_num_funcionarios           = "'". $old_num_funcionarios  . "'";
$endcobr_email		              == "" ? $endcobr_email		            = 'NULL'  :  $endcobr_email		             = "'". $endcobr_email		 . "'";
$endcobr_contato	              == "" ? $endcobr_contato		            = 'NULL'  :  $endcobr_contato		         = "'". $endcobr_contato		 . "'";	
$matricula			              == "" ? $matricula			            = 'NULL'  :  $matricula			             = "'". $matricula			 . "'";
                      
$cod_pj_contabil	              == "" ? $cod_pj_contabil		            = 'NULL'  : $cod_pj_contabil                 = $cod_pj_contabil;
$cod_cnae_n5		              == "" ?  $cod_cnae_n5			            = 'NULL'  :	$cod_cnae_n5	                 = $cod_cnae_n5	;
                      
$porte	                          == "" ? $porte	                        = 'NULL'  : $porte	                         =  "'" . $porte	 . "'";
$categoria                        == "" ? $categoria                        = 'NULL'  : $categoria                       =  "'" . $categoria . "'";
$id_sussys                        == "" ? $id_sussys                        = 'NULL'  : $id_sussys                       =  "'" . $id_sussys . "'";
                                         
$dt_socio	                      ==  "" ? $dt_socio	                    = 'NULL'  : $dt_socio	                     = "'" . $dt_socio	  . "'"			         ;
$socio			                  ==  "" ? $socio		                    = 'NULL'  : $socio	                         = "'" . $socio		 . "'"           ;
$img_logo		                  ==  "" ? $img_logo	                    = 'NULL'  : $img_logo	                     = "'" . $img_logo	 . "'"           ;
$tp_edevento	                  ==  "" ? $tp_edevento                     = 'NULL'  : $tp_edevento                     = "'" . $tp_edevento . "'"     ;
$descr_portal	                  ==  "" ? $descr_portal                    = 'NULL'  : $descr_portal                    = "'" . $descr_portal . "'"     ;
$arquivo_4	                      ==  "" ? $arquivo_4	                    = 'NULL'  : $arquivo_4	                     = "'" . $arquivo_4	  . "'";

$dt_alteracao_contrato_social	  == "" ? $dt_alteracao_contrato_social	    = 'NULL'  : $dt_alteracao_contrato_social	 = "'"    .$dt_alteracao_contrato_social . "'";
$dt_associacao				      == "" ? $dt_associacao				    = 'NULL'  : $dt_associacao				     = "'".$dt_associacao				    . "'";
$dt_ingresso_quadro_associativo	  == "" ? $dt_ingresso_quadro_associativo	= 'NULL'  : $dt_ingresso_quadro_associativo	 = "'".$dt_ingresso_quadro_associativo	      . "'";


$codigo_ans				          == ""  ? $codigo_ans			            =  'NULL'  : $codigo_ans			         = $codigo_ans				 ;      
$num_beneficiarios_medica         == ""  ? $num_beneficiarios_medica        =  'NULL'  : $num_beneficiarios_medica       = $num_beneficiarios_medica;  
$num_beneficiarios_odonto         == ""  ? $num_beneficiarios_odonto        =  'NULL'  : $num_beneficiarios_odonto       = $num_beneficiarios_odonto;  

$vinculo_abramge                  == ""? $vinculo_abramge = 'false'       :    $vinculo_abramge = 'true';
$vinculo_sinamge                  == ""? $vinculo_sinamge = 'false'       :    $vinculo_sinamge = 'true';
$vinculo_uca	                  == ""? $vinculo_uca	 = 'false'       :    $vinculo_uca	 = 'true'	;
$vinculo_sinog	                   == ""? $vinculo_sinog	 = 'false'       :    $vinculo_sinog	 = 'true'	;





echo "<br> busca: ".$intCodPjBuscaSinog;
	//if ($intCodPjBusca == ""){
		$strSQLIns =  "INSERT INTO cad_pj (cod_tipo_normal      ,cod_tipo_prest       ,cod_categoria        ,cod_segmento         ,cod_atividade         ,   cod_atuacao      ,cod_cnae_n1         ,cod_cnae_n2         ,cod_cnae_n3         ,cod_cnae_n4         ,razao_social         ,nome_fantasia         ,nome_comercial         ,cnpj         ,insc_est         ,insc_munic         ,email         ,email_extra         ,website         ,contato         ,arquivo_1         ,arquivo_2        ,arquivo_3       ,endprin_cep       ,endprin_logradouro       ,endprin_numero       ,endprin_complemento       ,endprin_bairro       ,endprin_cidade       ,endprin_estado       ,endprin_pais       ,endprin_fone1       ,endprin_fone2       ,endprin_fone3       ,endprin_fone4       ,endprin_fone5       ,endprin_fone6       ,endcobr_cep       ,endcobr_rotulo       ,endcobr_logradouro       ,endcobr_numero       ,endcobr_complemento       ,endcobr_bairro       ,endcobr_cidade       ,endcobr_estado       ,endcobr_pais       ,endcobr_fone1       ,endcobr_fone2       ,endcobr_fone3       ,endcobr_fone4       ,endcobr_fone5       ,endcobr_fone6       ,dtt_inativo       ,sys_usr_ins       ,sys_dtt_ins       ,sys_dtt_upd       ,sys_usr_upd       ,num_funcionarios       ,dtt_fundacao       ,capital       ,obs       ,tmp_cod_associado       ,old_num_funcionarios       ,endcobr_email       ,endcobr_contato       ,matricula       ,cod_pj_contabil       ,cod_cnae_n5       ,porte       ,categoria       ,id_sussys       ,dt_socio       ,socio       ,img_logo       ,tp_edevento       ,descr_portal       ,arquivo_4       ,dt_alteracao_contrato_social       ,dt_associacao      ,dt_ingresso_quadro_associativo        ,codigo_ans       ,num_beneficiarios_medica       ,num_beneficiarios_odonto       ,vinculo_abramge       ,vinculo_sinamge       ,vinculo_uca       ,vinculo_sinog) ";
		$strSQLIns .=    "VALUES          (".$cod_tipo_normal .",". $cod_tipo_prest .",". $cod_categoria  .",".  $cod_segmento  .",".  $cod_atividade  .",".  $cod_atuacao  .",".  $cod_cnae_n1  .",".  $cod_cnae_n2  .",".  $cod_cnae_n3  .",".  $cod_cnae_n4  .",".  $razao_social  .",".  $nome_fantasia  .",".  $nome_comercial  .",".  $cnpj  .",".  $insc_est  .",".  $insc_munic  .",".  $email  .",".  $email_extra  .",".  $website  .",".  $contato  .",".  $arquivo_1  .",".  $arquivo_2 .",". $arquivo_3 .",". $endprin_cep .",". $endprin_logradouro .",". $endprin_numero .",". $endprin_complemento .",". $endprin_bairro .",". $endprin_cidade .",". $endprin_estado .",". $endprin_pais .",". $endprin_fone1 .",". $endprin_fone2 .",". $endprin_fone3 .",". $endprin_fone4 .",". $endprin_fone5 .",". $endprin_fone6 .",". $endcobr_cep .",". $endcobr_rotulo .",". $endcobr_logradouro .",". $endcobr_numero .",". $endcobr_complemento .",". $endcobr_bairro .",". $endcobr_cidade .",". $endcobr_estado .",". $endcobr_pais .",". $endcobr_fone1 .",". $endcobr_fone2 .",". $endcobr_fone3 .",". $endcobr_fone4 .",". $endcobr_fone5 .",". $endcobr_fone6 .",". $dtt_inativo .",". $sys_usr_ins .",". $sys_dtt_ins .",". $sys_dtt_upd .",". $sys_usr_upd .",". $num_funcionarios .",". $dtt_fundacao .",". $capital .",". $obs .",". $tmp_cod_associado .",". $old_num_funcionarios .",". $endcobr_email .",". $endcobr_contato .",". $matricula .",". $cod_pj_contabil .",". $cod_cnae_n5 .",". $porte .",". $categoria .",". $id_sussys .",". $dt_socio .",". $socio .",". $img_logo .",". $tp_edevento .",". $descr_portal .",". $arquivo_4 .",". $dt_alteracao_contrato_social .",". $dt_associacao .",". $dt_ingresso_quadro_associativo .",". $codigo_ans .",". $num_beneficiarios_medica .",". $num_beneficiarios_odonto .",". $vinculo_abramge .",". $vinculo_sinamge .",". $vinculo_uca .",". $vinculo_sinog.")";
	//}else{
		$strSQLUpd = "UPDATE cad_pj SET ";
		$strSQLUpd .=					", cod_tipo_normal			     =   ". $cod_tipo_normal;
		$strSQLUpd .=					", cod_tipo_prest				 =   ". $cod_tipo_prest;
		$strSQLUpd .=					", cod_categoria				 =   ". $cod_categoria;
		$strSQLUpd .=					", cod_segmento				     =   ". $cod_segmento;
		$strSQLUpd .=					", cod_atividade				 =   ". $cod_atividade;
		$strSQLUpd .=					", cod_atuacao				     =   ". $cod_atuacao;
		$strSQLUpd .=					", cod_cnae_n1				     =   ". $cod_cnae_n1;
		$strSQLUpd .=					", cod_cnae_n2				     =   ". $cod_cnae_n2;
		$strSQLUpd .=					", cod_cnae_n3				     =   ". $cod_cnae_n3;
		$strSQLUpd .=					", cod_cnae_n4				     =   ". $cod_cnae_n4;
		$strSQLUpd .=					", razao_social				     =   ". $razao_social;
		$strSQLUpd .=					", nome_fantasia				 =   ". $nome_fantasia;
		$strSQLUpd .=					", nome_comercial				 =   ". $nome_comercial;
		$strSQLUpd .=					", cnpj				             =   ". $cnpj;
		$strSQLUpd .=					", insc_est				         =   ". $insc_est;
		$strSQLUpd .=					", insc_munic				     =   ". $insc_munic;
		$strSQLUpd .=					", email				         =   ". $email;
		$strSQLUpd .=					", email_extra				     =   ". $email_extra;
		$strSQLUpd .=					", website				         =   ". $website;
		$strSQLUpd .=					", contato				         =   ". $contato;
		$strSQLUpd .=					", arquivo_1				     =   ". $arquivo_1;
		$strSQLUpd .=					", arquivo_2				     =   ". $arquivo_2;
		$strSQLUpd .=					", arquivo_3				     =   ". $arquivo_3;
		$strSQLUpd .=					", endprin_cep				     =   ". $endprin_cep;
		$strSQLUpd .=					", endprin_logradouro			 =   ". $endprin_logradouro;
		$strSQLUpd .=					", endprin_numero				 =   ". $endprin_numero;
		$strSQLUpd .=					", endprin_complemento		     =   ". $endprin_complemento;
		$strSQLUpd .=					", endprin_bairro				 =   ". $endprin_bairro;
		$strSQLUpd .=					", endprin_cidade				 =   ". $endprin_cidade;
		$strSQLUpd .=					", endprin_estado				 =   ". $endprin_estado;
		$strSQLUpd .=					", endprin_pais				     =   ". $endprin_pais;
		$strSQLUpd .=					", endprin_fone1				 =   ". $endprin_fone1;
		$strSQLUpd .=					", endprin_fone2				 =   ". $endprin_fone2;
		$strSQLUpd .=					", endprin_fone3				 =   ". $endprin_fone3;
		$strSQLUpd .=					", endprin_fone4				 =   ". $endprin_fone4;
		$strSQLUpd .=					", endprin_fone5				 =   ". $endprin_fone5;
		$strSQLUpd .=					", endprin_fone6				 =   ". $endprin_fone6;
		$strSQLUpd .=					", endcobr_cep				     =   ". $endcobr_cep;
		$strSQLUpd .=					", endcobr_rotulo				 =   ". $endcobr_rotulo;
		$strSQLUpd .=					", endcobr_logradouro			 =   ". $endcobr_logradouro;
		$strSQLUpd .=					", endcobr_numero				 =   ". $endcobr_numero;
		$strSQLUpd .=					", endcobr_complemento			 =   ". $endcobr_complemento;
		$strSQLUpd .=					", endcobr_bairro				 =   ". $endcobr_bairro;
		$strSQLUpd .=					", endcobr_cidade				 =   ". $endcobr_cidade;
		$strSQLUpd .=					", endcobr_estado				 =   ". $endcobr_estado;
		$strSQLUpd .=					", endcobr_pais				     =   ". $endcobr_pais;
		$strSQLUpd .=					", endcobr_fone1				 =   ". $endcobr_fone1;
		$strSQLUpd .=					", endcobr_fone2				 =   ". $endcobr_fone2;
		$strSQLUpd .=					", endcobr_fone3				 =   ". $endcobr_fone3;
		$strSQLUpd .=					", endcobr_fone4				 =   ". $endcobr_fone4;
		$strSQLUpd .=					", endcobr_fone5				 =   ". $endcobr_fone5;
		$strSQLUpd .=					", endcobr_fone6				 =   ". $endcobr_fone6;
		$strSQLUpd .=					", dtt_inativo				     =   ". $dtt_inativo;
		$strSQLUpd .=					", sys_usr_ins				     =   ". $sys_usr_ins;
		$strSQLUpd .=					", sys_dtt_ins				     =   ". $sys_dtt_ins;
		$strSQLUpd .=					", sys_dtt_upd				     =   ". $sys_dtt_upd;
		$strSQLUpd .=					", sys_usr_upd				     =   ". $sys_usr_upd;
		$strSQLUpd .=					", num_funcionarios				 =   ". $num_funcionarios;
		$strSQLUpd .=					", dtt_fundacao				     =   ". $dtt_fundacao;
		$strSQLUpd .=					", capital				         =   ". $capital;
		$strSQLUpd .=					", obs				             =   ". $obs;
		$strSQLUpd .=					", tmp_cod_associado			 =   ". $tmp_cod_associado;
		$strSQLUpd .=					", old_num_funcionarios			 =   ". $old_num_funcionarios;
		$strSQLUpd .=					", endcobr_email				 =   ". $endcobr_email;
		$strSQLUpd .=					", endcobr_contato				 =   ". $endcobr_contato;
		$strSQLUpd .=					", matricula				     =   ". $matricula;
		$strSQLUpd .=					", cod_pj_contabil				 =   ". $cod_pj_contabil;
		$strSQLUpd .=					", cod_cnae_n5				     =   ". $cod_cnae_n5;
		$strSQLUpd .=					", porte				         =   ". $porte;
		$strSQLUpd .=					", categoria				     =   ". $categoria;
		$strSQLUpd .=					", id_sussys				     =   ". $id_sussys;
		$strSQLUpd .=					", dt_socio				         =   ". $dt_socio;
		$strSQLUpd .=					", socio				         =   ". $socio;
		$strSQLUpd .=					", img_logo			             =	 ". $img_logo;
		$strSQLUpd .=					", tp_edevento				     =   ". $tp_edevento;
		$strSQLUpd .=					", descr_portal				     =   ". $descr_portal;
		$strSQLUpd .=					", arquivo_4				     =   ". $arquivo_4;
		$strSQLUpd .=					", dt_alteracao_contrato_social	 =	 ". $dt_alteracao_contrato_social;
		$strSQLUpd .=					", dt_associacao				 =   ". $dt_associacao;
		$strSQLUpd .=					", dt_ingresso_quadro_associativo=   ". $dt_ingresso_quadro_associativo;
		$strSQLUpd .=					", codigo_ans				     =   ". $codigo_ans;
		$strSQLUpd .=					", num_beneficiarios_medica	     =   ". $num_beneficiarios_medica;
		$strSQLUpd .=					", num_beneficiarios_odonto	     =   ". $num_beneficiarios_odonto;
		$strSQLUpd .=					", vinculo_abramge			     =   ". $vinculo_abramge;
		$strSQLUpd .=					", vinculo_sinamge			     =   ". $vinculo_sinamge;
		$strSQLUpd .=					", vinculo_uca				     =   ". $vinculo_uca;
		$strSQLUpd .=					", vinculo_sinog				 =   ". $vinculo_sinog;
		$strSQLUpd .=					" where cod_pj = " ;

	//}
echo($strSQL);

echo "<br> busca: ".$intCodPjBuscaSinog;
echo "<br> busca: ".$intCodPjBuscaSinamge;
echo "<br> busca: ".$intCodPjBuscaUca;

if ($intCodPjBuscaSinog == ""){
	$strSQLCadPjSinog = $strSQLIns;
}else{
	$strSQLCadPjSinog = $strSQLUpd . " " . $intCodPjBuscaSinog;
}
$objConnSinog->query($strSQLCadPjSinog);


if ($intCodPjBuscaSinamge == ""){
	$strSQLCadPjSinamge = $strSQLIns;
}else{
	$strSQLCadPjSinamge = $strSQLUpd . " " . $intCodPjBuscaSinamge;
}
$objConnSinamge->query($strSQLCadPjSinamge);


if ($intCodPjBuscaUca == ""){
	$strSQLCadPjUca = $strSQLIns;
}else{
	$strSQLCadPjUca = $strSQLUpd . " " . $intCodPjBuscaUca;
}
$objConnUca->query($strSQLCadPjUca);

echo "<br>Sinog: ".$strSQLCadPjSinog     ."<br>";    
echo "<br>Sinamge".$strSQLCadPjSinamge   ."<br>";   
echo "<br>Uca".$strSQLCadPjUca       ."<br>";



die();    
  
				     
//----------------------------------
// Insere dados da PESSOA FISICA 
//----------------------------------
$objConn->beginTransaction();
if ($strAction == "INS"){
	try {

		$strSQL  = " INSERT INTO cad_pf_funcao (cod_pf, funcao) ";
		$strSQL .= " VALUES ( " . $intCodPF . ", '" . $strFuncao . "') ";
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