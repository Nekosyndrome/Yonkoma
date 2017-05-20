<?php
//modified from mod_code_prettify
class mod_ascii_art extends ModuleHelper {
	// 是否硬轉換 (直接儲存轉換後的結果，減少系統日後輸出時的多次軟轉換負擔)
	// 優點是只需轉換一次，以後不再需要轉換
	private $HARD_TRANSLATE = false;
	
	//TODO: 這邊要消掉，因為沒有舊的mod_aa這東西
	// 硬轉換後是否相容舊 mod_code 繼續軟轉換板上已有之 [aa] (因硬轉換啟動後預設不再軟轉換處理)
	// 簡單說就是硬/軟轉換並行，這樣硬轉換的好處沒有辦法完全表現出來
	private $MOD_AA_COMPATIBLE = true;
	
	public function __construct($PMS) {
		parent::__construct($PMS);
		if(method_exists($PMS,'addCHP')) {
			$PMS->addCHP('mod_bbbutton_addButtons',array($this,'addButtons'));
		}
	}

	public function addButtons($txt) {
		$txt .= 'bbbuttons.tags = $.extend({
			 aa:{desc:"Insert AA block"},
			},bbbuttons.tags);';
	}

	public function getModuleName(){
		return 'mod_ascii_art : AA prettify';
	}

	public function getModuleVersionInfo(){
		return '1th.Release (v170514)';
	}

	public function autoHookHead(&$dat){
		$dat .= '<link rel="stylesheet" type="text/css" href="module/ascii_art/ascii_art.css" />'."\n";
	}

	public function autoHookPostInfo(&$postinfo){
		$postinfo .= '<li>AA可使用 [aa][/aa] 防止變形</li>'."\n";
	}

	// 發文儲存前即進行轉換 (硬轉換)
	public function autoHookRegistBeforeCommit(&$name, &$email, &$sub, &$com, &$category, &$age, $dest, $isReply, $imgWH, &$status){
		if(!$this->HARD_TRANSLATE) return; // 選擇不作硬轉換
		if(strpos($com, '[/aa]') !== false){
			$com = preg_replace('/\[aa(?:=(\S*?))?](?:<br \/>)?(.*?)(?:<br \/>)?\[\/aa\]/us',
				'<pre class="aa">$2</pre>', $com);
		}
	}

	// 輸出時才即時轉換 (軟轉換)
	public function autoHookThreadPost(&$arrLabels, $post, $isReply){
		if($this->HARD_TRANSLATE && !$this->MOD_AA_COMPATIBLE) return; // 不需再做軟轉換 (除非啟動相容模式)
		if(strpos($arrLabels['{$COM}'], '[/aa]')===false) return;

		$arrLabels['{$COM}'] = preg_replace('/\[aa(?:=(\S*?))?](?:<br \/>)?(.*?)(?:<br \/>)?\[\/aa\]/us',
			'<pre class="aa">$2</pre>', $arrLabels['{$COM}']);
	}

	public function autoHookThreadReply(&$arrLabels, $post, $isReply){
		$this->autoHookThreadPost($arrLabels, $post, $isReply);
	}

	public function autoHookFoot(&$dat){
		//don't know what this is
	}
}//End-Of-Module


