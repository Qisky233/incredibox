<?php

$editor_mode = 'php';
$format =  isset($_POST['file_path']) ? pathinfo($_POST['file_path'])['extension'] : 'php';

if($format == 'js'){
	$editor_mode = 'javascript';
} elseif($format == 'css'){
	$editor_mode = 'css';
}

$error = null;
if(isset($_POST['action'])){
	if($_POST['action'] == 'new_page'){
		if(strpos($_POST['page_name'], '.php')){
			$error = 'Don\'t put file format on it, fill page name without ".php"';
		} elseif(file_exists(ABSPATH.TEMPLATE_PATH.'/'.$_POST['page_name'].'.php')){
			$error = 'Page file with name "'.$_POST['page_name'].'.php'.'" already exist!';
		}
		if(substr($_POST['page_name'], 0, 5) != 'page-'){
			$error = 'Page name not valid, page name must have "page-" identifier';
		}
		if(!$error){
			file_put_contents(ABSPATH.TEMPLATE_PATH.'/'.$_POST['page_name'].'.php', '');
			$_POST['action'] = 'edit';
			$_POST['file_path'] = $_POST['page_name'].'.php';
			show_alert('Page created!', 'success');
		}
	} elseif($_POST['action'] == 'delete'){
		if(file_exists(ABSPATH.TEMPLATE_PATH.'/'.$_POST['file_path'])){
			unlink('../'.TEMPLATE_PATH.'/'.$_POST['file_path']);
			unset($_POST['file_path']);
			show_alert('Page deleted!', 'success');
		} else {
			$error = 'File not exist!';
		}
	} elseif($_POST['action'] == 'update_name'){
		if(strlen($_POST['new_name']) >= 1){
			if (preg_match('/[\\/:"*?<>|]+/', $_POST['new_name'])) {
				$error = "Invalid filename";
			} else {
				if(file_exists('../'.TEMPLATE_PATH.'/page-'.$_POST['new_name'].'.php')){
					$error = 'Filename already exist!';
				} else {
					if(rename('../'.TEMPLATE_PATH.'/'.$_POST['file_path'], '../'.TEMPLATE_PATH.'/page-'.$_POST['new_name'].'.php')){
						show_alert('Page name updated!', 'success');
					} else {
						$error = 'Failed to rename file.';
					}
				}
			}
		} else {
			$error = 'File name is empty!';
		}
	}
}

if($error){
	show_alert($error, 'warning');
}

$file_list = array();
$files = scan_files(TEMPLATE_PATH);
foreach ($files as $file) {
	$file_format = pathinfo($file)['extension'];
	if($file_format == 'php'){
		$file_path = str_replace(TEMPLATE_PATH,'',$file);
		if(substr($file_path, 1, 5) == 'page-'){
			$file_list[] = substr($file_path, 1);
		}
	}
}

?>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/codemirror.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/theme/ayu-mirage.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/codemirror.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/addon/edit/matchbrackets.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/mode/htmlmixed/htmlmixed.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/mode/xml/xml.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/mode/javascript/javascript.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/mode/css/css.js"></script>
<?php

if($format == 'php'){
	?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/mode/clike/clike.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/mode/php/php.js"></script>
	<?php
}

?>
<div class="section">
	<p>Current theme: <b><?php echo THEME_NAME ?></b></p>
	<?php if(count($file_list)){ ?>
	<form action="dashboard.php?viewpage=plugin&name=custom-pages" method="post" enctype="multipart/form-data">
		<div class="mb-3">
			<label class="form-label" for="file_path">Select page file:</label>
			<input type="hidden" name="action" value="edit">
			<select class="form-control" name="file_path" id="file_path">
				<?php
				$current = isset($_POST['file_path']) ? $_POST['file_path'] : '';
				foreach ($file_list as $file) {
					$selected = '';
					if($current == $file){
						$selected = 'selected';
					}
					echo('<option value="'.$file.'" '.$selected.'>'.$file.'</option>');
				}

				?>
			</select>
		</div>
		<button type="submit" class="btn btn-primary btn-md">Edit</button>
	</form>
	<hr>
	<?php } ?>
	<?php
	if(isset($_POST['file_path'])){
		if(file_exists(ABSPATH.TEMPLATE_PATH.'/'.$_POST['file_path'])){
			if(isset($_POST['action']) && $_POST['action'] != 'delete'){
				if(isset($_POST['content']) && $_POST['action'] == 'update_file'){
					file_put_contents(ABSPATH.TEMPLATE_PATH.'/'.$_POST['file_path'], $_POST['content']);
					show_alert('Page saved!', 'success');
				}
				?>
				<br>
				<form action="dashboard.php?viewpage=plugin&name=custom-pages" method="post" enctype="multipart/form-data">
					<input type="hidden" name="action" value="update_file">
					<input type="hidden" name="file_path" value="<?php echo $_POST['file_path'] ?>">
					<div class="mb-3">
						<textarea class="form-control" id="editor" name="content" rows="5"><?php echo file_get_contents(ABSPATH.TEMPLATE_PATH.'/'.$_POST['file_path']) ?></textarea>
					</div>
					<button type="submit" class="btn btn-primary btn-md">Save</button>
				</form>
				<div class="mb-3"></div>
				<a href="<?php echo get_permalink(str_replace(array('page-', '.php'), '', $_POST['file_path'])) ?>" target="_blank"><button class="btn btn-secondary btn-md">Visit page</button></a>
				<div class="mb-3"></div>
				<form method="post" enctype="multipart/form-data">
					<input type="hidden" name="action" value="update_name">
					<input type="hidden" name="file_path" value="<?php echo $_POST['file_path'] ?>">
					<div class="mb-3">
						<input type="text" class="form-control" name="new_name" minlength="1" value="<?php echo str_replace('page-', '', pathinfo($_POST['file_path'], PATHINFO_FILENAME)) ?>">
					</div>
					<button type="submit" class="btn btn-primary btn-md">Rename</button>
				</form>
				<div class="mb-5"></div>
				<form action="dashboard.php?viewpage=plugin&name=custom-pages" method="post" enctype="multipart/form-data" id="form-delete-page">
					<input type="hidden" name="action" value="delete">
					<input type="hidden" name="file_path" value="<?php echo $_POST['file_path'] ?>">
					<button type="submit" class="btn btn-danger btn-md">Delete</button>
				</form>
				<?php
			}
		}
	} else {
		?>
		<br>
		<form action="dashboard.php?viewpage=plugin&name=custom-pages" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="new_page">
			<div class="mb-3">
				<label class="form-label" for="page_name">Page name:</label>
				<input type="text" class="form-control" name="page_name" value="page-yourpage">
			</div>
			<button type="submit" class="btn btn-primary btn-md">Create page</button>
		</form>
		<?php
	}

	?>
</div>
<script type="text/javascript">
	var rm_page_accepted = false;
	$(document).ready(function(){
		$( "form" ).submit(function( event ) {
			if($(this).attr('id') === 'form-delete-page'){
				if(!rm_page_accepted){
					event.preventDefault();
					if(confirm('Are you sure want to delete this page file ?')){
						rm_page_accepted = true;
						$("#form-delete-page").submit();
					}
				}	
			}
		});
		
		if($("#editor").length){
			var cm = new CodeMirror.fromTextArea(document.getElementById("editor"), {
				lineNumbers: true, 
				mode: "<?php echo $editor_mode ?>",
				theme: "ayu-mirage",
				styleActiveLine: true,
				height: 20,
				matchBrackets: true,
				startOpen: true
			});
			cm.setSize(null, 600);
		}
	});
</script>