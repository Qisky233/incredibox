<?php

?>
<div class="section">
	<div class="bs-callout bs-callout-info">
		Free self-hosted HTML5 games that are already integrated with the CloudArcade API. Unlike fetched games, these game files are hosted or stored on your server. Leaderboard should work with these games.
	</div>
	<div>
		<button class="btn btn-primary btn-md" onclick="loadGameBundle()" id="LOAD-GAME-BUNDLE"><?php _e('Load games') ?></button>
	</div>
	<div class="fetch-list mb-3" style="display: none;" id="fgb-wrapper">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>#</th>
						<th>Thumbnail</th>
						<th>Game name</th>
						<th>Version</th>
						<th>Category</th>
						<th>Demo</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="gameList">
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	var jsonData = null;

	function loadGameBundle() {
		let button = document.getElementById("LOAD-GAME-BUNDLE");
		button.disabled = true; // Disable the button
		button.textContent = "Loading ...";
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200) {
				jsonData = JSON.parse(xhr.responseText);
				if (jsonData.message) {
					alert(jsonData.message);
					console.log(jsonData);
				} else {
					document.getElementById("fgb-wrapper").style.display = "block";
					let htmlList = '';
					let tmplt = '<tr id="tr--index--" data-slug="--slug--"><th scope="row">--index--</th><td><img src="--icon--" width="60px" height="auto" class="gamelist"></td><td>--title--</td><td>--version--</td><td><span class="categories">--category--</span></td><td><a href="--demo--" target="_blank">Play</a></td><td><span class ="actions"><a href="#" onclick="addGameBundle(--id--)"><i class="fa fa-plus circle" aria-hidden="true"></i></a></span></td></tr>';
					for (let i = 0; i < jsonData.length; i++) {
						let strItem = tmplt.replace('--index--', i + 1);
						strItem = strItem.replace('--index--', i + 1);
						strItem = strItem.replace('--icon--', jsonData[i].icon);
						strItem = strItem.replace('--demo--', jsonData[i].demo);
						strItem = strItem.replace('--id--', i + 1);
						strItem = strItem.replace('--category--', jsonData[i].category);
						strItem = strItem.replace('--version--', jsonData[i].version);
						strItem = strItem.replace('--title--', jsonData[i].title);
						strItem = strItem.replace('--slug--', jsonData[i].slug);
						htmlList += strItem;
					}
					document.getElementById("gameList").innerHTML = htmlList;
					button.remove();
					console.log(jsonData);
				}
			}
		};
		xhr.open("GET", "https://api.cloudarcade.net/free-game-bundle/list.php?code=<?php echo check_purchase_code() ?>", true);
		xhr.send();
	}

	// Function to add Bootstrap alert
	function showAlert(message, type = 'success') {
		const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

		// Find alert container or create one if it doesn't exist
		let alertContainer = document.querySelector('.alert-container');
		if (!alertContainer) {
			alertContainer = document.createElement('div');
			alertContainer.className = 'alert-container mb-3';
			const fetchList = document.querySelector('.fetch-list');
			fetchList.parentNode.insertBefore(alertContainer, fetchList);
		}

		// Add new alert
		alertContainer.insertAdjacentHTML('beforeend', alertHtml);

		// Optional: Auto-dismiss after 5 seconds
		setTimeout(() => {
			const alert = alertContainer.querySelector('.alert:last-child');
			if (alert) {
				alert.remove();
			}
		}, 5000);
	}

	function addGameBundle(id) {
		$('.fetch-list').addClass('disabled-list');
		let elem = document.getElementById("tr" + id);

		let formData = new FormData();
		formData.append('action', 'unzip');
		formData.append('slug', elem.dataset.slug);

		fetch("../content/plugins/free-game-bundle/action.php", {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.status === 'ok') {
					showAlert(data.message, 'success');
					_addGameData(id - 1);
					elem.remove();
				} else if (data.status === 'warning') {
					showAlert(data.message, 'warning');
					elem.remove(); // Remove the game from the list since it already exists
				} else if (data.status === 'error') {
					throw new Error(data.message);
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert(error.message); // Keep JS alert for errors
			})
			.finally(() => {
				$('.fetch-list').removeClass('disabled-list');
			});
	}

	function _addGameData(index) {
		if (!jsonData) return;

		const game = jsonData[index];
		const data = {
			title: game.title,
			slug: game.slug,
			description: game.description,
			instructions: game.instructions,
			category: game.category,
			tags: game.tags,
			ref: 'upload',
			source: 'self',
			url: '/games/' + game.slug + '/',
			is_mobile: true,
			published: true,
			width: game.width || 720,
			height: game.height || 1080,
			thumb_1: game.thumb_1 || '/games/' + game.slug + '/thumb_1.png',
			thumb_2: game.thumb_2 || '/games/' + game.slug + '/thumb_2.png',
			action: 'addGame',
			thumb_method: 'upload',
			dont_store_fields: true
		};

		sendRequest(data, false, 'remove', index + 1)
			.then(result => {
				const status = result.slice(0, 5);
				if (status === 'added' || status === 'exist') {
					console.log(result);
				} else {
					console.error(result);
					throw new Error('Error adding game data');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert(error.message);
			});
	}
</script>