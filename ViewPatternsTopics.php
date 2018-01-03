<?php
class ViewPatternsTopics
{
	public static $dbConnection;
	public static function list($pattern)
	{
		return mysqli_query(self::$dbConnection, "SELECT * FROM research_paper_topics 
			WHERE design_pattern_id = $pattern AND status = 0 and score < 1"
		);
	}
	
	public static function getPatterns()
	{
		return mysqli_query(self::$dbConnection, "SELECT * FROM patterns ORDER BY id ASC");
	}	

	public static function deleteTopic($topicId)
	{
		mysqli_query(self::$dbConnection, "UPDATE research_paper_topics SET status = 0 WHERE id = " . $topicId);
	}
}
ViewPatternsTopics::$dbConnection  = mysqli_connect("localhost", "root", "", "design_patterns_text_analysis");
if (!isset($_GET['pattern'])) {
	$_GET['pattern'] = 1;	
}
if (isset($_GET['delete']) && $_GET['delete'] > 0) {
	ViewPatternsTopics::deleteTopic($_GET['delete']);
}
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
			<?php
			$result = ViewPatternsTopics::getPatterns();
			while ($pdfResult = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			?>
				<option value="<?php echo $pdfResult['id']; ?>" <?php if(isset($_GET['pattern']) && $_GET['pattern'] == $pdfResult['id']) echo "selected"; ?>>
					<?php echo $pdfResult['name']; ?>
				</option>
			<?php
			}
			?>
		</select>
	</form>

	<table style="width: 100%;text-align: center;">
		<thead>
			<tr>
				<th style="width: 15%;">#</th>
				<th style="width: 55%;">Topic</th>
				<th style="width: 15%;">Score</th>
				<th style="width: 15%;">Operation</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$topics =ViewPatternsTopics::list($_GET['pattern']);
			$i = 1;
			while ($topic = mysqli_fetch_array($topics, MYSQLI_ASSOC)) {
			?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $topic['topic']; ?></td>
					<td><?php echo number_format($topic['score'], 3); ?></td>
					<td>
						<a href="ViewPatternsTopics.php?pattern=<?php echo $_GET['pattern']; ?>&delete=<?php echo $topic['id']; ?>">
							Delete
						</a>
					</td>
				</tr>
			<?php
			$i++;
			}
			?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	function callUrl(val)
	{
		window.location = 'ViewPatternsTopics.php?pattern='+val;
	}
</script>