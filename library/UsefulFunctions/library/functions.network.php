<?php

if (!function_exists('ClientRequest')) {
	/**
	* Perform client request to server.
	* Options: see here http://www.php.net/manual/en/function.curl-setopt.php
	* Bool options: 
	* 	ReturnTransfer, Post, FollowLocation, Header
	* Integer options: 
	* 	ConnectTimeout, Timeout, Timeout_Ms
	* Other options: 
	* 	Url, Cookie, CookieFile, CustomRequest, PostFields, Referer, UserAgent, UserPwd
	* 
	* @param mixed $Url or array $Options.
	* @return mixed $Result.
	*/
	function ClientRequest($Url, $Options = False) {
		static $Connections = array();
		static $ManualFollowLocation;
		$NumArgs = func_num_args();
		if ($NumArgs == 1) {
			$Options = $Url;
			if (is_string($Options)) $Options = array('Url' => $Options);
		} else {
			$Options['Url'] = $Url;
		}

		$NewOptions = $Options;

		$Cache = GetValue('Cache', $Options, False, True);
		if ($Cache !== False) {
			$Crc = sprintf('%u', crc32(serialize($Options)));
			$CacheDirectory = PATH_CACHE . DS . 'client-request';
			$CacheFile = $CacheDirectory . DS . $Crc . '.php';
			if (file_exists($CacheFile)) {
				$IncludeCache = True;
				if (is_int($Cache)) {
					$LifeTime = time() - filemtime($CacheFile);
					$Expired = ($LifeTime > $Cache);
					if ($Expired) {
						$IncludeCache = False;
					}
				}
				if ($IncludeCache) {
					$Result = include $CacheFile;
					return $Result;
				}
			}
		}

		$Url = GetValue('Url', $Options, False, True);
		$ConvertEncoding = GetValue('ConvertEncoding', $Options, False, True);
		$Header = GetValue('Header', $Options);
		$FollowLocation = GetValue('FollowLocation', $Options);
		if ($FollowLocation) {
			if ($ManualFollowLocation === Null) {
				$ManualFollowLocation = (array_filter(array_map('ini_get', array('safe_mode', 'open_basedir'))) > 0);
			}
			if ($ManualFollowLocation) {
				unset($Options['FollowLocation']);
			}
		}
		$GetInfo = GetValue('GetInfo', $Options, False, True);
		TouchValue('ReturnTransfer', $Options, True);
		//TouchValue('ConnectTimeout', $Options, 30);
		//TouchValue('Timeout', $Options, 5);
		
		if (!array_key_exists($Url, $Connections)) $Connections[$Url] = curl_init($Url); 
		$Connection =& $Connections[$Url];
		
		foreach ($Options as $Option => $Value) {
			$Constant = 'CURLOPT_' . strtoupper($Option);
			if (!defined($Constant)) {
				$InfoConstant = 'CURLINFO_' . strtoupper($Option);
				if (!defined($InfoConstant)) {
					trigger_error("cURL. Unknown option: $Constant ($InfoConstant)");
				} else {
					$Constant = $InfoConstant;
				}

			}
			curl_setopt($Connection, constant($Constant), $Value);
		}

		$Result = curl_exec($Connection);
		if ($Result === False) {
			$ErrorMessage = curl_error($Connection);
			//$ErrorNo = curl_errno($Connection);
			trigger_error($ErrorMessage);
			return False;
		}

		if ($Header != False) {
			$ResponseLines = explode("\n", trim($Result));
			$Status = array_shift($ResponseLines);
			$Response = array();
			$Response['HTTP'] = trim($Status);
			$Response['StatusCode'] = array_pop(array_slice(explode(' ', trim($Status)), 1, 1));
			for ($Count = count($ResponseLines), $i = 0; $i < $Count; $i++) {
				$Line = trim($ResponseLines[$i]);
				unset($ResponseLines[$i]);
				if ($Line === '') break;
				$Line = explode(':', $Line);
				$Key = trim(array_shift($Line));
				$Value = trim(implode(':', $Line));
				if (!isset($Response[$Key])) {
					$Response[$Key] = $Value;
				} else {
					if (!is_array($Response[$Key])) $Response[$Key] = array($Response[$Key]);
					$Response[$Key][] = $Value;
				}
			}
			$Result = implode("\n", $ResponseLines);
			unset($ResponseLines);
		}

		if ($GetInfo || $ConvertEncoding || $Header) {
			$Result = array('Result' => $Result);
			$Result['Info'] = curl_getinfo($Connection);
			if ($Header) {
				$Result['Headers'] = $Response;
			}
		}

		if ($FollowLocation != False && $ManualFollowLocation) {
			$Code = GetValueR('Info.http_code', $Result);
			if (in_array($Code, array(301, 302))) {
				$Location = GetValueR('Info.redirect_url', $Result);
				if ($Location === False) $Location = GetValueR('Headers.Location', $Result);
				$NewOptions['Url'] = $Location;
				return ClientRequest($NewOptions);
			}
		}

		if ($ConvertEncoding) {
			list($MimeType, $DirtyCharsetInfo) = array_pad(explode(';', $Result['Info']['content_type']), 2, Null);
			$Result['MimeType'] = $MimeType;
			preg_match('/charset=(.+)/', $DirtyCharsetInfo, $Match);
			$Charset = strtolower(GetValue(1, $Match));
			if ($Charset && $Charset != 'utf-8') {
				$Result['Result'] = mb_convert_encoding($Result['Result'], 'utf-8', $Charset);
			}

			if (!$GetInfo) {
				$Result = $Result['Result'];
			}
		}

		if (isset($Result['Info']['content_type'])) {
			$Tmp = explode(';', $Result['Info']['content_type']);
			$Type = trim(array_shift($Tmp));
			if (substr($Type, -4) == 'json') {
				$Result['Json'] = json_decode($Result['Result']);
			}
		}

		if ($Cache !== False) {
			if (!is_dir($CacheDirectory)) {
				mkdir($CacheDirectory, 0777, True);
			}
			$Contents = "<?php if (!defined('APPLICATION')) exit(); \nreturn " . var_export($Result, True) . ';';
			file_put_contents($CacheFile, $Contents);
		}

		return $Result;
	}

}

