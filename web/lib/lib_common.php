<?php
/**
 * Pixmicat! Common Library
 *
 * 存放常用函式供主程式引入
 *
 * @package PMCLibrary
 * @version $Id$
 * @date $Date$
 */
 
 use Yonkoma\Singleton;
 use Yonkoma\Helper;

//TODO: fix this shit
function transformTemplateArray($input)
{
	$result = [];
	foreach ($input as $key=>$val) {
		$newKey = str_replace('$', '', $key);
		$newKey = str_replace('{', '', $newKey);
		$newKey = str_replace('}', '', $newKey);
		$result[$newKey] = $val;
	}
	return $result;
}


/* 輸出表頭 */
function head(&$dat,$resno=0){
	$PMS = PMCLibrary::getPMSInstance();
	$twig = Singleton::getTwig('page.twig');
	$board = Helper\current_board();

	$pte_vals = array('{$TITLE}'=>TITLE,'{$RESTO}'=>$resno?$resno:'');
	$dat .= $twig->renderBlock('HEADER', transformTemplateArray($pte_vals));
	$PMS->useModuleMethods('Head', array(&$dat,$resno)); // "Head" Hook Point
	$pte_vals+=array('{$ALLOW_UPLOAD_EXT}' => ALLOW_UPLOAD_EXT,
		'{$JS_REGIST_WITHOUTCOMMENT}' => str_replace('\'', '\\\'', _T('regist_withoutcomment')),
		'{$JS_REGIST_UPLOAD_NOTSUPPORT}' => str_replace('\'', '\\\'', _T('regist_upload_notsupport')),
	);
	$dat .= $twig->renderBlock('JSHEADER', transformTemplateArray($pte_vals));
	$dat .= '</head>';
	$pte_vals += array('{$TOP_LINKS}' => TOP_LINKS,
		'{$HOME}' => '[<a href="'. Helper\anchor($board).'" target="_top">'._T('head_home').'</a>]',
		'{$STATUS}' => '[<a href="'. Helper\anchor($board).'/?mode=status">'._T('head_info').'</a>]',
		'{$ADMIN}' => '[<a href="'. Helper\anchor($board).'/?mode=admin">'._T('head_admin').'</a>]',
		'{$REFRESH}' => '[<a href="'. Helper\anchor($board, PHP_SELF2).'?">'._T('head_refresh').'</a>]',
		'{$SEARCH}' => (USE_SEARCH) ? '[<a href="'. Helper\anchor($board).'/?mode=search">'._T('head_search').'</a>]' : '',
		'{$HOOKLINKS}' => '');
	$PMS->useModuleMethods('Toplink', array(&$pte_vals['{$HOOKLINKS}'],$resno)); // "Toplink" Hook Point
	$dat .= $twig->renderBlock('BODYHEAD', transformTemplateArray($pte_vals));
}

