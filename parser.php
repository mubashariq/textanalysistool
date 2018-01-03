<?php
 ini_set('memory_limit', '250M');
// Include Composer autoloader if not already done.
include 'vendor/autoload.php';
require_once('TextRazor.php');

TextRazorSettings::setApiKey("96484baecf996ed2b48f9bbaf14bcefed6ff60896f49b92bd498e3a8");
// Parse pdf file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf    = $parser->parseFile('HeadMouse.pdf');
 
$text = $pdf->getText();
 
//  $pages  = $pdf->getPages();
// foreach ($pages as $page) {
//     echo $page->getText();
// }

// $details  = $pdf->getDetails();
// foreach ($details as $property => $value) {
//     if (is_array($value)) {
//         $value = implode(', ', $value);
//     }
//     echo $property . ' => ' . $value . "\n";
// }
testAnalysis($text);
function testAnalysis($text) {
	$textrazorClassifier = new ClassifierManager();

    $classifierId = 'recommender';

    try {
        print_r($textrazorClassifier->deleteClassifier($classifierId));
    }
    catch (Exception $e){
        // Silently ignore missing classifier for now.
    }

    // Define some new categories and upload them as a new classifier.
    $newCategories = array();
    array_push($newCategories, array('categoryId' => '1', 'query' => "concept('recommender')"));
    array_push($newCategories, array('categoryId' => '2', 'query' => "concept('system>computing')"));
    array_push($newCategories, array('categoryId' => '3', 'query' => "concept('behavior')"));
    array_push($newCategories, array('categoryId' => '4', 'query' => "concept('technology')"));
    array_push($newCategories, array('categoryId' => '5', 'query' => "concept('shopping')"));
    array_push($newCategories, array('categoryId' => '6', 'query' => "concept('design')"));
    array_push($newCategories, array('categoryId' => '7', 'query' => "concept('patterns')"));

    $textrazorClassifier->createClassifier($classifierId, $newCategories);

// 	print_r($textrazorClassifier->allCategories($classifierId));
// die;
    $textrazor = new TextRazor();
    $textrazor->addClassifier($classifierId);
    // $textrazor->addExtractor('entities');
    $textrazor->addExtractor('topics');
    // $textrazor->addExtractor('words');
    // $textrazor->addExtractor('phrases');
    // $textrazor->addExtractor('dependency-trees');
    // $textrazor->addExtractor('relations');
    // $textrazor->addExtractor('entailments');
    // $textrazor->addExtractor('senses');
    // $textrazor->addExtractor('spelling');
    $response = $textrazor->analyze($text);
    echo "<pre>";
    print_r($response);
}
?>