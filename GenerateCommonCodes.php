<?php
class GenerateCommonCodes
{
	public static $dbConnection;	

	public static function relatedTopics($pattern, $comparePattern)
	{
		return mysqli_query(self::$dbConnection, "SELECT a.* 
				FROM research_paper_topics a 
				WHERE a.topic IN (SELECT topic b
                FROM research_paper_topics b
                WHERE b.topic = a.topic 
                AND b.`design_pattern_id` = ". $comparePattern . " AND b.status = 1) 
                AND a.design_pattern_id = " . $pattern . " AND a.status = 1 ORDER BY a.score DESC");
	}

	public static function relatedParameters($pattern, $comparePattern)
	{
		return mysqli_query(self::$dbConnection, "SELECT a.* , pt.name type
				FROM parameters a 
				LEFT JOIN parameters_type pt ON pt.id = a.type
				WHERE a.parameter IN (SELECT parameter
                FROM parameters b
                WHERE b.parameter = a.parameter 
                AND b.`design_pattern_id` = ". $comparePattern . ") 
                AND a.design_pattern_id = " . $pattern . " ORDER BY pt.id ASC");
	}

	public static function insertCommonCodes($patternId, $code)
	{
		mysqli_query(self::$dbConnection, "INSERT INTO common_codes(design_pattern_id, topic) 
			VALUES(" . $patternId .", '" . $code . "')");
	}

	public static function deleteCommonCodes()
	{
		mysqli_query(self::$dbConnection, 'TRUNCATE table common_codes');
	}
}
GenerateCommonCodes::$dbConnection  = mysqli_connect("localhost", "root", "", "design_patterns_text_analysis");
$patternId = isset($_GET['id']) ? $_GET['id'] : 1;
// GenerateCommonCodes::deleteCommonCodes();
// for($j=1; $j<=47 ;$j++) {
// 	$patterns = [];
// 	for($i=1; $i<=47 ;$i++) {
// 		if ($i == $j) {
// 			continue;
// 		}
// 		$topics = [];
// 		$result = GenerateCommonCodes::relatedTopics($j, $i);
// 		while ($topic = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
// 			$topics[] = $topic['topic'];
// 		}

// 		$parameters = [];
// 		$result = GenerateCommonCodes::relatedParameters($j, $i);
// 		while ($parameter = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
// 			if ($parameter['parameter']  == 'Public' || $parameter['parameter']  == 'Private') {
// 				$parameter['parameter']  = $parameter['parameter'] . ' device';
// 			}
// 			$parameters[] = $parameter['parameter'];
// 		}
// 		$patterns = array_merge($patterns, $topics, $parameters);
// 	}

// 	$patterns = array_values(array_unique($patterns));
// 	foreach($patterns as $code) {
// 		GenerateCommonCodes::insertCommonCodes($j, $code);
// 	}

// }
		