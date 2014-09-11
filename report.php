<!DOCTYPE html>
	<head>
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="author" content="Mashhood Rastgar" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Report</title>
		<!-- Scripts to handle HTML5 tags on IE -->
		<!--[if lt IE 9]>
			<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
		<![endif]-->
		<!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<style type="text/css">
			td, th {
				padding: 5px;
				text-align: left;
			}
		</style>
	</head>
	<body>
		<?php

			date_default_timezone_set('Asia/Karachi');

			# shows the results for the stuff in the database
			require_once 'DBService.class.php';

			$db = new DBService();

			$result = $db -> getResults('SELECT * FROM devices ORDER BY id DESC LIMIT 10');

			if($result) {

				?>
				<h3>
					Last updated: <?= date('m/d/Y h:i:s', $result[0]['lastUpdated']) ?>
					<small><?= (strtotime('now') - intval($result[0]['lastUpdated'])) . ' seconds ago' ?></small>
				</h3>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<table>
					<tr>
						<th>MAC</th>
						<th>Status</th>
						<th>Last Updated</th>
					</tr>
					<?php
						foreach ($result as $record) {
							?>
							<tr>
								<td><?= $record['mac'] ?></td>
								<td><?= $record['status'] ?></td>
								<td><?= date('m/d/Y h:i:s', $record['lastUpdated']) ?></td>
							</tr>
							<?php
						}
					?>
				</table>
				<?php
			}

		?>

	</body>
</html>