<?php
class CommonCodes
{
	public static $dbConnection;	

	public static function relatedTopics($pattern)
	{
		return mysqli_query(self::$dbConnection, "SELECT * 
				FROM common_codes WHERE design_pattern_id = " . $pattern);
	}

	public static function getPatterns()
	{
		return mysqli_query(self::$dbConnection, "SELECT * FROM patterns WHERE is_unique = 1 ORDER BY id ASC");
	}
}
CommonCodes::$dbConnection  = mysqli_connect("localhost", "root", "", "design_patterns_text_analysis");
$patternId = isset($_GET['id']) ? $_GET['id'] : 1;
$codes = CommonCodes::relatedTopics($patternId);
$session = 15;
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
	.common-codes:visited {
		background: black;
	}
</style>
<div class="container">
	<form style="float: left;">
		<select onchange="callUrl(this.value)">
			<?php
			$result = CommonCodes::getPatterns();
			while ($pdfResult = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			?>
				<option value="<?php echo $pdfResult['id']; ?>" <?php if(isset($_GET['id']) && $_GET['id'] == $pdfResult['id']) echo "selected"; ?>>
					<?php echo $pdfResult['name']; ?>
				</option>
			<?php
			}
			?>
		</select>
	</form>
	<a style="float: right; color: blue; margin-top: 10px;" href="SelectedCodes.php?session=<?php echo $session ?>&pattern=<?php echo $patternId ?>">
		Selected Code
	</a>
	<table style="width: 100%;text-align: center;">
		<thead>
			<tr>
				<!-- <th style="width: 15%;">#</th> -->
				<!-- <th style="width: 55%;">Topic</th> -->
			</tr>
		</thead>
		<tbody>
			<tr>
			<?php
				$i = 1;
				while($row = mysqli_fetch_array($codes, MYSQLI_ASSOC)) {
				?>
					<!-- <td><?php echo $i; ?></td> -->
					<td style="text-align: left;height: 40px;" class="common-codes" code="<?php echo $row['topic']; ?>">
						<?php echo $row['topic']; ?>
					</td>
				<?php
				if($i > 5) {
					?>
						</tr><tr>
					<?php
					$i = 0;
				}
				$i++;
				}
			?>
			</tr>
		</tbody>
	</table>
</div>
<script src="jquery.js"></script>
<script type="text/javascript">
	function callUrl(val)
	{
		window.location = 'CommonCodes.php?id='+val;
	}
	$(document).ready(function(){
		$(".common-codes").click(function(){
			var code = $(this).attr('code');
			$(this).css('background', '#ccc');
			$.getJSON('SaveUserSelectedCode.php', {code: code, session: <?php echo $session; ?>, pattern: <?php echo $patternId; ?>}, function(response) {
        		console.log(response);
         	});
		});
	});
</script>