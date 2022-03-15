<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

ini_set("error_reporting","E_ERROR & ~E_WARNING & ~E_NOTICE");

include_once("../_database/athdbconn.php");

$strCPF        = request("cpf");

$objConn = abreDBConn("tradeunion_abfm");



	$strSQL = " SELECT
				  
				 cad_pf.cod_pf
				,cad_pf.old_entidade
				,cad_pf.nome
				,cad_pf.email
				,cad_pf.endprin_cep
				,cad_pf.endprin_logradouro
				,cad_pf.endprin_numero
				,cad_pf.endprin_complemento
				,cad_pf.endprin_bairro
				,cad_pf.endprin_cidade
				,cad_pf.endprin_estado

				,cad_pf_curriculo.graducao_curso
				,cad_pf_curriculo.graducao_ano_conclusao
				,cad_pf_curriculo.graducao_faculdade
				,cad_pf_curriculo.graducao_arquivo
				,cad_pf_curriculo.experiencia_profissional
				,cad_pf_curriculo.experiencia_profissional_arquivo
				,cad_pf_curriculo.curriculo_arquivo
				,cad_pf_curriculo.posgraducao_area
				,cad_pf_curriculo.posgraducao_ano
				,cad_pf_curriculo.posgraducao_instituicao
				,cad_pf_curriculo.posgraducao_arquivo

				,(Select 1 from cad_pf_atuacao_regiao where cod_pf = cad_pf.cod_pf and cod_regiao_pais = 1) as norte		
				,(Select 1 from cad_pf_atuacao_regiao where cod_pf = cad_pf.cod_pf and cod_regiao_pais = 3) as sul		
				,(Select 1 from cad_pf_atuacao_regiao where cod_pf = cad_pf.cod_pf and cod_regiao_pais = 4) as nordeste
				,(Select 1 from cad_pf_atuacao_regiao where cod_pf = cad_pf.cod_pf and cod_regiao_pais = 5) as sudeste		
				,(Select 1 from cad_pf_atuacao_regiao where cod_pf = cad_pf.cod_pf and cod_regiao_pais = 6) as centro_oeste	
				,(Select 1 from cad_pf_atuacao_regiao where cod_pf = cad_pf.cod_pf and cod_regiao_pais = 7) as aoutro
				,(Select 1 from cad_pf_atuacao_regiao where cod_pf = cad_pf.cod_pf and cod_regiao_pais = 8) as exterior	

				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 17) as radioterapia
				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 18) as radiodiagnostico
				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 19) as medicina_nuclear
				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 20) as protecao
				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 21) as ens_superior
				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 22) as manut_com_rep
				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 23) as ens_medio
				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 24) as orgao
				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 25) as industria
				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 26) as pesquisa
				,(Select 1 from cad_pf_atuacao where cod_pf = cad_pf.cod_pf and cod_atuacao = 26) as routro
				  
				  
			 FROM cad_pf 
			 LEFT JOIN cad_pf_curriculo on cad_pf_curriculo.cod_pf = cad_pf.cod_pf
			 
				
			WHERE cad_pf.cpf like '".$strCPF."'";
			  
$objResult = $objConn->query($strSQL);

$strRetorno = "";

foreach($objResult as $objRS){

$strRetorno = $strRetorno . getValue($objRS,"cod_pf") . "|";
$strRetorno = $strRetorno . getValue($objRS,"old_entidade") . "|";
$strRetorno = $strRetorno . getValue($objRS,"nome") . "|";
$strRetorno = $strRetorno . getValue($objRS,"email") . "|";
$strRetorno = $strRetorno . getValue($objRS,"endprin_cep") . "|";
$strRetorno = $strRetorno . getValue($objRS,"endprin_logradouro") . "|";
$strRetorno = $strRetorno . getValue($objRS,"endprin_numero") . "|";
$strRetorno = $strRetorno . getValue($objRS,"endprin_complemento") . "|";
$strRetorno = $strRetorno . getValue($objRS,"endprin_bairro") . "|";
$strRetorno = $strRetorno . getValue($objRS,"endprin_cidade") . "|";
$strRetorno = $strRetorno . getValue($objRS,"endprin_estado") . "|";
$strRetorno = $strRetorno . getValue($objRS,"graducao_curso") . "|";
$strRetorno = $strRetorno . getValue($objRS,"graducao_ano_conclusao") . "|";
$strRetorno = $strRetorno . getValue($objRS,"graducao_faculdade") . "|";
$strRetorno = $strRetorno . getValue($objRS,"graducao_arquivo") . "|";
$strRetorno = $strRetorno . getValue($objRS,"experiencia_profissional") . "|";
$strRetorno = $strRetorno . getValue($objRS,"experiencia_profissional_arquivo") . "|";
$strRetorno = $strRetorno . getValue($objRS,"curriculo_arquivo") . "|";
$strRetorno = $strRetorno . getValue($objRS,"posgraducao_area") . "|";
$strRetorno = $strRetorno . getValue($objRS,"posgraducao_ano") . "|";
$strRetorno = $strRetorno . getValue($objRS,"posgraducao_instituicao") . "|";
$strRetorno = $strRetorno . getValue($objRS,"posgraducao_arquivo") . "|";
$strRetorno = $strRetorno . getValue($objRS,"norte") . "|";
$strRetorno = $strRetorno . getValue($objRS,"sul") . "|";
$strRetorno = $strRetorno . getValue($objRS,"nordeste") . "|";
$strRetorno = $strRetorno . getValue($objRS,"sudeste") . "|";
$strRetorno = $strRetorno . getValue($objRS,"centro_oeste	") . "|";
$strRetorno = $strRetorno . getValue($objRS,"aoutro") . "|";
$strRetorno = $strRetorno . getValue($objRS,"exterior	") . "|";
$strRetorno = $strRetorno . getValue($objRS,"radioterapia") . "|";
$strRetorno = $strRetorno . getValue($objRS,"radiodiagnostico") . "|";
$strRetorno = $strRetorno . getValue($objRS,"medicina_nuclear") . "|";
$strRetorno = $strRetorno . getValue($objRS,"protecao") . "|";
$strRetorno = $strRetorno . getValue($objRS,"ens_superior") . "|";
$strRetorno = $strRetorno . getValue($objRS,"manut_com_rep") . "|";
$strRetorno = $strRetorno . getValue($objRS,"ens_medio") . "|";
$strRetorno = $strRetorno . getValue($objRS,"orgao") . "|";
$strRetorno = $strRetorno . getValue($objRS,"industria") . "|";
$strRetorno = $strRetorno . getValue($objRS,"pesquisa") . "|";
$strRetorno = $strRetorno . getValue($objRS,"routro"));
	
}
print_r($strRetorno);
$objResult->closeCursor();
?>