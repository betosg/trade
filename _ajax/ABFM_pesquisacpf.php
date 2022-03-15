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

foreach($objResult as $objRS){

echo(getValue($objRS,"cod_pf") . "|");
echo(getValue($objRS,"old_entidade") . "|");
echo(getValue($objRS,"nome") . "|");
echo(getValue($objRS,"email") . "|");
echo(getValue($objRS,"endprin_cep") . "|");
echo(getValue($objRS,"endprin_logradouro") . "|");
echo(getValue($objRS,"endprin_numero") . "|");
echo(getValue($objRS,"endprin_complemento") . "|");
echo(getValue($objRS,"endprin_bairro") . "|");
echo(getValue($objRS,"endprin_cidade") . "|");
echo(getValue($objRS,"endprin_estado") . "|");
echo(getValue($objRS,"graducao_curso") . "|");
echo(getValue($objRS,"graducao_ano_conclusao") . "|");
echo(getValue($objRS,"graducao_faculdade") . "|");
echo(getValue($objRS,"graducao_arquivo") . "|");
echo(getValue($objRS,"experiencia_profissional") . "|");
echo(getValue($objRS,"experiencia_profissional_arquivo") . "|");
echo(getValue($objRS,"curriculo_arquivo") . "|");
echo(getValue($objRS,"posgraducao_area") . "|");
echo(getValue($objRS,"posgraducao_ano") . "|");
echo(getValue($objRS,"posgraducao_instituicao") . "|");
echo(getValue($objRS,"posgraducao_arquivo") . "|");
echo(getValue($objRS,"norte") . "|");
echo(getValue($objRS,"sul") . "|");
echo(getValue($objRS,"nordeste") . "|");
echo(getValue($objRS,"sudeste") . "|");
echo(getValue($objRS,"centro_oeste	") . "|");
echo(getValue($objRS,"aoutro") . "|");
echo(getValue($objRS,"exterior	") . "|");
echo(getValue($objRS,"radioterapia") . "|");
echo(getValue($objRS,"radiodiagnostico") . "|");
echo(getValue($objRS,"medicina_nuclear") . "|");
echo(getValue($objRS,"protecao") . "|");
echo(getValue($objRS,"ens_superior") . "|");
echo(getValue($objRS,"manut_com_rep") . "|");
echo(getValue($objRS,"ens_medio") . "|");
echo(getValue($objRS,"orgao") . "|");
echo(getValue($objRS,"industria") . "|");
echo(getValue($objRS,"pesquisa") . "|");
echo(getValue($objRS,"routro"));
	
}

$objResult->closeCursor();
?>