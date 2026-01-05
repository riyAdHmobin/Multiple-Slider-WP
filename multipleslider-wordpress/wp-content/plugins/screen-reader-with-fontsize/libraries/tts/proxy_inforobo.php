<?php
/**
 * Proxy della REST API di INFOROBO
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
// voice speed
$voiceSpeed = ($GLOBALS['_' . strtoupper('get')] ['voicespeed']);

if ($token === md5 ( $_SERVER ['HTTP_HOST'] )) {
	// Lazy loading
	require_once '../http/http.php';

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
	// Format the request header array
	$headers = array (
			'Cache-Control' => 'no-cache',
			'Connection' => 'Keep-Alive',
			'User-Agent' => $ua,
			'Accept' => '*/*',
			'GetContentFeatures.DLNA.ORG' => '1',
			'Pragma' =>	'getIfoFileURI.dlna.org',
			'DNT' => '1',
			'Referer' => 'https://inforobo.com/text-to-speech-online/',
			'Accept-Language' => 'en-GB, en'
	);

	$mappedLangCode = array (
			'ar-AR' => 'ar',
			'ar-AE' => 'ar',
			'ar-AA' => 'ar',
			'ar' => 'ar',
			'cs-CZ' => 'cs',
			'cs' => 'cs',
			'da-DK' => 'da',
			'da' => 'da',
			'fi-FI' => 'fi',
			'fi' => 'fi',
			'de-DE' => 'de',
			'de' => 'de',
			'el-GR' => 'el',
			'el' => 'el',
			'en-US' => 'en-US',
			'en-GB' => 'en-GB',
			'en' => 'en-GB',
			'es-ES' => 'es',
			'es' => 'es',
			'fr-CA' => 'fr',
			'fr-FR' => 'fr',
			'fr' => 'fr',
			'hy-AM' => 'hy',
			'hy' => 'hy',
			'hr-HR' => 'hr',
			'hr' => 'hr',
			'hu-HU' => 'hu',
			'hu' => 'hu',
			'hi-IN' => 'hi',
			'hi' => 'hi',
			'id-ID' => 'id',
			'id' => 'id',
			'it-IT' => 'it',
			'it' => 'it',
			'ja-JP' => 'ja',
			'ja' => 'ja',
			'ko-KR' => 'ko',
			'ko' => 'ko',
			'nb-NO' => 'no',
			'nb' => 'no',
			'nl-BE' => 'nl',
			'nl-NL' => 'nl',
			'nl' => 'nl',
			'pl-PL' => 'pl',
			'pl' => 'pl',
			'pt-PT' => 'pt-PT',
			'pt-BR' => 'pt-BR',
			'pt' => 'pt-PT',
			'ro-RO' => 'ro',
			'ro' =>'ro',
			'ru-RU' => 'ru',
			'ru' =>'ru',
			'sk-SK' => 'sk',
			'sk' => 'sk',
			'sv-FI' => 'sv',
			'sv-SE' => 'sv',
			'sv' => 'sv',
			'tr-TR' => 'tr',
			'tr' => 'tr',
			'vi-VN' => 'vi',
			'vi' => 'vi',
			'zh-CN' => 'zh-CN',
			'zh-HK' => 'zh-HK',
			'zh' => 'zh-CN'
	);

	if(array_key_exists($lang, $mappedLangCode)) {
		$langSelectedVoice = $mappedLangCode[$lang];
	} else {
		$langSelectedVoice = 'en-GB';
	}

	// Remote POST using sockets or CURL lib
	$HTTPClient = new jscrHttp ();
	
	$speedMapping = array(
			'veryslow' => '0.25',
			'slow'  => '0.35',
			'normal'  => '0.45',
			'fast'  => '0.55',
			'veryfast'  => '0.60'
	);
	$ttsSpeed = array_key_exists($voiceSpeed, $speedMapping) ? $speedMapping[$voiceSpeed] : '1';
	
	// Make the first POST form request and get the HTML page including the MP3 link
	$qs = http_build_query ( array (
			't' => $text,
			'tl' => $langSelectedVoice,
			'sv' => null,
			'vn' => null,
			'pitch' => '0.5',
			'rate' => $ttsSpeed,
			'vol' => '1'
	) );
	$HTTPResponse = $HTTPClient->get ( "https://www.defit.org/gv.php?" . $qs, $headers );

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