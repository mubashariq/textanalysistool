<?php
ini_set('memory_limit', '250M');

include 'vendor/autoload.php';
require_once('TextRazor.php');

TextRazorSettings::setApiKey("96484baecf996ed2b48f9bbaf14bcefed6ff60896f49b92bd498e3a8");

class ResearchPapersTextAnalysis
{

	public static $dbConnection;
	
	public static function createTextFile()
	{
		$result = self::getPatterns();
	 	while ($pdfResult = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		 	if (in_array($pdfResult['id'], array(13, 16, 25, 30, 34, 45))) {
		 		continue;
		 	}
			$pdfFile = $pdfResult['id'].'_'.strtolower(str_replace(array(' ', '-'), '_', str_replace(array('(', ')', '!'), '', $pdfResult['name'])));
			$text = self::getPDFText("pdfs/".$pdfFile.".pdf");

			$file = fopen("pdfs-text/".$pdfFile.".txt", "w");
			fwrite($file, $text);
			fclose($file);
		}
	}

	public static function textAnalysis()
	{
		$textrazor = new TextRazor();
		$textrazor->addExtractor('topics');
		$result = self::getPatterns();
		 while ($pdfResult = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		 	// if (!in_array($pdfResult['id'], array())) {
		 	// 	continue;
		 	// }
			$pdfFile = $pdfResult['id'].'_'.strtolower(str_replace(array(' ', '-'), '_', str_replace(array('(', ')', '!'), '', $pdfResult['name'])));
			$text = file_get_contents("pdfs-text/".$pdfFile.".txt");

			// $text = mb_convert_encoding($text, 'UTF-8', 'OLD-ENCODING');

			$response = $textrazor->analyze(self::w1250_to_utf8($text));

			self::saveTopicsInDB($response['response']['coarseTopics'], $pdfResult['id']);
		 	self::saveTopicsInDB($response['response']['topics'], $pdfResult['id']);
		}

	}

	public static function w1250_to_utf8($text) {
	    $map = array(
	        chr(0x8A) => chr(0xA9),
	        chr(0x8C) => chr(0xA6),
	        chr(0x8D) => chr(0xAB),
	        chr(0x8E) => chr(0xAE),
	        chr(0x8F) => chr(0xAC),
	        chr(0x9C) => chr(0xB6),
	        chr(0x9D) => chr(0xBB),
	        chr(0xA1) => chr(0xB7),
	        chr(0xA5) => chr(0xA1),
	        chr(0xBC) => chr(0xA5),
	        chr(0x9F) => chr(0xBC),
	        chr(0xB9) => chr(0xB1),
	        chr(0x9A) => chr(0xB9),
	        chr(0xBE) => chr(0xB5),
	        chr(0x9E) => chr(0xBE),
	        chr(0x80) => '&euro;',
	        chr(0x82) => '&sbquo;',
	        chr(0x84) => '&bdquo;',
	        chr(0x85) => '&hellip;',
	        chr(0x86) => '&dagger;',
	        chr(0x87) => '&Dagger;',
	        chr(0x89) => '&permil;',
	        chr(0x8B) => '&lsaquo;',
	        chr(0x91) => '&lsquo;',
	        chr(0x92) => '&rsquo;',
	        chr(0x93) => '&ldquo;',
	        chr(0x94) => '&rdquo;',
	        chr(0x95) => '&bull;',
	        chr(0x96) => '&ndash;',
	        chr(0x97) => '&mdash;',
	        chr(0x99) => '&trade;',
	        chr(0x9B) => '&rsquo;',
	        chr(0xA6) => '&brvbar;',
	        chr(0xA9) => '&copy;',
	        chr(0xAB) => '&laquo;',
	        chr(0xAE) => '&reg;',
	        chr(0xB1) => '&plusmn;',
	        chr(0xB5) => '&micro;',
	        chr(0xB6) => '&para;',
	        chr(0xB7) => '&middot;',
	        chr(0xBB) => '&raquo;',
	    );
	    return html_entity_decode(mb_convert_encoding(strtr($text, $map), 'UTF-8', 'ISO-8859-2'), ENT_QUOTES, 'UTF-8');
	}

	public static function saveTopicsInDB($response, $design_pattern_id)
	{
		foreach($response as $detail) {
			if ($detail['score'] >= 0.079) {
				$res = mysqli_query(self::$dbConnection, "SELECT id FROM research_paper_topics WHERE design_pattern_id = $design_pattern_id AND topic = '".$detail['label']."'");
				$rowcount = mysqli_num_rows($res);
				if ($rowcount == 0) {
					mysqli_query(self::$dbConnection, "INSERT INTO research_paper_topics(design_pattern_id, topic, score) 
							VALUES('" . $design_pattern_id . "', '" . $detail['label'] . "', " . $detail['score'] . ")");
				}
			}
		}
	}

	public static function getPatterns()
	{
		return mysqli_query(self::$dbConnection, "SELECT * FROM patterns ORDER BY id ASC");
	}

	public static function getPDFText($pdfFile)
	{
		$parser = new \Smalot\PdfParser\Parser();
		$pdf    = $parser->parseFile($pdfFile);
		 
		return $pdf->getText();
	}

}

ResearchPapersTextAnalysis::$dbConnection  = mysqli_connect("localhost", "root", "", "design_patterns_text_analysis");
// ResearchPapersTextAnalysis::createTextFile();
// ResearchPapersTextAnalysis::textAnalysis();