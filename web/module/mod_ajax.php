<?php
use Yonkoma\Singleton;
class mod_ajax extends ModuleHelper{

	protected $MAX_POSTS = 114514;

	function __construct($PMS){
		parent::__construct($PMS);
	}

	/* Get the name of module */
	function getModuleName(){
		return 'mod_ajax';
	}

	/* Get the module version infomation */
	function getModuleVersionInfo(){
		return '8th';
	}

	function autoHookToplink(&$linkbar, $isReply){
		
	}

	function autoHookThreadFront(&$txt,$isReply){
		
	}

	function output_error($err)
	{
		$out = array(
			'error' => 1,
			'msg' => $err
		);
		echo json_encode($out);
		exit(0);
	}

	function output_json(&$ar)
	{
		echo json_encode($ar);
		exit(0);
	}

	function string_greater($a, $b)
	{
		$a = ltrim($a, '0');
		$b = ltrim($b, '0');

		$la = strlen($a);
		$lb = strlen($b);
		if($la != $lb) return $la > $lb;

		//same length: return lexicographical order
		return $a > $b;
	}

	function filterPost(&$post)
	{
		$FileIO=PMCLibrary::getFileIOInstance();
		$exp = explode(' ', $post['now']);

		//no id
		if(!isset($exp[1]))
		{
			$exp[1] = 'ID:';
		}

		$tmp = array(
			'no' => intval($post['no']),
			'resto' => intval($post['resto']),
			'sub' => $post['sub'],
			'com' => $post['com'],
			'now' => $exp[0],
			'id' => substr($exp[1], 3),
			'time' => $post['tim'],
			'name' => $post['name'],
			'mail' => $post['email']	
		);
		if($post['ext'] != '')
		{
			$tmp['ext'] = $post['ext'];
			$tmp['w'] = intval($post['imgw']);
			$tmp['h'] = intval($post['imgh']);
			$tmp['image'] = $FileIO->getImageURL($post['tim'].$post['ext']);
			$tmp['thumb'] = $FileIO->getImageURL($post['tim'].'s.jpg');
		}

		return $tmp;
	}

	function outputAllThreads()
	{
		$PIO = PMCLibrary::getPIOInstance();

		$re = array(
			'threads' => array()
		);
		$threads = $PIO->fetchThreadList();
		foreach($threads as $opno)
		{
			$plist = $PIO->fetchPostList($opno);
			$op = $PIO->fetchPosts(array($plist[0]))[0];
			$ed = $PIO->fetchPosts(array( $plist[count($plist)-1] ))[0];

			$dat = array(
				'no' => intval($opno),
				'replies' => $PIO->postCount($opno)-1,
				'created' => $op['tim'],
				'last_modified' => $ed['tim']
			);
			array_push($re['threads'], $dat);

			unset($dat);
			unset($plist);
			unset($op);
			unset($ed);
		}
		$this->output_json($re);
	}

	function dumpPosts($mx, $after)
	{
		$PIO = PMCLibrary::getPIOInstance();

		$re = array(
			'posts' => array()
		);

		$posts = $PIO->fetchPosts( $PIO->fetchPostList(0,0,$mx) );
		foreach($posts as $post)
		{
			if( !$this->string_greater($post['tim'], $after) ) continue;
			array_push($re['posts'], $this->filterPost($post));
		}

		$this->output_json($re);
	}

