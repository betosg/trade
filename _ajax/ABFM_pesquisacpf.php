<?php
header("Content-Type:text/html; charset=iso-8859-1");
include_once("../_database/athdbconn.php");

$intCPF = request("cpf");

$objConn = abreDBConn("tradeunion_abfm");

try {
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
			 
				
			WHERE cad_pf.cpf like '".$intCPF."'";
	$objResult = $objConn->query($strSQL);
	$objRS = $objResult->fetch();
}catch(PDOException $e){ 	
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

echo(getValue($objRS,'cod_pf')."|".getValue($objRS,'old_entidade')."|".getValue($objRS,'nome')."|".getValue($objRS,'email')."|".getValue($objRS,'endprin_cep')."|".getValue($objRS,'endprin_logradouro')."|".getValue($objRS,'endprin_numero')."|".getValue($objRS,'endprin_complemento')."|".getValue($objRS,'endprin_bairro')."|".getValue($objRS,'endprin_cidade')."|".getValue($objRS,'endprin_estado')."|".getValue($objRS,'graducao_curso')."|".getValue($objRS,'graducao_ano_conclusao')."|".getValue($objRS,'graducao_faculdade')."|".getValue($objRS,'graducao_arquivo')."|".getValue($objRS,'experiencia_profissional')."|".getValue($objRS,'experiencia_profissional_arquivo')."|".getValue($objRS,'curriculo_arquivo')."|".getValue($objRS,'posgraducao_area')."|".getValue($objRS,'posgraducao_ano')."|".getValue($objRS,'posgraducao_instituicao')."|".getValue($objRS,'posgraducao_arquivo')."|".getValue($objRS,'norte')."|".getValue($objRS,'sul')."|".getValue($objRS,'nordeste')."|".getValue($objRS,'sudeste')."|".getValue($objRS,'centro_oeste	')."|".getValue($objRS,'aoutro')."|".getValue($objRS,'exterior	')."|".getValue($objRS,'radioterapia')."|".getValue($objRS,'radiodiagnostico')."|".getValue($objRS,'medicina_nuclear')."|".getValue($objRS,'protecao')."|".getValue($objRS,'ens_superior')."|".getValue($objRS,'manut_com_rep')."|".getValue($objRS,'ens_medio')."|".getValue($objRS,'orgao')."|".getValue($objRS,'industria')."|".getValue($objRS,'pesquisa')."|".getValue($objRS,'routro'));
$objResult->closeCursor();

?>