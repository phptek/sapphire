<?php
/**
 * Represents a variable-length string of up to 2 megabytes, designed to store 
 * raw text in JSON format.
 * 
 * Example definition via {@link DataObject::$db}:
 * <code>
 * static $db = array(
 * 	"MyField" => "JSON",
 * );
 * </code>
 * 
 * @see Text
 * @package framework
 * @subpackage model
 */
class JSON extends Text {

	/**
	 * 
	 * Retrieve the value of the named property in $prop in the form of an array.
	 * This behaviour can be altered for the result of single occurring properties
	 * so that the string/numeric value is returned instead. See the $returnAsArray parameter.
	 * 
	 * @param string $prop
	 * @param boolean $returnAsArray
	 * @return mixed (array | string | number)
	 */
	public function getProperty($prop, $returnAsArray = true) {
		$values = $this->decode();
		$retArr = array();
		array_walk_recursive($values, function($v, $k) use(
				$prop, &$retArr
				) {
			if($k === $prop) {
				$retArr[] = $k;
			}
		});
		
		if(count($retArr[]) === 0) {
			return '';
		}
		else if(count($retArr[]) === 1 && $returnAsArray === true) {
			return $retArr;
		}
		else if(count($retArr[]) === 1 && $returnAsArray === false) {
			return $retArr[0];
		}
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function isValid() {
		$value = $this->decode();
		return (is_bool($value) || is_null($value) ? false : true);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function isEmpty() {
		return trim($this->getValue() === '{}' ? true : false);
	}	
	
	/**
	 * 
	 * @param string $string
	 * @return mixed (@see json_decode())
	 * @todo which PHP version did short ternary's get introduced?
	 */
	public function decode($string = '') {
		return $string ?: $this->getValue();
	}
	
}