	function _dumpHtml($post, $tree, $isop)
	{
		$PIO = PMCLibrary::getPIOInstance();
		$FileIO = PMCLibrary::getFileIOInstance();
		$PMS = PMCLibrary::getPMSInstance();
		$twig = Singleton::getTwig('page.twig');
		$tree = array_flip($tree);
		extract($post);

		// 設定欄位值
		$name = str_replace('&'._T('trip_pre'), '&amp;'._T('trip_pre'), $name); // 避免 &#xxxx; 後面被視為 Trip 留下 & 造成解析錯誤
		if(CLEAR_SAGE) $email = preg_replace('/^sage( *)/i', '', trim($email)); // 清除E-mail中的「sage」關鍵字
		if(ALLOW_NONAME==2)
			$name = preg_match('/(\\'._T('trip_pre').'.{10})/', $name, $matches) ? '<span class="nor">'.$matches[1].'</span>' : '';
		else
			$name = preg_replace('/(\\'._T('trip_pre').'.{10})/', '<span class="nor">$1</span>', $name); // Trip取消粗體

		//com setting
		if(AUTO_LINK) $com = auto_link($com);
		$com = quoteLight($com);
		if( USE_QUOTESYSTEM )
		{
			if(preg_match_all('/((?:&gt;|＞)+)(?:No\.)?(\d+)/i', $com, $matches, PREG_SET_ORDER)){ // 找尋>>No.xxx
				$matches_unique = array();
				foreach($matches as $val){ if(!in_array($val, $matches_unique)) array_push($matches_unique, $val); }
				foreach($matches_unique as $val) if( isset($tree[$val[2]]) ) 
					$com = str_replace($val[0], '<a class="qlink" href="#r'.$val[2].'">'.$val[0].'</a>', $com);
			}
		}

		// 設定附加圖檔顯示
		if($ext && $FileIO->imageExists($tim.$ext)){
			$imageURL = $FileIO->getImageURL($tim.$ext); // image URL
			$thumbName = $FileIO->resolveThumbName($tim); // thumb Name

			$imgsrc = '<a class="file-thumb" href="'.$imageURL.'" target="_blank" rel="nofollow"><img src="nothumb.gif" class="img" alt="'.$imgsize.'" title="'.$imgsize.'" /></a>'; // 預設顯示圖樣式 (無預覽圖時)
			if($tw && $th){
				if($thumbName != false){ // 有預覽圖
					$thumbURL = $FileIO->getImageURL($thumbName); // thumb URL
					$img_thumb = '<small>'._T('img_sample').'</small>';
					$imgsrc = '<a class="file-thumb" href="'.$imageURL.'" target="_blank" rel="nofollow"><img src="'.$thumbURL.'" style="width: '.$tw.'px; height: '.$th.'px;" class="img" alt="'.$imgsize.'" title="'.$imgsize.'" /></a>';
				}elseif($ext=='.swf') $imgsrc = ''; // swf檔案不需預覽圖
			}
			if(SHOW_IMGWH) $imgwh_bar = ', '.$imgw.'x'.$imgh; // 顯示附加圖檔之原檔長寬尺寸
			$IMG_BAR = _T('img_filename').'<a href="'.$imageURL.'" target="_blank" rel="nofollow">'.$tim.$ext.'</a>-('.$imgsize.$imgwh_bar.')';
		}

		$QUOTEBTN = 'No.'. $no;

		if(!$isop) //回應
		{ 
			$arrLabels = array(
				'{$NO}'=>$no,
				'{$SUB}'=>$sub,
				'{$NAME}'=>$name,
				'{$NOW}'=>$now,
				'{$QUOTEBTN}'=>$QUOTEBTN,
				'{$IMG_BAR}'=>isset($IMG_BAR) ? $IMG_BAR : '',
				'{$IMG_SRC}'=>isset($imgsrc) ? $imgsrc : '',
				'{$WARN_BEKILL}'=>isset($WARN_BEKILL) ? $WARN_BEKILL : '',
				'{$NAME_TEXT}'=>_T('post_name'),
				'{$SELF}'=>PHP_SELF,
				'{$COM}'=>$com
			);
			if(isset($resno) && $resno) $arrLabels['{$RESTO}']=$resno;
			$PMS->useModuleMethods('ThreadReply', array(&$arrLabels, $post, 1)); // "ThreadReply" Hook Point
			return $twig->renderBlock('REPLY', transformTemplateArray($arrLabels));
		}
		else // 首篇
		{
			$arrLabels = array(
				'{$NO}'=>$no,
				'{$SUB}'=>$sub,
				'{$NAME}'=>$name,
				'{$NOW}'=>$now,
				'{$QUOTEBTN}'=>$QUOTEBTN,
				'{$REPLYBTN}'=>isset($REPLYBTN) ? $REPLYBTN : '',
				'{$IMG_BAR}'=>isset($IMG_BAR) ? $IMG_BAR : '',
				'{$IMG_SRC}'=>isset($imgsrc) ? $imgsrc : '',
				'{$WARN_OLD}'=>isset($WARN_OLD) ? $WARN_OLD : '',
				'{$WARN_BEKILL}'=>isset($WARN_BEKILL) ? $WARN_BEKILL : '',
				'{$WARN_ENDREPLY}'=>isset($WARN_ENDREPLY) ? $WARN_ENDREPLY : '',
				'{$WARN_HIDEPOST}'=>isset($WARN_HIDEPOST) ? $WARN_HIDEPOST : '',
				'{$NAME_TEXT}'=>_T('post_name'),
				'{$SELF}'=>PHP_SELF, '{$COM}'=>$com
			);
			if(isset($resno) && $resno) $arrLabels['{$RESTO}']=$resno;
			$PMS->useModuleMethods('ThreadPost', array(&$arrLabels, $post, 0)); // "ThreadPost" Hook Point
			return $twig->renderBlock('THREAD', transformTemplateArray($arrLabels));
		}
	}

	function dumpThread($op, $html = false)
	{
		$PIO=PMCLibrary::getPIOInstance();
		$op = intval($op);
		if( !$PIO->isThread($op) ) $this->output_error('thread doesnt exist');

		$tree = $PIO->fetchPostList($op);

		$re = array(
			'posts' => array()
		);

		$cnt = 0;
		$posts = $PIO->fetchPosts( $tree );
		foreach($posts as $post)
		{
			$ar = $this->filterPost($post);
			if($html) $ar['html'] = $this->_dumpHtml($post, $tree, $cnt==0);
			$cnt++;
			array_push($re['posts'], $ar);
		}
		$this->output_json($re);
	}

	/* 模組獨立頁面 */
	function ModulePage(){
		$valid_action = array('threads', 'posts', 'thread');
		if( isset($_GET['action']) ) $action = $_GET['action'];
		if( !in_array($action, $valid_action) ) $this->output_error('invalid action');

		$html = false;
		if( isset($_GET['html']) && strtolower($_GET['html'])=='true' ) 
			$html = true;

		if($action == 'threads')
		{
			$this->outputAllThreads();
		}
		else if($action=='posts')
		{
			$limit = $this->MAX_POSTS;
			$after = 0;
			
			if( isset($_GET['limit']) )
			{
				$x = intval( $_GET['limit'] );
				if($x) $limit = $x;
			}
			
			if( isset($_GET['after']) )
			{
				$tmp = $_GET['after'];
				if( ctype_digit($tmp) ) //every character is a digit
				{
					$after = $tmp;
					$after = ltrim($after, '0');
					if($after == '') $after = '0';
				}
			}

			$this->dumpPosts($limit, $after);
		}
		else if($action=='thread')
		{
			if( isset($_GET['op']) && intval($_GET['op']) )
			{
				$this->dumpThread( intval($_GET['op']), $html );
			}
			else
			{
				$this->output_error('invalid op number');
			}
		}

	}
}
?>
