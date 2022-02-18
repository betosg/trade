<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
//include("../modulo_ArquivosValidacao/STValidacaoTools.php");
$_SESSION['ValidaErro']= "";
//////recebendo dados da dialog//////////
$arqName  = $_POST['uploadArquivo'];

/////////////////////////////////////////
$arquivo = alteraNomeArq($arqName,"I");
$linhaT = ""; $linhaU = "";
analisaArquivo($arquivo);

function analisaArquivo($arquivo){
	$dias = 0;
	$conta = (int)$_POST['conta'];
	$planoConta = (int)$_POST['planoConta'];
	$centroCusto = (int)$_POST['centroCusto'];
	$opcao = (int)$_POST['dtVcto'];
	if ($opcao == 1){
		$dias = (int)$_POST['dias'];
	}
	$msgFinal = "";
	$objConn  = abreDBConn(CFG_DB);
	$imp="";
	$fp = file($arquivo);

	for($z=0; $z < count($fp); $z++){
			$cont = 0;
			$row = str_split($fp[$z]);
			@$first_Number = $row[0].$row[1].$row[2];
		if((!ctype_digit($first_Number)) and (trim ($fp[$z]) <> "")){
			die(mensagem("err_sql_titulo","err_arq_desc","Arquivo Inválido para Validação.",
						  "../modulo_ArquivosValidacao/STArqUpload.php","erro",1));
			}else{
					verificaLinha($row);
				}
			$linha = substr($fp[$z], 13, 1);
			$x=1; $y = 1;
			while($y == 1){
					@$proxLinha = substr($fp[$z+$x], 13, 1);
					if(($linha == "T") && ($proxLinha == "U")){
								$linhaT = $fp[$z];
								$linhaU = $fp[$z+$x];
								$y = 0;
								$verifica = impotaArquivo($linhaT,$linhaU);
								$linhaT = ""; $linhaU = "";
					}else{
							$y = 0;
						}
			}
	}
	if(@$verifica <> 0){
			$dateSysIns = date("Y-m-d h:i:s");
			$userSys =  getsession(CFG_SYSTEM_NAME . "_id_usuario");
			$strSQL = "SELECT out_var_pgto, out_var_gerado, out_var_quitado_tot,
							  out_var_quitado_parc, out_var_duplic, out_var_emp
			 		   FROM sp_processa_retorno_cobr('".$userSys."','".$dateSysIns."',
					   		'".$conta."','".$planoConta."','".$centroCusto."','".$opcao."','".$dias."')";
			$objResult = $objConn->query($strSQL);
			$objRS = $objResult->fetch();
			//pegando return da trigger
			$pgto = getValue($objRS,"out_var_pgto");
			$gerado = getValue($objRS,"out_var_gerado");
			$quitatoTotal = getValue($objRS,"out_var_quitado_tot");
			$quitadoParcial = getValue($objRS,"out_var_quitado_parc");
			$duplicado = getValue($objRS,"out_var_duplic");
			$qtEmpCriada = getValue($objRS,"out_var_emp");
			
			$msgFinal  = $gerado." Titulo(s) Gerado(s).<br><br>";
			$msgFinal .= $pgto." Pagamento(s) Registrado(s).<br><br>";
			$msgFinal .= $quitatoTotal." Titulo(s) Quitado(s) (Total).<br><br>";
			$msgFinal .= $quitadoParcial." Titulo(s) Quitado(s) (Parcial).<br><br>";
			$msgFinal .= $duplicado." Lançamento(s) duplicado(s).<br><br>";
			$msgFinal .= $qtEmpCriada." Empresa(s) Criada(s).<br><br>";
			$_POST['uploadArquivo'] = "";
			mensagem("info_importacao_realizada","info_importacao_realizada_desc",$msgFinal,
					  "../modulo_PainelAdmin/STindex.php","info",1); 
	}
}
function impotaArquivo($rowT,$rowU){
		$erro = ""; $contador = 0;
		$objConn  = abreDBConn(CFG_DB);
		$erro = $_SESSION['ValidaErro'];
		if ($erro == ""){
			$numSeqT = isNumber($rowT, 9, 13,"numSeqT");
			$numSeqU = isNumber($rowU, 9, 13,"numSeqU");
			$codMoviT = isNumber($rowT, 16, 17,"codMoviT");
					if(($codMoviT == "02") || ($codMoviT=="06") || ($codMoviT=="09") || ($codMoviT=="44")){
							$rowT = str_split($rowT); //Converte uma string em um array
							$codArquivo = $_SESSION['ArqValida_numSeqArqHA'];
							//select para pegar o código
							$strSQL = "SELECT cod_arq_retorno FROM arq_retorno_cobr WHERE id_arq = $codArquivo";
							$objResult = $objConn->query($strSQL);
							$objRS = $objResult->fetch();
							$cod_arq_retorno = getValue($objRS,"cod_arq_retorno");
							
							// dados no Segmento T
							$numReg = (int)$numSeqT;
							$tipo_Carteira = (int)isNumber($rowT, 58, 58,"tipo_Carteira");
							$cod_Ocorrencia = (int)$codMoviT;
							$motivo_Ocorrência = (int)preg_replace("/[^0-9]/", "", isAlfa($rowT, 214, 223));//int nao está no if
							$cod_Banco_Cobr = (int)isNumber($rowT,97,99,"cod_Banco_Cobr");
							$cod_Agencia_Cobr = isNumber($rowT, 100,104,"cod_Agencia_Cobr");//varchar
							$Valortit = isNumber($rowT, 82, 96, "vlr_tit");//double
							$vlr_tit = substr($Valortit,-13,11).".".substr($Valortit,-2);
							$ValorDespcobr = isNumber($rowT, 199, 213, "vlr_Desp_cobr");//double
							$vlr_Desp_cobr = substr($ValorDespcobr,-13,11).".".substr($ValorDespcobr,-2);
							$nosso_Numero = isNumber($rowT, 47, 57, "nosso_Numero");//varchar
							$num_documento = trim(isAlfa($rowT, 59, 69)); //varchar
							$dtVctoBanco = isNumber($rowT, 74, 81, "dt vencimento");
							if(($dtVctoBanco=="00000000") or ($dtVctoBanco == "11111111") 
								or ($dtVctoBanco == "99999999")){
									$dtVctoBanco = "NULL";
								}else{
										$dtVctoBanco = "'".convertDate($dtVctoBanco)."'";
									}
							$tipo_inscricao = isNumber($rowT, 133, 133, "tipo inscricao");//varchar		 		
							$num_inscricao = isNumber($rowT, 134, 148, "num incricao");//varchar		
									
							//dados no segment U
							$dt_Ocorrencia = isNumber($rowU, 138, 145, "dt_Ocorrencia");// date
							if($dt_Ocorrencia=="00000000"){
									$dt_Ocorrencia = "NULL";
								}else{
										$dt_Ocorrencia = "'".convertDate($dt_Ocorrencia)."'";
									}
							$Valor_Desp_Outras = isNumber($rowU, 108, 122, "vlr_Desp_Outras");//double
							$vlr_Desp_Outras = substr($Valor_Desp_Outras,-13,11).".".substr($Valor_Desp_Outras,-2);
							$Valor_Abatimento = isNumber($rowU, 48, 62, "vlr_Abatimento");//double
							$vlr_Abatimento = substr($Valor_Abatimento,-13,11).".".substr($Valor_Abatimento,-2);
							$ValorDesconto = isNumber($rowU, 33, 47, "vlr_Desconto");//double
							$vlr_Desconto = substr($ValorDesconto,-13,11).".".substr($ValorDesconto,-2);
							$ValorPago = isNumber($rowU, 78, 92, "vlr_Pago");//double
							$vlr_Pago = substr($ValorPago,-13,11).".".substr($ValorPago,-2);
							$ValorJuros = isNumber($rowU, 18, 32, "vlr_Juros");//double
							$vlr_Juros = substr($ValorJuros,-13,11).".".substr($ValorJuros,-2);
							$ValorOutros = isNumber($rowU, 123, 137, "dt_Outros");//double
							$vlr_Outros = substr($ValorOutros,-13,11).".".substr($ValorOutros,-2);
							
							//dados SYS
							$dateSysIns = date("Y-m-d h:i:s");
							$userSys =  getsession(CFG_SYSTEM_NAME . "_id_usuario");
														
							$strSQL = "INSERT INTO arq_retorno_cobr_item ( 
														dt_vcto_banco,
													 	num_registro, tipo_carteira, cod_ocorrencia,
														motivo_ocorrencia, cod_banco_cobr, cod_agencia_cobr,
														vlr_tit,vlr_desp_cobr, nosso_numero, num_documento,
														dt_ocorrencia,vlr_desp_outras, vlr_abatimento,
														vlr_desconto,vlr_pago, vlr_juros, vlr_outros,
														cod_arq_retorno, tipo_inscricao, num_inscricao,
														sys_dtt_ins, sys_usr_ins
														)					
										   VALUES (	
										   		".$dtVctoBanco.",
												'".$numReg."','".$tipo_Carteira."','".$cod_Ocorrencia."',
												'".$motivo_Ocorrência."','".$cod_Banco_Cobr."',
												'".$cod_Agencia_Cobr."','".$vlr_tit."','".$vlr_Desp_cobr."',
												'".$nosso_Numero."','".$num_documento."',".$dt_Ocorrencia.", 
												'".$vlr_Desp_Outras."','".$vlr_Abatimento."','".$vlr_Desconto."',
												'".$vlr_Pago."','".$vlr_Juros."','".$vlr_Outros."',
												'".$cod_arq_retorno."','".$tipo_inscricao."',".$num_inscricao.",
												'".$dateSysIns."','".$userSys."'
												  )";
												  
								$objConn->query($strSQL);		
								$situacao = "imp";	
								$nome = $_SESSION['novoNome'];		  
								$strSQL = "UPDATE arq_retorno_cobr SET nome = '$nome', situacao = '$situacao',
																	   sys_dtt_upd = '$dateSysIns',
																	   sys_usr_upd = '$userSys'
						   	       			WHERE cod_arq_retorno = $cod_arq_retorno";
								$objConn->query($strSQL);
								$contador++;
								
					}
	}else{
			mensagem("err_sql_titulo","err_arq_desc","Por favor faça a validação do arquivo para analisar os campos que estão com erros","../modulo_ArquivosValidacao/STArqUpload.php","erro",1);
			}
return $contador;			
}

function convertDate($date){
	$data = substr($date,4,4)."-".substr($date,2,2)."-".substr($date,0,2);
	return $data;
}
$objConn = NULL;
?>
