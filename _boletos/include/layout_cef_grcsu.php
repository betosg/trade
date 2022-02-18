<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Versão Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote; se não, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa				  |
// | 														              |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto CEF: Elizeu Alcantara                         |
// +----------------------------------------------------------------------+
?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>
<HTML>
<HEAD>
<TITLE><?php echo $dadosboleto["identificacao"]; ?></TITLE>
<META http-equiv=Content-Type content=text/html charset=ISO-8859-1>
<meta name="Generator" content="Projeto BoletoPHP - www.boletophp.com.br - Licença GPL" />
<meta name="Description" content="GRCS online - Guia de recolhimento da contribuição sindical">
<meta name="KeyWords" content="GRCSU, nova guia sindical GRCSU, guia de sindical, imposto sindical, código de barras, GRCS online">
<link href="<?php echo($dadosboleto["path_base"])?>/_boletos/css/grcsu.css" type=text/css rel=stylesheet>
<style type=text/css>
<!--.cp {  font: bold 10px Arial; color: black}
<!--.ti {  font: 9px Arial, Helvetica, sans-serif}
<!--.ld { font: bold 15px Arial; color: #000000}
<!--.ct { FONT: 9px "Arial Narrow"; COLOR: #000033} 
<!--.cn { FONT: 9px Arial; COLOR: black }
<!--.bc { font: bold 20px Arial; color: #000000 }
<!--.ld2 { font: bold 12px Arial; color: #000000 }
-->
</style> 
<script language=JavaScript1.2>
	function init(){
		window.focus();
	}
	
	function preloadImages(){
		var d=document;
		if(d.images){
			if(!d.MM_p) d.MM_p=new Array();
			var i,j=d.MM_p.length,a=preloadImages.arguments;
			for(i=0; i<a.length; i++)
				if(a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}
		}
	 }
</script>
</head>
<body bgColor="#FFFFFF" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0" onLoad="preloadImages('<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/p.gif','<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/b.gif','<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/logocaixa.jpg','<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/logocaixamenor.gif','<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif');">
<table cellSpacing=0 cellPadding=0 width=650 border=0>
  <tbody>

  <tr>
    <td vAlign=top width=12>
      <table height=540 cellSpacing=0 cellPadding=0 width=12 border=0>
        <tbody>
        <tr><td vAlign=top noWrap width=12><img height=123 hspace=0 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_viacontribuinte.gif" width=12 vspace=60></td></tr>
        <tr><td vAlign=bottom noWrap width=12 height=513><img height=150 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_documentobanco.gif" width=12></td></tr>
        </tbody>
      </table>
    </td>

    <td width=5>&nbsp;</td>
    <td vAlign=top>
      <table cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr><td class=fa11pbold colSpan=3><img height=34 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/logocaixa.jpg" width=147  align=absMiddle>&nbsp; &nbsp;GRCSU - Guia de Recolhimento da Contribuição Sindical Urbana</td></tr>
        <tr>
          <td class=txtimpbold vAlign=bottom><img height=10 hspace=0 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_dadosentidadesindical.gif" width=160 vspace=2></td><td><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=320></td>
          <td class=txttitimp align=right>

             <table class=fa7p cellSpacing=0 cellPadding=0 width=140 align=right border=0>
              <tbody>
              <tr>
                <td width=1 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p noWrap width=79>&nbsp;Vencimento</td>
                <td width=1 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p noWrap width=58>&nbsp;Exercício</td>
                <td width=1 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>

              </tr>
              <tr>
                <td class=fa7p>&nbsp;<?php echo($dadosboleto["data_vencimento"]);?></td><!-- VENCIMENTO -->
                <td class=fa7p>&nbsp;<?php echo($dadosboleto["exercicio"]);?></td>		<!-- EXERCICIO -->
              </tr>
              <tr><td bgColor=#000000 colSpan=5 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=140></td></tr>
              <tr><td colSpan=5 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
              </tbody>

             </table>
          </td>
        </tr>
       </tbody>
      </table>
      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td class=fa7p noWrap width=1><img height=14 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>

          <td class=fa7p noWrap width=449>&nbsp;Nome da Entidade</td>
          <td class=fa7p width=1 bgColor=#000000><img height=14 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=178>&nbsp;Código da Entidade Sindical</td>
          <td class=fa7p noWrap width=1 bgColor=#000000><img height=14 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td class=fa7p width=1><img height=14 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=449 height=12>&nbsp;<?php echo($dadosboleto["cedente_nome_completo"]);?></td>

          <td class=fa7p width=1 bgColor=#000000><img height=14 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=178>&nbsp;<?php echo($dadosboleto["cedente_cod_entsindical"]);?></td>
          <td class=fa7p width=1 bgColor=#000000><img height=14 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr><td bgColor=#000000 colSpan=7 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td colSpan=7><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        </tbody>
      </table>

      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=250>&nbsp;Endereço</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=80>&nbsp;Número</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>

          <td class=fa7p noWrap width=117>&nbsp;Complemento</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=178>&nbsp;CNPJ da Entidade</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td class=fa7p>&nbsp;<?php echo($dadosboleto["cedente_logradouro_end"]);?></td>

          <td class=fa7p width=80>&nbsp;<?php echo($dadosboleto["cedente_numero_end"]);?></td>
          <td class=fa7p width=117>&nbsp;<?php echo($dadosboleto["cedente_complemento_end"]);?></td>
          <td class=fa7p noWrap width=178>&nbsp;<?php echo($dadosboleto["cedente_cpf_cnpj"]);?></td>
        </tr>
        <tr><td bgColor=#000000 colSpan=9 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td colSpan=9><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        </tbody>

      </table>
      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=230>&nbsp;Bairro/Distrito</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=100>&nbsp;CEP</td>

          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=265>&nbsp;Cidade/Município</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=30>&nbsp;UF</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td class=fa7p width=250>&nbsp;<?php echo($dadosboleto["cedente_bairro_end"]);?></td>

          <td class=fa7p width=70>&nbsp;<?php echo($dadosboleto["cedente_cep_end"]);?></td>
          <td class=fa7p width=275>&nbsp;<?php echo($dadosboleto["cedente_cidade_end"]);?> </td>
          <td class=fa7p noWrap width=1>&nbsp;<?php echo($dadosboleto["cedente_estado_end"]);?></td>
        </tr>
        <tr><td bgColor=#000000 colSpan=9 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td colSpan=9><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        </tbody>

      </table>
      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td vAlign=bottom noWrap width=1 rowSpan=3><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=txtimpbold noWrap colSpan=3><img height=10 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/arquivos/titDadosContribuinte.gif" width=129></td>
          <td vAlign=bottom noWrap width=1 bgColor=#000000 rowSpan=3><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>

          <td class=fa7p noWrap width=469>&nbsp;Nome/Razão Social/Denominação Social</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=158>&nbsp;CPF/CNPJ/Código do Contribuinte</td>
        </tr>
        <tr>
          <td class=fa7p width=469 height=12>&nbsp;<?php echo($dadosboleto["sacado"]);?></td>
          <td class=fa7p noWrap width=158>&nbsp;<?php echo($dadosboleto["sacado_cnpj"]);?></td>

        </tr>
        <tr>
          <td bgColor=#000000 colSpan=5 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td>
        </tr>
        <tr><td colSpan=5><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        </tbody>
      </table>
      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>

        <tr>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=250>&nbsp;Endereço</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=80>&nbsp;Número</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=296>&nbsp;Complemento</td>

          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td class=fa7p width=250>&nbsp;<?php echo($dadosboleto["sacado_logradouro_end"]);?></td>
          <td class=fa7p width=70>&nbsp;<?php echo($dadosboleto["sacado_numero_end"]);?></td>
          <td class=fa7p width=306>&nbsp;<?php echo($dadosboleto["sacado_complemento_end"]);?></td>
        </tr>

        <tr><td bgColor=#000000 colSpan=7 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td colSpan=7><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        </tbody>
      </table>
      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=80>&nbsp;CEP</td>

          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=250>&nbsp;Bairro/Distrito</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=174>&nbsp;Cidade/Município</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=30>&nbsp;UF</td>
          <td class=fa7p noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>

          <td class=fa7p noWrap width=90>&nbsp;Código Atividade</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td class=fa7p width=80>&nbsp;<?php echo($dadosboleto["sacado_cep_end"]);?></td>
          <td class=fa7p width=240>&nbsp;<?php echo($dadosboleto["sacado_bairro_end"]);?></td>
          <td class=fa7p width=184>&nbsp;<?php echo($dadosboleto["sacado_cidade_end"]);?></td>

          <td class=fa7p noWrap width=30>&nbsp;<?php echo($dadosboleto["sacado_estado_end"]);?></td>
          <td class=fa7p noWrap width=90 align=center>&nbsp;<!--<?php echo($dadosboleto["cod_atividade"]);?>--><?php echo($dadosboleto["cod_cnae_grupo"]);?></td><!-- CNAE -->
        </tr>
        <tr>
          <td bgColor=#000000 colSpan=11 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td>
        </tr>
        <tr><td colSpan=11><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        </tbody>

      </table>
      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td noWrap width=1 rowSpan=3></td>
          <td class=txtimpbold noWrap width=399><img height=10 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_dadosreferencia.gif" width=214></td>
          <td noWrap width=1></td>
          <td class=txtimpbold noWrap width=228><img height=10 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_dadoscontribuicao.gif" width=130></td>
          <td noWrap width=1></td>

        </tr>
        <tr>
          <td class=fa7p noWrap width=399>&nbsp;<B>Categoria</B></td>
          <td vAlign=bottom noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=228>&nbsp;(=) Valor do Documento</td>
          <td vAlign=bottom noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td class=fa7p noWrap width=399>&nbsp;
		  <?php if ($DadosBoleto_CATEGORIA == "OUTRAS") { 
		  //$DadosBoleto_HISTORICO = "xxxSIND";
		  		//echo (strpos(strtoupper("xx".$DadosBoleto_HISTORICO),"SIND"));
				$posicao = strpos(strtoupper("xx".$DadosBoleto_HISTORICO),"SIND");
				//if($posicao == "2")
				if(strpos(strtoupper($DadosBoleto_HISTORICO),"SIND")===false)
				{
					$strIMGPatronal   = $dadosboleto["path_base"]."/_boletos/imagens/GRCSU_box.gif";
					$strIMGEmpregados = $dadosboleto["path_base"]."/_boletos/imagens/GRCSU_box.gif";
					
				}else{
					$strIMGPatronal = $dadosboleto["path_base"]."/_boletos/imagens/GRCSU_box_x.gif";
					$strIMGEmpregados = $dadosboleto["path_base"]."/_boletos/imagens/GRCSU_box.gif";
				}
				if (getSession("tradeunion_db_name") == "tradeunion_sindieventos"){
					$strIMGEmpregados = $dadosboleto["path_base"]."/_boletos/imagens/GRCSU_box_x.gif";
					$strIMGPatronal   = $dadosboleto["path_base"]."/_boletos/imagens/GRCSU_box.gif";	
				}else{
    				$strIMGEmpregados = $dadosboleto["path_base"]."/_boletos/imagens/GRCSU_box.gif";
					$strIMGPatronal   = $dadosboleto["path_base"]."/_boletos/imagens/GRCSU_box.gif";	
				}
				
		  ?>
            <img height='14' src='<?php echo($strIMGPatronal);?>' width='14' align='absMiddle'>
            &nbsp;Patronal/Empregador&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <img height='14' src='<?php echo($strIMGEmpregados)?>' width='14' align='absMiddle'>
            &nbsp;Empregados&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <img height='14' src='<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_box.gif' width='14' align='absMiddle'>
            &nbsp;Prof. Liberal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <img height='14' src='<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_box.gif' width='14' align='absMiddle'>
            &nbsp;Autônomos
		  <?php } else { ?>
		  	&nbsp;Laboral&nbsp;
		  <?php } ?>
          </td>
          <td class=fa8p noWrap width=218 align=right>&nbsp;<?php echo($dadosboleto["valor_boleto"]);?></td>
        </tr>
        <tr>
          <td noWrap height=1></td>
          <td class=fa7p noWrap height=1></td>
          <td class=fa7p noWrap bgColor=#000000 colSpan=3 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=230></td>
        </tr>
        <tr>
          <td noWrap height=1></td>
          <td class=fa7p noWrap colSpan=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td>
          <td noWrap height=1></td>
        </tr>
        </tbody>
      </table>
      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=210>&nbsp;Capital Social - Empresa</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=188>&nbsp;Nº Empregados Contribuintes</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p noWrap width=218>&nbsp;(-) Desconto / Abatimento</td>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td class=fa7p width=210>&nbsp;</td>
          <td class=fa7p width=188>&nbsp;</td>
          <td class=fa8p width=218 align=right>&nbsp;<?php echo($dadosboleto["valor_desc_abatim"]);?></td>
        </tr>
        <tr><td bgColor=#000000 colSpan=7 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td colSpan=7 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        <tr>
          <td noWrap width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=210>&nbsp;Capital Social - Estabelecimento</td>
          <td noWrap bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=188>&nbsp;Total Remuneração - Contribuintes</td>
          <td noWrap bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=218>&nbsp;(-) Outras Deduções</td>
          <td noWrap bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td class=fa7p width=210>&nbsp;</td>
          <td class=fa7p width=188>&nbsp;</td>
          <td class=fa8p width=218 align=right>&nbsp;<?php echo($dadosboleto["valor_outras_deducoes"]);?></td></tr>
        <tr><td bgColor=#000000 colSpan=7><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td colSpan=7><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        <tr>
          <td vAlign=bottom noWrap width=1 bgColor=#000000 rowSpan=11><img height=88 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=210>&nbsp;</td>
          <td noWrap bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=188>&nbsp;Total Empregados - Estabelecimento</td>
          <td noWrap bgColor=#000000 height=1 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=218>&nbsp;(+) Mora / Multa</td>
          <td noWrap bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td class=fa7p width=210>&nbsp;<I>MENSAGEM DESTINADA AO CONTRIBUINTE</I></td>
          <td class=fa7p width=188>&nbsp;<?php echo($dadosboleto["total_empregados"]);?></td>
          <td class=fa8p width=218 align=right>&nbsp;<?php echo($dadosboleto["valor_mora_multa"]);?></td>
        </tr>
        <tr>
          <td width=1></td>
          <td width=1 colSpan=5><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=419></td>
        </tr>
        <tr><td colSpan=5 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        <tr>
          <td class=fa7p vAlign=top colSpan=3 rowSpan=7>
		  	<?php echo($dadosboleto["msg_extra_1"]);?><br>
			<?php echo($dadosboleto["msg_extra_2"]);?><br>
			<?php echo($dadosboleto["msg_extra_3"]);?><br>
			<?php echo($dadosboleto["msg_extra_4"]);?>
		  </td>
          <td vAlign=bottom width=1 rowSpan=7><img height=65 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=218>&nbsp;(+) Outros Acréscimos</td>
          <td vAlign=bottom width=1 rowSpan=7><img height=65 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr><td class=fa8p width=218 align=right>&nbsp;<?php echo($dadosboleto["valor_outros_acresc"]);?></td></tr>
        <tr><td class=fa7p bgColor=#000000 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=228></td></tr>
        <tr><td width=296><img height=2 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=2></td></tr>
        <tr><td class=fa7p height=1></td></tr>
        <tr><td class=fa7p width=218>&nbsp;(=) Valor Cobrado</td></tr>
        <tr><td class=fa8p width=218 align=right>&nbsp;<?php echo($dadosboleto["valor_cobrado"]);?></td></tr>
        <tr><td noWrap bgColor=#000000 colSpan=7 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td noWrap colSpan=7 height=1><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        </tbody>
      </table>
      <table cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td class=fa11pbold width=60><DIV align=center><?php echo $dadosboleto["codigo_banco_com_dv"]?></DIV></td>
          <td width=1 bgColor=#000000><img height=17 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>

          <td class=fa11pbold width=568 align=right>&nbsp;<?php echo $dadosboleto["linha_digitavel"]?></td>
        </tr>
        <tr><td class=fa11pbold bgColor=#000000 colSpan=3><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td class=fa11pbold colSpan=3 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        </tbody>
      </table>
      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td width=1 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=121 height=1>&nbsp;Código do Benefic&aacute;rio</td>
          <td width=1 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=120 height=1>&nbsp;Nosso Número</td>
          <td width=1 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=141 height=1>&nbsp;Valor do Documento</td>
          <td rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=144 height=1>&nbsp;Data Vencimento</td>
          <td width=1 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p width=98 height=1>&nbsp;Exercício</td>
          <td width=1 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td class=fa7p width=121 height=1 align=center>
		  	&nbsp;<?php echo($dadosboleto["cedente_cod_entsindical"]);?></td>
          <td class=fa7p width=120 height=1 align=center>&nbsp;<?php echo($dadosboleto["nosso_numero"]);?></td>
          <td class=fa7p width=141 height=1 align=center>&nbsp;<?php echo($dadosboleto["valor_boleto"]);?></td>
          <td class=fa7p width=144 height=1 align=center>&nbsp;<?php echo($dadosboleto["data_vencimento"]);?></td>
          <td class=fa7p width=98  height=1 align=center>&nbsp;<?php echo($dadosboleto["exercicio"]);?></td>
        </tr>
        <tr><td colSpan=11><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td colSpan=11><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        </tbody>
      </table>
      <table cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td width=350 height=1>&nbsp;</td>
          <td width=1 bgColor=#000000><img height=32 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa7p vAlign=top width=279>&nbsp;Autenticação Mecânica</td>
        </tr>
        </tbody>
      </table>
      <table cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr><td height=10><P><img height="18" src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_cortepontilhado.gif" width=630></P></td></tr>
        <tr><td><img height="10" src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=1></td></tr>
        </tbody>
      </table>
      <table cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td width=130><img height=29 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/logocaixamenor.gif" width=125></td>
          <td class=fa7p width=1>&nbsp;<BR><BR></td>
          <td width=1><img height=29 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa11pbold vAlign=bottom align=middle width=50><?php echo $dadosboleto["codigo_banco_com_dv"]?></td>
          <td width=1><img height=29 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
          <td class=fa11pbold vAlign=bottom width=447 aling=right>&nbsp;<?php echo $dadosboleto["linha_digitavel"]?></td>
        </tr>
        <tr bgColor=#000000><td colSpan=6 height=2><img height=2 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td colSpan=6><img height=2 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=1></td></tr>
        </tbody>
      </table>
      <table cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td vAlign=top>
            <table class=fa7p cellSpacing=0 cellPadding=0 width=420 border=0>
              <tbody>
              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=420>&nbsp;Local de Pagamento</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr><td class=fa7p>&nbsp;<?php echo($dadosboleto["local_pgto"]);?></td></tr>
              <tr><td bgColor=#000000 colSpan=3><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=420></td></tr>
              <tr><td colSpan=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
              </tbody>

            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 width=420 border=0>
              <tbody>
              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=418>&nbsp;Benefic&aacute;rio</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>

              <tr><td class=fa7p>&nbsp;<?php echo($dadosboleto["cedente_nome_completo"]);?></td></tr>
              <tr><td bgColor=#000000 colSpan=3><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif"  width=420></td></tr>
              <tr><td colSpan=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
              </tbody>
            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 width=420 border=0>
              <tbody>
              <tr>

                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=90>Data do Documento</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=140>&nbsp;Número do Documento</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=60>&nbsp;Esp. Docum.</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>

                <td class=fa7p width=40>&nbsp;Aceite</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=100>&nbsp;Data Processamento</td>
                <td bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr>
                <td class=fa7p>&nbsp;<?php echo($dadosboleto["data_documento"]);?></td>

                <td class=fa7p>&nbsp;<?php echo($dadosboleto["numero_documento"]);?></td>
                <td class=fa7p>&nbsp;<?php echo($dadosboleto["especie_doc"]);?></td>
                <td class=fa7p>&nbsp;<?php echo($dadosboleto["aceite"]);?></td>
                <td class=fa7p>&nbsp;<?php echo($dadosboleto["data_processamento"]);?></td>
              </tr>
              <tr bgColor=#000000><td colSpan=11><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=420></td></tr>
              <tr><td colSpan=11><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>

              </tbody>
            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 width=420 border=0>
              <tbody>
              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=87 bgColor=#cccccc rowSpan=2>&nbsp;Uso do Banco<BR>&nbsp;EXERC (<?php echo($dadosboleto["exercicio"]);?>)</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>

                <td class=fa7p width=63>&nbsp;Carteira</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=69 rowSpan=2>&nbsp;Espécie<BR>&nbsp;R$ </td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=88>&nbsp;Quantidade</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=107>&nbsp;Valor</td>

                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr>
                <td class=fa7p>&nbsp;<?php echo($dadosboleto["carteira"]);?></td>
                <td>&nbsp;</td>
                <td class=fa7p>&nbsp;<?php echo($dadosboleto["valor_unitario"]);?></td>
              </tr>
              <tr bgColor=#000000><td colSpan=11><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=420></td></tr>

              <tr><td colSpan=11><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
              </tbody>
            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 width=420 border=0>
              <tbody>
              <tr>
                <td width=1 bgColor=#000000 rowSpan=4><img height=145 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p vAlign=top width=418 height=3>&nbsp;instruções</td>

                <td bgColor=#000000 rowSpan=3><img height=145 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr>
                <td class=fa7p vAlign=top height=100 style="text-align:center"><br>
<?php echo($dadosboleto["instrucoes1"]);?><br>
<?php echo($dadosboleto["instrucoes2"]);?><br>
<?php echo($dadosboleto["instrucoes3"]);?><br>
<?php echo($dadosboleto["instrucoes4"]);?><br>
<?php echo($dadosboleto["instrucoes5"]);?>
				</td>
              </tr>
              <tr><td colSpan=3><img src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=419 height=1></td></tr>
              <tr><td colSpan=3><img src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=1 height=3></td></tr>
              </tbody>
            </table>
          </td>
          <td><img height=10 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=10></td>
          <td vAlign=top width=250>
            <table cellSpacing=0 cellPadding=0 border=0>
              <tbody>

              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=198>&nbsp;Vencimento</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr><td class=fa8p width=180 align=right>&nbsp;<?php echo($dadosboleto["data_vencimento"]);?></td></tr>
              <tr><td bgColor=#000000 colSpan=3 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=200></td></tr>
              <tr><td colSpan=3 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>

              </tbody>
            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 border=0>
              <tbody>
              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=198>&nbsp;Agência&nbsp;/&nbsp;Código Benefic&aacute;rio</td>

                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr>
			  	<td class=fa8p width=180 align=right>
				  	&nbsp;<?php echo($dadosboleto["agencia"]);?>/<?php echo($dadosboleto["cedente_cod_entsindical"]);?>
			  	</td>
			  </tr>
              <tr><td bgColor=#000000 colSpan=3 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=200></td></tr>
              <tr><td colSpan=3 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
              </tbody>
            </table>

            <table class=fa7p cellSpacing=0 cellPadding=0 border=0>
              <tbody>
              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=198>&nbsp;Nosso Número</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr><td class=fa8p width=180 align=right>&nbsp;<?php echo($dadosboleto["nosso_numero"]);?></td></tr>

              <tr><td bgColor=#000000 colSpan=3 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=200></td></tr>
              <tr><td colSpan=3 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
              </tbody>
            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 border=0>
              <tbody>
              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=198>&nbsp;(=) Valor do Documento</td>

                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr><td class=fa8p width=180 align=right>&nbsp;<?php echo($dadosboleto["valor_boleto"]);?></td></tr>
              <tr><td bgColor=#000000 colSpan=3 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=200></td></tr>
              <tr><td colSpan=3 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
              </tbody>
            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 border=0>

              <tbody>
              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=198>&nbsp;(-) Desconto / Abatimento</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr><td class=fa8p width=180 align=right>&nbsp;<?php echo($dadosboleto["valor_desc_abatim"]);?></td></tr>
              <tr><td bgColor=#000000 colSpan=3 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=200></td></tr>

              <tr><td colSpan=3 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
              </tbody>
            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 border=0>
              <tbody>
              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=198>&nbsp;(-) Outras Deduções</td>

                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr><td class=fa8p width=180 align=right>&nbsp;<?php echo($dadosboleto["valor_outras_deducoes"]);?></td></tr>
              <tr><td bgColor=#000000 colSpan=3 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=200></td></tr>
              <tr><td colSpan=3 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
             </tbody>
            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 border=0>
              <tbody>

              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=198>&nbsp;(+) Mora / Multa</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr><td class=fa8p width=180 align=right>&nbsp;<?php echo($dadosboleto["valor_mora_multa"]);?></td></tr>
              <tr><td bgColor=#000000 colSpan=3 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=200></td></tr>
              <tr><td colSpan=3 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>

              </tbody>
            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 border=0>
              <tbody>
              <tr>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=198>&nbsp;(+) Outros Acréscimos</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>

              </tr>
              <tr><td class=fa8p width=180 align=right>&nbsp;<?php echo($dadosboleto["valor_outros_acresc"]);?></td></tr>
              <tr><td bgColor=#000000 colSpan=3 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=200></td></tr>
              <tr><td colSpan=3 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
              </tbody>
            </table>
            <table class=fa7p cellSpacing=0 cellPadding=0 border=0>
              <tbody>
              <tr>

                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
                <td class=fa7p width=198>&nbsp;(=) Valor Cobrado</td>
                <td width=1 bgColor=#000000 rowSpan=2><img height=26 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
              </tr>
              <tr><td class=fa8p width=180 align=right>&nbsp;<?php echo($dadosboleto["valor_cobrado"]);?></td></tr>
              <tr><td bgColor=#000000 colSpan=3 height=1><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=200></td></tr>
              <tr><td colSpan=3 height=3><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
              </tbody>

            </table>
          </td>
        </tr>
        </tbody>
      </table>
      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr>
          <td width=1 bgColor=#000000 rowSpan=2><img height=46 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>

          <td width=1><img height=15 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=1></td>
          <td class=fa7p vAlign=top width=628 height=25>
		  	&nbsp;Pagador:<BR>&nbsp;<?php echo($dadosboleto["sacado"]);?><BR>
			&nbsp;<?php echo($dadosboleto["sacado_logradouro_end"]);?>,
			<?php echo($dadosboleto["sacado_numero_end"]);?>, <?php echo($dadosboleto["sacado_complemento_end"]);?> - 
			CEP: <?php echo($dadosboleto["sacado_cep_end"]);?> - <?php echo($dadosboleto["sacado_bairro_end"]);?> - 
			<?php echo($dadosboleto["sacado_cidade_end"]);?>/<?php echo($dadosboleto["sacado_estado_end"]);?>
          </td>
          <td width=1><img height=15 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=1></td>
          <td width=1 bgColor=#000000 rowSpan=2><img height=46 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=1></td>
        </tr>
        <tr>
          <td width=1><img height=15 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=1></td>

          <td class=fa7p vAlign=bottom width=628>&nbsp;Pagador / Avalista:&nbsp;</td>
          <td width=1><img height=15 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=1></td>
        </tr>
        <tr><td bgColor=#000000 colSpan=5><img height=1 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_lineblack.gif" width=630></td></tr>
        <tr><td colSpan=5><img height=3 src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_linetransparente.gif" width=3></td></tr>
        </tbody>
      </table>
      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>

        <tbody>
        <tr>
          <td class=fa7p width=428>&nbsp;Código de Barras</td>
          <!--<td class=fa7p width=428>&nbsp;</td>-->
          <td class=fa7p align=right width=300>
		  	<font face="times new roman">Ficha de Compensação / Autenticação Mecânica</font></td>
        </tr>
        </tbody>
      </table>

      <table class=fa7p cellSpacing=0 cellPadding=0 width=630 border=0>
        <tbody>
        <tr><td noWrap width=600><?php fbarcode($dadosboleto["codigo_barras"],$dadosboleto["path_base"]); ?></td></tr>
        <tr><td height="35"><img src="<?php echo($dadosboleto["path_base"])?>/_boletos/imagens/GRCSU_cortepontilhado.gif" height="18" width="630" border=0></td>
        </tbody>
      </table>
    </td>
   </tr>
  </tbody>
</table>
</body>
</html>