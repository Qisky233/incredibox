<?php

if(!USER_ADMIN){
	die('x');
}

if(!isset($_GET['days'])){
	$_GET['days'] = 0;
}

$start_date = date("Y-m-d", strtotime("-".$_GET['days']." days"));

$conn = open_connection();
$sql = 'SELECT data FROM statistics WHERE created_date BETWEEN :start_date AND :end_date';
$st = $conn->prepare($sql);
$st->bindValue(":start_date", $start_date, PDO::PARAM_STR);
$st->bindValue(":end_date", date('Y-m-d'), PDO::PARAM_STR);
$st->execute();
$row = $st->fetchAll(PDO::FETCH_ASSOC);

$data = merge_data($row);

function merge_data($array){
	$data = array();
	foreach ($array as $item) {
		$list = json_decode($item['data'], true);
		foreach ($list as $name => $a) {
			foreach ($a as $child => $b) {
				if(isset($data[$name][$child])){
					$data[$name][$child] += $b;
				} else {
					$data[$name][$child] = $b;
				}
			}
		}
	}
	return $data;
}

function show_data_list($key){
	global $data;
	if(isset($data[$key])){
		$list = $data[$key];
		$index = 0;
		foreach ($list as $name => $value) {
			$index++;
			?>
			<tr>
				<th scope="row"><?php echo $index ?></th>
				<td><?php echo $name ?></td>
				<td><?php echo $value ?></td>
			</tr>
			<?php
		}
	}
}

?>
<div class="section section-full">
	<ul class="nav nav-tabs custom-tab" role="tablist">
		<li class="nav-item" role="presentation">
			<a class="nav-link active" data-bs-toggle="tab" href="#browser">Browser</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" data-bs-toggle="tab" href="#os">OS</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" data-bs-toggle="tab" href="#language">Language</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" data-bs-toggle="tab" href="#refferer">Refferer</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" data-bs-toggle="tab" href="#vendor">Device vendor</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" data-bs-toggle="tab" href="#screen">Screen size</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" data-bs-toggle="tab" href="#country">Country</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane tab-container active" id="browser">
			<table class="table">
				<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Name</th>
					<th scope="col">Total</th>
				</tr>
				</thead>
				<tbody>
				<?php show_data_list('browser') ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane tab-container fade" id="os">
			<table class="table">
				<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Name</th>
					<th scope="col">Total</th>
				</tr>
				</thead>
				<tbody>
				<?php show_data_list('os') ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane tab-container fade" id="language">
			<table class="table">
				<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Name</th>
					<th scope="col">Total</th>
				</tr>
				</thead>
				<tbody>
				<?php show_data_list('language') ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane tab-container fade" id="refferer">
			<table class="table">
				<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Name</th>
					<th scope="col">Total</th>
				</tr>
				</thead>
				<tbody>
				<?php show_data_list('refferer') ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane tab-container fade" id="vendor">
			<table class="table">
				<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Name</th>
					<th scope="col">Total</th>
				</tr>
				</thead>
				<tbody>
				<?php show_data_list('device_vendor') ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane tab-container fade" id="screen">
			<table class="table">
				<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Name</th>
					<th scope="col">Total</th>
				</tr>
				</thead>
				<tbody>
				<?php show_data_list('screen_size') ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane tab-container fade" id="country">
			<table class="table">
				<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Name</th>
					<th scope="col">Total</th>
				</tr>
				</thead>
				<tbody>
				<?php show_data_list('country') ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="general-wrapper">
		<div>
			<select class="form-select form-select-sm" id="switch-date" style="max-width: 200px">
			    <option value="1" selected>Date Range</option>
			    <option value="1">Today</option>
			    <option value="7">Last 7 days</option>
			    <option value="30">Last 30 days</option>
			</select>
			<br>
			<p>
				Show data between <strong><?php echo $start_date ?></strong> to <strong><?php echo date('Y-m-d') ?></strong> (Past <?php echo $_GET['days'] ?> days).
			</p>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(()=>{
		$('#switch-date').change(function(){
			let val = $(this).val();
			window.location.href = "<?php echo DOMAIN ?>admin/dashboard.php?viewpage=plugin&name=statistics&days="+val;
		});
	});
</script>