/* 發表用表單輸出 */
function form(&$dat, $board, $resno, $iscollapse=true, $retURL=PHP_SELF, $name='', $mail='', $sub='', $com='', $cat='', $mode='regist'){
	global $ADDITION_INFO;
	$PMS = PMCLibrary::getPMSInstance();
	$twig = Singleton::getTwig('page.twig');

	$pte_vals = array('{$SELF}'=>$retURL, '{$FORMTOP}'=>'', '{$MODE}'=>$mode);
	$isedit = ($mode == 'edit'); // 是否為編輯模式
	if($resno && !$isedit){
		$links = '[<a href="'.PHP_SELF2.'?'.time().'">'._T('return').'</a>]';
		$PMS->useModuleMethods('LinksAboveBar', array(&$links,'reply',$resno)); // "LinksAboveBar" Hook Point
		$pte_vals['{$FORMTOP}'] = $links.'<div class="bar_reply">'._T('form_top').'</div>';
	}
	if(USE_FLOATFORM && !$resno && $iscollapse) $pte_vals['{$FORMTOP}'] .= "\n".'[<span id="show" class="hide">'._T('form_showpostform').'</span><span id="hide" class="show">'._T('form_hidepostform').'</span>]';
	$pte_vals += array('{$MAX_FILE_SIZE}' => MAX_KB * 1024,
		'{$RESTO}' => $resno ? '<input type="hidden" name="resto" value="'.$resno.'" />' : '',
		'{$BOARD}' => $board,
		'{$FORM_NAME_TEXT}' => _T('form_name'),
		'{$FORM_NAME_FIELD}' => '<input class="hide" value="DO NOT FIX THIS" type="text" name="sub" /><input class="hide" type="text" name="name" value="spammer" /><input maxlength="'.INPUT_MAX.'" type="text" name="'.FT_NAME.'" id="fname" size="28" value="'.$name.'" />',
		'{$FORM_EMAIL_TEXT}' => _T('form_email'),
		'{$FORM_EMAIL_FIELD}' => '<input type="checkbox" name="'.FT_EMAIL.'" id="femail" value="sage" /><input type="text" class="hide" name="email" value="foo@foo.bar" />',
		'{$FORM_TOPIC_TEXT}' => _T('form_topic'),
		'{$FORM_TOPIC_FIELD}' => '<input maxlength="'.INPUT_MAX.'"  type="text" name="'.FT_SUBJECT.'" id="fsub" size="28" value="'.$sub.'" />',
		'{$FORM_SUBMIT}' => '<input type="submit" name="sendbtn" value="'._T('form_submit_btn').'" />',
		'{$FORM_COMMENT_TEXT}' => _T('form_comment'),
		'{$FORM_COMMENT_FIELD}' => '<textarea maxlength="'.COMM_MAX.'" name="'.FT_COMMENT.'" id="fcom" cols="48" rows="4" style="width: 400px; height: 80px;">'.$com.'</textarea><textarea name="com" class="hide" cols="48" rows="4">EID OG SMAPS</textarea>',
		'{$FORM_DELETE_PASSWORD_FIELD}' => '<input type="password" name="pwd" size="8" maxlength="8" value="" />',
		'{$FORM_DELETE_PASSWORD_TEXT}' => _T('form_delete_password'),
		'{$FORM_DELETE_PASSWORD_NOTICE}' => _T('form_delete_password_notice'),
		'{$FORM_EXTRA_COLUMN}' => '',
		'{$FORM_NOTICE}' => _T('form_notice',str_replace('|',', ',ALLOW_UPLOAD_EXT),MAX_KB,($resno ? MAX_RW : MAX_W),($resno ? MAX_RH : MAX_H)),
		'{$HOOKPOSTINFO}' => '',
		'{$ADDITION_INFO}' => $ADDITION_INFO,
		'{$FORM_NOTICE_NOSCRIPT}' => _T('form_notice_noscript'));
	$PMS->useModuleMethods('PostForm', array(&$pte_vals['{$FORM_EXTRA_COLUMN}'])); // "PostForm" Hook Point
	if(!$isedit && (RESIMG || !$resno)){
		$pte_vals += array('{$FORM_ATTECHMENT_TEXT}' => _T('form_attechment'),
			'{$FORM_ATTECHMENT_FIELD}' => '<input type="file" name="upfile" id="fupfile"/><input class="hide" type="checkbox" name="reply" value="yes" />',
			'{$FORM_NOATTECHMENT_TEXT}' => _T('form_noattechment'),
			'{$FORM_NOATTECHMENT_FIELD}' => '<input type="checkbox" name="noimg" id="noimg" value="on" />');
		if(USE_UPSERIES) { // 啟動連貼機能
			$pte_vals['{$FORM_CONTPOST_FIELD}'] = '<input type="checkbox" name="up_series" id="up_series" value="on"'.((isset($_GET["upseries"]) && $resno)?' checked="checked"':'').' />';
			$pte_vals['{$FORM_CONTPOST_TEXT}'] = _T('form_contpost');
		}
	}
	if(!$resno) { //not in reply mode
		$pte_vals += array(
			'{$MAIN_PAGE}' => '11'
		);
	}
	if(STORAGE_LIMIT) $pte_vals['{$FORM_NOTICE_STORAGE_LIMIT}'] = _T('form_notice_storage_limit',total_size(),STORAGE_MAX);
	$PMS->useModuleMethods('PostInfo', array(&$pte_vals['{$HOOKPOSTINFO}'])); // "PostInfo" Hook Point

	if(USE_FLOATFORM && !$resno && $iscollapse) $pte_vals['{$FORMBOTTOM}'] = '<script type="text/javascript">hideform();</script>';
	$dat .= $twig->renderBlock('POSTFORM', transformTemplateArray($pte_vals));
}

