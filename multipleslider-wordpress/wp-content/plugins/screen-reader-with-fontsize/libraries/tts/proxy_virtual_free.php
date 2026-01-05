<?php
/**
 * Proxy della REST API di Virtual Speaker
 * @package SCREENREADER::plugins
 * @author JExtensions Store 
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
ini_set ( 'display_errors', false );

// testo
$text = preg_replace ( "/[" . PHP_EOL . "]+/", " ", ($GLOBALS['_' . strtoupper('get')] ['text']) );
// lingua
$lang = ($GLOBALS['_' . strtoupper('get')] ['lang']);
// security token same domain
$token = ($GLOBALS['_' . strtoupper('get')] ['token']);

if(!function_exists('curl_version')) {
	exit();
}

if ($token === md5 ( $_SERVER ['HTTP_HOST'] )) {
	// Random user agents DB
	$userAgents = array (
			"Mozilla/5.0 (Windows NT 6.2; WOW64; rv:63.0) Gecko/20100101 Firefox/63.0",
			"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.10; rv:62.0) Gecko/20100101 Firefox/62.0",
			"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9a1) Gecko/20060814 Firefox/51.0",
			"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0",
			"Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36",
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.2227.1 Safari/537.36",
			"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36",
			"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.2224.3 Safari/537.36",
			"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.2224.3 Safari/531.11",
			"Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.2224.3 Safari/533.23",
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A",
			"Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25",
			"Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko",
			"Mozilla/5.0 (Windows NT 6.2; WOW64; Trident/7.1; AS; rv:11.0) like Gecko",
			"Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko",
			"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)",
			"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)",
			"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)",
			"Mozilla/5.0 (compatible; MSIE 10.0; Macintosh; Intel Mac OS X 10_7_3; Trident/6.0)" 
	);
	$ua = $userAgents [rand ( 0, count ( $userAgents ) - 1 )];

	// Remote GET using CURL lib to retrieve the PHPSESSID header cookie
	$ch = curl_init();
	$url = "https://www.acapela-group.com/www/static/website/demoOptionsDef_voicedemo.php?_=" . time();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$headers = array (
			'Accept: text/javascript, application/javascript, application/ecmascript, application/x-ecmascript, */*; q=0.01',
			'Accept-Encoding: text/plain',
			'Host: www.acapela-group.com',
			'Cache-Control: no-cache',
			'Connection: keep-alive',
			'Pragma: no-cache',
			'X-Requested-With: XMLHttpRequest',
			'Referer: https://www.acapela-group.com/demos/',
			'X-Requested-With: XMLHttpRequest',
			'User-Agent: ' . $ua,
			'Accept-Language: en-US,en;q=0.8,de;q=0.6,es;q=0.4,fr;q=0.2,it;q=0.2,ru;q=0.2,ja;q=0.2,el;q=0.2,sk;q=0.2,nl;q=0.2,ar;q=0.2,sv;q=0.2'
	);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// execute post
	$HTTPResponse = new stdClass();
	$HTTPResponse->body = curl_exec ($ch);

	// close the connection
	curl_close ($ch);
	
	if($HTTPResponse->body) {
		$jsonCode = str_replace('var vaasOptions = ', '', $HTTPResponse->body);
		$jsonCode = str_replace(';', '', $jsonCode);
		$sessionInformations = json_decode($jsonCode);
	} else {
		$sessionInformations = new stdClass();
		$sessionInformations->session = new stdClass();
		$sessionInformations->session->start = null;
		$sessionInformations->session->time = null;
		$sessionInformations->session->key = null;
		$sessionInformations->json_service_url = null;
	}
	
	// Mapped language to code
	// Mapped language to code
	$mappedLangCode = array (
			'ar-AR' => 'leila22k',
			'ar-AE' => 'leila22k',
			'ar-AA' => 'leila22k',
			'ar' => 'leila22k',
			'en-US' => 'sharon22k',
			'en-GB' => 'rachel22k',
			'en' => 'rachel22k',
			'fi-FI' => 'sanna22k',
			'fi' => 'sanna22k',
			'fo-FO' => 'hanna22k',
			'fo' => 'hanna22k',
			'fr-FR' => 'manon22k',
			'fr' => 'manon22k',
			'de-DE' => 'claudia22k',
			'de' => 'claudia22k',
			'es-ES' => 'ines22k',
			'es' => 'ines22k',
			'it-IT' => 'fabiana22k',
			'it' => 'fabiana22k',
			'nl-BE' => 'zoe22k',
			'nl-NL' => 'jasmijn22k',
			'nl' => 'jasmijn22k',
			'ca-ES' => 'laia22k',
			'ca' => 'laia22k',
			'cs-CZ' => 'eliska22k',
			'cs' => 'eliska22k',
			'da-DK' => 'mette22k',
			'da' => 'mette22k',
			'el-GR' => 'dimitrishappy22k',
			'el' => 'dimitrishappy22k',
			'ja-JP' => 'sakura22k',
			'ja' => 'sakura22k',
			'ko-KR' => 'minji22k',
			'ko' => 'minji22k',
			'nb-NO' => 'bente22k',
			'nb' => 'bente22k',
			'pl-PL' => 'ania22k',
			'pl' => 'ania22k',
			'pt-PT' => 'celia22k',
			'pt-BR' => 'marcia22k',
			'pt' => 'celia22k',
			'ru-RU' => 'alyona22k',
			'ru' =>'alyona22k',
			'sv-FI' => 'samuel22k',
			'sv-SE' => 'elin22k',
			'sv' => 'elin22k',
			'tr-TR' => 'ipek22k',
			'tr' => 'ipek22k'
	);
	if(array_key_exists($lang, $mappedLangCode)) {
		$langSelectedVoice = $mappedLangCode[$lang];
	} else {
		$langSelectedVoice = 'rachel22k';
	}
	
	// Make the first POST form request and get the HTML page including the MP3 link
	$qs = array (
			'req_voice' => $langSelectedVoice,
			'cl_login' => 'AcapelaGroup',
			'cl_app' => 'AcapelaGroup_WebDemo_HTML',
			'session_start' => $sessionInformations->session->start,
			'session_time' => $sessionInformations->session->time,
			'session_key' => $sessionInformations->session->key,
			'req_text' => ($text)
	);
	$builtQuery = http_build_query($qs, '', '&', PHP_QUERY_RFC3986);
	
	// Remote POST using sockets or CURL lib
	$ch = curl_init();
	$url = $sessionInformations->json_service_url . '?' . $builtQuery;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$headers = array (
			'Accept: application/json, text/javascript, */*; q=0.01',
			'Accept-Encoding: text/plain',
			'Accept-Language: en-US,en;q=0.8,de;q=0.6,es;q=0.4,fr;q=0.2,it;q=0.2,ru;q=0.2,ja;q=0.2,el;q=0.2,sk;q=0.2,nl;q=0.2,ar;q=0.2,sv;q=0.2',
			'Cache-Control: no-cache',
			'Connection: keep-alive',
			'Host: h-ir-ssd-1.acapela-group.com',
			'Referer: http://www.acapela-group.com/demos/',
			'Pragma: no-cache',
			'User-Agent: ' . $ua
	);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$webServiceJsonResponse = curl_exec ($ch);
	
	// close the connection
	curl_close ($ch);
	
	$mp3Url = null;
	if($webServiceJsonResponse) {
		$webServiceJsonResponseObject = json_decode($webServiceJsonResponse);
		$mp3Url = $webServiceJsonResponseObject->snd_url;
	}
	
	if($mp3Url) {
		// Finally grab the MP3 audio binary
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $mp3Url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$headers = array (
				'Accept: */*',
				'Accept-Encoding: identity;q=1, *;q=0',
				'Accept-Language: en-US,en;q=0.8,de;q=0.6,es;q=0.4,fr;q=0.2,it;q=0.2,ru;q=0.2,ja;q=0.2,el;q=0.2,sk;q=0.2,nl;q=0.2,ar;q=0.2,sv;q=0.2',
				'Cache-Control: no-cache',
				'Connection: keep-alive',
				'Host: h-ir-ssd-1.acapela-group.com',
				'Referer: http://www.acapela-group.com/demos/',
				'Pragma: no-cache',
				'Range: bytes=0-',
				'User-Agent: ' . $ua
		);
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$mp3BinaryString = curl_exec ($ch);
		
		// close the connection
		curl_close ($ch);
			
		$download_size = strlen ( $mp3BinaryString );
		
		// send the headers
		header ( "Pragma: public" ); // purge the browser cache
		header ( "Expires: 0" ); // ...
		header ( "Cache-Control:" ); // ...
		header ( "Cache-Control: public" ); // ...
		header ( "Content-Description: File Transfer" ); //
		header ( "Content-Type: audio/mpeg" ); // file type
		header ( "Content-Disposition: attachment; filename=tts.mp3" );
		header ( "Content-Transfer-Encoding: binary" ); // transfer method
		header ( "Content-Length: $download_size" ); // download length
		
		echo $mp3BinaryString;
	}
}
exit ();