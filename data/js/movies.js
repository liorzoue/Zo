var diID = "explorer",
	dir = '',
	key = 'your_api_key',
	conf = { }; /* API CONFIG */
	
function get_db_config() {
	var xhr = new XMLHttpRequest();
	xhr.open("GET", "http://private-c689d-themoviedb.apiary.io/3/configuration?api_key=" + key);
	xhr.setRequestHeader("Accept", "application/json");
	xhr.onreadystatechange = function () {
	  if (this.readyState == 4) {
		conf = JSON.parse(this.responseText);
	  }
	};
	xhr.send(null);
}

function ajax_api(api, req, callback) {
	var api_url = 'http://private-c689d-themoviedb.apiary.io';
	
	var xhr = new XMLHttpRequest();
	xhr.open('GET', api_url + api + id + "?api_key=" + key);
	xhr.setRequestHeader("Accept", "application/json");
	xhr.onreadystatechange = function () { if (this.readyState == 4) { r = JSON.parse(this.responseText); } else { r = { result: false } } };
	xhr.send(null);
	callback(r);
}

function get_movie_info(id) {
	function after(r) {
		genre='';
		for(i=0;i<r.genres.length;i++) {
			genre+=r.genres[i].name+', ';
		}
		genre = genre.substring(0, genre.length-2)+'.';
		
		production_countries='';
		for(i=0;i<r.production_countries.length;i++) {
			production_countries+=r.production_countries[i].name+', ';
		}
		production_countries = production_countries.substring(0, production_countries.length-2)+'.';
		
		tagline = r.tagline;
		$('#movie-resume').html(r.overview);
		$('#movie-genres').html(genre);
		$('#movie-countries').html(production_countries);
		$('#movie-tagline').html(tagline);
	}
	
	ajax_api('/3/movie/', id, after);
	
	/*
	// var xhr = new XMLHttpRequest();
	// xhr.open("GET", "http://private-c689d-themoviedb.apiary.io/3/movie/" + id + "?api_key=" + key);
	// xhr.setRequestHeader("Accept", "application/json");
	// xhr.onreadystatechange = function () {
	  // if (this.readyState == 4) {
		// r = JSON.parse(this.responseText);
		
		
	  // }
	// };
	// xhr.send(null);
	*/
}
	
function get_file_info (file) {
	// var callback = function foo (response) {
		// return response;
	// };
	
	function movie(response) {
		res = JSON.parse(response);

		if(parseInt(res.total_results, 10) != 0) {
				
			sres = res.results[0];
			title = sres.original_title;
			date = sres.release_date;
			note = sres.vote_average;
			poster_url = sres.poster_path;
			backdrop =  conf.images.base_url + 'original' + sres.backdrop_path;
			
			img = '<img src="' + conf.images.base_url + conf.images.poster_sizes[1] + poster_url + '" class="img-thumbnail img-responsive" alt="Poster">';
			
			
			$('#movie-title').html(title);
			$('#movie-responses').html(res.total_results);
			$('#movie-release-date').html(date);
			$('#movie-votes').css('width', parseFloat(note)*10 + '%');
			$('#movie-poster').html(img);
			
			// $('#movie-backdrop').append(backdrop);
			$('#myModal').css('background-image', "url('" + backdrop + "')");
			$('#myModal').css('background-size', '100%');
			
			get_movie_info(sres.id);
			
			$('#myModal-load').modal('hide');
			$('#myModal').modal('show');
		} else {
			title = file;
			date = '--';
			note = '--';
			img = '<span class="glyphicon glyphicon-ban-circle"></span>';
			$('#movie-resume').html('No movie found');
		}
		
		$('#movie-title').html(title);
		$('#movie-release-date').html(date);
		$('#movie-votes').html(note);
		$('#movie-poster').html(img);
	
	}
	
	function music(response) {
		r = JSON.parse(response);
		
		img = '<img src="data/img/albumart.jpg" class="img-thumbnail img-responsive" alt="Poster">';
		
		// N/A
		date = 'N/A';
		note = 'N/A';
		genre = 'N/A';
		production_countries = 'N/A';
		tagline = 'N/A';
		
		$('#movie-poster').html(img);
		$('#movie-title').html(file);
		$('#movie-resume').html(r.test);
		
		
		$('#movie-release-date').html(date);
		$('#movie-votes').html(note);
		$('#movie-genres').html(genre);
		$('#movie-countries').html(production_countries);
		$('#movie-tagline').html(tagline);
		
		// show modal
		$('#myModal-load').modal('hide')
		$('#myModal').modal('show')
	}
	
	$('#myModal').css('background-image', "");
	$('#myModal').css('background-size', '100%');
			
	if (dir.substring(0,14) == '/var/www/films') { callback = movie; }
	if (dir.substring(0,14) == '/var/www/musiq') { callback = music; }
			
	if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); }
	else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
	
	xmlhttp.onreadystatechange = function() {
		console.log('readyState:'+xmlhttp.readyState);
		console.log('status:'+xmlhttp.status);
		console.log('responseText'+xmlhttp.responseText);
		if (xmlhttp.readyState==4 && xmlhttp.status==200) { callback(xmlhttp.responseText); } else {
			$('#myModal-load').modal('hide');
		}
	}
	
	xmlhttp.open("GET","get_file_info.php?q="+file,true);
	xmlhttp.send();
	
	movie_url = 'http://'+window.location.hostname+':'+window.location.port+'/'+dir.substring(9, dir.length)+'/'+file;
	$('#movie-link').html('<code><a href="'+movie_url+'">'+movie_url+'</a></code>');
}

function scan_the_dir(str) {
	// alert(str);
	// if (str=="") {
		// document.getElementById(diID).innerHTML="";
		// return;
	// }
	function dir_prev (d) {
		if (d.substring(d.length-1, d.length) == '/') { d = d.substring(0, d.length-1); }

		var pos = d.lastIndexOf("/");
		d = d.substring(0, pos);
		
		return d;
	}		
	
	if(str == '' || str == '/var/www') {
		dir = '/var/www';
	} else {
		if (str == '..') {
			dir = dir_prev(dir);
		} else {
			dir = dir + '/' + str;
		}
	}
	
	if(str.substring(0, 9) == '/var/www/') { dir = str; }

	if (dir=='/var/www') { $('#btn-prev').attr("disabled", "disabled"); }
	else { $('#btn-prev').removeAttr("disabled", "disabled"); }
	
	str = dir;
	
	if (window.XMLHttpRequest) { xmlhttp=new XMLHttpRequest(); }
	else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) { document.getElementById(diID).innerHTML= xmlhttp.responseText; }
	}
	
	xmlhttp.open("GET","explore.php?q="+str,true);
	xmlhttp.send();
}

$(function () {
	scan_the_dir('');
	
	get_db_config();
});
