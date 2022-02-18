<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$strOperacao  		= request("var_oper");       		// Operação a ser realizada
	$intCodDado   		= request("var_chavereg");   		// Código chave da página
	$strExec      		= request("var_exec");       		// Executor externo (fora do kernel)
	$strPopulate  		= request("var_populate");   		// Flag para necessidade de popular o session ou não
	$strAcao   	  		= request("var_acao");       		// Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
	$intRange			= request("var_range");             // Define o range
	
	$auxCountHOMO = 0;
	$auxCountCARD = 0;
	
	// VERIFICA PJ DA SESSÃO
	$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
	
	$objConn = abreDBConn(CFG_DB);
	
	if ($intRange == "") {
		//Se não veio o range de pesquisa procura a primeira letra que vai ter gente para exibir
		$strLetra = "";
		try{
			$strSQL = " SELECT SUBSTRING(t2.nome, 1, 1) AS letra
						FROM cad_pj t1 
						INNER JOIN relac_pj_pf t3     ON (t1.cod_pj = t3.cod_pj AND t3.dt_demissao IS NULL) 
						INNER JOIN cad_pf t2          ON (t2.cod_pf = t3.cod_pf) 
						LEFT OUTER JOIN cad_cargo t4  ON (t3.cod_cargo = t4.cod_cargo)  
						LEFT OUTER JOIN prd_pedido t7 ON (t7.situacao <> 'cancelado' AND t7.it_tipo = 'homo' AND t7.it_cod_pj_pf = t3.cod_pj_pf AND t3.dt_demissao IS NULL AND t7.dtt_inativo IS NULL) 
						WHERE t1.cod_pj = ".$intCodDado."
						ORDER BY t2.nome
						LIMIT 1 ";	
			$objResult = $objConn->query($strSQL);
		} catch(PDOException $e) {
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
		if($objResult->rowCount()>0){
			$objRS = $objResult->fetch();
			$strLetra = strtoupper(getValue($objRS,"letra"));
		}
		$objResult->closeCursor();
		
		if ($strLetra != "") {
			if (ord($strLetra) <= 68) $intRange = 1;
			if ((ord($strLetra) >= 69) && (ord($strLetra) <= 72)) $intRange = 2;
			if ((ord($strLetra) >= 73) && (ord($strLetra) <= 76)) $intRange = 3;
			if ((ord($strLetra) >= 77) && (ord($strLetra) <= 80)) $intRange = 4;
			if (ord($strLetra) >= 81) $intRange = 5;
		}
	}
	
	switch($intRange){
		case 1:
			$strAlfa = "(upper(t2.nome) >= 'A' AND  upper(t2.nome) <= 'D')";
		break;
		case 2:
			$strAlfa = "(upper(t2.nome) >= 'E' AND  upper(t2.nome) <= 'H')";
		break;
		case 3:
			$strAlfa = "(upper(t2.nome) >= 'I' AND  upper(t2.nome) <= 'L')";
		break;
		case 4:
			$strAlfa = "(upper(t2.nome) >= 'M' AND  upper(t2.nome) <= 'P')";
		break;	
		case 5:
			$strAlfa = "(upper(t2.nome) >= 'Q')";
		break;
		default:
			$strAlfa = "(upper(t2.nome) >= 'A' AND  upper(t2.nome) <= 'D')";
		break;
	}
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_2;
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	// SÓ ENTRA NO PAINEL PJ SE O USUÁRIO DA PJ
	// ESTIVER SETADO NA SESSÃO
	if($intCodDado != ""){
		$objConn->beginTransaction();
		try{
			// LOCALIZA OS COLABORADORES PARA A PJ
			// CORRENTE LOGADA NA SESSÃO. BUSCA SÓ
			// OS COLABORADORES NÃO DEMITIDOS (HOMOLOGADOS)
			$strSQL  = " 
				SELECT 
					  count(t7.cod_pedido) AS qtde_ped_homo
					, t1.cod_pj
					, t2.cod_pf
					, t2.foto
					, t2.matricula||' - '||t2.nome as matr_nome
					, t2.cpf
					, t2.ctps
					, t2.sys_dtt_ins
					, t3.cod_pj_pf
			  		, t3.dt_admissao
					, t4.nome AS cargo
					, t3.dt_demissao
					, t3.tipo
					, t3.categoria
					, t3.funcao
					, t3.obs
					, t3.departamento
					, t3.arquivo_1
					, t3.arquivo_2
					, t3.arquivo_3
					, (CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' AS mais_de_uma_hora 
				FROM cad_pj t1 
				INNER JOIN relac_pj_pf t3     ON (t1.cod_pj = t3.cod_pj AND t3.dt_demissao IS NULL) 
				INNER JOIN cad_pf t2          ON (t2.cod_pf = t3.cod_pf) 
			    LEFT OUTER JOIN cad_cargo t4  ON (t3.cod_cargo = t4.cod_cargo)  
				LEFT OUTER JOIN prd_pedido t7 ON (t7.situacao <> 'cancelado' AND t7.it_tipo = 'homo' AND t7.it_cod_pj_pf = t3.cod_pj_pf AND t3.dt_demissao IS NULL AND t7.dtt_inativo IS NULL) 
				WHERE t1.cod_pj = ".$intCodDado ;
				if ($intRange != "full"){
				$strSQL .= "	AND (".$strAlfa.") ";
				}
				$strSQL .= " 	GROUP BY 
					  t1.cod_pj
					, t2.cod_pf
					, t2.foto
					, t2.nome
					, t2.matricula
					, matr_nome
					, t2.cpf
					, t2.ctps
					, t2.sys_dtt_ins 
					, t3.cod_pj_pf
					, t3.categoria
					, t3.dt_admissao
					, t4.nome
					, t3.dt_demissao
					, t3.tipo
					, t3.funcao
					, t3.obs
					, t3.departamento
					, t3.arquivo_1
					, t3.arquivo_2
					, t3.arquivo_3 
					, (CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' 
				ORDER BY t2.nome";
			$objResult = $objConn->query($strSQL);
			// COLUNAS EXTRA
			// $strSQL .= "   , (t5.dt_validade -CURRENT_DATE ) AS vencida";
			// CLAUSULAS WHERE
			// AND t6.cod_pedido = t5.cod_pedido 
			
			// VERIFICA SE EXISTE PRODUTO CREDENCIAL
			// PARA O ANO VIGENTE, PARA MOSTRA DE ICONE
			$strSQL = "
				SELECT cod_produto
					  ,rotulo
					  ,descricao 
				 FROM prd_produto 
				WHERE CURRENT_DATE BETWEEN dt_ini_val_produto AND dt_fim_val_produto
				  AND tipo = 'card' AND visualizacao = 'publico'
				  AND dtt_inativo IS NULL ORDER BY dt_fim_val_produto DESC";
			$objResultCARDATUAL  = $objConn->query($strSQL);
			$objRSCARDATUAL      = $objResultCARDATUAL->fetch();
			
			// VERIFICA SE EXISTE PRODUTO CREDENCIAL
			// PARA O ANO SEGUINTE, PARA EXIBIÇÃO DE
			// ICONE DE GERAÇÃO DE CREDENCIAL PARA O
			// ANO QUE VEM
			$strSQL = "
				SELECT  cod_produto
					   ,rotulo
					   ,descricao 
					  ,( SELECT count(cod_produto)
						  FROM prd_produto 
						 WHERE CURRENT_DATE <= dt_ini_val_produto 
						   AND CURRENT_DATE <= dt_fim_val_produto
						   AND tipo = 'card' 
						   AND visualizacao = 'publico'
						   AND dtt_inativo IS NULL
					   ) as qtde_prod
				  FROM prd_produto 
				 WHERE CURRENT_DATE <= dt_ini_val_produto 
				   AND CURRENT_DATE <= dt_fim_val_produto
				   AND tipo = 'card' AND visualizacao = 'publico'
				   AND dtt_inativo IS NULL ORDER BY dt_fim_val_produto DESC";
			$objResultCARDPOSTE = $objConn->query($strSQL);
			$objRSCARDPOSTE		= $objResultCARDPOSTE->fetch();
			
			// LOCALIZA CONTA A PAGAR DO ANO VIGENTE OU ANTERIOR QUE ESTEJA ABERTA E QUE SEJA SINDICAL
			// Reativando(??? já deveria estar ativo) código de verificação dessa condição. A idéia é que a PJ não pode pedir 
			// credenciais se tiver um título de sindical em aberto. Isso foi feito uma vez, depois alterado em final de 
			// novembro pelo Gabriel, rediscutido por email com Patrícia e agora está assim a pedido do Alexandre depois da 
			// conversa dele com Aless no dia de hoje
			//
			// by Clv - 09/01/2012
            //------------------------------------------			
            //Alexandre reclamou que as PJs não estavam conseguindo solicitar as credenciais, então identificamos que se trata da
			//regra abaixo, que NÃO permite que uma PJ que tenha boleto sindical em aberto, possa solicitar uma credencial.
			//O Alexandre reclamou que esta não seria a regra correta, porém conforme observação acima escrita pelo Cleverson
			//ficou acertado em janeiro em reunião e depois o próprio Alexandre confirmou em conversa com o Aless que a regra 
			//era para bloquear. 			
 			//Porém, conforme chamado 5134 (Todo 15401) Alexandre pediu para retirarmos a regra.
            //
			// by Vinicius - 04.12.2012
			//------------------------------------------
			$intTotalSindEmAberto = 0;
			/*
			if((getsession(CFG_SYSTEM_NAME."_grp_user") != "ADMIN") && (getsession(CFG_SYSTEM_NAME."_grp_user") != "SU")){
				try{
					$strSQL = " SELECT cod_conta_pagar_receber FROM fin_conta_pagar_receber 
								WHERE situacao ILIKE 'aberto' 
								AND (historico ILIKE '%sindical%' OR historico ILIKE '%sind%' OR historico ILIKE '%GRCS%') 
								AND codigo = ".$intCodDado." AND ano_vcto <= ".date("Y");
					$objResultSIND = $objConn->query($strSQL);
					$intTotalSindEmAberto = $objResultSIND->rowCount();
				}catch(PDOException $e) {
					mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
					die();
				}
				if ($intTotalSindEmAberto == "") $intTotalSindEmAberto = 0;
			}
			*/
			// COMMIT NA TRANSAÇÃO
			$objConn->commit();
		}
		catch(PDOException $e) {
			$objConn->rollBack();
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}

?>
<html>
  <head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<style>
		.headerleft  { width:86px; vertical-align:middle; text-align:left; display:inline-block; } 
		.headerright { width:85px; vertical-align:middle; text-align:right; display:inline-block; }
		ul{ margin-top: 0px; margin-bottom: 0px; }
	    li{ margin-left: 0px; }
	</style>
      <link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css">
      <link href='../_css/tablesort.css' rel='stylesheet' type='text/css'>
      <link href="_css/default.css" rel="stylesheet" type="text/css">	      
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/javascript' src='../_scripts/tablesort.js'></script>	
	<script language="JavaScript" type="text/javascript">
		function switchColor(prObj, prColor){
			prObj.style.backgroundColor = prColor;
		}
		
		var allTrTags = new Array();
		var detailTrFrameAnt = '';
		var moduloDetailAnt = '';
		/*window.onload = function (){
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(getValue($objRS,"cod_pj_pf")); ?>').style.height = 0;
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(getValue($objRS,"cod_pj_pf")); ?>').style.height = document.body.scrollHeight;
			document.frmSizeBody.sizeBody.value = document.body.scrollHeight;
		}*/
	</script>
  </head>
<body style="margin:10px 10px 10px 0px;" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME);?>_main.jpg">
<div style="padding-left:10px;">
<?php athBeginFloatingBox("100%","","<strong>".getTText("colaboradores",C_UCWORDS)."</strong></a>",CL_CORBAR_GLASS_2);?>
<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td align="left">						
            
			<?php
				// concatenamos o link corretamente para os casos
				// onde o redirect tenha sido informado ou não
	
					athBeginCssMenu();
						athCssMenuAddItem("","_self","Colaboradores" ,1);
							athBeginCssSubMenu();								
							athCssMenuAddItem("","_self","escolha a letra",1);
							athBeginCssSubMenu();
								athCssMenuAddItem("STColabAtivos.php?var_chavereg=".$intCodDado."&var_range=1"  ,"_self","A .. D");
								athCssMenuAddItem("STColabAtivos.php?var_chavereg=".$intCodDado."&var_range=2"  ,"_self","E .. H");
								athCssMenuAddItem("STColabAtivos.php?var_chavereg=".$intCodDado."&var_range=3"  ,"_self","I .. L");
								athCssMenuAddItem("STColabAtivos.php?var_chavereg=".$intCodDado."&var_range=4"  ,"_self","M .. P");
								athCssMenuAddItem("STColabAtivos.php?var_chavereg=".$intCodDado."&var_range=5"  ,"_self","Q .. Z");
								athCssMenuAddItem("STColabAtivos.php?var_chavereg=".$intCodDado."&var_range=full"  ,"_self","TODOS ATIVOS");								
							athEndCssSubMenu();							
					athEndCssMenu();		
				?>
                
			</td>
		</tr>
	</table>
	<?php //athEndFloatingBox(); ?>
    <br>	



<table bgcolor="#FFFFFF" style="width:100%;padding-left: 10px;">
	<tr>
    	<td>		
				
			<?php
			
			 // INICIALIZA BOX
           // athBeginFloatingBox("100%","","<strong>".getTText("colaboradores",C_UCWORDS)."</strong></a>",CL_CORBAR_GLASS_2);?>
            <table bgcolor="#FFFFFF" style="width:100%;margin-bottom:0px;" class="tablesort">
                <?php if($objResult->rowCount() > 0){?>
                <thead>
                    <tr>
                        <th width="01%"></th> <!-- REMOVER COLAB -->
                        <th width="01%"></th> <!-- EDITAR DADOS COLAB -->
                        <th width="01%"></th> <!-- FICHA DE ADESÃO OMFG -->
                        <th width="01%"></th> <!-- DEMITIR / HOMOLOGAR -->
                        <th width="01%"></th> <!-- RENOVAR CARD / SOLICITAR -->
                        <th width="01%"></th> <!-- SOLICITAR CARD / ANO SEGUINTE -->
                        <th width="05%" class="sortable-numeric"><?php echo getTText("cod",C_NONE); ?></th>
                        <th width="10%" class="sortable"><?php echo getTText("cpf",C_NONE); ?></th>
                        <th width="35%" class="sortable"><?php echo getTText("matr_nome",C_NONE); ?></th>
                        <th width="15%" class="sortable"><?php echo getTText("funcao",C_NONE); ?></th>
                        <th width="10%" class="sortable"><?php echo getTText("categoria",C_NONE); ?></th>
                        <th width="08%" class="sortable-date-dmy"><?php echo getTText("admissao",C_NONE); ?></th>
                        <th width="08%" class="sortable-date-dmy"><?php echo getTText("validade",C_NONE); ?></th>
                        <th width="01%"></th> <!-- OBSERVAÇÃO COLABORADOR -->
                        <th width="01%"></th> <!-- STATUS HOMOLOGAÇÃO  -->
                        <th width="01%"></th> <!-- STATUS CARTEIRINHA -->
                        <th width="01%"></th> <!-- FOTO DO COLABORADOR -->
                        <th width="01%"></th> <!-- DOC 1 -->
                        <th width="01%"></th> <!-- DOC 2 -->
                        <th width="01%"></th> <!-- DOC 3 -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($objResult as $objRS){ ?>
                    <?php
                        $objConn->beginTransaction();
                        try{
                            // SQL PARA LISTAGEM DE CARDS BUSCA A QTDE
                            // DE PEDIDOS DO TIPO CARD E CARDS ATIVA
							//----------------------------------------
							// Alteramos este SQL para passar a pegar a data de fim da validede
							// do produto e não mais do item no pedido.
							// Dessa forma, caso o usuario altere a data de validade do produto
							// o sistema passara a considerar a nova data imediatamente. Antes, 
							// mesmo que usuário alterasse não faria diferença pois estava 
							// buscando a data que estava gravada no pedido.
							// By Vini - 05.12.2012							
                            $strSQL = " 
                                SELECT 
                                      count(t5.cod_credencial) AS qtde_credencial
                                    , count(t6.cod_pedido)     AS qtde_ped_card
                                    , t2.nome, t5.qtde_impresso, t5.dt_validade
                                    , t6.it_cod_produto
                                FROM  cad_pf t2 
                                LEFT OUTER JOIN  sd_credencial t5 ON ((t5.cod_pf = t2.cod_pf) AND (t5.dtt_inativo IS NULL) AND (CURRENT_DATE <= t5.dt_validade) AND (t5.cod_pj_pf = ".getValue($objRS,"cod_pj_pf")."))
                                LEFT OUTER JOIN  prd_pedido    t6 ON ((t6.situacao <> 'cancelado') AND (t6.it_tipo = 'card') AND (t6.it_cod_pf = t2.cod_pf) AND (t6.cod_pj = ".getValue($objRS,"cod_pj")."))
                                LEFT OUTER JOIN  prd_produto   t7 ON (t7.cod_produto = t6.it_cod_produto )
                                WHERE t2.cod_pf = ".getValue($objRS,"cod_pf")."
                                AND (CURRENT_DATE <= t7.dt_fim_val_produto)
                                GROUP BY 
                                      t2.nome
                                    , t5.qtde_impresso
                                    , t5.dt_validade
                                    , t6.it_cod_produto";
                            // die($strSQL);
                            $objResultPF = $objConn->query($strSQL);
                            $objRSPF     = $objResultPF->fetch();
                            
                            // COMMIT NA TRANSAÇÃO
                            $objConn->commit();
                        }catch(PDOException $e){
                            $objConn->rollBack();
                            mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
                            die();
                        }
                    ?>
                    <tr bgcolor="<?php getLineColor($strColor);?>">
                        <td align="center" valign="top">
                        <?php if(getValue($objRS,"mais_de_uma_hora") == false){?>
                            <a href="STdelcolab.php?var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&cod_emp=<?php echo($intCodDado);?>" target="<?php echo(CFG_SYSTEM_NAME."_frmain");?>" style="border:none;">
                            <img src="../img/icon_trash.gif" title="<?php echo(getTText("remover",C_NONE));?>" border="0">
                            </a>
                        <?php } else{?>
                            <img src="../img/icon_trash_off.gif" title="<?php echo(getTText("remover",C_NONE));?>" border="0">
                        <?php }?>
                        </td>
                        <td align="center" valign="top">
                            <a href="STupdcolab.php?var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_cod_pj=<?php echo($intCodDado);?>" target="<?php echo(CFG_SYSTEM_NAME."_frmain");?>" style="border:none;">
                            <img src="../img/icon_write.gif" title="<?php echo(getTText("editar",C_NONE));?>" border="0">
                            </a>
                        </td>
                        <td align="center">
                            <img src="../img/icon_pdf.gif" title="<?php echo(getTText("ficha_de_adesao",C_NONE));?>" border="0" onClick="AbreJanelaPAGE('STfichaadesao.php?var_chavereg=<?php echo(getValue($objRS,"cod_pj_pf"));?>','950','600');" style="cursor:pointer;">
                        </td>
                        <td align="center">
                        <?php if((getValue($objRS,"mais_de_uma_hora") == true) && (getValue($objRS,"qtde_ped_homo") == 0)){ $auxCountHOMO++;?>
                            <a href="STGeraHomo.php?var_chavereg=<?php echo(getValue($objRS,"cod_pj_pf"));?>" target="<?php echo(CFG_SYSTEM_NAME."_frmain");?>" style="border:none;">
                            <img src="../img/icon_solicitacao_homo.gif" title="<?php echo(getTText("solicitar_homologacao",C_NONE));?>" border="0">
                            </a>
                        <?php } else{?>
                            <img src="../img/icon_solicitacao_homo_off.gif" title="<?php echo(getTText("solicitar_homologacao",C_NONE));?>" border="0">
                        <?php }?>
                        </td>
                        <td align="center">
                        <?php if((getValue($objRSPF,"qtde_credencial") < 1) && (getValue($objRSPF,"qtde_ped_card") < 1) && (getValue($objRSCARDATUAL,"cod_produto") != "") && ($intTotalSindEmAberto == 0)){ $auxCountCARD++;?>
                            <a href="STsoliccard.php?var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_cod_pj=<?php echo(getValue($objRS,"cod_pj"));?>&var_cod_produto=<?php echo(getValue($objRSCARDATUAL,"cod_produto"));?>&chave=0" target="<?php echo(CFG_SYSTEM_NAME."_frmain");?>" style="border:none;">
                            <img src="../img/icon_renova_card.gif" title="<?php echo(getTText("solicitar_credencial",C_NONE));?>" border="0">
                            </a>
                        <?php } else{?>
                            <img src="../img/icon_renova_card_off.gif" border="0" title="<?php echo(getTText("solicitar_credencial",C_NONE));?>">
                        <?php }?>
                        </td>		
                        <td align="center">
                        <!--  Conforme o chamado que gerou a TAREFA 22049 (25/11/2013)
                                     O cliente solicita que (além das regras já discutidas em chamadso anteripores e comentadas 
                              neste código nas linhas (189 a 205 aproximadamente - falando sobre a variável $intTotalSindEmAberto)
                              NESTE CASO SE houver APENAS um produto VIGENTE não deve mostar o ICONCE ATIVADO
							  **ver imagem anexa ao chamado na época: 
                              http://virtualboss.proevento.com.br/virtualboss/upload/proevento/RESPOSTA_Anexos/%7Btgvfnlom5apq44430mm4pj3bq3_221113160805%7D_Image2.jpg)	
                              
                              ANA ÚLTIMA HORA - observamso que o tal icojne apareceu desabuiitado, deduzimso então que o cliente(Alexandre) possa ter modificadpo alguma data de produto
                              desta forma Tatiana e Aless resolveram aguardar e nçao colcoar este IF abaixo que considera a quantidade (qtde_prod)
                              //if( (getValue($objRSCARDPOSTE,"cod_produto") != "") && ($intTotalSindEmAberto == 0) && (getValue($objRSCARDPOSTE,"qtde_prod") > 1) ){
                        //-->
                        <?php if( (getValue($objRSCARDPOSTE,"cod_produto") != "") && ($intTotalSindEmAberto == 0) ){
						?>
                            <a href="STsoliccredencial.php?var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_cod_pj=<?php echo(getValue($objRS,"cod_pj"));?>&var_cod_produto=<?php echo(getValue($objRSCARDPOSTE,"cod_produto"));?>" target="<?php echo(CFG_SYSTEM_NAME."_frmain");?>" style="border:none;">
                            <img src="../img/icon_renova_card_novo.gif" title="<?php echo(getTText("solicitar_proxima_credencial",C_NONE)." - (".getValue($objRSCARDPOSTE,"rotulo").")");?>" border="0">
                            </a>
                        <?php } else{?>
                            <img src="../img/icon_renova_card_novo_off.gif" title="<?php echo(getTText("solicitar_proxima_credencial",C_NONE));?>" border="0">
                        <?php }?>
                        </td>
                        <td align="center" valign="top"><?php echo(getValue($objRS,"cod_pf"));?></td>
                        <td align="center" valign="top"><?php echo(getValue($objRS,"cpf"));?></td>
                        <td align="left" valign="top"><?php echo(getValue($objRS,"matr_nome"));?></td>
                        <td align="center" valign="top"><?php echo(getValue($objRS,"funcao"));?></td>
                        <td align="center" valign="top"><?php echo(getValue($objRS,"categoria"));?></td>
                        <td align="center" valign="top"><span style="font-size:10px;color:#AAA;"><?php echo(dDate(CFG_LANG, getValue($objRS,"dt_admissao"),false));?></span></td>
                        <td align="center" valign="top"><span style="font-size:10px;color:#AAA;"><?php echo(dDate(CFG_LANG, getValue($objRSPF,"dt_validade"),false));?></span></td>
                        <td align="center">
                        <?php if(getValue($objRS,"obs")!=""){?>
                            <img src="../img/icon_obs.gif" title="<?php echo(getValue($objRS,"obs"));?>" /></td>
                        <?php } ?>
                        <td align="center">
                        <?php
                            // DECLARAÇÃO DE STATUS
                            // Colab com Credencial Vencida
                            if(((getValue($objRSPF,"qtde_credencial") < 1)&&(getValue($objRSPF,"qtde_ped_card") < 1))){
                                echo("<img src='../img/icon_sit_vencida.gif' title='".getTText("colab_card_vencido",C_TOUPPER)."'   border='0' />"); 
                            } 
                            // Colab com Credencial OK, mas SEM IMPRESSÃO
                            if((getValue($objRSPF,"qtde_credencial") >= 1)&&(getValue($objRSPF,"qtde_ped_card") >= 1)&&(getValue($objRSPF,"qtde_impresso") == 0)){
                                echo("<img src='../img/icon_sit_zero.gif'    title='".getTText("colab_card_impr_zero",C_TOUPPER)."' border='0' />");
                            } 
                            // CARTEIRINHA ATIVA, OK
                            else if((getValue($objRSPF,"qtde_credencial") >= 1)&&(getValue($objRSPF,"qtde_ped_card") >= 1)){
                                echo("<img src='../img/icon_sit_normal.gif'  title='".getTText("colab_card_ativo",C_TOUPPER)."'     border='0' />");
                            } 
                            // CARTEIRINHA SOLICITADA
                            else if((getValue($objRSPF,"qtde_ped_card") >= 1)&&(getValue($objRSPF,"qtde_credencial") < 1)){
                                echo("<img src='../img/icon_sit_solicitacao.gif' title='".getTText("colab_card_solic",C_TOUPPER)."' border='0' />");
                            }
                        ?>
                        </td>
                        <td align="center">
                        <?php  if(getValue($objRS,"qtde_ped_homo") > 0){?>
                            <img src="../img/icon_sit_saindo.gif" title="<?php echo(getTText("colab_proc_homo",C_TOUPPER));?>"  border="0" />
                        <?php }?>
                        </td>
                        <td align="center">
                        <?php if(getValue($objRS,"foto") != ""){?>
                            <img src="../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/fotospf/".getValue($objRS,"foto"));?>" height="14" id="var_foto_mini_<?php echo(getValue($objRS,"cod_pf"));?>" alt="<?php echo(getTText("clique_ampliar",C_NONE));?>"  ondblclick="this.style.height = '14px';" onClick="this.style.height = '90px';" style="cursor:pointer;" />
                        <?php }?>
                        </td>
                        <td align="center">
                        <?php if(getValue($objRS,"arquivo_1") != ""){?>
                            <img src="../img/icon_anexo.gif" border="0" style="cursor:pointer;" title="DOC 1: <?php echo(getValue($objRS,"arquivo_1"));?>" onClick="AbreJanelaPAGE('../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/docspf/<?php echo(getValue($objRS,"arquivo_1"));?>','800','600');" />
                        <?php }?>
                        </td>
                        <td align="center">
                        <?php if(getValue($objRS,"arquivo_2") != ""){?>
                            <img src="../img/icon_anexo.gif" border="0" style="cursor:pointer;" title="DOC 2: <?php echo(getValue($objRS,"arquivo_2"));?>" onClick="AbreJanelaPAGE('../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/docspf/<?php echo(getValue($objRS,"arquivo_2"));?>','800','600');" />
                        <?php }?>
                        </td>
                        <td align="center">
                        <?php if(getValue($objRS,"arquivo_3") != ""){?>				
                            <img src="../img/icon_anexo.gif" border="0" style="cursor:pointer;" title="DOC 3: <?php echo(getValue($objRS,"arquivo_3"));?>" onClick="AbreJanelaPAGE('../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/docspf/<?php echo(getValue($objRS,"arquivo_3"));?>','800','600');" />
                        <?php }?>
                        </td>
                    </tr>
                <?php }?>
                </tbody>
                <?php } else{?>
                <tbody><tr><td colspan="20" style="border:1px dashed #CCC;color:#999;font-style:italic;text-align:center;"><?php echo(getTText("nenhum_colaborador_cadastrado",C_NONE));?></td></tr></tbody>
                <?php } if(($auxCountHOMO >= 2) || ($auxCountCARD >= 2)){ ?>
                <tfoot style="background:#E3EEF0;" >
                    <tr>
                        <td colspan="3" style="vertical-align:middle;background:#E3EEF0;border-right:none;"></td>
                        <td colspan="1" style="vertical-align:middle;background:#E3EEF0;border-right:none;">
                        <?php if($auxCountHOMO >= 2){?>
                            <a href="STsolictodoshomo.php?var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_cod_pj=<?php echo(getValue($objRS,"cod_pj"));?>&chave=1" target="<?php echo(CFG_SYSTEM_NAME."_frmain");?>">
                            <img src="../img/icon_solicitacao_homo_em_lote.gif" title="<?php echo(getTText("solicitar_homologacao_para_todos_colabs",C_NONE));?>" border="0" />
                            </a>
                        <?php }?>
                        </td>
                        <td style="vertical-align:middle;background:#E3EEF0;border-right:none;">
                        <?php if(($auxCountCARD >= 2) && (getValue($objRSCARDATUAL,"cod_produto") != "")){?>
                            <a href="STsoliccard.php?var_chavereg=<?php echo(getValue($objRS,"cod_pf"));?>&var_cod_pj=<?php echo(getValue($objRS,"cod_pj"));?>&var_cod_produto=<?php echo(getValue($objRSCARDATUAL,"cod_produto"));?>&chave=1" target="<?php echo(CFG_SYSTEM_NAME."_frmain"); ?>">
                            <img src="../img/icon_renova_card_todos.gif" title="<?php echo(getTText("renovar_todas_credenciais",C_NONE))?>" border="0" />
                            </a>
                        <?php }?>
                        </td>
                        <td colspan="15" style="background:#E3EEF0;"></td>
                    </tr>
                </tfoot>
                <?php }?>
            </table>
            
         </td>
    </tr>
</table>
<?php athEndFloatingBox();
				  $objResult->closeCursor(); } ?>
</div>
</body>
</html>