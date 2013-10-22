<?php session_start(); ?>

<?php
	include('data/template/header.php');
	$_SESSION['dir'] = '';
?>

        <div class="page-header">
          <h1>Millenium Falcon</h1>
        </div>
        <p class="lead">Web Media Server</p>

		<div class="panel panel-primary">
			<div id="explorer-action-bar" class="panel-heading btn-toolbar">
				<div class="btn-group">
					<button type="button" id="btn-prev" class="btn btn-default" onclick="scan_the_dir('..');"><span class="glyphicon glyphicon-chevron-left"></span> Retour</button>
					<button type="button" id="btn-home" class="btn btn-default" onclick="scan_the_dir('');"><span class="glyphicon glyphicon-home"></span> Home</button>
				</div>
				<div class="btn-group">
					<button type="button" id="btn-film" class="btn btn-default" onclick="scan_the_dir('/var/www/films');"><span class="glyphicon glyphicon-film"></span> Films</button>
					<button type="button" id="btn-serie" class="btn btn-default" onclick="scan_the_dir('/var/www/series');"><span class="glyphicon glyphicon-book"></span> Series</button>
					<button type="button" id="btn-music" class="btn btn-default" onclick="scan_the_dir('/var/www/musique');"><span class="glyphicon glyphicon-music"></span> Musique</button>
					<button type="button" id="btn-softs" class="btn btn-default" onclick="scan_the_dir('/var/www/softs');"><span class="glyphicon glyphicon-hdd"></span> Logiciels</button>
				</div>
			</div>
			<div id="explorer">
				<?php include('home.php'); ?>
			</div>
		</div>
		
		<!-- Modal wait -->
		<div class="modal fade" id="myModal-load" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
			  <div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title">Chargement</h4>
				</div>
				<div class="modal-body">
					Patientez ...
				</div>
				<div class="modal-footer">
				  <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
				</div>
			  </div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
		 <!-- Modal Film -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style="opacity: 0.93;">
			  <div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title" id="movie-title">Film</h4><br />
				  <small id="movie-tagline"></small>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-3" id="movie-poster">
							
						</div>
						<div class="col-md-9">
							<div class="row">
								<div class="col-md-2">
									Sortie
								</div>
								<div class="col-md-10">
									<span id="movie-release-date"></span>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2">
									Pays
								</div>
								<div class="col-md-10">
									<span id="movie-countries"></span>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2">
									Genres
								</div>
								<div class="col-md-10">
									<span id="movie-genres"></span>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2">
									Résumé
								</div>
								<div class="col-md-10">
									<span id="movie-resume"></span>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2">
									Note
								</div>
								<div class="col-md-6">
									<div class="progress">
									  <div class="progress-bar progress-bar-success"  id="movie-votes" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="10">
										<span class="sr-only"></span>
									  </div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2">
									Lien
								</div>
								<div class="col-md-10" style="overflow: hidden;">
									<span id="movie-link"></span>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2">
									
								</div>
								<div class="col-md-10">
									<span id="movie-play"></span>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
						<ul class="pager">
							<li><a href="#">&larr; Previous</a></li>
							<li><a href="#">Next &rarr;</a></li>
						</ul>
						</div>
					</div>
				</div>
				<div class="modal-footer">
				  <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
				</div>
			  </div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
<?php 	include('data/template/footer.php'); ?>

<!-- Movies scripts -->
<script src="/data/js/movies.js"></script>
</body></html>
