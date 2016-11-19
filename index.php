<?php
$sites = array();

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
}?>
<h3>Sites</h3>
<table class="table" id="sites">
	<thead>
		<tr>
			<th>Site</th>
			<th>Issuer</th>
			<th>Type</th>
			<th>Expiration</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($sites as $url)
		{
			$orignal_parse = parse_url("https://".$url, PHP_URL_HOST);
			$get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
			$read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
			$cert = stream_context_get_params($read);
			$certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);

			if(isset($certinfo['issuer']['CN']))
			{
				echo "<tr><td>";
				echo $url;
				echo "</td><td>";
				echo $certinfo['issuer']['CN'];
				echo "</td><td>";
				echo $certinfo['signatureTypeSN'];
				echo "</td><td>";
				echo date("Y-m-d", $certinfo['validTo_time_t']);
				echo "</td></tr>";
			}
			else
			{
				
			}
		}
		?>
	</tbody>
</table>
<script
  src="https://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function() {
		 
		 $('#sites').DataTable();
		 
	});

</script>