/* 輸出頁尾文字 */
function foot(&$dat){
	$PMS = PMCLibrary::getPMSInstance();
	$twig = Singleton::getTwig('page.twig');

	$pte_vals = array('{$FOOTER}'=>'<!-- GazouBBS v3.0 --><!-- ふたば改0.8 --><!-- Pixmicat! -->');
	$PMS->useModuleMethods('Foot', array(&$pte_vals['{$FOOTER}'])); // "Foot" Hook Point
	$pte_vals['{$FOOTER}'] .= '<small>- <a rel="nofollow noreferrer license" href="http://php.s3.to" target="_blank">GazouBBS</a> + <a rel="nofollow noreferrer license" href="http://www.2chan.net/" target="_blank">futaba</a> + <a rel="nofollow noreferrer license" href="https://github.com/pixmicat/pixmicat" target="_blank">Pixmicat!</a> -</small>';
	$dat .= $twig->renderBlock('FOOTER', transformTemplateArray($pte_vals));
}

/* 網址自動連結 */
function auto_link_callback($matches){
	return (strtolower($matches[3]) == "</a>") ? $matches[0] : preg_replace('/(https?|ftp|news)(:\/\/[\w\+\$\;\?\.\{\}%,!#~*\/:@&=_-]+)/u', '<a href="$1$2" target="_blank" rel="nofollow noreferrer">$1$2</a>', $matches[0]);
}
function auto_link($proto){
	$proto = preg_replace('|<br\s*/?>|',"\n",$proto);
	$proto = preg_replace_callback('/(>|^)([^<]+?)(<.*?>|$)/m','auto_link_callback',$proto);
	return str_replace("\n",'<br />',$proto);
}

/* 引用標註 */
function quoteLight($comment){
	return preg_replace('/(^|<br \/>)((?:&gt;|＞).*?)(?=<br \/>|$)/u', '$1<span class="resquote">$2</span>', $comment);
}

/* 取得完整的網址 */
function fullURL(){
	return '//'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], PHP_SELF));
}


/* 輸出錯誤畫面 */
function error($mes, $dest=''){
	$twig = Singleton::getTwig('page.twig');
	$board = Helper\current_board();
	$link = Helper\anchor($board, PHP_SELF2);

	if(is_file($dest)) unlink($dest);
	$pte_vals = array('{$SELF2}'=>$link.'?'.time(), '{$MESG}'=>$mes, '{$RETURN_TEXT}'=>_T('return'), '{$BACK_TEXT}'=>_T('error_back'));
	$dat = '';
	head($dat);
	$dat .= $twig->renderBlock('ERROR', transformTemplateArray($pte_vals));
	foot($dat);
	exit($dat);
}

/* 文字修整 */
function CleanStr($str, $IsAdmin=false){
	$str = trim($str); // 去除前後多餘空白
	if(get_magic_quotes_gpc()) $str = stripslashes($str); // "\"斜線符號去除
	// XML 1.1 Second Edition: 部分避免用字 (http://www.w3.org/TR/2006/REC-xml11-20060816/#charsets)
	$str = preg_replace('/([\x1-\x8\xB-\xC\xE-\x1F\x7F-\x84\x86-\x9F\x{FDD0}-\x{FDDF}])/u', '', htmlspecialchars($str));

	if($IsAdmin && CAP_ISHTML){ // 管理員開啟HTML
		$str = preg_replace('/&lt;(.*?)&gt;/', '<$1>', $str); // 如果有&lt;...&gt;則轉回<...>成為正常標籤
	}
	return $str;
}

/**
 * return a prefix of UTF-8 string, with character max length limited
 * 
 * @var string $str
 * @var integer $maxlen
 * @return string
 */
function str_prefix($str, $maxlen=20) {
	$suffix = "";
	if (mb_strlen($str, "UTF-8") > $maxlen) {
		$suffix = "...";
	}
	return mb_substr($str, 0, $maxlen, "UTF-8"). $suffix;
}

