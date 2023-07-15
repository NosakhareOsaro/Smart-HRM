<?php

$response = [
	'status' => false,
	'message' =>  'Unknown request.',
	'data' => null
];


if (isset($_POST['action']) && $_POST['action'] == 'COVER_ART_UPLOAD')
{
	if (csrf_verify('post') === true) {
		
		$cover_art_file		= (isset($_FILES['cover_art_file'])) ? $_FILES['cover_art_file'] : null;
		
		$project =  new Project;
		$project_data = $project->get($project_id);
		
		if (is_array($cover_art_file) && isset($cover_art_file['error']) && $cover_art_file['error'] == 0) {
			
			$accept_filetype = ['image/jpeg', 'image/jpg', 'image/png'];
			
			if(!in_array($cover_art_file["type"], $accept_filetype)){
				$response = [ 'status' => false, 'message' =>  'Please upload a JPEG or PNG Image.', 'data' => null ];
			} elseif( $cover_art_file['size'] > 30000000) { // in bytes = 30mb
				$response = [ 'status' => false, 'message' =>  'Image file size too large, please upload a maximum of 12MB file.', 'data' => null ];
			} elseif ($project_data == false || (isset($project_data['user_id']) && $project_data['user_id'] != $active_user_data['id'])){
				$response = [ 'status' => false, 'message' =>  'Page expired! Please reload page and try again.', 'data' => null ];
			} else {
				
				//get uploads dir value from config
				$uploads_dir = $config['uploads_dir'];
				$uploads_cover_arts_dir = $config['uploads_cover_arts_dir'];
				
				// create the file name
				$name_arr = explode(".", $cover_art_file["name"]);
				$file_ext = end($name_arr);
				$the_name = $name_arr[0];
				$new_name = $the_name . md5(time());
				$cover_art_name = create_slug($new_name) . "." . $file_ext;	
				$cover_art_name = strtolower($cover_art_name);
				
				// create the file and path 
				$dynamic_directory 	= date("Y-m-d");				
				$cover_art_file_path = $dynamic_directory. '/' . $cover_art_name;
				$destination_sub_path 	= $uploads_cover_arts_dir . $dynamic_directory;					
				$upload_cover_art_destination = $destination_sub_path. '/' . $cover_art_name;

				// check and create upload directory if not in existence
				if (! file_exists($uploads_dir)) {
					mkdir($uploads_dir); 
				}
				if (! file_exists($uploads_cover_arts_dir)) {
					mkdir($uploads_cover_arts_dir); 
				}
				if (! file_exists($destination_sub_path)) {
					mkdir($destination_sub_path); 
				}
			
				if (move_uploaded_file($cover_art_file['tmp_name'], $upload_cover_art_destination)) {
					
					$existing_file_path = $project_data['cover_art'];
					
					$update_data = [
						'cover_art' => $cover_art_file_path
					];					
					$update = $project->updateProject('id', $project_id, $update_data);
					
					if ($update === true) {
						//delete previously uploaded /existing file if any
						if ($existing_file_path != null) {
							unlink($uploads_cover_arts_dir . $existing_file_path);
						}
						
						$cover_art_url = base_url($upload_cover_art_destination);
						
						$response = [
							'status' => true,
							'message' =>  'Cover art uploaded successfully.',
							'data' => $cover_art_url
						];
					} else {
						//delete already upload file
						unlink($upload_cover_art_destination);
		
						$response = [ 'status' => false, 'message' =>  'Something went wrong, please re-upload...', 'data' => null ];
					}

				} else {
					$response = [ 'status' => false, 'message' =>  'Something went wrong, track file not uploaded.', 'data' => null ];
				}
			
				
			}
		} else {
			$response = [ 'status' => false, 'message' =>  'Cover art not uploaded, please try again.', 'data' => null ];
		}
	} else {
		$response = [
			'status' => false,
			'message' =>  'Form expired, please try again or reload page.',
			'data' => null
		];
	}
}	


if(isset($_POST['action']) && $_POST['action'] == 'UPDATE_PROJECT' ){
	//print_r($_POST);
	if(csrf_verify('post') === true ) {
		
		$title	= (isset($_POST['title'])) ? trim($_POST['title']) : null;
		//$type = (isset($_POST['type'])) ? trim($_POST['type']) : null;
		$release_label = (isset($_POST['release_label'])) ? trim($_POST['release_label']) : null;
		$release_date = (isset($_POST['release_date'])) ? trim($_POST['release_date']) : null;
		$right_owner_name = (isset($_POST['right_owner_name'])) ? trim($_POST['right_owner_name']) : null;
		$genre 		= (isset($_POST['genre'])) ? trim($_POST['genre']) : null;
		$description 		= (isset($_POST['description'])) ? trim($_POST['description']) : null;
		$pre_order 		= (isset($_POST['pre_order'])) ? trim($_POST['pre_order']) : null;
		$spotify_playlisting = (isset($_POST['spotify_playlisting'])) ? trim($_POST['spotify_playlisting']) : null;
		$playlists_pitching = (isset($_POST['playlists_pitching'])) ? trim($_POST['playlists_pitching']) : null;
		$main_artiste 	= (isset($_POST['main_artiste'])) ? $_POST['main_artiste'] : null; // form will send this as array bcoz the input name="featured_artiste[]"
		$price_category	= (isset($_POST['price_category'])) ? trim($_POST['price_category']) : null;
		
		if (is_array($main_artiste)) {
			$main_artiste = implode(",", $main_artiste); // convert the array values into comma-separated string
		} else {
			$main_artiste =  null;
		}
		
		$project =  new Project;
		$project_data = $project->get($project_id);
		
		if( empty($title) || empty($main_artiste) || empty($release_label) || empty($release_date) || empty($right_owner_name) || empty($genre) || empty($price_category) ) {
			$response = [ 'status' => false, 'message' =>  'Please fill/complete the required form fields.', 'data' => null ];
		}elseif ($project_data == false || (isset($project_data['user_id']) && $project_data['user_id'] != $active_user_data['id'])){
				$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => null ];
		} else {
			
			if ($release_label == $config['default_custom_label']) {
				$release_label = null;
				$has_custom_label = 0;
			} else {
				$has_custom_label = 1;
			}
			
			$project = new Project;			
			$update_data = [
			    'playlists_pitching' => $playlists_pitching,
			    'spotify_playlisting' => $spotify_playlisting,
			    'pre_order' => $pre_order,
				'title' => $title,
				'release_label' => $release_label,
				'has_custom_label' => $has_custom_label,
				'release_date' => $release_date,
				'main_artiste'	=> $main_artiste,
				'right_owner_name' => $right_owner_name,
				'genre' => $genre,
				'description' => $description,
				'price_category' => $price_category
			];
			
			$update = $project->updateProject('id', $project_id, $update_data);
			
			if ($update === true) {
				$response = [
					'status' => true,
					'message' =>  'Project updated successfully.',
					'data' => $project_data
				];
			} else {
				$response = [ 'status' => true, 'message' =>  'No changes was made.', 'data' => $project_data ];
			}
		}			
	
	}
}



