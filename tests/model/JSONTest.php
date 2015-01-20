<?php
/**
 * @package framework
 * @subpackage tests
 */
class JSONTest extends SapphireTest {

	public function testGetProperty() {
		$jsonStr = "{
			simple_string: 'this is a simple string',
			simple_string_2: 'this is the 2nd simple string',
			array_single: ['gruffalo'],
			array_multiple: ['gruffalo', 'thomas', 'chuggington'],
			simple_string_level2: {
				simple_string_2: 'this is the 3rd simple string'
			}
		}";
		
		$json = new JSON();
		$json->setValue($jsonStr);		

		// Tests a simple string, with no other property occurance of the same nam
		$prop = $json->getProperty('simple_string');
		$this->assertInternalType('string', $prop);
		$this->assertEquals('this is a simple string', $prop);
		
		// Tests a simple string, with another property occurance of the same name
		$prop = $json->getProperty('simple_string_2');
		$this->assertInternalType('array', $prop);
		$this->assertCount(2, count($prop));
		
		// Tests a simple, single item array, with no other property occurance of the same name
		$prop = $json->getProperty('array_single');
		$this->assertInternalType('array', $prop);
		$this->assertCount(1, count($prop));
		$this->assertEquals('gruffalo', $prop[0]);
		
		// Tests a simple, multi item array, with no other property occurance of the same name
		$prop = $json->getProperty('array_multiple');
		$this->assertInternalType('array', $prop);
		$this->assertCount(3, count($prop));
		$this->assertContains('gruffalo', $prop);
		$this->assertContains('thomas', $prop);
		$this->assertContains('chuggington', $prop);
	}
		
}
