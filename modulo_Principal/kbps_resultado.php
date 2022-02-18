<html>
<body style="margin:0px 0px 0px 0px; vertical-align:middle; text-align:center;" bgcolor="#DDDDDD">
<?php
    // 250 KB/seg = 2000 Kbps ou 2Mbps 
	// kbps * 0.1220703125 = KB/s
	/* A variável $kbps é inicializada com o valor kbps enviado como parâmetro através do 
	   método "GET" fazendo-se a leitura do elemento 'kbps' do array $_GET. Com isto temos 
	   os kilobits por segundo, lidos na página anterior, arredondados com duas casas decimais. */

	/* Os cálculos seguintes apenas transformam as unidades. $ksec corresponde ao número de kilobytes 
	   lidos (1 byte = 8 bits), $mbps corresponde ao número de megabits lidos (1 megabit = 1024 bits) 
	   e $msec corresponde ao número de megabytes lidos (1 byte = 8 bits). 
	   So 512 kbps / 8 = 64.000 bytes
       then 64.000 bytes / 1,024 = 62,5 Kilobytes/s or 62,5 kB/s  */
	$kbps	= round($_GET['kbps'], 2);
	$ksec	= round($kbps / 8, 2);
	$mbps	= round($kbps / 1024, 2);
	$msec	= round($mbps / 8, 2);
	$strIcon = "kbps_icon1000.png";

	if ($mbps < 1000) { $strIcon = "kbps_icon1000.png"; } 
	elseif ($mbps < 2000) { $strIcon = "kbps_icon2000.png"; } 
	elseif ($mbps < 3000) { $strIcon = "kbps_icon3000.png"; } 
	elseif ($mbps < 4000) { $strIcon = "kbps_icon4000.png"; } 
	elseif ($mbps < 5000) { $strIcon = "kbps_icon5000.png"; } 
	else  { $strIcon = "kbps_icon.png"; } 
	
	//echo("[" . $mbps . "-" . $msec . "]"); // DEBUG
    $strTitle = "";

	/* Se o número de megabits lidos for maior do que 1, formatamos o valor como número flutuante 
	   de duas casas decimais e o imprimimos com printf ("%.2f",$mbps); seguido de mais uma saída 
	   para a tela de echo " Mbps<br><br>;" para especificar a grandeza. Se o número de megabits 
	   for menor do que 1, procede-se da mesma maneira com o valor dos kilobits. */
	if ($mbps > 1) {
		//printf ("%.2f",$mbps); 	
		$strTitle .= sprintf("%.2f",$mbps/1000); 	
		$strTitle .= " Mbps";
	} else {
		$strTitle .= sprintf ("%.2f",$kbps); 	
		$strTitle .= " kbps";
	}
	//echo("[" . $mbps . "-" . $msec . "]");// DEBUG

	/* Depois de apresentar o total de kilo ou megabits lidos, mostramos o volume lido por segundo. 
	   Usando echo, se $msec for maior do que 1, mostramos o valor dos megabytes por segundo; se não, 
	   mostramos o valor dos kilobytes por segundo. */
	$strTitle .= " ~" ;
	if ($msec > 1) {
		//echo("(".$msec . " MB/seg.)"); ???
		$strTitle .= $msec . " KB/s";
	} else {
		$strTitle .= $ksec . " KB/s";
	}
	
	echo("<a href='kbps_leitura.php' target='_self' style='text-decoration:none;' title='" . $strTitle . "'>");
	echo("<img src='../img/" . $strIcon . "' border='0'>");
	echo("</a>");
?>
</body>
</html>