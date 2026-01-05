<?php
/**
 * Google Token Generator
 *
 * @package SCREENREADER::plugins
 * @author JExtensions Store
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 * Thanks to @helen5106 and @tehmaestro and few other cool guys
 * at https://github.com/Stichoza/google-translate-php/issues/32
 */
class GoogleTokenGenerator {
	/**
	 * Plugin params
	 *
	 * @access private
	 * @var object
	 */
	private $pluginParams;
	
	/**
	 * Generate and return a token
	 *
	 * @param string $source
	 *        	Source language
	 * @param string $target
	 *        	Target language
	 * @param string $text
	 *        	Text to translate
	 * @return mixed A token
	 */
	public function generateToken($text) {
		// CONFIG LOAD DA DB OPTIONS
		if (file_exists('../../../../../wp-config.php')) {
			require_once '../../../../../wp-config.php';
			$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
			$dbSelected = mysqli_select_db($link, DB_NAME);
			$screenreaderQuery = "SELECT * FROM " . $table_prefix . "screenreader_config";
			$this->pluginParams = mysqli_fetch_object(mysqli_query($link, $screenreaderQuery));
			mysqli_close($link);
		}
		
		return $this->TL ( $text );
	}
	
	/**
	 * Generate a valid Google Translate TKK token
	 *
	 * @access private
	 * @return string
	 */
	private function staticTKK() {
		$a = 561666268;
		$b = 1526272306;
		return 406398 . '.' . ($a + $b);
	}
	
	/**
	 * Generate a valid Google Translate TKK token
	 *
	 * @access private
	 * @return string
	 */
	private function TKK() {
		session_start();
		
		if(isset($_SESSION['screenreader_engine_google_token'])) {
			$sessionGoogleToken = $_SESSION['screenreader_engine_google_token'];
			return $sessionGoogleToken;
		}
			
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
				'Accept' => 'text/html',
				'Referer' => 'https://translate.google.com/',
				'Accept-Language' => 'en-GB, en'
		);
		
		// Request API GET Transport wrapper
		$HTTPClient = new jscrHttp ();
		$HTTPResponse = $HTTPClient->get ( "https://translate.google.com", $headers );
		
		if($HTTPResponse->code == '200') {
			$bodyResponsePage = $HTTPResponse->body;
			preg_match("/tkk:'(\d+)\.(\d+)'/i", $bodyResponsePage, $AandBArray);
			
			if(is_array($AandBArray) && $AandBArray[0]) {
				// First var $a
				$a = $AandBArray[1];
				
				// Second var $b
				$b = $AandBArray[2];
				
				// Fallback if not valid
				if(!$a || !$b) {
					$a = 561666268;
					$b = 1526272306;
					$hoursElapsed = 406398;
				} else {
					// Return dynamic values
					$_SESSION['screenreader_engine_google_token'] = $a . '.' . $b;
					return $a . '.' . $b;
				}
			} else {
				// Fallback if not valid
				$a = 561666268;
				$b = 1526272306;
				$hoursElapsed = 406398;
			}
		} else {
			// Fallback if not valid
			$a = 561666268;
			$b = 1526272306;
			$hoursElapsed = 406398;
		}
		
		$_SESSION['screenreader_engine_google_token'] = $hoursElapsed . '.' . ($a + $b);

