<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<META HTTP-EQUIV="Expires" CONTENT="Fri, Jun 12 1981 08:20:00 GMT"> 
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache"> 
	<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache"> 
</head>
<body style="margin:0px 0px 0px 0px; vertical-align:middle; text-align:center;" bgcolor="#DDDDDD">
<img src="../img/icon_ajax_loader.gif" alt="Realizando os testes de largura de banda..." height="8" border="0" title="Realizando os testes de largura de banda...">
<script>
<!--
   hora    = new Date();
   horaIni = hora.getTime();
// -->
</script>
<?php
/* � interessante que o arquivo de teste tenha mais de 3MB.
   O arquivo de leitura precisa ser relativamente grande para permitir a leitura de uma grande quantidade 
   de bytes num tempo significativo que facilite os c�lculos (� mais f�cil lidar com 10 do que com 0,0000000001). */
   $fd = fopen ("../img/kbps_arquivoteste.bin", "rb");
   $conteudo = fread ($fd, 512 * 1024);
   echo "<!-- $conteudo -->";
   fclose ($fd);
?>
<script>
<!--
	hora     = new Date();
	horaFim  = hora.getTime();

/*  Obtemos novamente a hora, armazenamos o resultado na vari�vel horaFim e comparamos as 
    horas inicial e final. Caso sejam iguais, o tempo de leitura (ou download de bits do arquivo) 
	foi zero; caso sejam diferentes, o tempo de download corresponde � diferen�a dos dois hor�rios 
	em segundos, que ser� dividido por 1000 para serem expressos em milissegundos. Qualquer que seja 
    o resultado, ele � armazenado na vari�vel tempoDown. */	 
	if (horaFim == horaIni) { tempoDown = 0
	} else { tempoDown = (horaFim - horaIni)/1000; }

/*  Tendo o tempo de leitura, dividimos o volume de dados pelo tempo gasto e armazenamos o resultado 
    na vari�vel velocidade. A seguir, transformamos os bytes por segundo em kilobits por segundo ou kbps: 
	arrendondamos a multiplica��o da velocidade por 8 (cada byte possui 8 bits) e por 1024 para transform�-la 
	em kilo (no sistema bin�rio kilo corresponde a 210 e 2 elevado a 10 � igual a 1024). A multiplica��o e 
	posterior divis�o por 10 se anulam. Isto � utilizado apenas para se obter uma casa decimal no resultado final. */
	kbytes_de_dados = 512 * 1024;
	velocidade      = kbytes_de_dados/tempoDown;
	kbps            = (Math.round((velocidade*8)*10*1.024))/10;

/*  Com estes dados � poss�vel calcular a velocidade de transmiss�o de acordo com a largura de banda dispon�vel. 
    Este c�lculo ser� realizado na segunda p�gina (kbps_resultado.php). A p�gina de resultado espera alguns par�metros. 
	Para isto compomos uma string, proxPage, que contenha a chamada de resultado.php seguida 
	das vari�veis e seus valores: "resultado.php?kbps=" + kbps + "&tempo=" + tempoDown + "&KB=" + kbytes_de_dados. Esta 
	string ser� o valor passado para document.location.href, que acionar� a leitura de resultado.php. */
	proxPage = "kbps_resultado.php?kbps=" + kbps + "&tempo=" + tempoDown + "&KB=" + kbytes_de_dados;
	document.location.href=proxPage;
// -->
</script>
</body>
</html>