if (isset($_POST['action']) && $_POST['action'] == 'ADD_TRACK')
{	
	if (csrf_verify('post') === true) {
		
		$project_id 		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$track_title 		= (isset($_POST['track_title'])) ? trim($_POST['track_title']) : null;		
		$track_genre 		= (isset($_POST['track_genre'])) ? trim($_POST['track_genre']) : null;		
		$producer_name 		= (isset($_POST['producer_name'])) ? trim($_POST['producer_name']) : null;		
		$composer_name 		= (isset($_POST['composer_name'])) ? trim($_POST['composer_name']) : null;		
		$lyrics 			= (isset($_POST['lyrics'])) ? trim($_POST['lyrics']) : null;		
		$is_explicit 		= (isset($_POST['is_explicit'])) ? trim($_POST['is_explicit']) : null;
		$track_pitch 		= (isset($_POST['track_pitch'])) ? trim($_POST['track_pitch']) : null;
		$track_file 		= (isset($_FILES['track_file'])) ? $_FILES['track_file'] : null;
		$featured_artiste 	= (isset($_POST['featured_artiste'])) ? $_POST['featured_artiste'] : null; // form will send this as array bcoz the input name="featured_artiste[]"
		
		if (is_array($featured_artiste)) {
			$featured_artiste = implode(", ", $featured_artiste); // convert the array values into comma-separated string
		} else {
			$featured_artiste =  null;
		}
	
		$project =  new Project;
		$project_data = $project->get($project_id);	
		
		$accept_filetype = [ 'audio/mpeg', 'audio/mp3', 'audio/x-aiff', 'audio/basic', 'audio/ogg', 'audio/flac', 'audio/mpeg', 'audio/mpeg3', 'audio/x-mpeg', 'audio/x-mp3', 'audio/x-mpeg3', 'audio/x-mpg', 'audio/x-mpegaudio', 'audio/x-mpeg-3', 'audio/aac', 'audio/m4a', 'audio/aacp','audio/wma','audio/wav', 'audio/mpeg2', 'audio/x-mpeg2', 'audio/x-aac','video/mpeg', ];

		if ($track_file !== null && isset($track_file['error']) && $track_file['error'] == 0) {
			if (empty($track_title) || empty($track_genre) || (empty($is_explicit) && !is_numeric($is_explicit)) )  {
				$response = [ 'status' => false, 'message' =>  'Please fill/complete the required form fields.', 'data' => null ];
			} elseif( $track_file['size'] > 75000000) { // in bytes = 75mb 
				$response = [ 'status' => false, 'message' =>  'File size too large, please upload a maximum of 75MB file.', 'data' => null ];
			} elseif( ! in_array($track_file['type'], $accept_filetype) ) {
				$response = [ 'status' => false, 'message' =>  'Error (1) Please properly rename your audio file and/or upload an mp3 file format instead.', 'data' => null ];
			} elseif ($project_data == false || (isset($project_data['user_id']) && $project_data['user_id'] != $active_user_data['id'])){
				$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => null ];
			} else {
				
				//get uploads dir value from config
				$uploads_dir = $config['uploads_dir'];
				$uploads_tracks_dir = $config['uploads_tracks_dir'];
				
				// create the file name
				$name_arr = explode(".", $track_file["name"]);
				$file_ext = end($name_arr);
				$the_name = $name_arr[0];
				$new_name = $the_name . md5(time());
				$track_file_name = create_slug($new_name) . "." . $file_ext;	
				$track_file_name = strtolower($track_file_name);
				
				// create the file and path 
				$dynamic_directory 	= date("Y-m-d");				
				$track_file_path = $dynamic_directory. '/' . $track_file_name;
				$destination_sub_path 	= $uploads_tracks_dir . $dynamic_directory;					
				$upload_destination = $destination_sub_path. '/' . $track_file_name;

				// check and create upload directory if not in existence
				if (! file_exists($uploads_dir)) {
					mkdir($uploads_dir); 
				}
				if (! file_exists($uploads_tracks_dir)) {
					mkdir($uploads_tracks_dir); 
				}
				if (! file_exists($destination_sub_path)) {
					mkdir($destination_sub_path); 
				}
										
				if (move_uploaded_file($track_file['tmp_name'], $upload_destination)) {
					
					$project = new Project;
					$track = $project->createTrack($project_id, $track_title, $featured_artiste, $track_genre, $lyrics, $producer_name, $composer_name, $is_explicit, $track_pitch, $track_file_path);
					$project_data = $project->get($project_id);
					
					if ($track !== false && is_numeric($track)) {
						$response = [
							'status' => true,
							'message' =>  'Track uploaded successfully.',
							'data' => $project_data
						];
					} else {
						//delete already upload file
						unlink($upload_destination);
						
						$response = [ 'status' => false, 'message' =>  'Something went wrong, please re-upload.', 'data' => $track ];
					}
					
				} else {
					$response = [ 'status' => false, 'message' =>  'Something went wrong, track file not uploaded.', 'data' => null ];
				}				
			}
			
		} else {
			$response = [ 'status' => false, 'message' =>  'Track file not uploaded.', 'data' => null ];
		}		
	} else {
		$response = [
			'status' => false,
			'message' =>  'Form expired, please try again or reload page.',
			'data' => null
		];
	}
	
}

			
if(isset($_GET['action']) && $_GET['action'] == 'GET_TRACKS' ){
	
	if(csrf_verify('get') === true ) {
		
		$project =  new Project;
		$project_tracks = $project->getTracks($project_id);
		
		$active_page_filename= base_url("project/$project_id/manage");
												
		
		if (isset($project_tracks) && $project_tracks !== false && is_array($project_tracks)){
			$counter = 0;
			foreach($project_tracks as $tracks)
			{
				$counter++;
				$project =  new Project;
				$project_data = $project->get($project_id);	
				$track_id =$tracks['id'];
				
					if ($tracks[track_pitch] != 1){
					    
				}else{
				    $playlisted = 1;
					    $update_data = [
					        'playlisted_track' => $tracks['title'],
					        'playlisted' => $playlisted
					        ];
					    $project->updateProject('id', $project_id, $update_data);
				}
				
				echo "<ul class=\"list-group list-group-flush\">";
						echo "<li class=\"list-group-item \">";
							echo "<div class=\"row\">";
								echo "<div class=\"col-md-6 font-weight-bold\">";
											echo $counter . ".&nbsp;&nbsp;<i class=\"fa fa-headphones fa-lg\"></i>&nbsp&nbsp"; 
													echo $tracks['title'];
											echo"</div>";
								echo "<div class=\"col-md-6 text-center\">";
											echo"<span class=\" m-3\">";
												echo "<a href=\"\" class=\"edit-track text-primary \" data-track-id=\"$track_id\">";
												echo"<i class=\"fa fa-edit\"></i>&nbspEdit</a>";
											echo"</span>";
											echo"<span class=\" m-3\">";
												echo "<a href=\"#\" class=\"delete-track text-danger\" data-track-id=\"$track_id\" data-track-counter=\"$counter\"><i class=\"fa fa-remove\"></i>&nbspDelete</a>";
											echo"</span>";
								echo"</div>";
							echo"</div>";
						echo "</li>";
				echo "</ul>";
			}
		} else {
			echo "No track(s) added yet.";
		}		
		exit;
	}
}



