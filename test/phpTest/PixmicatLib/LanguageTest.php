<?php


class LanguageTest extends PHPUnit_Framework_TestCase {
	public $Lang;
	public function setUp() {
		$this->Lang = PMCLibrary::getLanguageInstance();
	}
	public function testGetInstance() {
		$expect = $this->Lang;
		$result = PMCLibrary::getLanguageInstance();
		$this->assertSame($expect, $result);
	}
	public function testGetLocale() {
		$expect = 'zh_TW';
		$result = $this->Lang->getLocale();
		$this->assertSame($expect, $result);
	}
	public function testGetLanguage() {
		$expect = 189;
		$result = count($this->Lang->getLanguage());
		$this->assertSame($expect, $result);
	}
	public function testGetTranslation() {
		$expect = '[Notice] Your sending was canceled because of the incorrect file size.';
		$result = $this->Lang->getTranslation('regist_upload_killincomp');
		$this->assertEquals($expect, $result);
	}
	public function testGetTranslationWithArgs() {
		$expect = '�Q�C�b DNSBL(127.0.0.1) ����W�椧��';
		$result = $this->Lang->getTranslation('ip_dnsbl_banned', '127.0.0.1');
		$this->assertEquals($expect, $result);
	}
	public function testGetTranslationNoArg() {
		$expect = '';
		$result = $this->Lang->getTranslation();
		$this->assertEquals($expect, $result);
	}
	public function testGetTranslationIndexNotExists() {
		$expect = 'WTF_IS_THIS';
		$result = $this->Lang->getTranslation('WTF_IS_THIS');
		$this->assertEquals($expect, $result);
	}
	public function test_T() {
		$expect = '��ƪ�̨Τ�';
		$result = _T('admin_optimize');
		$this->assertEquals($expect, $result);
	}
	public function test_TWithArgs() {
		$expect = '�i ���[���ɨϥήe�q�`�p : <b>51200</b> KB �j';
		$result = _T('admin_totalsize', '51200');
		$this->assertEquals($expect, $result);
	}
	public function test_TNoArg() {
		$expect = '';
		$result = _T();
		$this->assertEquals($expect, $result);
	}
	public function test_TIndexNotExists() {
		$expect = 'WTF_IS_THIS_ANYWAY';
		$result = _T('WTF_IS_THIS_ANYWAY');
		$this->assertEquals($expect, $result);
	}
	public function testAttachLanguageOldway() {
		AttachLanguage(function(){
			global $language;
			$language['testIndex'] = 'testValue';
		});
		$expect = 'testValue';
		$result = _T('testIndex');
		$this->assertEquals($expect, $result);
	}
	public function testAttachLanguageNewway() {
		$langArray = array();
		$langArray['testIndex2'] = 'testValue2';
		PMCLibrary::getLanguageInstance()->attachLanguage($langArray);
		$expect = 'testValue2';
		$result = _T('testIndex2');
		$this->assertEquals($expect, $result);
	}
}