<?php

class Utils {
		
	static function truncate($string, $num, $snipFront = false) {
		if (strlen($string) > $num) {
			if ($snipFront) {
				$string = '...'.substr($string, strlen($string) - $num + 3, strlen($string));	
			}
			else {
				$string = substr($string, 0, $num - 3).'...';	
			}
		}
		return $string;
	}
	
	static function rand($length = 8, $possible = '123456789abcdefghjkmnpqrstuvwxyz') {
		$string = "";
		$i = 0;

		while ($i < $length) {
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			if (!strstr($string, $char)) { 
				$string .= $char;
				$i++;
			}
		}
		return $string;
	}
	
	static function fileinfo($file) {
		$return = array(
			'exif' => @exif_read_data($file),
			'checksum' => sha1_file($file),
			'size' => filesize($file)
		);
		list($return['width'], $return['height']) = getimagesize($file);
		
		$finfo = new finfo(FILEINFO_MIME);
		$type = $finfo->file($file);
		$return['type'] = substr($type, 0, strpos($type, ';'));
		return $return;
	}
	
	static function filename($file, $retina = false) {
		if ($retina) {
			$info = pathinfo($file);	
			$filename = $info['filename'].'@2x.'.$info['extension'];
		}
		else {
			$filename = basename($file);
		}
		return $filename;	
	}

	function unparse_url($parsed) {
		if (!is_array($parsed)) {
			return false;
		}

		$uri = isset($parsed['scheme']) ? $parsed['scheme'].':'.((strtolower($parsed['scheme']) == 'mailto') ? '' : '//') : '';
		$uri .= isset($parsed['user']) ? $parsed['user'].(isset($parsed['pass']) ? ':'.$parsed['pass'] : '').'@' : '';
		$uri .= isset($parsed['host']) ? $parsed['host'] : '';
		$uri .= isset($parsed['port']) ? ':'.$parsed['port'] : '';

		if (isset($parsed['path'])) {
			$uri .= (substr($parsed['path'], 0, 1) == '/') 
				? $parsed['path'] 
				: ((!empty($uri) ? '/' : '' ) . $parsed['path']);
		}

		$uri .= isset($parsed['query']) ? '?'.$parsed['query'] : '';
		$uri .= isset($parsed['fragment']) ? '#'.$parsed['fragment'] : '';

		return $uri;
	}

	function validate_url($url, $return = false) {
		$regexp = "^(https?://)?(([0-9a-z_!~*'().&=+$%-]+:)?[0-9a-z_!~*'().&=+$%-]+@)?((([12]?[0-9]{1,2}\.){3}[12]?[0-9]{1,2})|(([0-9a-z_!~*'()-]+\.)*([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.(com|net|org|edu|mil|gov|int|aero|coop|museum|name|info|biz|pro|[a-z]{2})))(:[1-6]?[0-9]{1,4})?((/?)|(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$";
	
		if (empty($url)) {
			return $return;
		}
		$urls = parse_url($url);
		$query = isset($urls['query']) ? $urls['query']: null;
		$fragment = isset($urls['fragment']) ? $urls['fragment']: null;
		unset($urls['query']);
		unset($urls['fragment']);
		$url = self::unparse_url($urls);
	
		if (eregi( $regexp, $url )){
			if (!empty($query)) {
				$url = $url.'?'.$query;
			}
			if (!empty($fragment)) {
				$url = $url.'#'.$url;
			}
		    // No http:// at the front? lets add it.
		    if (!eregi( "^https?://", $url )) $url = "http://" . $url;

		    // If it's a plain domain or IP there should be a / on the end
		    if (!eregi( "^https?://.+/", $url )) $url .= "";

		    // If it's a directory on the end we should add the proper slash
		    // We should first make sure it isn't a file, query, or fragment
		    if ((eregi( "/[0-9a-z~_-]+$", $url)) && (!eregi( "[\?;&=+\$,#]", $url))) $url .= "";
		    return $url;
		}
		return $return;
	}
	
	function validate_email($email) {
		if (!ereg("[^@]{1,64}@[^@]{1,255}", $email)) {     
			return false;
		}
		# Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
				return false;
			}
		}  
		# Check if domain is IP. If not, it should be valid domain name
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
					return false;
				}
			}
		}
		return $email;
	}	
	
	static function number_unformat($value) {
		return (float) str_replace(',', '', $value);
	}
}

function db($value) {
	echo '<pre>'.print_r($value, true).'</pre>';
}