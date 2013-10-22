<?php

	$default_folder = '';
	
	$q = $_GET['q'];
	
	$q = str_replace($jSep, $fSep, $q);
	
	if($q== '') { $q = getcwd(); }
	// echo '"'.$q.'"';
	
	scan($q);
	
	function dir_prev($dir) {
		if (substr($dir, -1) == '/') {
			$dir = substr($dir, 0, -1);
		}
		
		$pos = strripos($dir, "/");
		
		if ($pos != false) {
			$dir = substr($dir, 0, $pos);
		}
		
		return $dir;
	}
	
	function scan($folder) {
		if ($folder == '/var/www') {
			include('home.php');
			return;
		}
		// echo $folder.'<br />';
		
		//Gestion du répertoire en cours
		// if ($folder == getcwd() || $folder == '') {
			// $_SESSION['dir'] = getcwd();
		// } else {
			// if ($folder == '..') {
				// $_SESSION['dir'] = dir_prev($folder);
			// } else {
				// $_SESSION['dir'] = $_SESSION['dir'].'/'.$folder;
			// }
		// }
		
		// echo $_SESSION['dir'].'<br />';
		
		// $folder = $_SESSION['dir'];
		
		$fSep = '/';
		$jSep = '%2F';
		
		$files = scandir($folder);
		
		echo '<table class="table table-hover table-striped"><thead><tr>';
		echo '<th colspan="2">Name</th>';
		echo '<th class="col-md-1"></th>';
		// echo '<th>Last modified</th>';
		// echo '<th>Size</th>';
		// echo '<th>Type</th>';
		echo '</tr></thead><tbody class="table-condensed">';
		
		foreach ($files as $filename) {
			
			// Extension
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			
			// Type 2 icon
			$type = filetype($filename);
			
			switch ($ext) {
				case 'rar':
				case 'zip':
					$icon = 'compressed';
					break;
				case 'exe':
					$icon = 'cog';
					break;
				case 'php':
					$icon = 'list-alt';
					break;
				case 'txt':
					$icon = 'pencil';
					break;
				case 'mkv':
				case 'avi':
				case 'mp4':
				case 'm2ts':
					$icon = 'film';
					break;
				case 'mp3':
				case 'm4a':
					$icon = 'music';
					break;
				default:
					switch ($type) {
						case 'file':
						case 'dll':
							$icon = 'file';
							break;
						case 'dir':
							$icon = 'folder-open';
							break;
						case 'link':
							$icon = 'link';
							break;
						default:
							$icon = 'folder-open';
					}
			}
			
			if (($ext == '' && $type == '') || $type == 'dir') {
				$icon = 'folder-open';
			}
			
			
			// Date
			$date = '';
			
			if (filemtime($filename)) {
				$date = date ("d/m/Y H:i:s", filemtime($filename));
			}
			
			// Linked action
			if ($icon == 'folder-open' || $icon == 'link') {
				$action = '';
				$no_action = 'colspan="2"';
			} else {
				$action = '<a data-toggle="modal" href="#myModal-load" onclick="get_file_info(\''.str_replace('\'', '%27', $filename).'\');"><span class="glyphicon glyphicon-plus pull-right"></span></a>';
				$no_action = '';
			}
			
			// Onclick
			$url = $folder.$fSep.$filename;
			$jsFunction = 'scan_the_dir(\''.$filename.'\');';
			
			if ($icon == 'folder-open' || $icon == 'link') {
				$a_content = 'href="#" onclick="'.$jsFunction.'"';
			} else {
				$a_content = 'data-toggle="modal" href="#myModal-load" onclick="get_file_info(\''.str_replace('\'', '%27', $filename).'\');"';
				// $a_content = 'href="'.str_replace('/var/www', '', $url).'"';
			}
			
			// Custom icons
			if ($filename == '1080p' || $filename == '720p') {
				$icon = 'hd-video';
			}
			
			if ($filename == 'DVD-rip') {
				$icon = 'sd-video';
			}
			
			// Ecriture
			if ($filename != '..' && $filename != '.') {
				echo '<tr>';
				
				echo '<td class="col-md-1"> <span class="glyphicon glyphicon-'.$icon.'"> </span></td>'; // Nom
				echo '<td '.$no_action.'><a '.$a_content.'>'.$filename.'</a></td>'; 					// Nom
				if ($no_action == '') {
					echo '<td class="col-md-1">'.$action.'</td>';
				}
				
				echo '</tr>';
			}
		}

		echo '</tbody></table>';

	}

?>