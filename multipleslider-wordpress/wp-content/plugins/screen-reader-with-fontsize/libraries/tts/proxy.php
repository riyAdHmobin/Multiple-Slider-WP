<?php
/**
 * Proxy della REST API di Google TTS
 * @package SCREENREADER::plugins
 * @author JExtensions Store 
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
// voice speed
$voiceSpeed = ($_GET ['voicespeed']);

if ($token === md5 ( $_SERVER ['HTTP_HOST'] )) {
	// Lazy loading
	require_once '../http/http.php';
	require_once 'googletoken.php';
	
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
			'Cache-Control' => 'max-age=0',
			'User-Agent' => $ua,
			'Accept' => 'audio/mpeg',
			'Referer' => 'https://translate.google.com/',
			'Accept-Language' => 'en-GB, en' 
	);
	
	// Request API GET
	// Transport wrapper
	$HTTPClient = new jscrHttp ();
	
	$speedMapping = array(
		'veryslow' => '0.2',
		'slow'  => '0.4',
		'normal'  => '1'
	);
	$ttsSpeed = array_key_exists($voiceSpeed, $speedMapping) ? $speedMapping[$voiceSpeed] : '1';
	
	$qs = http_build_query ( array (
			'tl' => $lang,
			'ie' => 'UTF-8',
			'client' => 't',
			'q' => ($text),
			'ttsspeed' => $ttsSpeed,
			'textlen' => mb_strlen($text),
			'idx' => 0
	) );
	$googleTokenGenerator = new GoogleTokenGenerator();
	$qs .= '&tk=' . $googleTokenGenerator->generateToken($text);
	
	$HTTPResponse = $HTTPClient->get ( "https://translate.google.com/translate_tts?$qs", $headers );
	
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