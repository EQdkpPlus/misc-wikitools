<?php
ini_set('display_errors', 1);

define('EQDKP_WIKITOOLS', true);
include_once('keys.php');

$strInPassword = isset($_GET['key']) ? $_GET['key'] : false;
$strPingMethod = "GET";

if($strInPassword === $strPurgeKey){
	$arrHeaders = getallheaders( );
	
	$strEvent = $arrHeaders['X-Github-Event'];
	
	echo "Event: ".$strEvent."; ";
	
	$arrRepo = isset($_POST['repository']) ? $_POST['repository'] : false;
	if($arrRepo){
		$strRepo = strtolower($arrRepo['full_name']);
		$strRepo = stri_replace('EQdkpPlus/', '', $strRepo);
		
		echo "Found Repo ".$strRepo.'; ';
	} else {
		echo "No Repo found; ";
	}
	
	if($strEvent === 'create' && $strRepo != ""){
		$strPingURL = false;
		
		switch($strRepo){
			case 'plugin-awards':
				$strPingURL = 'https://eqdkp-plus.eu/wiki/Plugin:_Awards?action=purge';
				$strPingMethod = "POST";
			break;
			
		}
		
		if($strPingURL !== false){
			if ($strPingMethod === "GET"){
				$result = get_fopen($strPingURL, "", 5, 15);
			} else {
				$result = post_fopen($strPingURL, "", "text/html; charset=utf-8", "", 5, 15);
			}
			
			echo "Ping executed for Repo ".$strRepo;
		}
	}
} else {
	
	echo "No Purge Key.";
}



function post_fopen($url, $data, $content_type, $header, $conn_timeout, $timeout){
		$url_array	= parse_url($url);
		$port = ($url_array['scheme'] == 'https') ? 443 : 80;
		$fsock_host = ($url_array['scheme'] == 'https') ? 'ssl://'.$url_array['host'] : $url_array['host'];
		
		$getdata = '';
		if (isset($url_array['host']) && $fp = @fsockopen($fsock_host, $port, $errno, $errstr, $conn_timeout)){
			$out	 = "POST " .$url_array['path']."?".((isset($url_array['query'])) ? $url_array['query'] : '')." HTTP/1.0\r\n";
			$out	.= "Host: ".$url_array['host']." \r\n";
			$out	.= "User-Agent: EQdkpWebPing\r\n";
			$out	.= "Content-type: ".$content_type."\r\n";
			$out	.= "Content-Length: ".strlen($data)."\r\n";
			$out	.= ((is_array($header) && count($header) > 0) ? implode("\r\n", $header): '');
			$out	.= "Connection: Close\r\n";
			$out	.= "\r\n";
			$out	.= $data;
			fwrite($fp, $out);

			// Get rid of the HTTP headers
			while ($fp && !feof($fp)){
				$headerbuffer = fgets($fp, 1024);
				if (urlencode($headerbuffer) == "%0D%0A"){
					// We've reached the end of the headers
					break;
				}
			}
			// Read the raw data from the socket in 1kb chunks
			while (!feof($fp)){
				$getdata .= fgets($fp, 1024);
			}
			fclose($fp);
		}
		
		return $getdata;
	}

function get_fopen($geturl, $header, $conn_timeout, $timeout){
		$url_array	= parse_url($geturl);
		$port = ($url_array['scheme'] == 'https') ? 443 : 80;
		$fsock_host = ($url_array['scheme'] == 'https') ? 'ssl://'.$url_array['host'] : $url_array['host'];
		
		$getdata = '';
		if (isset($url_array['host']) AND $fp = @fsockopen($fsock_host, $port, $errno, $errstr, $conn_timeout)){
			$out	 = "GET " .$url_array['path']."?".((isset($url_array['query'])) ? $url_array['query'] : '')." HTTP/1.0\r\n";
			$out	.= "Host: ".$url_array['host']." \r\n";
			$out	.= "User-Agent: EQdkpWebPing\r\n";
			$out	.= "Connection: Close\r\n";
			$out	.= ((is_array($header) && count($header) > 0) ? implode("\r\n", $header): '');
			$out	.= "\r\n";
			fwrite($fp, $out);

			// Get rid of the HTTP headers
			while ($fp && !feof($fp)){
				$headerbuffer = fgets($fp, 1024);
				if (urlencode($headerbuffer) == "%0D%0A"){
					// We've reached the end of the headers
					break;
				}
			}
			// Read the raw data from the socket in 1kb chunks
			while (!feof($fp)){
				$getdata .= fgets($fp, 1024);
			}
			fclose($fp);
		}
		
		return $getdata;
	}
?>