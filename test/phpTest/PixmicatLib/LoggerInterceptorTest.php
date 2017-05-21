<?php


class LoggerInterceptorTest extends PHPUnit_Framework_TestCase {
	public function testInstance() {
		$obj = new LoggerInterceptor(PMCLibrary::getLoggerInstance('Test'));
		$this->assertNotNull($obj);
		$this->assertInstanceOf('MethodInterceptor', $obj);
	}
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInstanceException() {
		if (class_exists('\ArgumentCountError')) {
			try{
				$obj = new LoggerInterceptor();
			} catch (\ArgumentCountError $e) {
				throw new \PHPUnit_Framework_Error(
					'error',
					0,
					$e->getFile(),
					$e->getLine()
				);
			}
		} 
		else {
			$obj = new LoggerInterceptor();
		}
	}
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInstanceException2() {
		if (class_exists('\TypeError')) {
			try{
				$obj = new LoggerInterceptor(NULL);
			} catch (\TypeError $e) {
				throw new \PHPUnit_Framework_Error(
					'error',
					0,
					$e->getFile(),
					$e->getLine()
				);
			}
		} 
		else {
			$obj = new LoggerInterceptor(NULL);
		}
	}
}
