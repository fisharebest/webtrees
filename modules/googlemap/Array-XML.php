<?php
function generate_xml_from_array($array, $node_name) {
	$xml = '';

	if (is_array($array) || is_object($array)) {
		foreach ($array as $key=>$value) {
			if (is_numeric($key)) {
				$key = '&lt;marker ';
			}
			if ($key=='&lt;marker ') {
				$xml .= $key.generate_xml_from_array($value, $node_name);
			} else {
				$xml .= $key.'="'.generate_xml_from_array($value, $node_name).' "';
			}
			if ($key=='&lt;marker ') {
				$xml .= '/>' . "\n";
			}
		}
	} else {
		$xml = htmlspecialchars($array, ENT_QUOTES);
	}

	return $xml ;
}


function generate_valid_xml_from_array($array, $node_block, $node_name) {
	$xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

	$xml .= '&lt;markers&gt;' . "\n";
	$xml .= generate_xml_from_array($array, "<".$node_name);
	$xml .= '&lt;/' . $node_block . '>' . "\n";

	return $xml;
}
?>