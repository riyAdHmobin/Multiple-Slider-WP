<?php
/**
 * Proxy della REST API di https://text-to-speech-demo.ng.bluemix.net/
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
$text = preg_replace ( "/[" . PHP_EOL . "]+/", " ", ($GLOBALS['_' . strtoupper('get')] ['text']) );
// lingua
$lang = ($GLOBALS['_' . strtoupper('get')] ['lang']);
// security token same domain
$token = ($GLOBALS['_' . strtoupper('get')] ['token']);

if(!function_exists('curl_version')) {
	exit();
}

if ($token === md5 ( $_SERVER ['HTTP_HOST'] )) {
	// Lazy loading
	require_once '../http/http.php';

	// Random user agents DB
	$userAgents = array (
			"Mozilla/5.0 (Windows NT 6.2; WOW64; rv:63.0) Gecko/20100101 Firefox/63.0",
			"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.10; rv:62.0) Gecko/20100101 Firefox/62.0",
			"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9a1) Gecko/20060814 Firefox/51.0",
			"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0",
			"Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.3683.103 Safari/537.36",
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.2227.1 Safari/537.36",
			"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.3538.77 Safari/537.36",
			"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.2224.3 Safari/537.36",
			"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.2224.3 Safari/531.11",
			"Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.2224.3 Safari/533.23",
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
	// Format the request header array
	$headers = array (
			'Cache-Control' => 'max-age=0',
			'User-Agent' => $ua,
			'Accept' => 'audio/mp3',
			'Referer' => 'https://www.oddcast.com/',
			'Accept-Language' => 'en-GB, en'
	);
	
	// Mapped language to code
	$mappedLangCode = array (
			'ar-AR' => ['EID'=>7, 'LID'=>27, 'VID' =>1],
			'ar-AE' => ['EID'=>7, 'LID'=>27, 'VID' =>1],
			'ar-AA' => ['EID'=>7, 'LID'=>27, 'VID' =>1],
			'ar' => ['EID'=>7, 'LID'=>27, 'VID' =>1],
			'ca-ES' => ['EID'=>2, 'LID'=>5, 'VID' =>3],
			'ca' => ['EID'=>2, 'LID'=>5, 'VID' =>3],
			'cs-CZ' => ['EID'=>7, 'LID'=>18, 'VID' =>1],
			'cs' => ['EID'=>7, 'LID'=>18, 'VID' =>1],
			'da-DK' => ['EID'=>7, 'LID'=>19, 'VID' =>1],
			'da' => ['EID'=>7, 'LID'=>19, 'VID' =>1],
			'de-DE' => ['EID'=>7, 'LID'=>3, 'VID' =>1],
			'de' => ['EID'=>7, 'LID'=>3, 'VID' =>1],
			'el-GR' => ['EID'=>7, 'LID'=>8, 'VID' =>1],
			'el' => ['EID'=>7, 'LID'=>8, 'VID' =>1],
			'en-US' => ['EID'=>3, 'LID'=>1, 'VID' =>6],
			'en-GB' => ['EID'=>3, 'LID'=>1, 'VID' =>4],
			'en-AU' => ['EID'=>4, 'LID'=>1, 'VID' =>4],
			'en' => ['EID'=>3, 'LID'=>1, 'VID' =>4],
			'es-MX' => ['EID'=>3, 'LID'=>2, 'VID' =>1],
			'es-ES' => ['EID'=>2, 'LID'=>2, 'VID' =>9],
			'es' => ['EID'=>2, 'LID'=>2, 'VID' =>9],
			'fi-FI' => ['EID'=>7, 'LID'=>23, 'VID' =>1],
			'fi' => ['EID'=>7, 'LID'=>23, 'VID' =>1],
			'fr-FR' => ['EID'=>7, 'LID'=>4, 'VID' =>1],
			'fr' => ['EID'=>7, 'LID'=>4, 'VID' =>1],
			'hi-IN' => ['EID'=>7, 'LID'=>24, 'VID' =>2],
			'hi' => ['EID'=>7, 'LID'=>24, 'VID' =>2],
			'hu-HU' => ['EID'=>7, 'LID'=>29, 'VID' =>1],
			'hu' => ['EID'=>7, 'LID'=>29, 'VID' =>1],
			'id-ID' => ['EID'=>4, 'LID'=>28, 'VID' =>1],
			'id' => ['EID'=>4, 'LID'=>28, 'VID' =>1],
			'it-IT' => ['EID'=>7, 'LID'=>7, 'VID' =>1],
			'it' => ['EID'=>7, 'LID'=>7, 'VID' =>1],
			'ja-JP' => ['EID'=>3, 'LID'=>12, 'VID' =>3],
			'ja' => ['EID'=>3, 'LID'=>12, 'VID' =>3],
			'ko-KR' => ['EID'=>3, 'LID'=>13, 'VID' =>7],
			'ko' => ['EID'=>3, 'LID'=>13, 'VID' =>7],
			'nb-NO' => ['EID'=>7, 'LID'=>20, 'VID' =>1],
			'nb' => ['EID'=>7, 'LID'=>20, 'VID' =>1],
			'nl-BE' => ['EID'=>4, 'LID'=>11, 'VID' =>1],
			'nl-NL' => ['EID'=>4, 'LID'=>11, 'VID' =>2],
			'nl' => ['EID'=>4, 'LID'=>11, 'VID' =>2],
			'pl-PL' => ['EID'=>4, 'LID'=>14, 'VID' =>1],
			'pl' => ['EID'=>4, 'LID'=>14, 'VID' =>1],
			'pt-PT' => ['EID'=>7, 'LID'=>6, 'VID' =>3],
			'pt-BR' => ['EID'=>7, 'LID'=>6, 'VID' =>1],
			'pt' => ['EID'=>7, 'LID'=>6, 'VID' =>3],
			'ro-RO' => ['EID'=>4, 'LID'=>30, 'VID' =>1],
			'ro' => ['EID'=>4, 'LID'=>30, 'VID' =>1],
			'ru-RU' => ['EID'=>4, 'LID'=>21, 'VID' =>2],
			'ru' => ['EID'=>4, 'LID'=>21, 'VID' =>2],
			'sk-SK' => ['EID'=>7, 'LID'=>37, 'VID' =>1],
			'sk' => ['EID'=>7, 'LID'=>37, 'VID' =>1],
			'sv-FI' => ['EID'=>4, 'LID'=>9, 'VID' =>1],
			'sv-SE' => ['EID'=>4, 'LID'=>9, 'VID' =>1],
			'sv' => ['EID'=>4, 'LID'=>9, 'VID' =>1],
			'th-TH' => ['EID'=>4, 'LID'=>26, 'VID' =>1],
			'th' => ['EID'=>4, 'LID'=>26, 'VID' =>1],
			'tr-TR' => ['EID'=>2, 'LID'=>16, 'VID' =>3],
			'tr' => ['EID'=>2, 'LID'=>16, 'VID' =>3],
			'uk-UA' => ['EID'=>7, 'LID'=>40, 'VID' =>1],
			'uk' => ['EID'=>7, 'LID'=>40, 'VID' =>1],
			'zh-CN' => ['EID'=>3, 'LID'=>10, 'VID' =>6],
			'zh-HK' => ['EID'=>3, 'LID'=>10, 'VID' =>6],
			'zh' => ['EID'=>3, 'LID'=>10, 'VID' =>6]
	);
	
	if(array_key_exists($lang, $mappedLangCode)) {
		$langSelectedVoice = $mappedLangCode[$lang];
	} else {
		$langSelectedVoice = ['EID'=>3, 'LID'=>1, 'VID' =>6];
	}
	
	// Remote POST using sockets or CURL lib
	
	$HTTPClient = new \jscrHttp ();
	// Make the first POST form request and get the HTML page including the MP3 link
	$qs = http_build_query ( array_merge(array (
			'TXT' => $text,
			'EXT' => 'mp3',
			'ACC' => 15679
	), $langSelectedVoice) );
	$HTTPResponse = $HTTPClient->get ( "https://cache-a.oddcast.com/tts/genC.php?" . $qs, $headers ); // Reader page https://ttsdemo.com/
	
	$binaryString = $HTTPResponse->body;
	$download_size = strlen ( $HTTPResponse->body );

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

	echo $binaryString;
}
exit ();