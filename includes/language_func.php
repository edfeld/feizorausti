<?php
//include the DOMTranslator class and create a new instance
include("scripts/domtranslator.php");
$translator = new DOMTranslator();

//retrieve translations from database
$query_translations = $conCreative->prepare("SELECT * FROM translations");
$query_translations->execute();

//add translations from database to arrays
$counter = 0;
while ($row = $query_translations->fetch(PDO::FETCH_ASSOC)) {
	//add translation to the DOMTranslator object's dictionary
	$translator->addTranslation($row['translate'], $row['translation']);
}

function translate_text($text_content) {
	global $translator;
	return $translator->translate($html_content, TRUE, TRUE);
}

function translate_dom($html_content) {
	global $translator;
	return $translator->translate($html_content);
}
?>
