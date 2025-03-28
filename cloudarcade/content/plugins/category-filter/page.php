<?php

$data_array = null;
if(get_option("category-filter")){
	$data_array = json_decode(get_option("category-filter"), true);
}

?>

<div class="section">
	<div class="bs-callout bs-callout-info">
		The purpose of this plugin is to filtering and automatically change specific game category name during game import process.<br>
		Can be useful if you're already localize category name (non-english) and want to change english category name into translated one.
	</div>
	<?php if(true) {
		?>

		<div class="category-filter-section">
			<form id="form-category-filter" action="#" method="post">
				<div class="form-group row">
					<div class="col">
						<label>Original category name</label>
					</div>
					<div class="col">
						<label>Your category name</label>
					</div>
				</div>
					
				<?php

				if(!is_null($data_array)){
					foreach ($data_array as $item => $value) {
						?>
							<div class="form-group row">
								<div class="col">
									<input type="text" class="form-control c-original" name="val" value="<?php echo $item ?>">
								</div>
								<div class="col">
									<input type="text" class="form-control c-category" name="val" value="<?php echo $value ?>">
								</div>
							</div>
						<?php
					}
				} else { ?>
					<div class="form-group row">
						<div class="col">
							<input type="text" class="form-control c-original" name="val" placeholder="Adventure" value="">
						</div>
						<div class="col">
							<input type="text" class="form-control c-category" name="val" placeholder="Adventura" value="">
						</div>
					</div>
					<div class="form-group row">
						<div class="col">
							<input type="text" class="form-control c-original" name="val" placeholder="Puzzle" value="">
						</div>
						<div class="col">
							<input type="text" class="form-control c-category" name="val" placeholder="Pəzəl" value="">
						</div>
					</div>
				<?php } ?>
				<div id="inner-row"></div>
			</form>
			<button id="add-row" class="btn btn-success btn-md">Add more row</button>
			<br>
			<button id="save-category-filter" class="btn btn-primary btn-md">Save</button>
		</div>
	<?php }  ?>
	<div class="bs-callout bs-callout-info">
		If you want to delete a row, just set it blank then save.
	</div>
</div>

<script type="text/javascript">
	$(document).ready(()=>{
		$('#save-category-filter').click(()=>{
			if(true){
				let arr = $( '#form-category-filter' ).serializeArray();
				let res = '{';
				let error;
				let t1 = $('.c-original').serializeArray();
				let t2 = $('.c-category').serializeArray();
				let total = t1.length;
				for(let i=0; i<total; i++){
					if(t1[i].value && t2[i].value){
						res += '"'+t1[i].value+'"'+': "'+t2[i].value+'",';
					}
				}
				if(res.slice(-1) === ','){
					res = res.slice(0, -1);
				}
				res += '}';
				let x = JSON.parse(res);
				if(res !=  '{}'){
					$.ajax({
						url: "<?php echo DOMAIN ?>content/plugins/category-filter/action.php",
						type: 'POST',
						dataType: 'json',
						data: {action: 'submit', data: JSON.stringify(JSON.parse(res))},
						success: function (data) {
							//console.log(data.responseText);
						},
						error: function (data) {
							//console.log(data.responseText);
						},
						complete: function (data) {
							console.log(data.responseText);
							if(data.responseText === 'ok'){
								$('.section').before('<div class="alert alert-success alert-dismissible fade show" role="alert">Category filter updated<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
							} else {
								alert('Error! Check console log for more info!');
							}
						}
					});
				}
			}
		});
		$('#add-row').click(()=>{
			$('#inner-row').before('<div class="form-group row"><div class="col"><input type="text" class="form-control c-original" name="val" value=""></div><div class="col"><input type="text" class="form-control c-category" name="val" value=""></div></div>');
		});
	});
</script>