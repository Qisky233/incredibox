<?php

if (!has_admin_access()) {
	exit('Access denied');
}

// Get admin users first
$admin_users = User::getListByRole('admin', 'desc');
$crew_users = User::getListByRole('crew', 'desc');

// Get regular users with pagination
$cur_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$regular_users = User::getListByRole('user', 'desc', 30, 30 * ($cur_page - 1));
$total_pages = $regular_users['totalPages'];
?>

<?php

if (isset($_GET['edit']) && isset($_GET['user-id'])) {
	require dirname(__FILE__) . '/page-permissions.php';
} else {

?>

	<div>
		<?php if (USER_ADMIN) { ?>
			<!-- Admins Section -->
			<section class="section">
				<h5>Administrators</h5>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Username</th>
								<th>Email</th>
								<th>Join Date</th>
								<th>XP</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($admin_users['results'])) { ?>
								<?php foreach ($admin_users['results'] as $user) { ?>
									<tr>
										<td><?php echo esc_string($user->username) ?></td>
										<td><?php echo esc_string($user->email) ?></td>
										<td><?php echo date('Y-m-d', strtotime($user->join_date)) ?></td>
										<td><?php echo number_format($user->xp) ?></td>
										<td>
											<button class="btn btn-sm btn-primary edituser" data-id="<?php echo esc_int($user->id) ?>">
												<i class="fa fa-pencil-alt"></i>
											</button>
											<?php if ($user->username != $login_user->username) { ?>
												<?php if (USER_ADMIN) { ?>
													<button class="btn btn-sm btn-warning editrole" data-id="<?php echo esc_int($user->id) ?>" data-username="<?php echo esc_string($user->username) ?>" data-role="<?php echo esc_string($user->role) ?>">
														<i class="fa fa-user-tag" style="color: white;"></i>
													</button>
												<?php } ?>
												<button class="btn btn-sm btn-danger deleteuser" data-id="<?php echo esc_int($user->id) ?>">
													<i class="fa fa-trash"></i>
												</button>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>
							<?php } else { ?>
								<tr>
									<td colspan="5" class="text-center">No administrators found</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</section>

			<!-- Crew Section -->
			<section class="section">
				<h5>Crew Members</h5>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Username</th>
								<th>Email</th>
								<th>Join Date</th>
								<th>XP</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($crew_users['results'])) { ?>
								<?php foreach ($crew_users['results'] as $user) { ?>
									<tr>
										<td><?php echo esc_string($user->username) ?></td>
										<td><?php echo esc_string($user->email) ?></td>
										<td><?php echo date('Y-m-d', strtotime($user->join_date)) ?></td>
										<td><?php echo number_format($user->xp) ?></td>
										<td>
											<button class="btn btn-sm btn-primary edituser" data-id="<?php echo esc_int($user->id) ?>">
												<i class="fa fa-pencil-alt"></i>
											</button>
											<a href="dashboard.php?viewpage=plugin&name=users&edit&user-id=<?php echo esc_int($user->id) ?>">
												<button class="btn btn-sm btn-info permissions" data-id="<?php echo esc_int($user->id) ?>" data-username="<?php echo esc_string($user->username) ?>">
													<i class="fa fa-key" style="color: white;"></i>
												</button>
											</a>
											<?php if (USER_ADMIN) { ?>
												<button class="btn btn-sm btn-warning editrole" data-id="<?php echo esc_int($user->id) ?>" data-username="<?php echo esc_string($user->username) ?>" data-role="<?php echo esc_string($user->role) ?>">
													<i class="fa fa-user-tag" style="color: white;"></i>
												</button>
											<?php } ?>
											<?php if ($user->username != $login_user->username) { ?>
												<button class="btn btn-sm btn-danger deleteuser" data-id="<?php echo esc_int($user->id) ?>">
													<i class="fa fa-trash"></i>
												</button>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>
							<?php } else { ?>
								<tr>
									<td colspan="5" class="text-center">No crew members found</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</section>
		<?php } ?>
		<!-- Regular Users Section -->
		<section class="section">
			<h5>Regular Users</h5>
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>Username</th>
							<th>Email</th>
							<th>Join Date</th>
							<th>XP</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($regular_users['results'])) { ?>
							<?php foreach ($regular_users['results'] as $user) { ?>
								<tr>
									<td><?php echo esc_string($user->username) ?></td>
									<td><?php echo esc_string($user->email) ?></td>
									<td><?php echo date('Y-m-d', strtotime($user->join_date)) ?></td>
									<td><?php echo number_format($user->xp) ?></td>
									<td>
										<button class="btn btn-sm btn-primary edituser" data-id="<?php echo esc_int($user->id) ?>">
											<i class="fa fa-pencil-alt"></i>
										</button>
										<?php if (USER_ADMIN) { ?>
											<button class="btn btn-sm btn-warning editrole" data-id="<?php echo esc_int($user->id) ?>" data-username="<?php echo esc_string($user->username) ?>" data-role="<?php echo esc_string($user->role) ?>">
												<i class="fa fa-user-tag" style="color: white;"></i>
											</button>
										<?php } ?>
										<?php if ($user->username != $login_user->username) { ?>
											<button class="btn btn-sm btn-danger deleteuser" data-id="<?php echo esc_int($user->id) ?>">
												<i class="fa fa-trash"></i>
											</button>
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						<?php } else { ?>
							<tr>
								<td colspan="5" class="text-center">No users found</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>

			<!-- Pagination (only for regular users) -->
			<?php if (!empty($total_pages) && $total_pages > 1) { ?>
				<nav class="mt-3">
					<ul class="pagination justify-content-center">
						<?php for ($i = 1; $i <= $total_pages; $i++) { ?>
							<li class="page-item <?php echo ($i == $cur_page) ? 'active' : '' ?>">
								<a class="page-link" href="<?php echo DOMAIN ?>admin/dashboard.php?viewpage=plugin&name=users&page=<?php echo $i ?>">
									<?php echo $i ?>
								</a>
							</li>
						<?php } ?>
					</ul>
				</nav>
			<?php } ?>
		</section>
	</div>

<?php } ?>

<!-- Edit User Modal -->
<div class="modal fade" id="edit-user" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit User</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<form id="form-edituser">
					<input type="hidden" id="edit-id" name="id">

					<div class="mb-3">
						<label class="form-label">Username</label>
						<input type="text" class="form-control" id="edit-username" name="username" required>
					</div>

					<div class="mb-3">
						<label class="form-label">Email</label>
						<input type="email" class="form-control" id="edit-email" name="email">
					</div>
					<div class="mb-3">
						<label class="form-label">XP</label>
						<input type="number" class="form-control" id="edit-xp" name="xp" min="0" value="0">
					</div>

					<div class="mb-3">
						<label class="form-label">Bio</label>
						<textarea class="form-control" id="edit-bio" name="bio" rows="3"></textarea>
					</div>

					<div class="mb-3">
						<label class="form-label">New Password</label>
						<input type="password" class="form-control" id="edit-pass" autocomplete="off" name="pass">
						<div class="form-text">Leave blank to keep current password</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				<button type="submit" form="form-edituser" class="btn btn-primary">Save Changes</button>
			</div>
		</div>
	</div>
</div>

<?php if(USER_ADMIN){ ?>
	<div class="modal fade" id="edit-role" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-editrole">
                    <input type="hidden" id="role-user-id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" id="role-username" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" id="edit-user-role" name="role" required>
                            <option value="user">Regular User</option>
                            <option value="crew">Crew Member</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="form-editrole" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<?php } ?>

<script>
	$(document).ready(function() {
		// Handle edit user
		$('.edituser').click(function() {
			const userId = $(this).data('id');
			$.ajax({
				url: '<?php echo DOMAIN ?>content/plugins/users/action.php',
				type: 'POST',
				data: {
					action: 'get_user',
					id: userId
				},
				success: function(response) {
					try {
						const data = JSON.parse(response);
						if (data) {
							$('#edit-id').val(data.id);
							$('#edit-username').val(data.username);
							$('#edit-email').val(data.email);
							$('#edit-role').val(data.role);
							$('#edit-bio').val(data.bio);
							$('#edit-xp').val(data.xp);
							$('#edit-user').modal('show');
						}
					} catch (e) {
						alert('Error loading user data');
					}
				}
			});
		});

		// Handle edit form submit
		$('#form-edituser').submit(function(e) {
			e.preventDefault();
			const formData = $(this).serialize() + '&action=update';

			$.ajax({
				url: '<?php echo DOMAIN ?>content/plugins/users/action.php',
				type: 'POST',
				data: formData,
				success: function(response) {
					if (response === 'ok') {
						location.reload();
					} else {
						console.log(response);
						alert('Error updating user');
					}
				}
			});
		});

		// Handle delete user
		$('.deleteuser').click(function() {
			if (confirm('Are you sure you want to delete this user?')) {
				const userId = $(this).data('id');
				window.location.href = '<?php echo DOMAIN ?>content/plugins/users/action.php?action=delete&id=' + userId + '&redirect=<?php echo DOMAIN ?>admin/dashboard.php?viewpage=plugin&name=users';
			}
		});

		// Handle edit role button click
        $('.editrole').click(function() {
            const userId = $(this).data('id');
            const username = $(this).data('username');
            const currentRole = $(this).data('role');
            
            $('#role-user-id').val(userId);
            $('#role-username').val(username);
            $('#edit-user-role').val(currentRole);
            $('#edit-role').modal('show');
        });

        // Handle role edit form submit
        $('#form-editrole').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize() + '&action=update_role';

            $.ajax({
                url: '<?php echo DOMAIN ?>content/plugins/users/action.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response === 'ok') {
                        location.reload();
                    } else {
                        alert('Error updating user role');
                    }
                }
            });
        });
	});
</script>