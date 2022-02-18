<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodDado = request("var_chavereg");   // Código chave da página
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn    = abreDBConn(CFG_DB);
	
	
	// LOCALIZA OS DADOS DO COLABORADOR COM BASE NO
	// CODIGO DA RELAÇÃO PJ_PF ENCAMINHADO PARA SCP
	try{
		$strSQL = "
			SELECT 
				  cad_pf.matricula
				, cad_pf.foto
				, cad_pf.nome
				, cad_pf.cpf
				, cad_pf.rg
				, cad_pf.ctps
				, cad_pf.nome_pai
				, cad_pf.nome_mae
				, cad_pf.data_nasc
				, cad_pf.estado_civil
				, cad_pf.naturalidade
				, cad_pf.nacionalidade
				, cad_pf.ctps
				, cad_pf.pis
				, cad_pf.titulo_eleitoral				
				, cad_pf.endprin_logradouro
				, cad_pf.endprin_numero
				, cad_pf.endprin_complemento
				, cad_pf.endprin_bairro
				, cad_pf.endprin_cidade
				, cad_pf.endprin_estado
				, cad_pf.endprin_cep
				, cad_pf.endprin_fone1
				, 'ESPECIAL' as especial
				, relac_pj_pf.tipo
				, relac_pj_pf.funcao
				, relac_pj_pf.sys_dtt_ins
				, cad_pj.razao_social
				, cad_pj.cnpj
				, cad_pj.endprin_logradouro 	AS pj_logradouro
				, cad_pj.endprin_numero			AS pj_numero
				, cad_pj.endprin_complemento	AS pj_complemento
				, cad_pj.endprin_bairro			AS pj_bairro
				, cad_pj.endprin_cidade			AS pj_cidade
				, cad_pj.endprin_estado			AS pj_estado
				, cad_pj.endprin_cep			AS pj_cep
				, cad_pj.endprin_fone1			AS pj_fone1
				, cad_pj.email
			FROM relac_pj_pf
			INNER JOIN cad_pf ON (cad_pf.cod_pf = relac_pj_pf.cod_pf)
			INNER JOIN cad_pj ON (cad_pj.cod_pj = relac_pj_pf.cod_pj)
			WHERE relac_pj_pf.cod_pj_pf = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
		$objRS = $objResult->fetch();
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo(CFG_SYSTEM_TITLE);?></title>
	<script type="text/javascript" language="javascript"></script>
	<link rel="stylesheet" type="text/css" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" />
	<style type="text/css">
		.main{
			width:800px;
			position:relative;
			margin-left:-400px;
			left:50%;
			height:100%;
			/*border:1px dashed #CCC;*/
			font-size:14px;
			font-weight:bold;
		}
		
		.main #header{
			width:100%;
			height:100%;
			margin-left:5px;
		}
		
		.main #header img{ 
			display:inline-block;
			width:171px;
			height:100%;
			border-top:1px dashed black;
			border-bottom:1px dashed black;
			border-left:1px solid black;
			padding-bottom:5px;
		}
		
		.main #header div{ 
			display:inline-block;
			width:600px;
			height:100%;
			border-top:1px dashed black;
			border-bottom:1px dashed black;
			border-left:1px solid black;
			border-right:1px solid black;
			font-size:16px;
			font-weight:bold;
			vertical-align:top;
			padding:6px;
		}
		
		.main #content{
			width:790px;
			height:100%;
			margin:20px;
			margin-bottom:50px;
			/*border:1px solid green;*/
			font-size:14px;
			font-weight:bold;
		}
		
		.main #content_title{
			text-align:center;
			letter-spacing:2px;
			text-transform:uppercase;
			font-size:24px;
			font-weight:bold;
		}
		
		.main #content #data_now{
			width:300px;
			height:100%;
			display:inline-block;
			vertical-align:top;
			padding-top:20px;
		}
		
		.main #content #matricula{
			width:300px;
			height:100%;
			display:inline-block;
			vertical-align:top;
			padding-top:20px;
		}
		
		.main #content #foto{
			height:100%;
			float:right;
			border:2px solid black;
			margin-right:40px;
		}
		
		.main #content #block_title{
			margin-bottom:10px;
			font-size:17px;
			text-transform:uppercase;
			text-decoration:underline;
			width:100%;	
		}
		
		.main #content #content_data{
			font-size:14px;
			font-weight:bold;
			width:100%;
			text-transform:capitalize;
			line-height:25px;
		}
		
		.main #content #content_data_block{
			width:230px;
			height:100%;
			display:inline-block;
			vertical-align:top;
			line-height:25px;
			font-size:14px;
			font-weight:bold;
			text-transform:capitalize;
		}
			
		.main #footer{
			text-align:center;
			font-size:16px;
			font-weight:bold;
		}
	</style>	
