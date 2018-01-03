<?php
class SelectedCodes
{
	public static $dbConnection;

	public static function getSelectedCodes($session, $pattern)
	{
		return mysqli_query(self::$dbConnection, "SELECT * FROM users_selected_codes 
			WHERE session = $session AND pattern = $pattern");
	}
}

SelectedCodes::$dbConnection  = mysqli_connect("localhost", "root", "", "design_patterns_text_analysis");
$codes = SelectedCodes::getSelectedCodes($_GET['session'], $_GET['pattern']);
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
	<table style="width: 100%;text-align: center;">
		<thead>
			<tr>
				<!-- <th style="width: 15%;">Codes</th> -->
			</tr>
		</thead>
		<tbody>
			<tr>
			<?php
				$i = 1;
				while($row = mysqli_fetch_array($codes, MYSQLI_ASSOC)) {
				?>
					<!-- <td><?php echo $i; ?></td> -->
					<td style="text-align: left;height: 40px;">
						<?php echo $row['code']; ?>
					</td>
				<?php
				if($i > 3) {
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