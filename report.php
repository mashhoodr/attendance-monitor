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
		<link rel="stylesheet" href="vendor/bootstrap-3.2.0-dist/css/bootstrap-flatly.min.css">
		<script src="vendor/jquery-2.1.1.min.js" type="text/javascript"></script>
		<script src="vendor/bootstrap-3.2.0-dist/js/bootstrap.min.js" type="text/javascript"></script>
	</head>
	<body style="width: 80%; margin:0 auto;">
		<?php

			date_default_timezone_set('Asia/Karachi');
			define("ONE_DAY", 86400);
			define("NO_OF_DAYS", 6);
			# shows the results for the stuff in the database
			require_once 'DBService.class.php';

			$db = new DBService();

			$result = $db -> getResults('SELECT * FROM devices ORDER BY id DESC LIMIT 1');

			if(!$result) {
				echo "Did not find any results....";
				exit();
			}
			?>
			<div class="jumbotron">
				<h3>Last updated:</h3>
			  <h1><?= date('m/d/Y h:i:s', $result[0]['lastUpdated']) ?></h1>
			  <p><?= (strtotime('now') - intval($result[0]['lastUpdated'])) . ' seconds ago' ?></p>
			</div>
			<p>&nbsp;</p>
			<?php
				$devices = array(
					array(
						"name" => "Mashhood",
						"device" => "14:10:9F:D3:9A:07",
						"total" => 0
					),
					array(
						"name" => "Hussain",
						"device" => "F8:1E:DF:F1:22:6E",
						"total" => 0
					),
					array(
						"name" => "Obaid",
						"device" => "B8:E8:56:3F:2D:76",
						"total" => 0
					),
					array(
						"name" => "Kashif",
						"device" => "70:56:81:91:7A:05",
						"total" => 0
					),
					array(
						"name" => "Moeez",
						"device" => "00:26:BB:0E:56:93",
						"total" => 0
					),
					array(
						"name" => "Shamroze",
						"device" => "00:25:00:4B:23:B3",
						"total" => 0
					),
					array(
						"name" => "Suleman",
						"device" => "00:26:BB:01:DA:4E",
						"total" => 0
					)
				);
				$today = strtotime('today') + (ONE_DAY - 1);
				$last5 = $today - (ONE_DAY * NO_OF_DAYS);
				$weeksResults = $db -> getResults('SELECT * FROM devices WHERE lastUpdated BETWEEN ' . $last5 . ' AND ' . $today . ' ORDER BY lastUpdated ASC');

				function filterByDay($date, $list) {
					$filtered = array();
					$from = strtotime($date);
					$to = $from + (ONE_DAY - 1);
					foreach ($list as $item) {
						if($item['lastUpdated'] > $from && $item['lastUpdated'] < $to) {
							$filtered[] = $item;
						}
					}
					return $filtered;
				}

				function filterByMac($mac, $list) {
					$filtered = array();
					foreach ($list as $item) {
						if($item['mac'] == $mac) {
							$filtered[] = $item;
						}
					}

					return $filtered;
				}

				$last5Data = array();


			?>
			<h4>Last 6 days (<?= date('m/d/Y', $today) ?> - <?= date('m/d/Y', $last5 + 1) ?>)</h4>
			<table class="table table-striped table-bordered">
				<tr>
					<th></th>
					<?php
						for($x = 0; $x < NO_OF_DAYS; $x++) {
							$daySeconds = $today - (ONE_DAY * $x);
							$dayDate = date('m/d/Y', $daySeconds);
							$last5Data[$dayDate] = filterByDay($dayDate, $weeksResults);
							?>
							<th><?= $dayDate ?></th>
							<?php
						}
					?>
					<th></th>
				</tr>
				<tr>
					<td>Name</td>
					<?php
					foreach ($last5Data as $value) {
						?>
						<td>
							<table class="table">
								<tr>
									<th>In</th>
									<th>Out</th>
									<th>Total</th>
								</tr>
							</table>
						</td>
						<?php
					}
					?>
					<td>Total Hours</td>
				</tr>
				<?php
					foreach ($devices as $device) {
							?>
							<tr>
								<td><?= $device['name'] ?></td>
								<?php
									foreach ($last5Data as $todaysResults) {
										$usersRecords = filterByMac($device['device'], $todaysResults);
										if(count($usersRecords ) > 0) {
											$first = $usersRecords[0];
											$last = end($usersRecords);
											if($last['status'] !== 'down') {
												$last['lastUpdated'] = strtotime('now');
											}
											$total = (($last['lastUpdated'] - $first['lastUpdated']) / 60) / 60;
											$device['total'] += $total;

											?>
											<td>
												<table style="width:100%">
													<tr>
														<td style="padding: 5px;"><?= date('H:i', $first['lastUpdated']) ?></td>
														<td style="padding: 5px;"><?= ($last['status'] == 'down') ? date('H:i', $last['lastUpdated']) : '-' ?></td>
														<td style="padding: 5px;"><?= round($total, 2) ?></td>
													</tr>
												</table>
											</td>
										<?php
										}
										else {
											?>
											<td> - </td>
											<?php
										}
									}
								?>
								<td>
									<?= round($device['total'], 2) ?>
								</td>
							</tr>
							<?php
						}
					?>
			</table>
	</body>
</html>