		return $hoursElapsed . '.' . ($a + $b);
	}
	
	/**
	 * Generate a valid Google Translate request token
	 *
	 * @param string $a
	 *        	text to translate
	 * @return string
	 */
	private function TL($a) {
		// Evaluate the token generator mode, static values are default, otherwise scrape dynamic values from Google Translate
		if(isset($this->pluginParams->engine_google_token_mode) && $this->pluginParams->engine_google_token_mode) {
			$tkk = explode('.', $this->TKK());
		} else {
			$tkk = explode('.', $this->staticTKK());
		}
		
		$b = $tkk[0];
		
		for($d = array (), $e = 0, $f = 0; $f < mb_strlen ( $a, 'UTF-8' ); $f ++) {
			$g = $this->charCodeAt ( $a, $f );
			if (128 > $g) {
				$d [$e ++] = $g;
			} else {
				if (2048 > $g) {
					$d [$e ++] = $g >> 6 | 192;
				} else {
					if (55296 == ($g & 64512) && $f + 1 < mb_strlen ( $a, 'UTF-8' ) && 56320 == ($this->charCodeAt ( $a, $f + 1 ) & 64512)) {
						$g = 65536 + (($g & 1023) << 10) + ($this->charCodeAt ( $a, ++ $f ) & 1023);
						$d [$e ++] = $g >> 18 | 240;
						$d [$e ++] = $g >> 12 & 63 | 128;
					} else {
						$d [$e ++] = $g >> 12 | 224;
						$d [$e ++] = $g >> 6 & 63 | 128;
					}
				}
				$d [$e ++] = $g & 63 | 128;
			}
		}
		$a = $b;
		for($e = 0; $e < count ( $d ); $e ++) {
			$a += $d [$e];
			$a = $this->RL ( $a, '+-a^+6' );
		}
		$a = $this->RL ( $a, "+-3^+b+-f" );
		$a ^= $tkk[1];
		if (0 > $a) {
			$a = ($a & 2147483647) + 2147483648;
		}
		$a = fmod ( $a, pow ( 10, 6 ) );
		return $a . "." . ($a ^ $b);
	}
	
	/**
	 * Generate "b" parameter
	 * The number of hours elapsed, since 1st of January 1970
	 *
	 * @return double
	 */
	private function generateB() {
		$start = new DateTime ( '1970-01-01' );
		$now = new DateTime ( 'now' );
		
		$diff = $now->diff ( $start );
		
		return $diff->h + ($diff->days * 24);
	}
	
	/**
	 * Process token data by applying multiple operations
	 *
	 * @param
	 *        	$a
	 * @param
	 *        	$b
	 * @return int
	 */
	private function RL($a, $b) {
		for($c = 0; $c < strlen ( $b ) - 2; $c += 3) {
			$d = $b [$c + 2];
			$d = $d >= 'a' ? $this->charCodeAt ( $d, 0 ) - 87 : intval ( $d );
			$d = $b [$c + 1] == '+' ? $this->shr32 ( $a, $d ) : $a << $d;
			$a = $b [$c] == '+' ? ((int)($a + $d) & (int)4294967295) : $a ^ $d;
		}
		return $a;
	}
	
	/**
	 * Crypto function
	 *
	 * @param
	 *        	$x
	 * @param
	 *        	$bits
	 * @return number
	 */
	private function shr32($x, $bits) {
		if ($bits <= 0) {
			return $x;
		}
		if ($bits >= 32) {
			return 0;
		}
		$bin = decbin ( $x );
		$l = strlen ( $bin );
		if ($l > 32) {
			$bin = substr ( $bin, $l - 32, 32 );
		} elseif ($l < 32) {
			$bin = str_pad ( $bin, 32, '0', STR_PAD_LEFT );
		}
		return bindec ( str_pad ( substr ( $bin, 0, 32 - $bits ), 32, '0', STR_PAD_LEFT ) );
	}
	
	/**
	 * Get the Unicode of the character at the specified index in a string
	 *
	 * @param string $str        	
	 * @param int $index        	
	 * @return null number
	 */
	private function charCodeAt($str, $index) {
		$char = mb_substr ( $str, $index, 1, 'UTF-8' );
		if (mb_check_encoding ( $char, 'UTF-8' )) {
			$ret = mb_convert_encoding ( $char, 'UTF-32BE', 'UTF-8' );
			$result = hexdec ( bin2hex ( $ret ) );
			return $result;
		}
		return null;
	}
}