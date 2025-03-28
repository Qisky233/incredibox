<?php
require(dirname(__FILE__) . '/../../classes/Collection.php');

if (isset($_GET['status'])) {
	$type = 'success';
	$message = '';
	if ($_GET['status'] == 'added') {
		$message = 'New collection added!';
	} elseif ($_GET['status'] == 'exist') {
		$type = 'warning';
		$message = 'Collection already exist!';
	} elseif ($_GET['status'] == 'deleted') {
		$type = 'warning';
		$message = 'Collection deleted!';
	} elseif ($_GET['status'] == 'updated') {
		$message = 'Collection updated!';
		if (isset($_GET['info'])) {
			$message = $message . ' ' . $_GET['info'];
		}
	}
	show_alert($message, $type);
}

$can_add_collection = $login_user->hasAccess('collections', 'add');
$can_edit_collection = $login_user->hasAccess('collections', 'edit');
$can_delete_collection = $login_user->hasAccess('collections', 'delete');

?>
<div class="row">
	<div class="col-lg-8">
		<div class="section">
			<ul class="collection-list">
				<?php
				$results = array();
				$data = Collection::getList();
				if ($data['totalRows'] > 0) {
					$collections = $data['results'];
					foreach ($collections as $item) {
						echo '<li class="collection-item d-flex align-items-center">';
						echo esc_string($item->name);
						echo '<div style="margin-left: auto;">';
						echo '<span class="actions">';
						echo '<a class="text-success" href="'.get_permalink('collection', $item->slug).'" target="_blank"><i class="fa fa-external-link-alt circle" aria-hidden="true"></i></a>';
						if($can_edit_collection){
							echo '<a class="editcollection" href="#" id="' . esc_int($item->id) . '"><i class="fa fa-pencil-alt circle" aria-hidden="true"></i></a>';
						}
						if($can_delete_collection){
							echo '<a class="remove-collection text-danger" href="#" id="' . esc_int($item->id) . '"><i class="fa fa-trash circle" aria-hidden="true"></i></a>';
						}
						echo '</span>';
						echo '</div></li>';
					}
				} else {
					_e('No collections found!');
				}
				?>
			</ul>
		</div>
	</div>
	<?php if($can_add_collection){ ?>
	<div class="col-lg-4">
		<div class="section">
			<form id="form-newcollection" action="request.php" method="post">
				<input type="hidden" name="action" value="newCollection">
				<input type="hidden" name="redirect" value="<?php echo DOMAIN ?>admin/dashboard.php?viewpage=collections">
				<div class="mb-3">
					<label for="collection"><?php _e('Add new collection') ?>:</label>
					<input type="text" class="form-control" name="name" placeholder="Name" value="" minlength="2" maxlength="15" required>
				</div>
				<div class="mb-3">
					<label for="data">Game ids, separated by commas:</label>
					<input type="text" class="form-control" name="data" placeholder="2,4,11,12,23" value="" minlength="2" required>
				</div>
				<button type="submit" class="btn btn-primary btn-md"><?php _e('Add') ?></button>
			</form>
		</div>
	</div>
	<?php } ?>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-collection" tabindex="-1" role="dialog" aria-labelledby="edit-collection-modal-label" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="edit-collection-label"><?php _e('Edit collection') ?></h5>
				<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="form-editcollection" action="request.php" method="post">
					<input type="hidden" name="action" value="editCollection">
					<input type="hidden" name="redirect" value="<?php echo DOMAIN ?>admin/dashboard.php?viewpage=collections">
					<input type="hidden" id="edit-id" name="id" value="" />
					<div class="mb-3">
						<label class="form-label"><?php _e('Collection name / title') ?>:</label>
						<input type="text" class="form-control" id="edit-name" name="name" autocomplete="off" placeholder="Name of the collection" required minlength="2" maxlength="255" value="" />
					</div>
					<div class="mb-3">
						<label class="form-label"><?php _e('Collection slug') ?>:</label>
						<input type="text" class="form-control" id="edit-slug" name="slug" autocomplete="off" placeholder="collection-name" minlength="2" maxlength="255" value="" />
					</div>
					<div class="mb-3">
						<label for="data" class="form-label"><?php _e('Game ids') ?>:</label>
						<textarea class="form-control" name="data" id="edit-data" rows="3" placeholder="2,4,11,12,23" value="" minlength="2" required maxlength="10000"></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label"><?php _e('Description') ?>:</label>
						<textarea class="form-control" name="description" id="edit-description" rows="3" placeholder="" value="" maxlength="1000"></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label">Game list:</label>
						<select multiple class="form-control" id="collection-game-list" readonly="readonly">
						</select>
					</div>
					<div class="mb-3">
						<input id="edit-allow_dedicated_page" class="edit-allow_dedicated_page" name="allow_dedicated_page" type="checkbox">
						<label class="form-label" for="edit-allow_dedicated_page">Allow dedicated page</label><br>
					</div>
					<input type="submit" class="btn btn-primary" value="<?php _e('Save changes') ?>" />
					<input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="<?php _e('Close') ?>" />
				</form>
			</div>
		</div>
	</div>
</div>