</head>
<body>
	<div class="main">
		<div id="header">
			<img src="../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/imgdin/logotipo.jpg" />
			<div><?php echo(getVarEntidade($objConn,"razao_social_completo"));?></div>
		</div>
		<div id="content">
			<div id="content_title"><?php echo(getTText("solicitacao_de_adesao",C_NONE));?></div>
			<span id="foto">
				<?php if(getValue($objRS,"foto") != ""){?>
				<img src="../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/fotospf/<?php echo(getValue($objRS,"foto"));?>" />
				<?php } else{?>
				<img src="../img/unknownuser.jpg" />
				<?php }?>
			</span>
			<span id="data_now"><?php echo(getTText("data",C_NONE));?>: <?php echo(dDate(CFG_LANG,now(),false));?></span>
			<span id="matricula"><?php echo(getTText("matricula_no",C_NONE));?>: <?php echo(getValue($objRS,"matricula"));?><br />
			 <?php echo(getTText("categoria",C_NONE));?>: <?php echo(getValue($objRS,"especial"));?></span>
			<br />
			<br />
			<br />
			<div id="block_title"><?php echo(getTText("seus_dados_pessoais",C_NONE));?>:</div>
			<div id="content_data"><?php echo(getTText("nome_completo",C_NONE));?>: <?php echo(getValue($objRS,"nome"));?></div>
			<div id="content_data"><?php echo(getTText("nome_pai",C_NONE));?>: <?php echo(getValue($objRS,"nome_pai"));?></div>
			<div id="content_data"><?php echo(getTText("nome_mae",C_NONE));?>: <?php echo(getValue($objRS,"nome_mae"));?></div>
			<div id="content_data"><?php echo(getTText("data_nasc",C_NONE));?>: <?php echo(dDate(CFG_LANG,getValue($objRS,"data_nasc"),false));?></div>
			<div id="content_data"<?php echo(getTText("estado_civil",C_NONE));?>: <?php echo(getValue($objRS,"estado_civil"));?></div>
			<div id="content_data"<?php echo(getTText("naturalidade",C_NONE));?>: <?php echo(getValue($objRS,"naturalidade"));?></div>
			<div id="content_data"<?php echo(getTText("nacionalidade",C_NONE));?>: <?php echo(getValue($objRS,"nacionalidade"));?></div>
			<br />
			<br />			
			<div id="block_title"><?php echo(getTText("seus_documentos_pessoais",C_NONE));?>:</div>			
			<span id="content_data_block"><?php echo(getTText("carteira_de_trabalho",C_NONE));?>: <?php echo(getValue($objRS,"ctps"));?></span>
			<span id="content_data_block" style="text-transform:uppercase;"><?php echo(getTText("cpf",C_NONE));?>: <?php echo(getValue($objRS,"cpf"));?></span>
			<span id="content_data_block" style="text-transform:uppercase;"><?php echo(getTText("rg",C_NONE));?>: <?php echo(getValue($objRS,"rg"));?></span>
			<span id="content_data_block" style="text-transform:uppercase;"><?php echo(getTText("numero_pis_pasep",C_NONE));?>: <?php echo(getValue($objRS,"pis"));?></span>
			<span id="content_data_block"><?php echo(getTText("titulo_eleitoral",C_NONE));?>: <?php echo(getValue($objRS,"titulo_eleitoral"));?></span>
	<?php /*?>		<span id="content_data_block"><?php echo(getTText("tipo",C_NONE));?>: <?php echo(getValue($objRS,"tipo"));?></span> <?php */?>
			<br />
			<br />
			<div id="content_data">
				<?php echo(getTText("endereco_principal",C_NONE));?>:
				<?php 
					echo(getValue($objRS,"endprin_logradouro"));
					echo((getValue($objRS,"endprin_numero") != "") ? ", ".getValue($objRS,"endprin_numero") : "");
					echo((getValue($objRS,"endprin_complemento") != "") ? " ".getValue($objRS,"endprin_complemento") : "");
				?>
			</div>
			<span id="content_data_block"><?php echo(getTText("bairro",C_NONE));?>: <?php echo(getValue($objRS,"endprin_bairro"));?></span>
			<span id="content_data_block"><?php echo(getTText("cidade",C_NONE));?>: <?php echo(getValue($objRS,"endprin_cidade"));?></span>
			<span id="content_data_block"><?php echo(getTText("estado",C_NONE));?>: <?php echo(getValue($objRS,"endprin_estado"));?></span>
			<span id="content_data_block"><?php echo(getTText("cep",C_NONE));?>: <?php echo(getValue($objRS,"endprin_cep"));?></span>
			<span id="content_data_block"><?php echo(getTText("telefone",C_NONE));?>: <?php echo(getValue($objRS,"endprin_fone1"));?></span>
			<br />
			<br />
			<br />
			<div id="block_title"><?php echo(getTText("dados_da_empresa",C_NONE));?>:</div>
			<span id="content_data_block" style="width:500px;"><?php echo(getTText("razao_social",C_NONE));?>: <?php echo(getValue($objRS,"razao_social"));?></span>
			<span id="content_data_block" style="width:200px;"><?php echo(getTText("cnpj",C_NONE));?>: <?php echo(getValue($objRS,"cnpj"));?></span>
			<div id="content_data"><?php echo(getTText("funcao",C_NONE));?>: <?php echo(getValue($objRS,"funcao"));?></div>
			<div id="content_data">
				<?php echo(getTText("endereco_principal",C_NONE));?>:
				<?php 
					echo(getValue($objRS,"pj_logradouro"));
					echo((getValue($objRS,"pj_numero") != "") ? ", ".getValue($objRS,"pj_numero") : "");
					echo((getValue($objRS,"pj_complemento") != "") ? " ".getValue($objRS,"pj_complemento") : "");
				?>
			</div>
			<span id="content_data_block"><?php echo(getTText("bairro",C_NONE));?>: <?php echo(getValue($objRS,"pj_bairro"));?></span>
			<span id="content_data_block"><?php echo(getTText("cidade",C_NONE));?>: <?php echo(getValue($objRS,"pj_cidade"));?></span>
			<span id="content_data_block"><?php echo(getTText("estado",C_NONE));?>: <?php echo(getValue($objRS,"pj_estado"));?></span>
			<span id="content_data_block"><?php echo(getTText("cep",C_NONE));?>: <?php echo(getValue($objRS,"pj_cep"));?></span>
			<span id="content_data_block"><?php echo(getTText("telefone",C_NONE));?>: <?php echo(getValue($objRS,"pj_fone1"));?></span>
			<div id="content_data"><?php echo(getTText("email_rh",C_NONE));?>: <?php echo(getValue($objRS,"email"));?></div>
			<br />
			<br />
			<div id="block_title"><?php echo(getTText("forma_de_pagamento",C_NONE));?>:</div>
			<div id="content_data"><?php echo(getTText("1_forma_de_pagamento",C_NONE));?>: <img src="../img/icon_checkbox_off.gif" /><?php echo(getTText("sim",C_NONE));?>&nbsp;&nbsp;<img src="../img/icon_checkbox_off.gif" /><?php echo(getTText("nao",C_NONE));?></div>
			<div id="content_data"><?php echo(getTText("2_forma_de_pagamento",C_NONE));?></div>
			<br />
			<br />
			<span id="content_data_block" style="width:350px;"><?php echo(getTText("data_de_adesao",C_NONE));?>: <?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),false));?></span>
			<span id="content_data_block" style="width:400px;"><?php echo(getTText("assinatura",C_NONE).": ".getTText("linha_ass",C_NONE));?></span>
			<br />
			<br />
			<!--span id="content_data_block" style="width:350px;"><php echo(getTText("visto_do_secretario",C_NONE));?>: <php echo(getTText("linha_ass_min",C_NONE));?></span>
			<span id="content_data_block" style="width:400px;"><php echo(getTText("presidente",C_NONE).": ".getTText("linha_ass",C_NONE));?></span-->
		</div>
		<div id="footer">
			<?php echo(getVarEntidade($objConn,"endereco_completo"));?>
		</div>
	</div>
</body>
</html>
<?php $objConn = NULL; ?>