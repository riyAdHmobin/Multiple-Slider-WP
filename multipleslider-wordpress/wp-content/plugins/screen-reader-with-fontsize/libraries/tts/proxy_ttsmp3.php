<?php
/**
 * Proxy della REST API di TTSMp3
 * @package SCREENREADER::plugins
 * @author JExtensions Store 
 * @subpackage screenreader
 * @subpackage libraries
 * @subpackage tts
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
ini_set ( 'display_errors', false );

// testo
$text = preg_replace ( "/[" . PHP_EOL . "]+/", " ", ($_GET ['text']) );
// lingua
$lang = ($_GET ['lang']);
// security token same domain
$token = ($_GET ['token']);

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

	// Mapped language to code
	$mappedLangCode = array (
			'ar-AR' => 'Zeina',
			'ar-AE' => 'Zeina',
			'ar-AA' => 'Zeina',
			'ar' => 'Zeina',
			'cy-GB' => 'Gwyneth',
			'cy' => 'Gwyneth',
			'da-DK' => 'Naja',
			'da' => 'Naja',
			'de-DE' => 'Hans',
			'de' => 'Hans',
			'en-AU' => 'Nicole',
			'en-US' => 'Ivy',
			'en-GB' => 'Brian',
			'en' => 'Brian',
			'es-MX' => 'Mia',
			'es-ES' => 'Conchita',
			'es' => 'Conchita',
			'fr-CA' => 'Chantal',
			'fr-FR' => 'Celine',
			'fr' => 'Celine',
			'it-IT' => 'Bianca',
			'it' => 'Bianca',
			'ja-JP' => 'Mizuki',
			'ja' => 'Mizuki',
			'ko-KR' => 'Seoyeon',
			'ko' => 'Seoyeon',
			'nb-NO' => 'Liv',
			'nb' => 'Liv',
			'nl-BE' => 'Lotte',
			'nl-NL' => 'Lotte',
			'nl' => 'Lotte',
			'pl-PL' => 'Maja',
			'pl' => 'Maja',
			'pt-PT' => 'Ines',
			'pt-BR' => 'Vitoria',
			'pt' => 'Ines',
			'ru-RU' => 'Maxim',
			'ru' =>'Maxim',
			'sv-FI' => 'Astrid',
			'sv-SE' => 'Astrid',
			'sv' => 'Astrid',
			'tr-TR' => 'Filiz',
			'tr' => 'Filiz',
			'zh-CN' => 'Zhiyu',
			'zh-HK' => 'Zhiyu',
			'zh' => 'Zhiyu'
	);
	if(array_key_exists($lang, $mappedLangCode)) {
		$langSelectedVoice = $mappedLangCode[$lang];
	} else {
		$langSelectedVoice = 'Brian';
	}
	
	// Make the first POST form request and get the HTML page including the MP3 link
	$qs = array (
			'lang' => $langSelectedVoice,
			'source' => 'ttsmp3',
			'msg' => $text
	);
	
	// Remote POST using sockets or CURL lib
	$ch = curl_init();
	$url = "https://ttsmp3.com/makemp3_new.php";
	$data = http_build_query($qs);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$headers = array (
			'Accept: */*',
			'Content-Type: application/x-www-form-urlencoded',
			'Host: ttsmp3.com',
			'Origin: https://ttsmp3.com',
			'Pragma: no-cache',
			'Referer: https://ttsmp3.com/',
			'Cache-Control: no-cache',
			'Connection: keep-alive',
			'User-Agent: ' . $ua
	);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$webServiceJsonResponse = curl_exec ($ch);
	
	// close the connection
	curl_close ($ch);
	
	$mp3Url = null;
	if($webServiceJsonResponse) {
		$webServiceJsonResponseObject = json_decode($webServiceJsonResponse);
		if(is_object($webServiceJsonResponseObject) && isset($webServiceJsonResponseObject->URL)) {
			$mp3Url = $webServiceJsonResponseObject->URL;
		}
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
				'chrome-proxy: frfr',
				'Connection: keep-alive',
				'Host: ttsmp3.com',
				'Referer: https://ttsmp3.com/',
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