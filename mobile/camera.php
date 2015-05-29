<?php
	require_once('config.php');
	$message = '';
	if (isset($_POST['upload_photo'])) {
		if ($_FILES['image']){
			$uploads = '/home/mark/public_html/mobile/uploads';
			$filename = rand().'-'.$_FILES['image']['name'];
			$tmp_name = $_FILES['image']['tmp_name'];
			if (move_uploaded_file($tmp_name, "$uploads/$filename")) {
				$name = '';
				$notes = '';
				$ip = $_SERVER['REMOTE_ADDR'];
				$filename = mysql_escape_string($filename);
				if (!empty($_POST['name'])) $name = mysql_escape_string($_POST['name']);
				if (!empty($_POST['notes'])) $notes = mysql_escape_string($_POST['notes']);
				if (mysql_query("INSERT INTO `uploads` (`filename`, `name`, `notes`, `ip`) VALUES ('$filename', '$name', '$notes', '$ip')")) {
					$message = '<h3>Thanks for your photo!</h3>';
					$fh = fopen('/home/mark/public_html/mobile/log.txt', 'a'); fputs($fh, date('Y-m-d H:i:s')." Camera upload: $name | $notes | $filename\n"); fclose($fh);
				} else {
					$message = '<h3 class="error">There was a database error, please try again.</h3>'.mysql_error();
					$fh = fopen('/home/mark/public_html/mobile/log.txt', 'a'); fputs($fh, date('Y-m-d H:i:s')." Camera error: $name | $notes | $filename".mysql_error()."\n"); fclose($fh);
				}
			} else {
				$message = '<h3 class="error">There was a file error, please try again.</h3>';
				$fh = fopen('/home/mark/public_html/mobile/log.txt', 'a'); fputs($fh, date('Y-m-d H:i:s')." Camera error: $name | $notes | $filename\n"); fclose($fh);
			}
		}
	}
?>
<?php include('includes/header.php'); ?>
		<style>
			#preview {
				width: 100%; max-width: 300px;
			}
			#preview img {
				width: 100%;
			}
			.hiddenfile {
			 width: 0px;
			 height: 0px;
			 overflow: hidden;
			}
		</style>
		<div data-role="content">
			<?= $message ?>
			<form method="post" action="camera.php" data-ajax="false" enctype="multipart/form-data">
				<button id="chooseFile">Choose file</button>
				<div class="hiddenfile">
					<input type="file"  data-clear-btn="false" name="image" accept="image/*" capture>
				</div>
				<div id="preview"></div>
				<div data-role="fieldcontain">
					<label for="name">Your name:</label>
					<input type="text" name="name" id="name" value="" maxlength="30"  />
					<label for="notes">Notes:</label>
					<textarea cols="40" rows="8" name="notes" id="notes"></textarea>
				</div>
				<input type="submit" name="upload_photo" value="Upload" data-theme="b">
			</form>
		</div>

    <script>
    $('#main').on('pageinit', function(){
		$("#chooseFile").click(function(e){
			e.preventDefault();
			$("input[type=file]").trigger("click");
		});
		$("input[type=file]").change(function(){
			var file = $("input[type=file]")[0].files[0];            
			$("#preview").empty();
			displayAsImage3(file, "preview");
		});
    });

    function displayAsImage3(file, containerid) {
		if (typeof FileReader !== "undefined") {
			var container = document.getElementById(containerid),
			    img = document.createElement("img"),
			    reader;
			container.appendChild(img);
			reader = new FileReader();
			reader.onload = (function (theImg) {
				return function (evt) {
					theImg.src = evt.target.result;
				};
			}(img));
			reader.readAsDataURL(file);
		}
	}
	
    </script>
<?php include('includes/footer.php'); ?>
