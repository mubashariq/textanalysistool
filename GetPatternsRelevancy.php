<?php
class GetPatternsRelevancy
{
	public static $dbConnection;
	public static function relatedTopics($pattern, $comparePattern)
	{
		return mysqli_query(self::$dbConnection, "SELECT a.*
				FROM research_paper_topics a 
				WHERE a.topic IN (SELECT topic b
                FROM research_paper_topics b
                WHERE b.topic = a.topic 
                AND b.`design_pattern_id` = ". $comparePattern . " ) 
                AND a.design_pattern_id = " . $pattern);
	}

	public static function getPatternTopicsCount($pattern)
	{
		$result = mysqli_query(self::$dbConnection, "SELECT * FROM research_paper_topics 
			WHERE design_pattern_id = " . $pattern);
		return mysqli_num_rows($result);
	}

	public static function getPatternName($patternId)
	{
		$result = mysqli_query(self::$dbConnection, "SELECT name FROM patterns 
			WHERE id = " . $patternId);
		return mysqli_fetch_array($result, MYSQLI_ASSOC)['name'];
	}


}
GetPatternsRelevancy::$dbConnection  = mysqli_connect("localhost", "root", "", "design_patterns_text_analysis");
$patternId = $_GET['id'];
$allParameters = GetPatternsRelevancy::getPatternTopicsCount($patternId);
$patterns = $relevancy = [];
for($i=1; $i<=47 ;$i++)
{
	if ($i == $patternId) {
		continue;
	}
	$result = GetPatternsRelevancy::relatedTopics($patternId, $i);
	$relatedParameters = mysqli_num_rows($result);

	$patternName = GetPatternsRelevancy::getPatternName($i);

	$array = array();
	$array['name'] = $patternName; 
	$array['y'] = ($relatedParameters/$allParameters)*100;
	$array['drilldown']  = null; 
	$relevancy[] = $array['y'];
	
	// if ($array['y'] == 100) {
	// 	continue;
	// }

	$patterns[] = $array;
}

// $patternsCount = count($patterns);
// $leftCount = ceil($patternsCount/2);
// $rightCount = $patternsCount - $leftCount;
// $left = $right = [];

// for($i=0;$i<$leftCount; $i++) {
// 	$left[] = $patterns[$i];
// }

// for($j=$rightCount;$j<$patternsCount; $j++) {
// 	$right[] = $patterns[$j];
// }



// function sortLeft($x, $y) {
// 	return   $y['y'] - $x['y'];
// }
// function sortRight($x, $y) {
// 	return   $y['y'] - $x['y'];
// }

// usort($patterns, 'sortLeft');
// usort($right, 'sortRight');

// $patterns = array_merge($left, $right);

// echo "<pre>";
// print_r($patterns);

// die;

// $patterns = array_slice($patterns, 0, 5, true);

$data = ['pattern' => 'Mobies', 'data' => $patterns];
// sleep(1);
echo json_encode($data);
