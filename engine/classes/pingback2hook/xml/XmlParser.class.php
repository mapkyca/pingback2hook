<?php

/**
 * @file
 * 
 * XML Parser
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\xml {

    class XmlParser {
        
	/**
	 * Serialise an array into XML.
	 * @param array $array The array
	 * @return xml
	 */
	public static function serialise_array(array $array) { return array_to_xml($array); }

	/**
	 * Serialise an object.
	 * @param object $object The object to serialise
	 * @param string $root_tag Optional root tag name, if not specified the class name will be used.
	 * @return xml
	 */
	public static function serialise_object($object, $root_tag = '') { return object_to_xml($object, $root_tag); }

	/**
	 * Parse an XML file into an object.
	 * Based on code from http://de.php.net/manual/en/function.xml-parse-into-struct.php by
	 * efredricksen at gmail dot com
	 *
	 * @param string $xml The XML.
	 */
	public static function unserialise($xml)
	{
		$parser = xml_parser_create();

		// Parse $xml into a structure
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $xml, $tags);

		xml_parser_free($parser);

		$elements = array();
		$stack = array();

		foreach ($tags as $tag) {
			$index = count($elements);

			if ($tag['type'] == "complete" || $tag['type'] == "open") {
				$elements[$index] = new XmlElement;
				$elements[$index]->name = $tag['tag'];
				$elements[$index]->attributes = $tag['attributes'];
				$elements[$index]->content = $tag['value'];

				if ($tag['type'] == "open") {
					$elements[$index]->children = array();
					$stack[count($stack)] = &$elements;
					$elements = &$elements[$index]->children;
				}
			}

			if ($tag['type'] == "close") {
				$elements = &$stack[count($stack) - 1];
				unset($stack[count($stack) - 1]);
			}
		}

		return $elements[0];
	}

	protected function object_to_xml($data, $name = "", $n = 0)
	{
		$classname = ($name=="" ? get_class($data) : $name);

		$output = "";

		if (($n==0) || ( is_object($data) && !($data instanceof stdClass)))
		    $output = "<$classname>";

		foreach ($data as $key => $value) {
			$output .= "<$key type=\"".gettype($value)."\">";

			if (is_object($value)) {
				$output .= self::object_to_xml($value, $key, $n+1);
			} else if (is_array($value)) {
				$output .= self::array_to_xml($value, $n+1);
			} else if (gettype($value) == "boolean") {
				$output .= $value ? "true" : "false";
			} else {
				$output .= "<![CDATA[$value]]>";
			}

			$output .= "</$key>\n";
		}

		if (($n==0) || ( is_object($data) && !($data instanceof stdClass))) {
			$output .= "</$classname>\n";
		}

		return $output;
	}

	protected function array_to_xml(array $data, $n = 0)
	{
		$output = "";

		if ($n==0)
		    $output = "<array>\n";

		foreach ($data as $key => $value) {
			$item = "array_item";

			if (is_numeric($key)) {
				$output .= "<$item key=\"$key\" type=\"".gettype($value)."\">";
			} else {
				$item = $key;
				$output .= "<$item type=\"".gettype($value)."\">";
			}

			if (is_object($value)) {
				$output .= self::object_to_xml($value, "", $n+1);
			} else if (is_array($value)) {
				$output .= self::array_to_xml($value, $n+1);
			} else if (gettype($value) == "boolean") {
				$output .= $value ? "true" : "false";
			} else {
				$output .= "<![CDATA[$value]]>";
			}

			$output .= "</$item>\n";
		}

		if ($n==0) {
			$output = "</array>\n";
		}

		return $output;
	}
        
        
    }
    
}