if (isset($_GET['action']) && $_GET['action'] == 'GET_A_TRACK')
{
	if (csrf_verify('get') === true) {
		
		$project_id 	= $project_id;
		$track_id		= (isset($_GET['track_id'])) ? trim($_GET['track_id']) : null;

		$project 		=  new Project;
		$project_data 	= $project->get($project_id);	
		$track_data 	= $project->getTrackById($track_id);
		
		if ($project_data == false || (isset($project_data['user_id']) && $project_data['user_id'] != $active_user_data['id'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => null ];
		} elseif ($track_data == false || (isset($track_data['project_id']) && $track_data['project_id'] != $project_id)){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload...', 'data' => null ];
		} else {
			$response = [ 'status' => true, 'message' =>  'Request succesful.', 'data' => $track_data ];
		}			
	} else {
		$response = [
			'status' => false,
			'message' =>  'Request expired, please try again or reload page.',
			'data' => null
		];
	}
}

if (isset($_POST['action']) && $_POST['action'] == 'EDIT_TRACK')
{
	if (csrf_verify('post') === true) {
		
		$track_id			= (isset($_POST['track_id'])) ? trim($_POST['track_id']) : null;
		$project_id 		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$track_title 		= (isset($_POST['track_title'])) ? trim($_POST['track_title']) : null;		
		$track_genre 		= (isset($_POST['track_genre'])) ? trim($_POST['track_genre']) : null;		
		$track_lyrics 		= (isset($_POST['track_lyrics'])) ? trim($_POST['track_lyrics']) : null;		
		$producer_name 		= (isset($_POST['producer_name'])) ? trim($_POST['producer_name']) : null;		
		$composer_name 		= (isset($_POST['composer_name'])) ? trim($_POST['composer_name']) : null;		
		$is_explicit 		= (isset($_POST['is_explicit'])) ? trim($_POST['is_explicit']) : null;
		$track_pitch 		= (isset($_POST['track_pitch'])) ? trim($_POST['track_pitch']) : null;
		$featured_artiste 	= (isset($_POST['featured_artiste'])) ? $_POST['featured_artiste'] : null;

		$project 		=  new Project;
		$project_data 	= $project->get($project_id);	
		$track_data 	= $project->getTrackById($track_id);
		
			if ($track_pitch != 1){
					    $playlisted = 0;
					    $update_data = [
					        'playlisted_track' => null,
					        'playlisted' => $playlisted
					        ];
					    $project->updateProject('id', $project_id, $update_data);
				}else{
				    $playlisted = 1;
					    $update_data = [
					        'playlisted_track' => $track_title,
					        'playlisted' => $playlisted
					        ];
					    $project->updateProject('id', $project_id, $update_data);
				}
		
		if ($project_data == false || (isset($project_data['user_id']) && $project_data['user_id'] != $active_user_data['id'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => null ];
		} elseif ($track_data == false || (isset($track_data['project_id']) && $track_data['project_id'] != $project_id)){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload...', 'data' => null ];
		} else {
			
			$update_data = [
				'title' => $track_title,
				'featured_artiste' => $featured_artiste,
				'genre' => $track_genre,
				'lyrics' => $track_lyrics,
				'producer_name'	=> $producer_name,
				'composer_name' => $composer_name,
				'is_explicit' => $is_explicit,
				'track_pitch' => $track_pitch
			];
			
			$update = $project->updateTrack('id', $track_id, $update_data);
			
			if ($update === true) {
				$response = [ 'status' => true, 'message' =>  'Track updated.', 'data' => null ];
			} else {
				$response = [ 'status' => true, 'message' =>  'No changes was made.', 'data' => null ];
			}
			
		}			
	} else {
		$response = [
			'status' => false,
			'message' =>  'Request expired, please try again or reload page.',
			'data' => null
		];
	}
}

if (isset($_POST['action']) && $_POST['action'] == 'DELETE_TRACK')
{
	if (csrf_verify('post') === true) {
		
		$project_id 	= $project_id;
		
		$track_id		= (isset($_POST['track_id'])) ? trim($_POST['track_id']) : null;

		$project 		=  new Project;
		$project_data 	= $project->get($project_id);	
		$track_data 	= $project->getTrackById($track_id);
		
		
		if ($project_data == false || (isset($project_data['user_id']) && $project_data['user_id'] != $active_user_data['id'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => null ];
		} elseif ($track_data == false || (isset($track_data['project_id']) && $track_data['project_id'] != $project_id)){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload...', 'data' => null ];
		} else {
		    
		    	if ($track_data[track_pitch] != 1){
					    
				}else{
				    $playlisted = 0;
					    $update_data = [
					        'playlisted_track' => null,
					        'playlisted' => $playlisted
					        ];
					    $project->updateProject('id', $project_id, $update_data);
				}
				
			if ($project->deleteTrack($track_id)) {
				if ($track_data !== false && is_array($track_data)) { 
					$uploads_tracks_dir = $config['uploads_tracks_dir'];
					if ($track_data['track_file'] != null) {
						unlink($uploads_tracks_dir . $track_data['track_file']);
					}
				}
				
				$response = [ 'status' => true, 'message' =>  'Track deleted.', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}			
	} else {
		$response = [
			'status' => false,
			'message' =>  'Request expired, please try again or reload page.',
			'data' => null
		];
	}
}


if (isset($_POST['action']) && $_POST['action'] == 'CALCULATE_PRICING')
{
	if (csrf_verify('post') === true) {
	
		$project_id 	= $project_id;
		$has_custom_label	= (isset($_POST['custom_label']) && $_POST['custom_label'] == 1) ? 1 : 0;
		$has_promo_page		= (isset($_POST['promo_page']) && $_POST['promo_page'] == 1) ? 1 : 0;
		$pre_order		= (isset($_POST['pre_order']) && $_POST['pre_order'] == 1) ? 1 : 0;
		$spotify_playlisting		= (isset($_POST['spotify_playlisting']) && $_POST['spotify_playlisting'] == 1) ? 1 : 0;
		$playlists_pitching		= (isset($_POST['playlists_pitching']) && $_POST['playlists_pitching'] == 1) ? 1 : 0;
		$music_visualizer   = (isset($_POST['music_visualizer']) && $_POST['music_visualizer'] == 1) ? 1 : 0;

		$project 		=  new Project;
		$project_data 	= $project->get($project_id);	
		
		
		if ($project_data == false || (isset($project_data['user_id']) && $project_data['user_id'] != $active_user_data['id'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => null ];
		} else {
			
			$project = new Project;			
			$update_data = [
				'has_custom_label' => $has_custom_label,
				'has_promo_page' => $has_promo_page,
				'pre_order' => $pre_order,
				'spotify_playlisting' => $spotify_playlisting,
				'playlists_pitching' => $playlists_pitching,
				'has_music_visualizer' => $music_visualizer
			];
			
			$project->updateProject('id', $project_id, $update_data);
			$project_data 	= $project->get($project_id);
			
			if ($project_data !== false) {
				// recalculate pricing
				$project_tracks = $project->getTracks($project_id);
				$total_tracks = count($project_tracks);
				$first_track_price  = $config['pricing_first_track'];
				if ($total_tracks > 1) {									
					$other_track_price	= $config['pricing_other_track'];
					$distro_price		= $first_track_price + ($other_track_price * ($total_tracks - 1));
				} else {
					$distro_price = $first_track_price;
				}	
				$custom_label_price = $config['pricing_custom_label'];
				$add_custom_label_price = ($project_data["has_custom_label"] == 1) ? $custom_label_price : 0;
				$promo_page_price = $config['pricing_promotion_page'];
				$add_promo_page_price = ($project_data["has_promo_page"] == 1) ? $promo_page_price : 0;
				$music_visualizer_price = $config['pricing_music_visualizer'];
				$add_music_visualizer_price = ($project_data["has_music_visualizer"] == 1) ? $music_visualizer_price : 0;
				$total_price 		= $distro_price + $add_custom_label_price + $add_promo_page_price + $add_music_visualizer_price;
				$response = [
					'status' => true,
					'message' =>  'Request succesful.',
					'data' => [
						'distro_price' => $distro_price,
						'custom_label_price' => $custom_label_price,
						'promo_page_price' => $promo_page_price,
						'music_visualizer_price' => $music_visualizer_price,
						'total_price' => $total_price,
					]
				];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again.', 'data' => null ];
			}
		}			
	} else {
		$response = [
			'status' => false,
			'message' =>  'Request expired, please try again or reload page.',
			'data' => null
		];
	}
}



if (isset($_POST['action']) && $_POST['action'] == 'CALCULATE_TAKEDOWN_PRICING')
{
	if (csrf_verify('post') === true) {
		
		$project_id 	= $project_id;
		$takedown_number = (isset($_POST['takedown_number'])) ? trim($_POST['takedown_number']) : null;

		$project 		=  new Project;
		$project_data 	= $project->get($project_id);	
		
		
		if ($project_data == false || (isset($project_data['user_id']) && $project_data['user_id'] != $active_user_data['id'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => null ];
		} else {
			
			$project = new Project;			
			$update_data = [
				'takedown_number' => $takedown_number 
			];
			
			if ($update =	$project->updateProject('id', $project_id, $update_data)) {
			$response = [ 'status' => true, 'message' =>  null, 'data' => null ];
			}
			
			/**$project_data 	= $project->get($project_id);
			
			if ($project_data !== false) {
				// recalculate pricing
				$project_tracks = $project->getTracks($project_id);
				$total_tracks = count($project_tracks);
				$distro_price = $total_tracks * 120;
			
				$total_price 		= $distro_price * $takedown_number;
				
				$response = [
					'status' => true,
					'message' =>  'Request succesful.',
					'data' => [
						'distro_price' => $distro_price,
						'total_price' => $total_price
					]
				];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again.', 'data' => null ];
			} ****///
		}			
	} else {
		$response = [
			'status' => false,
			'message' =>  'Request expired, please try again or reload page.',
			'data' => null
		];
	}
}







if (isset($_GET['action']) && $_GET['action'] == 'GET_A_PROJECT')
{
	if (csrf_verify('get') === true) {
		
		$project_id 	= $project_id;

		$project 		=  new Project;
		$project_data 	= $project->get($project_id);	
	
		if ($project_data == false ){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => null ];
		} else {
			$response = [ 'status' => true, 'message' =>  'Request succesful.', 'data' => $project_data ];
		}			
	} else {
		$response = [
			'status' => false,
			'message' =>  'Request expired, please try again or reload page.',
			'data' => null
		];
	}
}


if (isset($_POST['action']) && $_POST['action'] == 'DELETE_PROJECT')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project 		=  new Project;
		$project_data 	= $project->get($project_id);
		$project_tracks = $project->getTracks($project_id);
		
		if ($project_data == false || (isset($project_data['user_id']) && $project_data['user_id'] != $active_user_data['id'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $project_data ];
		}else {
			if ($project->delete($project_id)) {
				$response = [ 'status' => true, 'message' =>  'project deleted.', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}

if (isset($_POST['action']) && $_POST['action'] == 'DELETE_PROJECT_BY_ADMIN')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project 		=  new Project;
		$project_data 	= $project->get($project_id);
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $project_data ];
		}else {
			if ($project->delete($project_id)) {
				$response = [ 'status' => true, 'message' =>  'project deleted.', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => $project ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}
  
  
if (isset($_POST['action']) && $_POST['action'] == 'DISTRIBUTE_PROJECT')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project_user_id		= (isset($_POST['user_id'])) ? trim($_POST['user_id']) : null;
		$project 		=  new Project;
		$user 		=  new User;
		$created_project_data 	= $project->get($project_id);
		$user_data = $user->get($project_user_id);
		$email = $user_data['email'];
		$name =  $user_data['name'];
		$title = $created_project_data['title'];
		
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$is_distributed = 1;
			$update_data = [
				'is_distributed' => $is_distributed
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'project distributed!', 'data' => null ];
				
				$from = $config['default_email'];
        			$from_name = 'Viralplaylists Digital';
        			$to = $email;
        			$to_name = $name;
        			$subject = 'Your Release Has Been Distributed!';
        			
        			$html_body = "<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">";
        			
            			$html_body .= "<head>
                                        <meta charset=\"utf-8\"> <!-- utf-8 works for most cases -->
                                        <meta name=\"viewport\" content=\"width=device-width\"> <!-- Forcing initial-scale shouldn't be necessary -->
                                        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"> <!-- Use the latest (edge) version of IE rendering engine -->
                                        <meta name=\"x-apple-disable-message-reformatting\">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
                                        <title>Release Notification</title>
                                    	<link rel=\"stylesheet\" type=\"text/css\" href=\"../assets/css/bullet_styles.css\">
                                    </head>"; 
        			    $html_body .= "<body width=\"100%\" style=\"margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f6f6f6;\">
                                    	<center style=\"width: 100%; background-color: #f6f6f6;\">
                                        <!--[if mso | IE]>
                                        <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #f6f6f6;\">
                                        <tr>
                                        <td>
                                        <![endif]-->
                                    
                                            <!-- Visually Hidden Preheader Text : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                              Your release is now live on Digital store!   
                                            </div>
                                            <!-- Visually Hidden Preheader Text : END -->
                                    
                                            <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
                                            <!-- Preview Text Spacing Hack : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                    	        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                                            </div>
                                            <!-- Preview Text Spacing Hack : END -->
                                    
                                           <!-- Email Body : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	       <tr>
                                    				<td style=\"padding: 30px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; background-color: #6f42c1; color:#ffffff;\">
                                    			
                                           <a href=\"#\" target=\"_blank\">
                                            <img src=\"https://send.viralplaylists.com/themes/aurora/assets/logo_white.png\" width=\"131\" height=\"40\" style=\"margin:0; padding:0; border:none; display:block;\" border=\"0\" alt=\"Viral Playlists\" />
                                          </a> 
                                         
                                    				</td>
                                    			</tr>
                                                <!-- Hero Image, Flush : BEGIN -->
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <img src=\"https://viralplaylists.com/wp-content/uploads/2015/09/spark_01.jpg\" width=\"400\" height=\"200\" alt=\"alt_text\" border=\"0\" style=\"width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;\" class=\"g-img\">
                                                    </td>
                                                </tr>
                                                <!-- Hero Image, Flush : END -->
                                    
                                                <!-- 1 Column Text + Button : BEGIN -->
            
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <h1 style=\"margin: 0 0 10px; font-size: 25px; line-height: 30px; color: #6f42c1; font-weight: normal;\">Release Notification</h1>
                                                                    <p style=\"margin: 0 0 10px;\">Hi Superstar,</p>
                                    								<p style=\"margin: 0 0 10px;\">We are pleased to announce to you that your release <b>$title</b> is now live on the digital stores.<br /></p>
                                    								<p style=\"margin: 0 0 10px;\">Please login to your dashboard to check it out.</p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style=\"padding: 0 20px 20px;\">
                                                                    <!-- Button : BEGIN -->
                                                                    <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: auto;\">
                                                                        <tr>
                                                                            <td>
                                    											<a class=\"button-a button-a-primary\" href=\"https://app.viralplaylists.com/dashboard\" style=\"background: #6f42c1;  font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;\">View Release</a>
                                    										</td>
                                                                        </tr>
                                                                    </table>
                                                                    <!-- Button : END -->
                                                                </td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <p style=\"margin: 0 0 10px;\">Warm Regards,</p>
                                    								<p style=\"margin: 0 0 10px;\"> Viralplaylists Digital Team<br /></p>
                                                                </td>
                                                            </tr>
                                                            <tr style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;  margin: 5; padding: 20px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; color:#fffff;\">
                        										<td class=\"content-block\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: center;\" valign=\"top\">
                                                                    <br><b>Need help? Got questions to ask?</b><br>
                                                                    <a href=\"mailto:distro@viralplaylists.com\" style=\"text-decoration: none;\">distro@viralplaylists.com</a> | <a href=\"tel:+2348151240876\" style=\"text-decoration: none;\">+2348151240876</a
                                                                        <br><br>
                                                                    <a href=\"https://twitter.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://i1.wp.com/marsfallpodcast.com/wp-content/uploads/2017/09/Twitter-Download-PNG.png\" alt=\"Twitter\">
                                                                    </a>
                                                                    <a href=\"https://facebook.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://www.freeiconspng.com/uploads/facebook-icon-5.png\" alt=\"Facebook\">
                                                                    </a>
                                                                    <a href=\"https://instagram.com/viral_playlists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png\" alt=\"Instagram\">
                                                                    </a>
                        										</td>
                        									</tr>
                                    
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- 1 Column Text + Button : END -->
                                    
                                                <!-- Background Image with Text : BEGIN -->
                                                
                                    	    </table>
                                    	    <!-- Email Body : END -->
                                    
                                    	    <!-- Email Footer : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	        <tr>
                                    	            <td style=\"padding: 20px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #ffffff; background-color: #6f42c1;\">
                                    	                Copyright &copy; 2019 Viral Playlists Digital<br />
                                    	                <div style=\"text-align: left; font-size: 11px; line-height: 1.3em;\">
                                    	                    This email was sent to $email because this email address was used to sign up at
                                                            <a href=\"viralplaylists.com\" target=\"_blank\" style=\"text-decoration: none;\"> viralplaylists.com </a>.
                                                            Please note that we will never ask you to provide your password. If this message has been sent to you in error,
                                                            please delete this immediately or notify our Support Team.
                                                        </div>
                                                    </td>
                                    	        </tr>
                                    	    </table>
                                    	    <!-- Email Footer : END -->
                                    
                                    	    
                                        <!--[if mso | IE]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                        </center>
                                    </body> ";
                                			 			
        			$html_body .= "</html>";	
        			$text_body = " Hi Superstar!\r\n";
        			$text_body .= "We are pleased to announce to you that your release $title is now live on the digital stores.\r\n";
        			$text_body .= "Please login to your dashboard to check it out\r\n";
        			$text_body .= "https://app.viralplaylists.com/login\r\n";
        			
        			$mail =  new Email;
        			$sendMail= $mail->send($from, $from_name, $to, $to_name, $subject, $html_body, $text_body);
			
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}  

if (isset($_POST['action']) && $_POST['action'] == 'UNDISTRIBUTE_PROJECT')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project 		=  new Project;
		$created_project_data 	= $project->get($project_id);
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$is_distributed = 0;
			$update_data = [
				'is_distributed' => $is_distributed
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'project has been marked undistributed!', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}

if (isset($_POST['action']) && $_POST['action'] == 'MARK_PRE_ORDER')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project_user_id		= (isset($_POST['user_id'])) ? trim($_POST['user_id']) : null;
		$project 		=  new Project;
		$user 		=  new User;
		$created_project_data 	= $project->get($project_id);
		$user_data = $user->get($project_user_id);
		$email = $user_data['email'];
		$name =  $user_data['name'];
		$title = $created_project_data['title'];
		$pre_order_done = $created_project_data['pre_order_done'];
		
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$pre_order_done = 1;
			$update_data = [
				'pre_order_done' => $pre_order_done
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'Pre order Marked', 'data' => null ];
			
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}  

if (isset($_POST['action']) && $_POST['action'] == 'UNMARK_PRE_ORDER')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project 		=  new Project;
		$created_project_data 	= $project->get($project_id);
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$pre_order_done = 0;
			$update_data = [
				'pre_order_done' => $pre_order_done
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'pre order has been umarked!', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}


if (isset($_POST['action']) && $_POST['action'] == 'MARK_PLAYLISTS_PITCHING')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project_user_id		= (isset($_POST['user_id'])) ? trim($_POST['user_id']) : null;
		$project 		=  new Project;
		$user 		=  new User;
		$created_project_data 	= $project->get($project_id);
		$user_data = $user->get($project_user_id);
		$email = $user_data['email'];
		$name =  $user_data['name'];
		$title = $created_project_data['title'];
		$playlists_pitching_done = $created_project_data['playlists_pitching_done'];
		
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$playlists_pitching_done = 1;
			$update_data = [
				'playlists_pitching_done' => $playlists_pitching_done
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'Playlsists pitching marked!', 'data' => null ];
				
				$from = $config['default_email'];
        			$from_name = 'Viralplaylists Digital';
        			$to = $email;
        			$to_name = $name;
        			$subject = 'Your Release Has Been Pitched to Top 3rd Party Playlists!';
        			
        			$html_body = "<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">";
        			
            			$html_body .= "<head>
                                        <meta charset=\"utf-8\"> <!-- utf-8 works for most cases -->
                                        <meta name=\"viewport\" content=\"width=device-width\"> <!-- Forcing initial-scale shouldn't be necessary -->
                                        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"> <!-- Use the latest (edge) version of IE rendering engine -->
                                        <meta name=\"x-apple-disable-message-reformatting\">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
                                        <title>Release Notification</title>
                                    	<link rel=\"stylesheet\" type=\"text/css\" href=\"../assets/css/bullet_styles.css\">
                                    </head>"; 
        			    $html_body .= "<body width=\"100%\" style=\"margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f6f6f6;\">
                                    	<center style=\"width: 100%; background-color: #f6f6f6;\">
                                        <!--[if mso | IE]>
                                        <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #f6f6f6;\">
                                        <tr>
                                        <td>
                                        <![endif]-->
                                    
                                            <!-- Visually Hidden Preheader Text : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                              Your release is now live on Digital store!   
                                            </div>
                                            <!-- Visually Hidden Preheader Text : END -->
                                    
                                            <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
                                            <!-- Preview Text Spacing Hack : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                    	        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                                            </div>
                                            <!-- Preview Text Spacing Hack : END -->
                                    
                                           <!-- Email Body : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	       <tr>
                                    				<td style=\"padding: 30px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; background-color: #6f42c1; color:#ffffff;\">
                                    			
                                           <a href=\"#\" target=\"_blank\">
                                            <img src=\"https://send.viralplaylists.com/themes/aurora/assets/logo_white.png\" width=\"131\" height=\"40\" style=\"margin:0; padding:0; border:none; display:block;\" border=\"0\" alt=\"Viral Playlists\" />
                                          </a> 
                                         
                                    				</td>
                                    			</tr>
                                                <!-- Hero Image, Flush : BEGIN -->
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <img src=\"https://viralplaylists.com/wp-content/uploads/2015/09/spark_01.jpg\" width=\"400\" height=\"200\" alt=\"alt_text\" border=\"0\" style=\"width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;\" class=\"g-img\">
                                                    </td>
                                                </tr>
                                                <!-- Hero Image, Flush : END -->
                                    
                                                <!-- 1 Column Text + Button : BEGIN -->
            
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <h1 style=\"margin: 0 0 10px; font-size: 25px; line-height: 30px; color: #6f42c1; font-weight: normal;\">Playlists Pitching Notification</h1>
                                                                    <p style=\"margin: 0 0 10px;\">Dear $name,</p>
                                    								<p style=\"margin: 0 0 10px;\">Your release has been submitted to top playlists on Spotify, Apple music and more.<br /></p>
                                    								<p style=\"margin: 0 0 10px;\">You can see all the playlists that liked your music and featured it on your Spotify for artist and Apple music for artist.</p><br>
                                    								<p style=\"margin: 0 0 10px;\"><b>Thank you.</b></p><br><br>
                                    								</span><br>
                                                                </td>
                                                            </tr>
                                                          
                                                            
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <p style=\"margin: 0 0 10px;\">Warm Regards,</p>
                                    								<p style=\"margin: 0 0 10px;\"> Viralplaylists Digital Team<br /></p>
                                                                </td>
                                                            </tr>
                                                            <tr style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;  margin: 5; padding: 20px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; color:#fffff;\">
                        										<td class=\"content-block\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: center;\" valign=\"top\">
                                                                    <br><b>Need help? Got questions to ask?</b><br>
                                                                    <a href=\"mailto:distro@viralplaylists.com\" style=\"text-decoration: none;\">distro@viralplaylists.com</a> | <a href=\"tel:+2348151240876\" style=\"text-decoration: none;\">+2348151240876</a
                                                                        <br><br>
                                                                    <a href=\"https://twitter.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://i1.wp.com/marsfallpodcast.com/wp-content/uploads/2017/09/Twitter-Download-PNG.png\" alt=\"Twitter\">
                                                                    </a>
                                                                    <a href=\"https://facebook.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://www.freeiconspng.com/uploads/facebook-icon-5.png\" alt=\"Facebook\">
                                                                    </a>
                                                                    <a href=\"https://instagram.com/viral_playlists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png\" alt=\"Instagram\">
                                                                    </a>
                        										</td>
                        									</tr>
                                    
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- 1 Column Text + Button : END -->
                                    
                                                <!-- Background Image with Text : BEGIN -->
                                                
                                    	    </table>
                                    	    <!-- Email Body : END -->
                                    
                                    	    <!-- Email Footer : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	        <tr>
                                    	            <td style=\"padding: 20px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #ffffff; background-color: #6f42c1;\">
                                    	                Copyright &copy; 2019 Viral Playlists Digital<br />
                                    	                <div style=\"text-align: left; font-size: 11px; line-height: 1.3em;\">
                                    	                    This email was sent to $email because this email address was used to sign up at
                                                            <a href=\"viralplaylists.com\" target=\"_blank\" style=\"text-decoration: none;\"> viralplaylists.com </a>.
                                                            Please note that we will never ask you to provide your password. If this message has been sent to you in error,
                                                            please delete this immediately or notify our Support Team.
                                                        </div>
                                                    </td>
                                    	        </tr>
                                    	    </table>
                                    	    <!-- Email Footer : END -->
                                    	    
                                        <!--[if mso | IE]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                        </center>
                                    </body> ";
                                			 			
        			$html_body .= "</html>";
        			
        			$text_body = "Dear $name\r\n";
        			$text_body .= "You release has been submitted to top playlists on Spotify, Apple music and more.\r\n";
        			$text_body .= "You can see all the playlists that liked your song and featured it on your Spotify for Artist and Apple Music for Artist.\r\n";
        			$text_body .= "------------------------\r\n";
        			$text_body .= "Thank you.\r\n";
        			$mail =  new Email;
        			$sendMail= $mail->send($from, $from_name, $to, $to_name, $subject, $html_body, $text_body);
			
				
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}

if (isset($_POST['action']) && $_POST['action'] == 'UNMARK_PLAYLISTS_PITCHING')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project 		=  new Project;
		$created_project_data 	= $project->get($project_id);
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$playlists_pitching_done = 0;
			$update_data = [
				'playlists_pitching_done' => $playlists_pitching_done
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'Playlists pitching unmarked!', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}

if (isset($_POST['action']) && $_POST['action'] == 'MARK_SPOTIFY_PLAYLIST')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project_user_id		= (isset($_POST['user_id'])) ? trim($_POST['user_id']) : null;
		$project 		=  new Project;
		$user 		=  new User;
		$created_project_data 	= $project->get($project_id);
		$user_data = $user->get($project_user_id);
		$email = $user_data['email'];
		$name =  $user_data['name'];
		$title = $created_project_data['title'];
		$spotify_playlisting_done = $created_project_data['spotify_playlisting_done'];
		
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$spotify_playlisting_done = 1;
			$update_data = [
				'spotify_playlisting_done' => $spotify_playlisting_done
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'Spotify playlistin marked!', 'data' => null ];
				
				$from = $config['default_email'];
        			$from_name = 'Viralplaylists Digital';
        			$to = $email;
        			$to_name = $name;
        			$subject = 'Editorial Playlists Update!';
        			
        			$html_body = "<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">";
        			
            			$html_body .= "<head>
                                        <meta charset=\"utf-8\"> <!-- utf-8 works for most cases -->
                                        <meta name=\"viewport\" content=\"width=device-width\"> <!-- Forcing initial-scale shouldn't be necessary -->
                                        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"> <!-- Use the latest (edge) version of IE rendering engine -->
                                        <meta name=\"x-apple-disable-message-reformatting\">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
                                        <title>Release Notification</title>
                                    	<link rel=\"stylesheet\" type=\"text/css\" href=\"../assets/css/bullet_styles.css\">
                                    </head>"; 
        			    $html_body .= "<body width=\"100%\" style=\"margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f6f6f6;\">
                                    	<center style=\"width: 100%; background-color: #f6f6f6;\">
                                        <!--[if mso | IE]>
                                        <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #f6f6f6;\">
                                        <tr>
                                        <td>
                                        <![endif]-->
                                    
                                            <!-- Visually Hidden Preheader Text : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                              Editorial Playlists Update!   
                                            </div>
                                            <!-- Visually Hidden Preheader Text : END -->
                                    
                                            <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
                                            <!-- Preview Text Spacing Hack : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                    	        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                                            </div>
                                            <!-- Preview Text Spacing Hack : END -->
                                    
                                           <!-- Email Body : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	       <tr>
                                    				<td style=\"padding: 30px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; background-color: #6f42c1; color:#ffffff;\">
                                    			
                                           <a href=\"#\" target=\"_blank\">
                                            <img src=\"https://send.viralplaylists.com/themes/aurora/assets/logo_white.png\" width=\"131\" height=\"40\" style=\"margin:0; padding:0; border:none; display:block;\" border=\"0\" alt=\"Viral Playlists\" />
                                          </a> 
                                         
                                    				</td>
                                    			</tr>
                                                <!-- Hero Image, Flush : BEGIN -->
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <img src=\"https://viralplaylists.com/wp-content/uploads/2015/09/spark_01.jpg\" width=\"400\" height=\"200\" alt=\"alt_text\" border=\"0\" style=\"width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;\" class=\"g-img\">
                                                    </td>
                                                </tr>
                                                <!-- Hero Image, Flush : END -->
                                    
                                                <!-- 1 Column Text + Button : BEGIN -->
            
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <h1 style=\"margin: 0 0 10px; font-size: 25px; line-height: 30px; color: #6f42c1; font-weight: normal;\">Spotify Offiial Playlists Submission</h1>
                                                                    <p style=\"margin: 0 0 10px;\">Dear $name,</p>
                                    								<p style=\"margin: 0 0 10px;\">Your release has been pitched officially on Apple music and Spotify.<br /></p>
                                    								<br>
                                    								<p style=\"margin: 0 0 10px;\"><b>Thank you.</b></p><br><br>
                                    								</span><br>
                                                                </td>
                                                            </tr>
                                                          
                                                            
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <p style=\"margin: 0 0 10px;\">Warm Regards,</p>
                                    								<p style=\"margin: 0 0 10px;\"> Viralplaylists Digital Team<br /></p>
                                                                </td>
                                                            </tr>
                                                            <tr style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;  margin: 5; padding: 20px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; color:#fffff;\">
                        										<td class=\"content-block\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: center;\" valign=\"top\">
                                                                    <br><b>Need help? Got questions to ask?</b><br>
                                                                    <a href=\"mailto:distro@viralplaylists.com\" style=\"text-decoration: none;\">distro@viralplaylists.com</a> | <a href=\"tel:+2348151240876\" style=\"text-decoration: none;\">+2348151240876</a
                                                                        <br><br>
                                                                    <a href=\"https://twitter.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://i1.wp.com/marsfallpodcast.com/wp-content/uploads/2017/09/Twitter-Download-PNG.png\" alt=\"Twitter\">
                                                                    </a>
                                                                    <a href=\"https://facebook.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://www.freeiconspng.com/uploads/facebook-icon-5.png\" alt=\"Facebook\">
                                                                    </a>
                                                                    <a href=\"https://instagram.com/viral_playlists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png\" alt=\"Instagram\">
                                                                    </a>
                        										</td>
                        									</tr>
                                    
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- 1 Column Text + Button : END -->
                                    
                                                <!-- Background Image with Text : BEGIN -->
                                                
                                    	    </table>
                                    	    <!-- Email Body : END -->
                                    
                                    	    <!-- Email Footer : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	        <tr>
                                    	            <td style=\"padding: 20px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #ffffff; background-color: #6f42c1;\">
                                    	                Copyright &copy; 2019 Viral Playlists Digital<br />
                                    	                <div style=\"text-align: left; font-size: 11px; line-height: 1.3em;\">
                                    	                    This email was sent to $email because this email address was used to sign up at
                                                            <a href=\"viralplaylists.com\" target=\"_blank\" style=\"text-decoration: none;\"> viralplaylists.com </a>.
                                                            Please note that we will never ask you to provide your password. If this message has been sent to you in error,
                                                            please delete this immediately or notify our Support Team.
                                                        </div>
                                                    </td>
                                    	        </tr>
                                    	    </table>
                                    	    <!-- Email Footer : END -->
                                    	    
                                        <!--[if mso | IE]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                        </center>
                                    </body> ";
                                			 			
        			$html_body .= "</html>";
        			
        			$text_body = "Dear $name\r\n";
        			$text_body .= "You release has been pitched officially on Apple music & Spotify.\r\n";
        		
        			$text_body .= "------------------------\r\n";
        			$text_body .= "Thank you.\r\n";
        			$mail =  new Email;
        			$sendMail= $mail->send($from, $from_name, $to, $to_name, $subject, $html_body, $text_body);
			
				
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}

if (isset($_POST['action']) && $_POST['action'] == 'UNMARK_SPOTIFY_PLAYLIST')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project 		=  new Project;
		$created_project_data 	= $project->get($project_id);
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$spotify_playlisting_done = 0;
			$update_data = [
				'spotify_playlisting_done' => $spotify_playlisting_done
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'Spotify Playlistsing unmarked!', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}

if (isset($_POST['action']) && $_POST['action'] == 'PUBLISH_PROJECT')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project_user_id		= (isset($_POST['user_id'])) ? trim($_POST['user_id']) : null;
		$project 		=  new Project;
		$user 		=  new User;
		$created_project_data 	= $project->get($project_id);
		$user_data = $user->get($project_user_id);
		$email = $user_data['email'];
		$name =  $user_data['name'];
		$title = $created_project_data['title'];
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$is_published = 1;
			$update_data = [
				'is_published' => $is_published
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'project published!', 'data' => null ];
				
				$from = $config['default_email'];
        			$from_name = 'Viralplaylists Digital';
        			$to = $email;
        			$to_name = $name;
        			$subject = 'Your Release Has Been Published!';
        			
        			$html_body = "<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">";
        			
            			$html_body .= "<head>
                                        <meta charset=\"utf-8\"> <!-- utf-8 works for most cases -->
                                        <meta name=\"viewport\" content=\"width=device-width\"> <!-- Forcing initial-scale shouldn't be necessary -->
                                        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"> <!-- Use the latest (edge) version of IE rendering engine -->
                                        <meta name=\"x-apple-disable-message-reformatting\">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
                                        <title>Release Notification</title>
                                    	<link rel=\"stylesheet\" type=\"text/css\" href=\"../assets/css/bullet_styles.css\">
                                    </head>"; 
        			    $html_body .= "<body width=\"100%\" style=\"margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f6f6f6;\">
                                    	<center style=\"width: 100%; background-color: #f6f6f6;\">
                                        <!--[if mso | IE]>
                                        <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #f6f6f6;\">
                                        <tr>
                                        <td>
                                        <![endif]-->
                                    
                                            <!-- Visually Hidden Preheader Text : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                              Your release is now live on Digital store!   
                                            </div>
                                            <!-- Visually Hidden Preheader Text : END -->
                                    
                                            <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
                                            <!-- Preview Text Spacing Hack : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                    	        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                                            </div>
                                            <!-- Preview Text Spacing Hack : END -->
                                    
                                           <!-- Email Body : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	       <tr>
                                    				<td style=\"padding: 30px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; background-color: #6f42c1; color:#ffffff;\">
                                    			
                                           <a href=\"#\" target=\"_blank\">
                                            <img src=\"https://send.viralplaylists.com/themes/aurora/assets/logo_white.png\" width=\"131\" height=\"40\" style=\"margin:0; padding:0; border:none; display:block;\" border=\"0\" alt=\"Viral Playlists\" />
                                          </a> 
                                         
                                    				</td>
                                    			</tr>
                                                <!-- Hero Image, Flush : BEGIN -->
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <img src=\"https://viralplaylists.com/wp-content/uploads/2015/09/spark_01.jpg\" width=\"400\" height=\"200\" alt=\"alt_text\" border=\"0\" style=\"width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;\" class=\"g-img\">
                                                    </td>
                                                </tr>
                                                <!-- Hero Image, Flush : END -->
                                    
                                                <!-- 1 Column Text + Button : BEGIN -->
            
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <h1 style=\"margin: 0 0 10px; font-size: 25px; line-height: 30px; color: #6f42c1; font-weight: normal;\">Payment Notification</h1>
                                                                    <p style=\"margin: 0 0 10px;\">Dear $name,</p>
                                    								<p style=\"margin: 0 0 10px;\">Thank you for choosing viralplaylists Digital for your music distribution.<br /></p>
                                    								<p style=\"margin: 0 0 10px;\">Your payments has been confirmed and your music placed on queue for its digital release.</p><br>
                                    								<p style=\"margin: 0 0 10px;\"><b>You will be notified once it's live.</b></p><br><br>
                                    								<p style=\"margin: 0 0 10px;\">Please login to your dashboard to confirm the status has turned to <span style= \"color:#ffc107\";><b>Published</b></p> </span><br>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style=\"padding: 0 20px 20px;\">
                                                                    <!-- Button : BEGIN -->
                                                                    <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: auto;\">
                                                                        <tr>
                                                                            <td>
                                    											<a class=\"button-a button-a-primary\" href=\"https://app.viralplaylists.com/dashboard\" style=\"background: #6f42c1;  font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;\">Confirm Release Status</a>
                                    										</td>
                                                                        </tr>
                                                                    </table>
                                                                    <!-- Button : END -->
                                                                </td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <p style=\"margin: 0 0 10px;\">Warm Regards,</p>
                                    								<p style=\"margin: 0 0 10px;\"> Viralplaylists Digital Team<br /></p>
                                                                </td>
                                                            </tr>
                                                            <tr style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;  margin: 5; padding: 20px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; color:#fffff;\">
                        										<td class=\"content-block\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: center;\" valign=\"top\">
                                                                    <br><b>Need help? Got questions to ask?</b><br>
                                                                    <a href=\"mailto:distro@viralplaylists.com\" style=\"text-decoration: none;\">distro@viralplaylists.com</a> | <a href=\"tel:+2348151240876\" style=\"text-decoration: none;\">+2348151240876</a
                                                                        <br><br>
                                                                    <a href=\"https://twitter.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://i1.wp.com/marsfallpodcast.com/wp-content/uploads/2017/09/Twitter-Download-PNG.png\" alt=\"Twitter\">
                                                                    </a>
                                                                    <a href=\"https://facebook.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://www.freeiconspng.com/uploads/facebook-icon-5.png\" alt=\"Facebook\">
                                                                    </a>
                                                                    <a href=\"https://instagram.com/viral_playlists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png\" alt=\"Instagram\">
                                                                    </a>
                        										</td>
                        									</tr>
                                    
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- 1 Column Text + Button : END -->
                                    
                                                <!-- Background Image with Text : BEGIN -->
                                                
                                    	    </table>
                                    	    <!-- Email Body : END -->
                                    
                                    	    <!-- Email Footer : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	        <tr>
                                    	            <td style=\"padding: 20px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #ffffff; background-color: #6f42c1;\">
                                    	                Copyright &copy; 2019 Viral Playlists Digital<br />
                                    	                <div style=\"text-align: left; font-size: 11px; line-height: 1.3em;\">
                                    	                    This email was sent to $email because this email address was used to sign up at
                                                            <a href=\"viralplaylists.com\" target=\"_blank\" style=\"text-decoration: none;\"> viralplaylists.com </a>.
                                                            Please note that we will never ask you to provide your password. If this message has been sent to you in error,
                                                            please delete this immediately or notify our Support Team.
                                                        </div>
                                                    </td>
                                    	        </tr>
                                    	    </table>
                                    	    <!-- Email Footer : END -->
                                    	    
                                        <!--[if mso | IE]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                        </center>
                                    </body> ";
                                			 			
        			$html_body .= "</html>";
        			
        			$text_body = "Dear $name\r\n";
        			$text_body .= "Thank you for choosing viralplaylists Digital for your music distribution.\r\n";
        			$text_body .= "Your payments has been confirmed and your music placed on queue for its digital release.\r\n";
        			$text_body .= "------------------------\r\n";
        			$text_body .= "You will be notified once it's live.\r\n";
        			$mail =  new Email;
        			$sendMail= $mail->send($from, $from_name, $to, $to_name, $subject, $html_body, $text_body);
        			
			
			//send editorial playlists email to applicable users
								
									$email = $user_data['email'];
		                            $name =  $user_data['name'];
		                            $editorial_playlists = $created_project_data['spotify_playlisting'];
		                            
		                            if($editorial_playlists == 1 ) {


		                            
		                            $from = $config['default_email'];
                        			$from_name = 'Viralplaylists Digital';
                        			$to = $email;
                        			$to_name = $name;
                        			$subject = 'Editorial Playlisting (Apple music/Spotify)';
                        			
        			$html_body = "<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">";
        			
            			$html_body .= "<head>
                                        <meta charset=\"utf-8\"> <!-- utf-8 works for most cases -->
                                        <meta name=\"viewport\" content=\"width=device-width\"> <!-- Forcing initial-scale shouldn't be necessary -->
                                        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"> <!-- Use the latest (edge) version of IE rendering engine -->
                                        <meta name=\"x-apple-disable-message-reformatting\">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
                                        <title>Release Notification</title>
                                    	<link rel=\"stylesheet\" type=\"text/css\" href=\"../assets/css/bullet_styles.css\">
                                    </head>"; 
        			    $html_body .= "<body width=\"100%\" style=\"margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f6f6f6;\">
                                    	<center style=\"width: 100%; background-color: #f6f6f6;\">
                                        <!--[if mso | IE]>
                                        <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #f6f6f6;\">
                                        <tr>
                                        <td>
                                        <![endif]-->
                                    
                                            <!-- Visually Hidden Preheader Text : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                              Editorial Playlisting!   
                                            </div>
                                            <!-- Visually Hidden Preheader Text : END -->
                                    
                                            <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
                                            <!-- Preview Text Spacing Hack : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                    	        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                                            </div>
                                            <!-- Preview Text Spacing Hack : END -->
                                    
                                           <!-- Email Body : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	       <tr>
                                    				<td style=\"padding: 30px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; background-color: #6f42c1; color:#ffffff;\">
                                    			
                                           <a href=\"#\" target=\"_blank\">
                                            <img src=\"https://send.viralplaylists.com/themes/aurora/assets/logo_white.png\" width=\"131\" height=\"40\" style=\"margin:0; padding:0; border:none; display:block;\" border=\"0\" alt=\"Viral Playlists\" />
                                          </a> 
                                         
                                    				</td>
                                    			</tr>
                                                <!-- Hero Image, Flush : BEGIN -->
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <img src=\"https://viralplaylists.com/wp-content/uploads/2015/09/spark_01.jpg\" width=\"400\" height=\"200\" alt=\"alt_text\" border=\"0\" style=\"width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;\" class=\"g-img\">
                                                    </td>
                                                </tr>
                                                <!-- Hero Image, Flush : END -->
                                    
                                                <!-- 1 Column Text + Button : BEGIN -->
            
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <h1 style=\"margin: 0 0 10px; font-size: 25px; line-height: 30px; color: #6f42c1; font-weight: normal;\">Editorial Playlisting</h1>
                                                                    <p style=\"margin: 0 0 10px;\">Dear $name,</p>
                                    								<p style=\"margin: 0 0 10px;\">You are receiving this email because you selected <b>Editorial Playlists</b> while checking out. <br /></p>
                                    								<p style=\"margin: 0 0 10px;\">Please kindly click on the button below to tell us more about your project and other related promotional activities.</p><br>
                                    								<p style=\"margin: 0 0 10px;\"><b>Please make sure your application is very strong, you will only be contacted by our editorial team if your application meets the cut </b></p><br><br>
                                    								
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style=\"padding: 0 20px 20px;\">
                                                                    <!-- Button : BEGIN -->
                                                                    <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: auto;\">
                                                                        <tr>
                                                                            <td>
                                    											<a class=\"button-a button-a-primary\" href=\"https://bit.ly/editorialplaylists\" style=\"background: #6f42c1;  font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;\">Editorial Playlists</a>
                                    										</td>
                                                                        </tr>
                                                                    </table>
                                                                    <!-- Button : END -->
                                                                </td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <p style=\"margin: 0 0 10px;\">Warm Regards,</p>
                                    								<p style=\"margin: 0 0 10px;\"> Viralplaylists Digital Team<br /></p> <br>
                                    								<p style=\"margin: 0 0 10px;\"><b>PS:</b> Please, reach out to us on whatsapp: 08128273620 if you need more clarifications.</p>
                                                                </td>
                                                            </tr>
                                                            <tr style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;  margin: 5; padding: 20px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; color:#fffff;\">
                        										<td class=\"content-block\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: center;\" valign=\"top\">
                                                                    <br><b>Need help? Got questions to ask?</b><br>
                                                                    <a href=\"mailto:distro@viralplaylists.com\" style=\"text-decoration: none;\">distro@viralplaylists.com</a> | <a href=\"tel:+2348151240876\" style=\"text-decoration: none;\">+2348151240876</a
                                                                        <br><br>
                                                                    <a href=\"twitter.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://i1.wp.com/marsfallpodcast.com/wp-content/uploads/2017/09/Twitter-Download-PNG.png\" alt=\"Twitter\">
                                                                    </a>
                                                                    <a href=\"facebook.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://www.freeiconspng.com/uploads/facebook-icon-5.png\" alt=\"Facebook\">
                                                                    </a>
                                                                    <a href=\"\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png\" alt=\"Instagram\">
                                                                    </a>
                        										</td>
                        									</tr>
                                    
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- 1 Column Text + Button : END -->
                                    
                                                <!-- Background Image with Text : BEGIN -->
                                                
                                    	    </table>
                                    	    <!-- Email Body : END -->
                                    
                                    	    <!-- Email Footer : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	        <tr>
                                    	            <td style=\"padding: 20px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #ffffff; background-color: #6f42c1;\">
                                    	                Copyright &copy; 2019 Viral Playlists Digital<br />
                                    	                <div style=\"text-align: left; font-size: 11px; line-height: 1.3em;\">
                                    	                    This email was sent to $email because this email address was used to sign up at
                                                            <a href=\"viralplaylists.com\" target=\"_blank\" style=\"text-decoration: none;\"> viralplaylists.com </a>.
                                                            Please note that we will never ask you to provide your password. If this message has been sent to you in error,
                                                            please delete this immediately or notify our Support Team.
                                                        </div>
                                                    </td>
                                    	        </tr>
                                    	    </table>
                                    	    <!-- Email Footer : END -->
                                    	    
                                        <!--[if mso | IE]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                        </center>
                                    </body> ";
                                			 			
        			$html_body .= "</html>";
        			
        			$text_body = "Dear $name\r\n";
        			$text_body .= "You are receiving this email because you selected Editorial Playlists while checking out.\r\n";
        			$text_body .= "Please click on the link below to tell us more about your project and other related promotional activities.\r\n";
        			$text_body .= "Make sure your application is very strong, you will only be contacted by our editorial team if your application meets the cut.\r\n";
        			$text_body .= "------------------------\r\n";
        			$text_body .= "Copy link below and paste on your browser\r\n";
        			$text_body .= "https://bit.ly/editorialplaylists\r\n";
        			$mail =  new Email;
        			$sendMail= $mail->send($from, $from_name, $to, $to_name, $subject, $html_body, $text_body);
        			
		                  } else {
                                    // your code
                                  }
								
			
			
				
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}

if (isset($_POST['action']) && $_POST['action'] == 'UNPUBLISH_PROJECT')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project 		=  new Project;
		$created_project_data 	= $project->get($project_id);
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$is_published = 0;
			$update_data = [
				'is_published' => $is_published
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'project has been marked unpublished!', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}

if(isset($_POST['action']) && $_POST['action'] == 'MODIFY_PROJECT' ){
	//print_r($_POST);
	if(csrf_verify('post') === true ) {
		$project_id = (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$upc	= (isset($_POST['upc'])) ? trim($_POST['upc']) : null;
		$isrc = (isset($_POST['isrc'])) ? trim($_POST['isrc']) : null;
		$store_links 	= (isset($_POST['store_links'])) ? $_POST['store_links'] : null; // form will send this as array bcoz the input name="featured_artiste[]"

		if (is_array($store_links)) {
			$store_links = implode(" , ", $store_links); // convert the array values into comma-separated string
		} else {
			$store_links =  null;
		}
		if( empty($upc) || empty($store_links) || empty($isrc)) {
			$response = [ 'status' => false, 'message' =>  'Please fill/complete the required form fields.', 'data' => null ];
		} else {
			
			$project = new Project;	
		    $user = new User; 
		    
			$sum = $user->sumByUPC($upc);
			$spotify = $user->sumBySpotify($upc);
			$google_play = $user->sumByGooglePlay($upc);
			$deezer = $user->sumByDeezer($upc);
		    $apple_music = $user->sumByAppleMusic($upc);
			$itunes = $user->sumByiTunes($upc);
			$tidal = $user->sumByTidal($upc);
			$youtube = $user->sumByYouTube($upc);
			$rhapsody = $user->sumByRhapsody($upc);
			
			$update_data = [
				'upc' => $upc,
				'isrc' => $isrc,
				'store_links'	=> $store_links,
				'net_total_eur' => $sum,
				'spotify' => $spotify,
				'google_play' => $google_play,
				'deezer' => $deezer,
				'apple_music' => $apple_music,
				'itunes' => $itunes,
				'tidal' => $tidal,
				'youtube' => $youtube,
				'rhapsody' => $rhapsody
			];
			
			
			
			$created_project_data 	= $project->get($project_id);
			$project_user_id = $created_project_data['user_id'];
			$user_data = $user->get($project_user_id);
			$email = $user_data['email'];
			$name = $user_data['name'];
			$title = $created_project_data['title'];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [
					'status' => true,
					'message' =>  'Project updated successfully.',
				'isrc' => $isrc,
					'data' => $update_data
				];
				
				
				$from = $config['default_email'];
        			$from_name = 'Viralplaylists Digital';
        			$to = $email;
        			$to_name = $name;
        			$subject = 'Release Update';
        			
        			$html_body = "<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">";
        			
            			$html_body .= "<head>
                                        <meta charset=\"utf-8\"> <!-- utf-8 works for most cases -->
                                        <meta name=\"viewport\" content=\"width=device-width\"> <!-- Forcing initial-scale shouldn't be necessary -->
                                        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"> <!-- Use the latest (edge) version of IE rendering engine -->
                                        <meta name=\"x-apple-disable-message-reformatting\">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
                                        <title>Release Notification</title>
                                    	<link rel=\"stylesheet\" type=\"text/css\" href=\"../assets/css/bullet_styles.css\">
                                    </head>"; 
        			    $html_body .= "<body width=\"100%\" style=\"margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f6f6f6;\">
                                    	<center style=\"width: 100%; background-color: #f6f6f6;\">
                                        <!--[if mso | IE]>
                                        <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #f6f6f6;\">
                                        <tr>
                                        <td>
                                        <![endif]-->
                                    
                                            <!-- Visually Hidden Preheader Text : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                              Your release is now live on Digital store!   
                                            </div>
                                            <!-- Visually Hidden Preheader Text : END -->
                                    
                                            <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
                                            <!-- Preview Text Spacing Hack : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                    	        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                                            </div>
                                            <!-- Preview Text Spacing Hack : END -->
                                    
                                            <!-- Email Body : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	       <tr>
                                    				<td style=\"padding: 30px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; background-color: #6f42c1; color:#ffffff;\">
                                    			
                                           <a href=\"#\" target=\"_blank\">
                                            <img src=\"https://send.viralplaylists.com/themes/aurora/assets/logo_white.png\" width=\"131\" height=\"40\" style=\"margin:0; padding:0; border:none; display:block;\" border=\"0\" alt=\"Viral Playlists\" />
                                          </a> 
                                         
                                    				</td>
                                    			</tr>
                                                <!-- Hero Image, Flush : BEGIN -->
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <img src=\"https://viralplaylists.com/wp-content/uploads/2015/09/spark_01.jpg\" width=\"400\" height=\"200\" alt=\"alt_text\" border=\"0\" style=\"width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;\" class=\"g-img\">
                                                    </td>
                                                </tr>
                                                <!-- Hero Image, Flush : END -->
                                    
                                                <!-- 1 Column Text + Button : BEGIN -->
            
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <h1 style=\"margin: 0 0 10px; font-size: 25px; line-height: 30px; color: #6f42c1; font-weight: normal;\">Release Notification</h1>
                                                                     <p style=\"margin: 0 0 10px;\">Hi $name,</p>
                                    								<p style=\"margin: 0 0 10px;\">Please check your dashboard.<br /></p>
                                    								<p style=\"margin: 0 0 10px;\">More digital stores have been added to your release $title!.</p><br>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style=\"padding: 0 20px 20px;\">
                                                                    <!-- Button : BEGIN -->
                                                                    <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: auto;\">
                                                                        <tr>
                                                                            <td>
                                    											<a class=\"button-a button-a-primary\" href=\"https://app.viralplaylists.com/dashboard\" style=\"background: #6f42c1;  font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;\">Check Update</a>
                                    										</td>
                                                                        </tr>
                                                                    </table>
                                                                    <!-- Button : END -->
                                                                </td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <p style=\"margin: 0 0 10px;\">Warm Regards,</p>
                                    								<p style=\"margin: 0 0 10px;\"> Viralplaylists Digital Team<br /></p>
                                                                </td>
                                                            </tr>
                                                            <tr style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;  margin: 5; padding: 20px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; color:#fffff;\">
                        										<td class=\"content-block\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: center;\" valign=\"top\">
                                                                    <br><b>Need help? Got questions to ask?</b><br>
                                                                    <a href=\"mailto:distro@viralplaylists.com\" style=\"text-decoration: none;\">distro@viralplaylists.com</a> | <a href=\"tel:+2348151240876\" style=\"text-decoration: none;\">+2348151240876</a
                                                                        <br><br>
                                                                    <a href=\"https://twitter.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://i1.wp.com/marsfallpodcast.com/wp-content/uploads/2017/09/Twitter-Download-PNG.png\" alt=\"Twitter\">
                                                                    </a>
                                                                    <a href=\"https://facebook.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://www.freeiconspng.com/uploads/facebook-icon-5.png\" alt=\"Facebook\">
                                                                    </a>
                                                                    <a href=\"https://indtagram.com/viral_playlists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png\" alt=\"Instagram\">
                                                                    </a>
                        										</td>
                        									</tr>
                                    
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- 1 Column Text + Button : END -->
                                    
                                                <!-- Background Image with Text : BEGIN -->
                                                
                                    	    </table>
                                    	    <!-- Email Body : END -->
                                    
                                    	    <!-- Email Footer : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	        <tr>
                                    	            <td style=\"padding: 20px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #ffffff; background-color: #6f42c1;\">
                                    	                Copyright &copy; 2019 Viral Playlists Digital<br />
                                    	                <div style=\"text-align: left; font-size: 11px; line-height: 1.3em;\">
                                    	                    This email was sent to $email because this email address was used to sign up at
                                                            <a href=\"viralplaylists.com\" target=\"_blank\" style=\"text-decoration: none;\"> viralplaylists.com </a>.
                                                            Please note that we will never ask you to provide your password. If this message has been sent to you in error,
                                                            please delete this immediately or notify our Support Team.
                                                        </div>
                                                    </td>
                                    	        </tr>
                                    	    </table>
                                    	    <!-- Email Footer : END -->
                                    
                                    	    
                                        <!--[if mso | IE]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                        </center>
                                    </body> ";
                                			 			
        			$html_body .= "</html>";
        			
        			$text_body = "Hi $name\r\n";
        			$text_body .= " Please check your dashboard \r\n";
        			$text_body .= "https//app.viralplaylists.com/dashboard \r\n";
        			$text_body .= "More digital stores have been added to your release $title \r\n";
        			$text_body .= "--------------------------------------------- \r\n";
        			$text_body .= "  \r\n";
        			$text_body .= "Regards\r\n";
        			$text_body .= "Eva\r\n";
        			$text_body .= "Artist Support\r\n";
        			$text_body .= "Viral Playlists Digital\r\n";
        			
        			$mail =  new Email;
        			
        			if($created_project_data["is_distributed"] == true){
        			    
        			    $sendMail= $mail->send($from, $from_name, $to, $to_name, $subject, $html_body, $text_body);
        			    
        			    }
				
			} else {
				$response = [ 'status' => false, 'message' =>  'No changes was made.', 'data' => $update  ];
			}
		}			
	
	}
}

if (isset($_POST['action']) && $_POST['action'] == 'TAKEDOWN_RELEASE')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project_user_id		= (isset($_POST['user_id'])) ? trim($_POST['user_id']) : null;
		$project 		=  new Project;
		$user 		=  new User;
		$created_project_data 	= $project->get($project_id);
		$user_data = $user->get($project_user_id);
		$email = $user_data['email'];
		$name =  $user_data['name'];
		$title = $created_project_data['title'];
		
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$is_takendown = 1;
				$is_distributed = 0;
				
			$update_data = [
				'is_takendown' => $is_takendown,
			  'is_distributed'   => $is_distributed
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'project Taken down!', 'data' => null ];
				
				$from = $config['default_email'];
        			$from_name = 'Viralplaylists Digital';
        			$to = $email;
        			$to_name = $name;
        			$subject = 'Your Release Has Been Taken Down From The Selected Digital Stores!';
        			
        			$html_body = "<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">";
        			
            			$html_body .= "<head>
                                        <meta charset=\"utf-8\"> <!-- utf-8 works for most cases -->
                                        <meta name=\"viewport\" content=\"width=device-width\"> <!-- Forcing initial-scale shouldn't be necessary -->
                                        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"> <!-- Use the latest (edge) version of IE rendering engine -->
                                        <meta name=\"x-apple-disable-message-reformatting\">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
                                        <title>Release Notification</title>
                                    	<link rel=\"stylesheet\" type=\"text/css\" href=\"../assets/css/bullet_styles.css\">
                                    </head>"; 
        			    $html_body .= "<body width=\"100%\" style=\"margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f6f6f6;\">
                                    	<center style=\"width: 100%; background-color: #f6f6f6;\">
                                        <!--[if mso | IE]>
                                        <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #f6f6f6;\">
                                        <tr>
                                        <td>
                                        <![endif]-->
                                    
                                            <!-- Visually Hidden Preheader Text : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                              Your release has been taken down from the selected Digital store!   
                                            </div>
                                            <!-- Visually Hidden Preheader Text : END -->
                                    
                                            <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
                                            <!-- Preview Text Spacing Hack : BEGIN -->
                                            <div style=\"display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">
                                    	        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
                                            </div>
                                            <!-- Preview Text Spacing Hack : END -->
                                    
                                           <!-- Email Body : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	       <tr>
                                    				<td style=\"padding: 30px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; background-color: #6f42c1; color:#ffffff;\">
                                    			
                                           <a href=\"#\" target=\"_blank\">
                                            <img src=\"https://send.viralplaylists.com/themes/aurora/assets/logo_white.png\" width=\"131\" height=\"40\" style=\"margin:0; padding:0; border:none; display:block;\" border=\"0\" alt=\"Viral Playlists\" />
                                          </a> 
                                         
                                    				</td>
                                    			</tr>
                                                <!-- Hero Image, Flush : BEGIN -->
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <img src=\"https://viralplaylists.com/wp-content/uploads/2015/09/spark_01.jpg\" width=\"400\" height=\"200\" alt=\"alt_text\" border=\"0\" style=\"width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;\" class=\"g-img\">
                                                    </td>
                                                </tr>
                                                <!-- Hero Image, Flush : END -->
                                    
                                                <!-- 1 Column Text + Button : BEGIN -->
            
                                                <tr>
                                                    <td style=\"background-color: #ffffff;\">
                                                        <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <h1 style=\"margin: 0 0 10px; font-size: 25px; line-height: 30px; color: #6f42c1; font-weight: normal;\">Release Notification</h1>
                                                                    <p style=\"margin: 0 0 10px;\">Hi Superstar,</p>
                                    								<p style=\"margin: 0 0 10px;\">We are pleased to announce to you that your release <b>$title</b> has been taken down from the selected digital stores.<br /></p>
                                    								<p style=\"margin: 0 0 10px;\">Please login to your dashboard to check it out.</p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style=\"padding: 0 20px 20px;\">
                                                                    <!-- Button : BEGIN -->
                                                                    <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: auto;\">
                                                                        <tr>
                                                                            <td>
                                    											<a class=\"button-a button-a-primary\" href=\"https://app.viralplaylists.com/dashboard\" style=\"background: #6f42c1;  font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;\">View Release</a>
                                    										</td>
                                                                        </tr>
                                                                    </table>
                                                                    <!-- Button : END -->
                                                                </td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td style=\"padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">
                                                                    <p style=\"margin: 0 0 10px;\">Warm Regards,</p>
                                    								<p style=\"margin: 0 0 10px;\"> Viralplaylists Digital Team<br /></p>
                                                                </td>
                                                            </tr>
                                                            <tr style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;  margin: 5; padding: 20px; text-align: left; font-family: sans-serif; font-size: 20px; line-height: 20px; color:#fffff;\">
                        										<td class=\"content-block\" style=\"font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: center;\" valign=\"top\">
                                                                    <br><b>Need help? Got questions to ask?</b><br>
                                                                    <a href=\"mailto:distro@viralplaylists.com\" style=\"text-decoration: none;\">distro@viralplaylists.com</a> | <a href=\"tel:+2348151240876\" style=\"text-decoration: none;\">+2348151240876</a
                                                                        <br><br>
                                                                    <a href=\"https://twitter.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://i1.wp.com/marsfallpodcast.com/wp-content/uploads/2017/09/Twitter-Download-PNG.png\" alt=\"Twitter\">
                                                                    </a>
                                                                    <a href=\"https://facebook.com/viralplaylists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://www.freeiconspng.com/uploads/facebook-icon-5.png\" alt=\"Facebook\">
                                                                    </a>
                                                                    <a href=\"https://instagram.com/viral_playlists\" target=\"_blank\" style=\"text-decoration: none;\">
                                                                        <img width=\"25\" src=\"https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png\" alt=\"Instagram\">
                                                                    </a>
                        										</td>
                        									</tr>
                                    
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- 1 Column Text + Button : END -->
                                    
                                                <!-- Background Image with Text : BEGIN -->
                                                
                                    	    </table>
                                    	    <!-- Email Body : END -->
                                    
                                    	    <!-- Email Footer : BEGIN -->
                                            <table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" style=\"margin: auto;\" class=\"email-container\">
                                    	        <tr>
                                    	            <td style=\"padding: 20px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #ffffff; background-color: #6f42c1;\">
                                    	                Copyright &copy; 2019 Viral Playlists Digital<br />
                                    	                <div style=\"text-align: left; font-size: 11px; line-height: 1.3em;\">
                                    	                    This email was sent to $email because this email address was used to sign up at
                                                            <a href=\"viralplaylists.com\" target=\"_blank\" style=\"text-decoration: none;\"> viralplaylists.com </a>.
                                                            Please note that we will never ask you to provide your password. If this message has been sent to you in error,
                                                            please delete this immediately or notify our Support Team.
                                                        </div>
                                                    </td>
                                    	        </tr>
                                    	    </table>
                                    	    <!-- Email Footer : END -->
                                    
                                    	    
                                        <!--[if mso | IE]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                        </center>
                                    </body> ";
                                			 			
        			$html_body .= "</html>";	
        			$text_body = " Hi Superstar!\r\n";
        			$text_body .= "We are pleased to announce to you that your release $title has been taken down from the selected digital stores.\r\n";
        			$text_body .= "Please login to your dashboard to check it out\r\n";
        			$text_body .= "https://app.viralplaylists.com/login\r\n";
        			
        			$mail =  new Email;
        			$sendMail= $mail->send($from, $from_name, $to, $to_name, $subject, $html_body, $text_body);
			
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}  

if (isset($_POST['action']) && $_POST['action'] == 'UNTAKEDOWN_RELEASE')
{
	if (csrf_verify('post') === true) {
	
		$project_id		= (isset($_POST['project_id'])) ? trim($_POST['project_id']) : null;
		$project 		=  new Project;
		$created_project_data 	= $project->get($project_id);
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $created_project_data ];
		}else {
				$is_takendown = 0;
			$update_data = [
				'is_takendown' => $is_takendown
			];
			
			if ($project->updateProject('id', $project_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'project has been marked untakedown!', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}





echo json_encode($response);