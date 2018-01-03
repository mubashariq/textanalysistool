<?php

require_once('TextRazor.php');

TextRazorSettings::setApiKey("96484baecf996ed2b48f9bbaf14bcefed6ff60896f49b92bd498e3a8");

function testAccount() {
    $accountManager = new AccountManager();
    print_r($accountManager->getAccount());
}

function testAnalysis() {
    $textrazor = new TextRazor();

//    $textrazor->addExtractor('entities');
//    $textrazor->addExtractor('words');
//    $textrazor->addExtractor('topics');
//    $textrazor->addExtractor('entailments');

//    $textrazor->addEnrichmentQuery("fbase:/location/location/geolocation>/location/geocode/latitude");
//    $textrazor->addEnrichmentQuery("fbase:/location/location/geolocation>/location/geocode/longitude");

    $text = 'LONDON - Barclays misled shareholders and the public about one of the biggest investments in the banks history, a BBC Paorama investigation has found.';

    $response = $textrazor->analyzeUrl('http://www.uxbooth.com/articles/what-is-ux-writing/');
     print_r($response);
    if (isset($response['response']['entities'])) {
       
//        foreach ($response['response']['entities'] as $entity) {
//            print("Entity ID: " . $entity['entityId']);
//		    $entity_data = $entity['data'];
//
//            if (!is_null($entity_data)) {
//			    print(PHP_EOL);
//			    print("Entity Latitude: " . $entity_data["fbase:/location/location/geolocation>/location/geocode/latitude"][0]);
//			    print(PHP_EOL);
//			    print("Entity Longitude: " . $entity_data["fbase:/location/location/geolocation>/location/geocode/longitude"][0]);
//		    }
//		    print(PHP_EOL);
//        }
    }
}

function testClassifier() {
    $textrazorClassifier = new ClassifierManager();

    $classifierId = 'test_cats_php';

    try {
        print_r($textrazorClassifier->deleteClassifier($classifierId));
    }
    catch (Exception $e){
        // Silently ignore missing classifier for now.
    }

    // Define some new categories and upload them as a new classifier.
    $newCategories = array();
    array_push($newCategories, array('categoryId' => '1', 'query' => "concept('user experience')"));
    array_push($newCategories, array('categoryId' => '2', 'query' => "concept('design')"));

    $textrazorClassifier->createClassifier($classifierId, $newCategories);

    // Test the new classifier out with an analysis request.
    $textrazor = new TextRazor();
    $textrazor->addClassifier($classifierId);

    $text = 'Barclays misled shareholders and the public about one of the biggest investments in the banks history, a BBC Panorama investigation has found.';
    $response = $textrazor->analyzeUrl('http://www.uxbooth.com/articles/what-is-ux-writing/');

    print_r($response['response']);

    // The client offers various methods for manipulating your stored classifier.
    print_r($textrazorClassifier->allCategories($classifierId));

    print_r($textrazorClassifier->deleteClassifier($classifierId));
}

function testEntityDictionary() {
    $textrazorDictionary = new DictionaryManager();

    $dictionaryId = 'test_ents_php';

    try {
        print_r($textrazorDictionary->deleteDictionary($dictionaryId));
    }
    catch (Exception $e){
        // Silently ignore missing dictionary for now.
    }

    // Define a new dictionary, then add some test entries
    print_r($textrazorDictionary->createDictionary($dictionaryId, 'STEM', true, "eng"));

    $new_entities = array();
    array_push($new_entities, array("id" => "UX", "text" => "user experience"));
     array_push($new_entities, array("id" => "TC", "text" => "design"));

    print_r($textrazorDictionary->addEntries($dictionaryId, $new_entities));

    // To use the new dictionary, simply add its ID to your analysis request.

    $textrazor = new TextRazor();

    $textrazor->addExtractor('topics');
    $textrazor->addEntityDictionary($dictionaryId);

    $text = 'Barclays misled shareholders and the public about one of the biggest investments in the banks history, a BBC Panorama investigation has found.';
    $response = $textrazor->analyzeUrl('http://www.uxbooth.com/articles/what-is-ux-writing/');

    // The matched entities will be available in the response

//    print_r($response);

    // The client offers various methods for manipulating your stored dictionary entries.

//    print_r($textrazorDictionary->getEntry($dictionaryId, "UX"));

    print_r($textrazorDictionary->allEntries($dictionaryId, 10,0));
//
//    print_r($textrazorDictionary->getDictionary($dictionaryId));
//
//    print_r($textrazorDictionary->allDictionaries());
//
    print_r($textrazorDictionary->deleteDictionary($dictionaryId));

}

//testAccount();
//testAnalysis();
testEntityDictionary();
//testClassifier();
