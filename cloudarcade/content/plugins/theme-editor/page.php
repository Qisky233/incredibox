<?php

$hightlight_files = ['custom.php', 'custom.js', 'custom.css'];
$file_list = array();
$files = scan_files(TEMPLATE_PATH);
foreach ($files as $file) {
	$format = pathinfo($file)['extension'];
	if($format == 'php' || $format == 'html' || $format == 'js' || $format == 'css' || $format == 'json'){
		if(!strpos($file, '.min.')){
			$file_list[] = str_replace(TEMPLATE_PATH,'',$file);
		}
	}
}
$editor_mode = 'application/x-httpd-php';
$format =  isset($_POST['file_path']) ? pathinfo($_POST['file_path'])['extension'] : 'php';

if($format == 'js'){
	$editor_mode = 'javascript';
} elseif($format == 'css'){
	$editor_mode = 'css';
}

$current = isset($_POST['file_path']) ? $_POST['file_path'] : '';
if($current == ''){
	$current = isset($_GET['edit']) ? $_GET['edit'] : '';
}

function printTree($tree, $isRoot = false) {
	global $current;
	global $hightlight_files;
	$folders = [];
	$files = [];

	// Separate folders and files
	foreach ($tree as $name => $subtree) {
		if (is_array($subtree)) {
			$folders[$name] = $subtree;
		} else {
			$files[$name] = $subtree;
		}
	}

	// Sort folders and files
	ksort($folders);
	ksort($files);

	// Print folders first
	foreach ($folders as $name => $subtree) {
		if (!$isRoot) echo '<div class="folder">';
		echo '<span class="folder-icon">📁</span> ' . basename($name);
		printTree($subtree);
		if (!$isRoot) echo '</div>';
	}

	// Then print files
	foreach ($files as $name => $subtree) {
		$selected = $subtree === $current ? 'style="background-color:#878ca6;"' : '';
		echo '<div class="file '.(in_array(basename($subtree), $hightlight_files) ? 'text-success' : '').'" ' . $selected . '><span class="file-icon">📄</span> <a class="file-link" href="dashboard.php?viewpage=plugin&name=theme-editor&edit='.$subtree.'">' . basename($subtree) . '</a></div>';
	}
}


// Organize the list into a hierarchical structure
$tree = [];
$_tree = [];
foreach ($file_list as $file) {
	$parts = explode('\\', trim($file, '\\'));
	$leaf = &$_tree;
	foreach ($parts as $part) {
		if (!isset($leaf[$part])) {
			$leaf[$part] = [];
		}
		$leaf = &$leaf[$part];
	}
	$leaf = $file;
}

$tree = array(
	THEME_NAME => $_tree
);

if(isset($_POST['action']) && isset($_POST['content']) && $_POST['action'] == 'update_file'){
	file_put_contents(ABSPATH.TEMPLATE_PATH.$_POST['file_path'], $_POST['content']);
	show_alert('File updated!', 'success');
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
<style type="text/css">
	.file-tree {
		overflow-x: auto;
		white-space: nowrap;
		padding-bottom: 20px;
	}
	.folder, .file {
		position: relative;
		padding-left: 20px;
	}
	.folder:before, .file:before {
		content: "";
		position: absolute;
		top: 0;
		left: 10px;
		border-left: 1px solid #636c99;
		height: 100%;
	}
	.folder:after, .file:after {
		content: "";
		position: absolute;
		top: 12px;
		left: 10px;
		border-top: 1px solid #636c99;
		width: 10px;
	}
	.folder-icon, .file-icon {
		position: relative;
		z-index: 1;
	}
	.folder:last-child:before, .file:last-child:before {
		height: 12px;
	}
	.root-folder:before {
		display: none;
	}
	.file-tree a.file-link {
		color: unset;
	}
	.CodeMirror-scroll {
		overflow-y: scroll !important;
		overflow-x: scroll !important;  /* add this if you also want the horizontal scrollbar to always appear */
	}
</style>
<?php

if($format == 'php'){
	?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/mode/clike/clike.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.1/mode/php/php.js"></script>
	<?php
}

?>
<div class="section">
	<div class="row">
		<div class="col-md-9">
			<?php
			if(isset($_GET['edit'])){
				$_GET['edit'] = str_replace('..', '', htmlspecialchars($_GET['edit']));
				$file_to_edit = ABSPATH . TEMPLATE_PATH . $_GET['edit'];
				if(file_exists($file_to_edit)){
					?>
					<div class="mb-3">
						<h5>File: <?php echo $_GET['edit'] ?></h5>
					</div>
					<form action="dashboard.php?viewpage=plugin&name=theme-editor&edit=<?php echo $_GET['edit'] ?>" method="post" enctype="multipart/form-data">
						<input type="hidden" name="action" value="update_file">
						<input type="hidden" name="file_path" value="<?php echo $_GET['edit'] ?>">
						<div class="mb-3">
							<textarea class="form-control" id="editor" name="content" rows="7"><?php echo htmlspecialchars(file_get_contents($file_to_edit)) ?></textarea>
						</div>
						<button type="submit" class="btn btn-primary btn-md">Save</button>
					</form>
					<?php
				} else {
					show_alert("File not exist!", 'warning');
				}
			} else {
			?>
			<div class="bs-callout bs-callout-warning">
				Note: All theme files can be overridden during an update, except custom.css, custom.js, and custom.php.
			</div>
			<div class="bs-callout bs-callout-info">
				Custom themes will never be overridden.
			</div>
			<div class="bs-callout bs-callout-info">
				Click file name in the right to start editing theme code.
			</div>
			<?php } ?>
		</div>
		<div class="col-md-3">
			<p>Current active theme</p>
			<?php
			echo '<div class="file-tree">';
			printTree($tree, true);
			echo '</div>';
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		if($("#editor").length){
			var cm = new CodeMirror.fromTextArea(document.getElementById("editor"), {
				lineNumbers: true, 
				mode: "<?php echo $editor_mode ?>",
				theme: "ayu-mirage",
				styleActiveLine: true,
				height: 20,
    			matchBrackets: true,
    			lineWrapping: false,
    			tabSize: 4,
    			indentUnit: 4,
    			lineWrapping: false,
    			startOpen: true
			});
			cm.setSize(null, 700);
		}
	});
</script>