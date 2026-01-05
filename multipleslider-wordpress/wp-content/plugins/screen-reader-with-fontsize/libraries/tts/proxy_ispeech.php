<?php
/**
 * Proxy della REST API di ISpeech
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
			'Accept' => '*/*',
			'Accept-Encoding' => 'identity;q=1, *;q=0',
			'Accept-Language' => 'en,it;q=0.9,en-US;q=0.8,de;q=0.7,es;q=0.6,fr;q=0.5,ru;q=0.4,ja;q=0.3,el;q=0.2,sk;q=0.1,nl;q=0.1,ar;q=0.1,sv;q=0.1,da;q=0.1',
			'Cache-Control' => 'no-cache',
			'Connection' => 'keep-alive',
			'Pragma' => 'no-cache',
			'Range' => 'bytes=0-',
			'Referer' => 'http://www.voicerss.org/api/demo.aspx',
			'User-Agent' => $ua
	);
	
	// Mapped language to code
	$mappedLangCode = array (
			'ar-AR' => array ('ar-sa', 'Salim'),
			'ar-EG' => array ('ar-eg', 'Oda'),
			'ar' => array ('ar-sa', 'Salim'),
			'bg-BG' => array ('bg-bg', 'Dimo'),
			'bg' => array ('bg-bg', 'Dimo'),
			'ca-ES' => array ('ca-es', 'Rut'),
			'ca' => array ('ca-es', 'Rut'),
			'zh-CN' => array ('zh-cn', 'Luli'),
			'zh-HK' => array ('zh-hk', 'Xia'),
			'zh-TW' => array ('zh-tw', 'Lin'),
			'zh' => array ('zh-cn', 'Luli'),
			'hr-HR' => array ('hr-hr', 'Nikola'),
			'hr' => array ('hr-hr', 'Nikola'),
			'cs-CZ' => array ('cs-cz', 'Josef'),
			'cs' => array ('cs-cz', 'Josef'),
			'da-DK' => array ('da-dk', 'Freja'),
			'da' => array ('da-dk', 'Freja'),
			'nl-BE' => array ('nl-be', 'Daan'),
			'nl-NL' => array ('nl-nl', 'Bram'),
			'nl' => array ('nl-be', 'Daan'),
			'en-AU' => array ('en-au', 'Isla'),
			'en-US' => array ('en-us', 'Mary'),
			'en-CA' => array ('en-ca', 'Clara'),
			'en-GB' => array ('en-gb', 'Alice'),
			'en' => array ('en-gb', 'Alice'),
			'fi-FI' => array ('fi-fi', 'Aada'),
			'fi' => array ('fi-fi', 'Aada'),
			'fr-CA' => array ('fr-ca', 'Olivia'),
			'fr-CH' => array ('fr-ch', 'Theo'),
			'fr-FR' => array ('fr-fr', 'Iva'),
			'fr' => array ('fr-fr', 'Iva'),
			'de-DE' => array ('de-de', 'Lina'),
			'de' => array ('de-de', 'Lina'),
			'el-GR' => array ('el-gr', 'Neo'),
			'el' => array ('el-gr', 'Neo'),
			'he-IL' => array ('he-il', 'Rami'),
			'he' => array ('he-il', 'Rami'),
			'hi-IN' => array ('hi-in', 'Puja'),
			'hi' => array ('hi-in', 'Puja'),
			'hu-HU' => array ('hu-hu', 'Mate'),
			'hu' => array ('hu-hu', 'Mate'),
			'id-ID' => array ('id-id', 'Intan'),
			'id' => array ('id-id', 'Intan'),
			'it-IT' => array ('it-it', 'Mia'),
			'it' => array ('it-it', 'Mia'),
			'ja-JP' => array ('ja-jp', 'Airi'),
			'ja' => array ('ja-jp', 'Airi'),
			'ko-KR' => array ('ko-kr', 'Nari'),
			'ko' => array ('ko-kr', 'Nari'),
			'ms-MY' => array ('ms-my', 'Aqil'),
			'ms' => array ('ms-my', 'Aqil'),
			'nb-NO' => array ('nb-no', 'Marti'),
			'nb' => array ('nb-no', 'Marti'),
			'pl-PL' => array ('pl-pl', 'Jan'),
			'pl' => array ('pl-pl', 'Jan'),
			'pt-PT' => array ('pt-pt', 'Leonor'),
			'pt-BR' => array ('pt-br', 'Ligia'),
			'pt' => array ('pt-pt', 'Leonor'),
			'ro-RO' => array ('ro-ro', 'Doru'),
			'ro' => array ('ro-ro', 'Doru'),
			'ru-RU' => array ('ru-ru', 'Marina'),
			'ru' => array ('ru-ru', 'Marina'),
			'sk-SK' => array ('sk-sk', 'Beda'),
			'sk' => array ('sk-sk', 'Beda'),
			'sl-SI' => array ('sl-si', 'Vid'),
			'sl' => array ('sl-si', 'Vid'),
			'es-ES' => array ('es-es', 'Diego'),
			'es-MX' => array ('es-mx', 'Silvia'),
			'es' => array ('es-es', 'Diego'),
			'sv-FI' => array ('sv-se', 'Molly'),
			'sv-SE' => array ('sv-se', 'Molly'),
			'sv' => array ('sv-se', 'Molly'),
			'ta-IN' => array ('ta-in', 'Sai'),
			'ta' => array ('ta-in', 'Sai'),
			'th-TH' => array ('th-th', 'Ukrit'),
			'th' => array ('th-th', 'Ukrit'),
			'tr-TR' => array ('tr-tr', 'Omer'),
			'tr' => array ('tr-tr', 'Omer'),
			'vi-VN' => array ('vi-vn', 'Chi'),
			'vi' => array ('vi-vn', 'Chi')
	);
	
	if(array_key_exists($lang, $mappedLangCode)) {
		$langSelectedVoice = $mappedLangCode[$lang][0];
		$langSelectedName = $mappedLangCode[$lang][1];
	} else {
		$langSelectedVoice = 'en-gb';
		$langSelectedName = 'Alice';
	}
	
	// Remote POST using sockets or CURL lib
	$HTTPClient = new jscrHttp ();

	// Make the first POST form request and get the HTML page including the MP3 link
	$qs = http_build_query ( array (
			'hl' => $langSelectedVoice,
			'v' => $langSelectedName,
			'src' => $text,
			'rnd'=> (float)rand()/(float)getrandmax()
	) );
	$HTTPResponse = $HTTPClient->get ( "https://www.voicerss.org/controls/speech.ashx?" . $qs, $headers);
	
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