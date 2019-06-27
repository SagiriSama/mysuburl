<?php 
/*读取许可链接列表*/
$t = time();
$sub_url_list = @file_get_contents("https://raw.githubusercontent.com/BGIII/mysuburl/master/url.dat?".$t);
$sub_url = array();
if(!$sub_url_list){
	exit('list read fail');
}else{
  	$sub_url_list = mb_convert_encoding($sub_url_list, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
    $sub_url = explode("\n", $sub_url_list);
  	$sub_url = array_filter($sub_url);
}
/*判断链接是否合法*/
if($_GET['s']) {
	$surl = $_GET['s'];
 	$isMatched = preg_match('/[a-zA-z]+:\/\/(.*?)[.](.*?)\//', $surl, $code_matches);
  	if($isMatched){
    	$ssr_url = $code_matches[1].'.'.$code_matches[2];
  		$url_check = in_array($ssr_url, $sub_url, TRUE);
    }
} else {
	header('HTTP/1.1 404 Not Found');
	header("status: 404 Not Found");
	exit();
}
/*拉取订阅内容*/
if($isMatched && $url_check) {
	$headers = randIp();
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$surl);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 ");
  	/*构造IP*/
	curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  	/*设置超时限制防止死循环*/
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$getsurl = curl_exec($ch);
	$httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($getsurl === FALSE||$httpCode >= "300" ){
		header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
      	exit();
	} else {
		echo $getsurl;
	}
} else {
	header('HTTP/1.1 404 Not Found');
	header("status: 404 Not Found");
  	if($url_check){
  		exit();
    } else {
      	echo "<pre>Authorization URL:\n".$sub_url_list. "<pre>";
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