<?php
header("Content-Type:text/html; charset=iso-8859-1");
include_once("../_database/athdbconn.php");

$intCPF = request("var_cpf");

$objConn  = abreDBConn(CFG_DB);

try {
	$strSQL = "	SELECT
					cad_pf.cod_pf, 
					cad_pf.nome,
					cad_pf.data_nasc,
					cad_pf.sexo,
					cad_endereco_pf.cep,
					cad_endereco_pf.logradouro,
					cad_endereco_pf.numero,
					cad_endereco_pf.complemento,
					cad_endereco_pf.endereco,
					cad_endereco_pf.bairro,
					cad_endereco_pf.cidade,
					cad_endereco_pf.estado,
					cad_endereco_pf.pais,
					cad_endereco_pf.fone,
					cad_endereco_pf.fone_extra1,
					cad_endereco_pf.fone_extra2,
					cad_endereco_pf.fone_extra3,
					cad_endereco_pf.email,
					cad_endereco_pf.email_extra,
					cad_endereco_pf.homepage,
					cad_endereco_pf.cod_endereco
				FROM 
					cad_pf
				JOIN cad_doc_pf ON (cad_pf.cod_pf = cad_doc_pf.cod_pf AND cad_doc_pf.nome = 'CPF' AND cad_doc_pf.valor = '".$intCPF."')
				LEFT JOIN cad_endereco_pf ON cad_pf.cod_pf =  cad_endereco_pf.cod_pf";
	$objResult = $objConn->query($strSQL);
	$objRS = $objResult->fetch();
}catch(PDOException $e){ 	
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
$dtDataNasc = '';
if(getvalue($objRS,'data_nasc') != '')
	$dtDataNasc = date("d/m/Y",strtotime(getvalue($objRS,'data_nasc')));
	
echo (getvalue($objRS,'cod_pf').";".trim(getvalue($objRS,'nome')).";".$dtDataNasc.";".getvalue($objRS,'sexo').";".getvalue($objRS,'cep').";".getvalue($objRS,'logradouro').";".getvalue($objRS,'numero').";".getvalue($objRS,'complemento').";".getvalue($objRS,'endereco').";".getvalue($objRS,'bairro').";".getvalue($objRS,'cidade').";".strtoupper(getvalue($objRS,'estado')).";".getvalue($objRS,'pais').";".getvalue($objRS,'fone').";".getvalue($objRS,'fone_extra1').";".getvalue($objRS,'fone_extra2').";".getvalue($objRS,'fone_extra3').";".getvalue($objRS,'email').";".getvalue($objRS,'email_extra').";".getvalue($objRS,'homepage').";".getvalue($objRS,'cod_endereco'));
$objResult->closeCursor();

?>
