<?php
$sites = array();
echo "Reading CSV <br>";
if (($handle = fopen("Inventory.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$num = count($data);
		$row++;
		if($row > 2)
		{
			$sites[] = $data[0];
		}
		
	}
	fclose($handle);
}
echo "Testing Sites <br>";
foreach ($sites as $url)
{
	echo "<b>".$url ."</b><br>";
	$orignal_parse = parse_url("https://".$url, PHP_URL_HOST);
	$get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
	$read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
	$cert = stream_context_get_params($read);
	$certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);

	if(isset($certinfo['issuer']['CN']))
	{
		echo $certinfo['issuer']['CN'].'<br>';
		echo $certinfo['signatureTypeSN'].'<br>';

	 // echo date("Y-m-d", $certinfo['validFrom_time_t']).'<br>';

		echo "Expires: " . date("Y-m-d", $certinfo['validTo_time_t']).'<br>';
	}
	else
	{
		echo "SSL not found<br>";
	}
}

