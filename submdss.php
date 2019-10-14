<?php 
$t = time();
$surl = "https://port.xshell.us/static/api/node.json";
$json = json_decode(get_ssr_json($surl, $t) , true);
$ssr = array_column($json, 'ssr-link');
foreach ($ssr as &$v) {
    $v = substr($v, 6);
    $v = base64_url_decode($v);
    $arr = explode('&group=', $v);
	$v = 'ssr://' . base64_url_encode($arr[0] . '&group=' . base64_url_encode('MDSS-Free'));
}
$res = base64_url_encode(implode(PHP_EOL, $ssr));
echo $res;

/*ssr-link 加密解密*/
function base64_url_encode($input) {
    return strtr(base64_encode($input), array('+' => '-', '/' => '_', '=' => ''));
}
function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}

/*拉取订阅内容*/
function get_ssr_json($surl, $t) {
	$headers = randIp();
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$surl."?".$t);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 ");
  	/*构造IP*/
	curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  	/*设置超时限制防止死循环*/
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$get_sub_json = curl_exec($ch);
	$httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($get_sub_json === FALSE||$httpCode >= "300" ) {
		header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
		exit();
	} else {
		return $get_sub_json;
	}
}

/*生成随机IP*/
function randIP(){
	$ip_long = array(
		array('607649792', '608174079'), //36.56.0.0-36.63.255.255
		array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
		array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
		array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
		array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
		array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
		array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
		array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
		array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
		array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
	);
	$rand_key = mt_rand(0, 9);
	$ip_rank = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
	$headers['CLIENT-IP'] = $ip_rank;
	$headers['X-FORWARDED-FOR'] = $ip_rank;
	$headerArr = array();
	foreach( $headers as $n => $v ) { 
		$headerArr[] = $n .':' . $v;  
	}
	return $headerArr;
} 
?>