/* 封鎖 IP / Hostname / DNSBL 綜合性檢查 */
function BanIPHostDNSBLCheck($IP, $HOST, &$baninfo){
	if(!BAN_CHECK) return false; // Disabled
	global $BANPATTERN, $DNSBLservers, $DNSBLWHlist;

	// IP/Hostname Check
	$HOST = strtolower($HOST);
	$checkTwice = ($IP != $HOST); // 是否需檢查第二次
	$IsBanned = false;
	foreach($BANPATTERN as $pattern){
		$slash = substr_count($pattern, '/');
		if($slash==2){ // RegExp
			$pattern .= 'i';
		}elseif($slash==1){ // CIDR Notation
			if(matchCIDR($IP, $pattern)){ $IsBanned = true; break; }
			continue;
		}elseif(strpos($pattern, '*')!==false || strpos($pattern, '?')!==false){ // Wildcard
			$pattern = '/^'.str_replace(array('.', '*', '?'), array('\.', '.*', '.?'), $pattern).'$/i';
		}else{ // Full-text
			if($IP==$pattern || ($checkTwice && $HOST==strtolower($pattern))){ $IsBanned = true; break; }
			continue;
		}
		if(preg_match($pattern, $HOST) || ($checkTwice && preg_match($pattern, $IP))){ $IsBanned = true; break; }
	}
	if($IsBanned){ $baninfo = _T('ip_banned'); return true; }

	// DNS-based Blackhole List(DNSBL) 黑名單
	if(!$DNSBLservers[0]) return false; // Skip check
	if(array_search($IP, $DNSBLWHlist)!==false) return false; // IP位置在白名單內
	$rev = implode('.', array_reverse(explode('.', $IP)));
	$lastPoint = count($DNSBLservers) - 1; if($DNSBLservers[0] < $lastPoint) $lastPoint = $DNSBLservers[0];
	$isListed = false;
	for($i = 1; $i <= $lastPoint; $i++){
		$query = $rev.'.'.$DNSBLservers[$i].'.'; // FQDN
		$result = gethostbyname($query);
		if($result && ($result != $query)){ $isListed = $DNSBLservers[$i]; break; }
	}
	if($isListed){ $baninfo = _T('ip_dnsbl_banned',$isListed); return true; }
	return false;
}
function matchCIDR($addr, $cidr) {
	list($ip, $mask) = explode('/', $cidr);
	return (ip2long($addr) >> (32 - $mask) == ip2long($ip.str_repeat('.0', 3 - substr_count($ip, '.'))) >> (32 - $mask));
}

//refer https://stackoverflow.com/questions/7951061/matching-ipv6-address-to-a-cidr-subnet

// converts inet_pton output to string with bits
function inet_to_bits($inet) 
{
    $unpacked = unpack('A16', $inet);
    $unpacked = str_split($unpacked[1]);
    $binaryip = '';
    foreach ($unpacked as $char) {
        $binaryip .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
    }
    return $binaryip;
}

function matchCIDRv6($addr, $cidr) {
    list($net, $mask) = explode('/', $cidr);
    $ip = inet_pton($addr);
    $binaryip=inet_to_bits($ip);
    $net=inet_pton($net);
    $binarynet=inet_to_bits($net);
    $ip_net_bits=substr($binaryip,0,$mask);
    $net_bits   =substr($binarynet,0,$mask);

    if($ip_net_bits!==$net_bits) {
        return false;
    }
    return true;
}

/**
 * 針對輸入密碼驗證。
 * 
 * @param string $passwordInput 使用者輸入密碼
 * @return bool 是否通過驗證
 * @since 8th.Release
 */
function passwordVerify($passwordInput) {
    return (crypt($passwordInput, ADMIN_HASH) === ADMIN_HASH);
}

/* 後端登入權限管理 */
function adminAuthenticate($mode){
	@session_start();
	$loginkey = md5($_SERVER['HTTP_USER_AGENT'].ADMIN_HASH.$_SERVER['REMOTE_ADDR']);
	switch($mode){
		case 'logout':
			if(isset($_SESSION['pmcLogin'])) unset($_SESSION['pmcLogin']);
			return true; break;
		case 'login':
			$_SESSION['pmcLogin'] = $loginkey;
			break;
		case 'check':
			if(isset($_SESSION['pmcLogin']) && $_SESSION['pmcLogin']==$loginkey){
				session_regenerate_id(true); // 更換 Session id key 避免 Hijacking
				return true;
			}
			return false;
			break;
	}
}

function getREMOTE_ADDR(){
    $ipProxy = getRemoteAddrThroughProxy();
    if (!empty($ipProxy)) {
        return $ipProxy;
    }

    return $_SERVER['REMOTE_ADDR'];
}

/**
 * 取得 (Transparent) Proxy 提供之 IP 參數
 */
function getRemoteAddrThroughProxy() {
    global $PROXYHEADERlist;

    if (!defined('TRUST_HTTP_X_FORWARDED_FOR') || !TRUST_HTTP_X_FORWARDED_FOR) {
        return '';
    }
    $ip='';
	$proxy = $PROXYHEADERlist;
	
	foreach ($proxy as $key) {
		if (array_key_exists($key, $_SERVER)) {
			foreach (explode(',', $_SERVER[$key]) as $ip) {
				$ip = trim($ip);
				// 如果結果為 Private IP 或 Reserved IP，捨棄改用 REMOTE_ADDR
				if (filter_var($ip, FILTER_VALIDATE_IP) !==false) {
					return $ip;
				}
			}
		}
	}

    return '';
}


function strlenUnicode($str) {
    return mb_strlen($str, 'UTF-8');
}
