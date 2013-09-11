<?php

/**
 * Tests to cover the {@link Session} class
 *
 * @package framework
 * @subpackage tests
 */

class SessionTest extends SapphireTest {
	
	public function testGetSetBasics() {
		Session::set('Test', 'Test');
		
		$this->assertEquals(Session::get('Test'), 'Test');
	}
	
	public function testClearElement() {
		Session::set('Test', 'Test');
		Session::clear('Test');
		
		$this->assertEquals(Session::get('Test'), '');
	}
	
	public function testClearAllElements() {
		Session::set('Test', 'Test');
		Session::set('Test-1', 'Test-1');
		
		Session::clear_all();
		
		// should session get return null? The array key should probably be
		// unset from the data array
		$this->assertEquals(Session::get('Test'), '');
		$this->assertEquals(Session::get('Test-1'), '');
	}
	
	public function testGetAllElements() {
		Session::clear_all(); // Remove all session that might've been set by the test harness
		
		Session::set('Test', 'Test');
		Session::set('Test-2', 'Test-2');
		
		$session = Session::get_all();
		unset($session['HTTP_USER_AGENT']);

		$this->assertEquals($session, array('Test' => 'Test', 'Test-2' => 'Test-2'));
	}

	public function testSettingExistingDoesntClear() {
		$s = new Session(array('something' => array('does' => 'exist')));

		$s->inst_set('something.does', 'exist');
		$result = $s->inst_changedData();
		unset($result['HTTP_USER_AGENT']);
		$this->assertEquals(array(), $result);
	}

	/**
	 * Check that changedData isn't populated with junk when clearing non-existent entries.
	 */
	public function testClearElementThatDoesntExist() {
		$s = new Session(array('something' => array('does' => 'exist')));

		$s->inst_clear('something.doesnt.exist');
		$result = $s->inst_changedData();
		unset($result['HTTP_USER_AGENT']);
		$this->assertEquals(array(), $result);

		$s->inst_set('something-else', 'val');
		$s->inst_clear('something-new');
		$result = $s->inst_changedData();
		unset($result['HTTP_USER_AGENT']);
		$this->assertEquals(array('something-else' => 'val'), $result);
	}

	/**
	 * Check that changedData is populated with clearing data.
	 */
	public function testClearElementThatDoesExist() {
		$s = new Session(array('something' => array('does' => 'exist')));

		$s->inst_clear('something.does');
		$result = $s->inst_changedData();
		unset($result['HTTP_USER_AGENT']);
		$this->assertEquals(array('something' => array('does' => null)), $result);
	}

	public function testNonStandardPath(){
		Config::inst()->update('Session', 'store_path', (realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session')));
		Session::start();

		$this->assertEquals(Config::inst()->get('Session', 'store_path'), '');
	}

	public function testUserAgentLockout() {
		// Set a user agent
		$_SERVER['HTTP_USER_AGENT'] = 'Test Agent';

		// Generate our session
		$s = new Session(array());
		$s->inst_set('val', 123);
		$s->inst_finalize();

		// Change our UA
		$_SERVER['HTTP_USER_AGENT'] = 'Fake Agent';
		
		// Verify the new session reset our values
		$s2 = new Session($s);
		$this->assertNotEquals($s2->inst_get('val'), 123);
	}
	
	/*
	 * Tests that different string values persist across multiple "page requests"
	 */
	public function testStringValuePersistsAcrossRequests() {
		// Visit a random controller URL
		$r1 = Director::test('dev/tasks?test=1');
		// Test a string _with_ value
		Session::set('something', 'exists');
		// Visit another
		$r2 = Director::test('dev/tasks?test=2');
		$this->assertEquals('exists', Session::get('something'), 'Assert that a non-empty string as a session-value, perists across requests.');
		
		// Visit a random controller URL
		$r1 = Director::test('dev/tasks?test=3');
		// Test a string _without_ value
		Session::set('something', '');
		// Visit another
		$r2 = Director::test('dev/tasks?test=4');
		$this->assertEquals('', Session::get('something'), 'Assert that an empty string as a session-value, perists across requests.');
	}
	
	/*
	 * Tests that different array values persist across multiple "page requests"
	 */
	public function testArrayValuePersistsAcrossRequests() {
		// Visit a random controller URL
		$r1 = Director::test('dev/tasks?test=1');
		// Test a string _with_ value
		Session::set('something', array('exists'));
		// Visit another
		$r2 = Director::test('dev/tasks?test=2');
		$this->assertEquals(array('exists'), Session::get('something'), 'Assert that a non-empty array as a session-value, perists across requests.');
		
		// Visit a random controller URL
		$r1 = Director::test('dev/tasks?test=3');
		// Test a string _without_ value
		Session::set('something', array());
		// Visit another
		$r2 = Director::test('dev/tasks?test=4');
		$this->assertEquals(array(), Session::get('something'), 'Assert that an empty array as a session-value, perists across requests.');
	}
}
