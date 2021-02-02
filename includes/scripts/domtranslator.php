<?php
/**
 * translate words and phrases while keeping the DOM structure intact
 *
 * @package DOM Translator
 * @version 1.11
 * @author Luke Hollenback <luke@mynamewasluke.com>
 */
class DOMTranslator {
	private $_translate = array();
	private $_translation = array();

	/**
	 * add a translation to the object
	 *
	 * @param string $translate the string that will be translated
	 * @param string $translation what the string will be translated to
	 */
	public function addTranslation($translate, $translation) {
		$this->_translate[] = $translate;
		$this->_translation[count($this->_translate) - 1] = $translation;
	}

	/**
	 * traverse the object's translation dictionary and replace the appropriate words and phrases in the supplied HTML content
	 *
	 * @param string $inputContent the string of HTML and/or text that is to be translated
	 * @param bool $wholePhrase (default: TRUE) when turned on, the whole phrase will be searched for in the dictionary before single words are
	 * @param bool $asText (default: FALSE) when turned on, the DOM structure will not be traversed and the $inputContent will be searched and translated as a normal string
	 * @param mixed[] $excludedElements (default: NULL) an array of elements who's value's should be excluded from translation
	 * @param string $autoRemove (default: '/(<!).+(>)|(<html>)|(<\/html>)|(<body>)|(<\/body>)|(<p>)|(<\/p>)/i') a REGEX string that will be automatically replaced with nothing ("") after translation
	 *
	 * @return string the translated HTML DOM content
	 */
	public function translate($inputContent, $wholePhrase = TRUE, $asText = FALSE, $excludedElements = array(), $autoRemove = '/(<!).+(>)|(<html>)|(<\/html>)|(<body>)|(<\/body>)|(<p>)|(<\/p>)|(\\n)/i') {
		//declare variables that make it easier to read the object's translation dictionary
		$translate = $this->_translate;
		$translation = $this->_translation;

		if (!$inputContent == null && !$inputContent == "") {
			$ret = $inputContent;

			if ($asText) {
				for ($i = 0; $i < count($translate); $i++) {
					//set up some basic variables to make translation easier
					$pattern = '/' . $translate[$i] . '/i';
					$replacement = $translation[$i];
					$isWholeString = 0;

					//check to see if there is a translation phrase that matches the entire content of what is being translated
					if ($wholePhrase) {
						for ($y = 0; $y < count($translate); $y++) {
							//if there is, translate the whole phrase at once
							if ($inputContent == $translate[$y]) {
								$isWholeString = 1;
								$ret = $translation[$y];
							}

							//if the string has already been translated, don't translate parts
							if ($inputContent == $translation[$y])
								$isWholeString = 1;
						}
					}

					//translate any occurences of the particular phrase if the whole phrase was not in the dictionary
					if ($isWholeString == 0)
						$ret = preg_replace($pattern, $replacement, $ret);
				}
			}
			else {
				//set up a new DOMDocument for traversing
				$dom = new DOMDocument;
				$htmlContent = mb_convert_encoding($inputContent, 'HTML-ENTITIES', 'UTF-8');
				@$dom->loadHTML($htmlContent);
				$domStart = $dom->documentElement;

				for ($i = 0; $i < count($translate); $i++) {
					//set up some basic variables to make translation easier
					$pattern = '/' . $translate[$i] . '/i';
					$replacement = $translation[$i];

					//translate any occurences of the particular phrase
					$this->preg_replace_dom($pattern, $replacement, $domStart, $dom, $wholePhrase, $excludedElements);
				}

				//convert the DOMDocument back into plaintext
				$ret = $dom->saveHTML();
			}

			//remove anything specified by the $autoRemove REGEX statement
			$ret = preg_replace($autoRemove, '', $ret);
		}
		else {
			$ret = "";
		}

		//return the translated content
		return $ret;
	}

	/**
	 * traverse a DOMDocument object and replace words and phrases appropriately
	 *
	 * @param string $pattern the REGEX pattern to search for in the values of the DOM elements
	 * @param string $replacement the string to replace occurences of $pattern with
	 * @param DOMNode $domStart reference to the node to start the DOM traversal inside of
	 * @param DOMDocument $dom reference to the DOMDocument to save the final DOM structure into
	 * @param bool $wholePhrase when turned on, the whole phrase will be searched for in the dictionary before single words are
	 * @param mixed[] $excludedElements an array of elements who's value's should be excluded from translation
	 * @param string $autoRemove a REGEX string that will be automatically replaced with nothing ("") after translation
	 */
	private function preg_replace_dom($pattern, $replacement, DOMNode $domStart, DOMDocument $dom, $wholePhrase, $excludedElements) {
		//declare variables that make it easier to read the object's translation dictionary
		$translate = $this->_translate;
		$translation = $this->_translation;

		//begin crawling through the nodes in the DOMDocument starting with $domStart
		if (!empty($domStart->childNodes)) {
			foreach ($domStart->childNodes as $node) {
				//search the node only if the node is not in the array of elements to be excluded from search and translation
				if (empty($excludedElements) || !in_array($node->nodeName, $excludedElements)) {
					//recursively search the node's child nodes
					if (!empty($node->childNodes)) {
						$this->preg_replace_dom($pattern, $replacement, $node, $dom, $wholePhrase, $excludedElements);
					}

					//if the node is simply text and does not contain any more child nodes, then search it and translate it
					if ($node->nodeName == "#text") {
						$textValue = htmlspecialchars_decode($node->nodeValue);
						$isWholeString = 0;

						//check to see if there is a translation phrase that matches the entire content of what is being translated
						if ($wholePhrase) {
							for ($i = 0; $i < count($translate); $i++) {
								//if there is, translate the whole phrase at once
								if ($textValue == $translate[$i]) {
									$isWholeString = 1;
									$node->nodeValue = $translation[$i];
								}

								//if the string has already been translated, don't translate parts
								if ($textValue == $translation[$i])
									$isWholeString = 1;
							}
						}

						//if there isn't a translation phrase that matches the entire content, or if whole phrase searching is turned off, search the phrase for the right part and translate it if it is found
						if ($isWholeString == 0 && preg_match($pattern, $textValue))
							$node->nodeValue = preg_replace($pattern, $replacement, $textValue);
					}
				}
			}
		}
	}
}

/******************
/** EXAMPLE USES **
/******************
 //create a new DOMTranslator object
 $translator = new DOMTranslator();

 //add translations to the object's dictionary
 $translator->addTranslation("How old are you?", "It doesn't matter.");
 $translator->addTranslation("bob", "Richard");
 $translator->addTranslation("Hey there bob!", "You're annoying.");
 $translator->addTranslation("Julian", "President John F. Kennedy");

 //translate a couple phrases using the object
 echo $translator->translate("North America is a large continent, Bob.", TRUE, TRUE); //this translation will not traverse the DOM structure
 echo $translator->translate("How old are you?", TRUE); //this translation will kick in whole phrase translation
 echo $translator->translate("Hey there Bob!", FALSE); //this translation will not use whole phrase searching
 echo $translator->translate("<br>I'm not sure what you want <span>from me Julian. Back the heck</span> away. <b>I don't want you!</b>", TRUE, FALSE, array('span')); //this translation will not search inside of any <span> elements
 */
?>