if (!function_exists('RealIpAddress')) {
	/**
	* Gets/converts IP-address (numeric format/dot format).
	* 
	* @param mixed $Ip.
	* @return mixed $Ip, converted or gotten numeric IP.
	*/
	function RealIpAddress($Ip = Null) {
		if (is_null($Ip)) {
			foreach(array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_X_CLUSTER_CLIENT_IP','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR') as $Key) {
				if (isset($_SERVER[$Key])) {
					list ($Ip) = explode(',', $_SERVER[$Key]);
					break;
				}
			}
		}
		if (!$Ip) return $Ip;
		return (is_numeric($Ip)) ? long2ip($Ip) : sprintf('%u', ip2long($Ip));
	}
}


if (!function_exists('IsOnline')) {
	function IsOnline() {
		return is_int(ip2long(gethostbyname('google.com')));
	}
}

if (!function_exists('CheckIpMask')) {
	function CheckIpMask($MaskIp, $RemoteAddr = False) {
		if($RemoteAddr === False) $RemoteAddr = $_SERVER['REMOTE_ADDR'];
		list($Ip, $MaskBit) = explode('/', $MaskIp);
		$IpLong = ip2long($Ip) >> (32 - $MaskBit);
        $SelfIpLong = ip2long($RemoteAddr) >> (32 - $MaskBit);
        return ($SelfIpLong == $IpLong);
	}
}

if (!function_exists ('getmxrr')) {
	/**
	* Get MX records corresponding to a given Internet host name for Windows.
	* 
	* @see http://www.php.net/manual/en/function.getmxrr.php
	* @credits This script was writed by Setec Astronomy - setec@freemail.it
	*/
	function getmxrr($hostname = '', &$mxhosts, &$weight = array()) {
		$weight = array();
		$mxhosts = array();
		$result = false;

		$command = 'nslookup -type=mx ' . escapeshellarg($hostname);
		exec($command, $result);
		$i = 0;
		$nslookup = array();
		while (list($key, $value) = each($result)) {
			if (strstr($value, 'mail exchanger')) {
				$nslookup[$i] = $value;
				$i++;
			}
		}

		$mx = array();
		while (list($key, $value) = each($nslookup)) {
			$temp = explode(' ', $value);
			$mx[$key][0] = substr($temp[3], 0, -1);
			$mx[$key][1] = $temp[7];
			$mx[$key][2] = gethostbyname($temp[7]);
		}

		array_multisort($mx);

		foreach ($mx as $value) {
			$mxhosts[] = $value[1];
			$weight[] = $value[0];
		}

		return count($mxhosts) > 0;
	}
}
