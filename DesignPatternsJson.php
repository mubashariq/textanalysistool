<?php

class DesignPatternsJson
{
	
	public static $file = 'design-patterns/design-patterns.json';
	public static $dbConnection;
	public static $patternId;

	public static function loadJson()
	{
		return json_decode(file_get_contents(self::$file), true);
	}

	public static function parseJson()
	{
		self::truncateDBTables();

		$data = self::loadJson();
		foreach ($data['results'] as $pattern => $details) {
			self::$patternId = self::saveDesignPattern($details);
			
			self::saveFamily($details['printouts']['family']);
			self::saveMotivations($details['printouts']['motivation']);
			self::saveGoals($details['printouts']['goal']);
			self::saveDevices($details['printouts']['device']);
			self::saveCites($details['printouts']['cites']);
			self::saveCitedBy($details['printouts']['cited']);
			self::saveRelatedPatterns($details['printouts']['related_patterns']);
			self::saveTechnology($details['printouts']['technology']);
			self::saveTheory($details['printouts']['theory']);
		}
	}

	public static function truncateDBTables()
	{
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE patterns");
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE parameters");
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE family");
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE cited_by");
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE cites");
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE devices");
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE goals");
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE motivations");
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE related_patterns");
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE technology");
		// mysqli_query(self::$dbConnection, "TRUNCATE TABLE theory");
	}

	public static function saveDesignPattern($pattern)
	{
		mysqli_query(self::$dbConnection, "INSERT INTO patterns(name, link) 
					VALUES('" . $pattern['fulltext'] . "', '" . $pattern['fullurl'] . "')");
		return mysqli_insert_id(self::$dbConnection);
	}

	public static function saveFamily($family)
	{
		self::saveParameters($family, 1);
	}

	public static function saveMotivations($motivations)
	{
		self::saveParameters($motivations, 2);
	}
	
	public static function saveGoals($goals)
	{
		self::saveParameters($goals, 3);
	}
	
	public static function saveDevices($devices)
	{
		self::saveParameters($devices, 4);
	}
	
	public static function saveCites($cites)
	{
		self::saveParameters($cites, 5);
	}
	
	public static function saveCitedBy($citedBy)
	{
		self::saveParameters($citedBy, 6);
	}
	
	public static function saveRelatedPatterns($relatedPatterns)
	{
		self::saveParameters($relatedPatterns, 7);
	}
	
	public static function saveTechnology($technology)
	{
		self::saveParameters($technology, 8);
	}

	public static function saveTheory($theory)
	{
		self::saveParameters($theory, 9);
	}

	public function saveParameters($parameters, $type)
	{
		if( count($parameters) > 0 ) {
			foreach($parameters as $parameter) {
				mysqli_query(self::$dbConnection, "INSERT INTO parameters(design_pattern_id, parameter, type) 
					VALUES(" . self::$patternId . ", '" . $parameter['fulltext'] . "', " . $type . ")");
			}
		}
	}
}

DesignPatternsJson::$dbConnection  = mysqli_connect("localhost", "root", "", "design_patterns_text_analysis");
// DesignPatternsJson::parseJson();
