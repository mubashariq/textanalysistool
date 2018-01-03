<?php
class ComparePatterns
{
	public static $dbConnection;	

	public static function getPatterns()
	{
		return mysqli_query(self::$dbConnection, "SELECT * FROM patterns ORDER BY id ASC");
	}

	public static function getPatternTopicsCount($pattern)
	{
		//AND status = 1
		$result = mysqli_query(self::$dbConnection, "SELECT * FROM research_paper_topics 
				WHERE design_pattern_id = " . $pattern . "");
		return mysqli_num_rows($result);
	}

	public static function relatedTopics($pattern, $comparePattern)
	{
		//AND b.status = 1
		return mysqli_query(self::$dbConnection, "SELECT a.* 
				FROM research_paper_topics a 
				WHERE a.topic IN (SELECT topic b
                FROM research_paper_topics b
                WHERE b.topic = a.topic 
                AND b.`design_pattern_id` = ". $comparePattern . " ) 
                AND a.design_pattern_id = " . $pattern . " ORDER BY score DESC");
	}
}
ComparePatterns::$dbConnection  = mysqli_connect("localhost", "root", "", "design_patterns_text_analysis");
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
			$result = ComparePatterns::getPatterns();
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
			$result = ComparePatterns::getPatterns();
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
		$topics = ComparePatterns::relatedTopics($_GET['pattern'], $_GET['related_pattern']);
	?>
		Total Topics: <?php echo $allTopics = ComparePatterns::getPatternTopicsCount($_GET['pattern']); ?> <br />
		Related Topics: <?php echo $relatedTopics = mysqli_num_rows($topics); ?> <br />
		Relevancy: 
		<?php
			echo number_format(($relatedTopics/$allTopics)*100, 2) . "%";
		?>
		<table style="width: 100%;text-align: center;">
			<thead>
				<tr>
					<th style="width: 15%;">#</th>
					<th style="width: 55%;">Topic</th>
					<th style="width: 15%;">Score</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$i = 1;
					while ($topic = mysqli_fetch_array($topics, MYSQLI_ASSOC)) {
					?>
						<tr>
							<td><?php echo $i; ?></td>
							<td><?php echo $topic['topic']; ?></td>
							<td><?php echo number_format($topic['score'], 3); ?></td>
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
			window.location = 'ComparePatterns.php?pattern='+val;
		}
	}
	function callCompareUrl(val, comparePattern)
	{
		if (comparePattern > 0) {
			window.location = 'ComparePatterns.php?pattern='+val+'&related_pattern='+comparePattern;
		}
	}
</script>