<?php
class SaveUserSelectedCode
{
	public static $dbConnection;

	public static function insertSelectedCode($session, $pattern, $code)
	{
		mysqli_query(self::$dbConnection, "INSERT INTO users_selected_codes(session, pattern, code) 
			VALUES(" . $session . ", " . $pattern . ", '" . $code . "')");
	}
}

SaveUserSelectedCode::$dbConnection  = mysqli_connect("localhost", "root", "", "design_patterns_text_analysis");
SaveUserSelectedCode::insertSelectedCode($_GET['session'], $_GET['pattern'], $_GET['code']);
?>