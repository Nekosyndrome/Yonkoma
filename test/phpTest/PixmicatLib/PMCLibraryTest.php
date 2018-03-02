<?php

use Yonkoma\Singleton;
class PMCLibraryTest extends PHPUnit_Framework_TestCase {
	public function testGetPIOInstance() {
		$PIO1 = PMCLibrary::getPIOInstance();
		$this->assertNotNull($PIO1);
		$PIO2 = PMCLibrary::getPIOInstance();
		$this->assertSame($PIO1, $PIO2);
	}
	public function testGetTwig() {
		$twig1 = Singleton::getTwig();
		$this->assertNotNull($twig1);
		$twig2 = Singleton::getTwig();
		$this->assertSame($twig1, $twig2);
	}
	public function testGetPMSInstance() {
		$PMS1 = PMCLibrary::getPMSInstance();
		$this->assertNotNull($PMS1);
		$PMS2 = PMCLibrary::getPMSInstance();
		$this->assertSame($PMS1, $PMS2);
	}
	public function testGetFileIOInstance() {
		$FileIO1 = PMCLibrary::getFileIOInstance();
		$this->assertNotNull($FileIO1);
		$FileIO2 = PMCLibrary::getFileIOInstance();
		$this->assertSame($FileIO1, $FileIO2);
	}
	public function testGetLoggerInstance() {
		$Logger1 = PMCLibrary::getLoggerInstance(__CLASS__);
		$this->assertNotNull($Logger1);
		$Logger2 = PMCLibrary::getLoggerInstance(__CLASS__);
		$this->assertSame($Logger1, $Logger2);
	}
	public function testGetLanguageInstance() {
		$Language1 = PMCLibrary::getLanguageInstance();
		$this->assertNotNull($Language1);
		$Language2 = PMCLibrary::getLanguageInstance();
		$this->assertSame($Language1, $Language2);
	}
}