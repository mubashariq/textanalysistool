<?php
class CompareParameters
{
	public static $dbConnection;	

	public static function getPatterns()
	{
		return mysqli_query(self::$dbConnection, "SELECT * FROM patterns ORDER BY id ASC");
	}

	public static function getPatternParametersCount($pattern)
	{
		$result = mysqli_query(self::$dbConnection, "SELECT * FROM parameters WHERE design_pattern_id = " . $pattern);
		return mysqli_num_rows($result);
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
}
CompareParameters::$dbConnection  = mysqli_connect("localhost", "root", "", "design_patterns_text_analysis");
?>
<style type="text/css">
	.container {
		font-family: arial;
	}
	table {
		border-collapse: collapse;
	}
	table, tr, td, th {
		border: 1px solid #ccc;
		padding: 8px;
	}
	select {
		height: 30px;
		padding: 5px;
		font-size: 14px;
	}
</style>
<div class="container">
	<form>
		<select onchange="callUrl(this.value)">
			<option value="-1">- - Select Pattern - -</option>
			<?php
			$result = CompareParameters::getPatterns();
			while ($pattern = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			?>
				<option value="<?php echo $pattern['id']; ?>" <?php if(isset($_GET['pattern']) && $_GET['pattern'] == $pattern['id']) echo "selected"; ?>>
					<?php echo $pattern['name']; ?>
				</option>
			<?php
			}
			?>
		</select>
	</form>
	<?php
	if(isset($_GET['pattern']) && $_GET['pattern'] > 0) {
	?>
		<form>
			<select onchange="callCompareUrl(<?php echo $_GET['pattern']; ?>, this.value)">
				<option value="-1">- - Select pattern to compare - -</option>
			<?php
			$result = CompareParameters::getPatterns();
			while ($pattern = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			?>
				<option value="<?php echo $pattern['id']; ?>" <?php if(isset($_GET['related_pattern']) && $_GET['related_pattern'] == $pattern['id']) echo "selected"; ?>>
					<?php echo $pattern['name']; ?>
				</option>
			<?php
			}
			?>
			</select>	
		</form>
	<?php
	}
	if (isset($_GET['pattern']) && isset($_GET['related_pattern'])) {
		$parameters = CompareParameters::relatedParameters($_GET['pattern'], $_GET['related_pattern']);
	?>
		Total Topics: <?php echo $allParameters = CompareParameters::getPatternParametersCount($_GET['pattern']); ?> <br />
		Related Topics: <?php echo $relatedParameters = mysqli_num_rows($parameters); ?> <br />
		Relevancy: 
		<?php
			echo number_format(($relatedParameters/$allParameters)*100, 2) . "%";
		?>
		<table style="width: 100%;text-align: center;">
			<thead>
				<tr>
					<th style="width: 15%;">#</th>
					<th style="width: 55%;">Parameter</th>
					<th style="width: 15%;">Parameter Type</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$i = 1;
					while ($parameter = mysqli_fetch_array($parameters, MYSQLI_ASSOC)) {
					?>
						<tr>
							<td><?php echo $i; ?></td>
							<td><?php echo $parameter['parameter']; ?></td>
							<td><?php echo $parameter['type']; ?></td>
						</tr>
					<?php
					$i++;
					}
				?>
			</tbody>
		</table>
	<?php
	}
	?>
</div>
<script type="text/javascript">
	function callUrl(val)
	{
		if (val > 0) {
			window.location = 'CompareParameters.php?pattern='+val;
		}
	}
	function callCompareUrl(val, comparePattern)
	{
		if (comparePattern > 0) {
			window.location = 'CompareParameters.php?pattern='+val+'&related_pattern='+comparePattern;
		}
	}
</script>