<?php $view->extend('ZcoFileBundle::layout.html.php') ?>

<h1><span id="file-name"><?php echo htmlspecialchars($file['name']) ?></span>.<?php echo $file['extension'] ?></h1>

<div class="row">
	<div class="span6">
		<div class="accordion" id="file-accordion">
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#file-accordion" href="#file-accordion-metadata">
						<i class="icon-flag"></i>
						Méta-données
					</a>
				</div>
				<div id="file-accordion-metadata" class="accordion-body <?php echo (!$input && !$textarea) ? 'in' : 'collapse' ?>">
					<div class="accordion-inner">
						<?php if ($file['license_id']): ?>
							<strong>Licence :</strong>
							<a href="<?php echo htmlspecialchars($file->License['summary_url']) ?>">
								<?php echo htmlspecialchars($file->License['name']) ?>
							</a><br />
						<?php endif ?>
						<strong>Création :</strong>
						<?php echo $view['humanize']->dateformat($file['date'], MINUSCULE) ?><br />
						<strong>Modification :</strong>
						<?php echo $view['humanize']->dateformat($file['edition_date'], MINUSCULE) ?><br />
						<strong>Taille :</strong>
						<?php echo $view['humanize']->sizeformat($file['size']) ?><br />
						<strong>Type MIME :</strong>
						<?php echo htmlspecialchars($file->getMimetype()) ?>
					</div>
				</div>
			</div>
			
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#file-accordion" href="#file-accordion-image-thumb">
						<i class="icon-pencil"></i>
						Modifier les propriétés
					</a>
				</div>
				<div id="file-accordion-image-thumb" class="accordion-body collapse">
					<form id="file-form" class="accordion-inner" method="post" action="<?php echo $view['router']->generate('zco_file_api_edit', array('id' => $file['id'])) ?>">
						<label for="nom">Nom</label>
						<input type="text" value="<?php echo htmlspecialchars($file['name']) ?>" />
						
						<label for="license">Licence</label>
						<select name="license" id="license">
							<option value="">Aucune license</option>
							<?php foreach ($licenses as $license): ?>
							<option value="<?php echo $license['id'] ?>"<?php if ($file['license_id'] == $license['id']) echo ' selected="selected"' ?>>
								<?php echo htmlspecialchars($license['name']) ?>
							</option>
							<?php endforeach ?>
						</select><br />
						
						<input type="submit" class="btn btn-primary" value="Appliquer les nouvelles propriétés" />
						
						<?php $view['javelin']->initBehavior('zco-files-edit', array(
							'file_id' => $file['id'], 
							'form_id' => 'file-form',
							'name_selector' => '#file-name',
							'pseudo' => $_SESSION['pseudo'],
						)) ?>
					</form>
				</div>
			</div>
			
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#file-accordion" href="#file-accordion-reuse">
						<i class="icon-repeat"></i>
						Réutiliser le fichier
					</a>
				</div>
				<div id="file-accordion-reuse" class="accordion-body <?php echo ($input || $textarea) ? 'in' : 'collapse' ?>">
					<div class="accordion-inner">
						<label for="reuse-raw-file">Lien textuel vers le fichier</label>
						<input type="text" id="reuse-raw-file" class="span4" value="<?php echo htmlspecialchars($insertRawFile) ?>" onclick="this.select();" />
						<?php if ($input || $textarea): ?>
							<a href="#" title="Insérer le fichier" id="reuse-raw-file-link"><i class="icon-plus"></i></a>
							<?php $view['javelin']->initBehavior('zco-files-insert-link', array(
								'id' => 'reuse-raw-file-link',
								'value' => $insertRawFile,
								'input_id' => $input,
								'textarea_id' => $textarea,
							)) ?>
						<?php endif ?>
						
						<?php if (isset($insertFullFile)): ?>
							<label for="reuse-full-file">Lien vers le fichier avec image originale</label>
							<input type="text" id="reuse-full-file" class="span4" value="<?php echo htmlspecialchars($insertFullFile) ?>" onclick="this.select();" />
							<?php if ($input || $textarea): ?>
								<a href="#" title="Insérer le fichier" id="reuse-full-file-link"><i class="icon-plus"></i></a>
								<?php $view['javelin']->initBehavior('zco-files-insert-link', array(
									'id' => 'reuse-full-file-link',
									'value' => $insertFullFile,
									'input_id' => $input,
									'textarea_id' => $textarea,
								)) ?>
							<?php endif ?>
						<?php endif ?>
						
						<?php if (isset($insertThumbnail)): ?>
							<label for="reuse-thumbnail">Lien vers le fichier avec miniature</label>
							<input type="text" id="reuse-thumbnail" class="span4" value="<?php echo htmlspecialchars($insertThumbnail) ?>" onclick="this.select();" />
							<?php if ($input || $textarea): ?>
								<a href="#" id="reuse-thumbnail-link" title="Insérer le fichier"><i class="icon-plus"></i></a>
								<?php $view['javelin']->initBehavior('zco-files-insert-link', array(
									'id' => 'reuse-thumbnail-link',
									'value' => $insertThumbnail,
									'input_id' => $input,
									'textarea_id' => $textarea,
								)) ?>
							<?php endif ?>
						<?php endif ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span3">
		<div class="thumbnail">
			<a href="<?php echo $file->getWebPath() ?>"><img src="<?php echo $file->getImageWebPath() ?>" id="file-image" /></a>
			<br />
			
			<p><em>Cliquez pour voir l’original.</em></p>
			
			<?php /*if ($file->isImage()): ?>
				<p><a class="btn" href="#" id="file-edit-link">Modifier l’image en ligne</a></p>
			<?php endif*/ ?>
		</div>
	</div>
</div>

<?php /*if ($file->isImage()): ?>
	<script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>
	<?php $view['javelin']->initBehavior('zco-files-aviary', array(
		'file_id' => $file['id'], 
		'link_id' => 'file-edit-link',
		'base_url' => $_SERVER['HTTP_HOST'],
		'apiKey'   => $apiKey,
		'options'  => array(
			'image' => 'file-image',
			'signature' => $signature,
			'timestamp' => $timestamp,
			'hiresUrl'  => $file->getWebPath(),
		),
	)) ?>
<?php endif*/ ?>

<?php $view['vitesse']->requireResource('bootstrap-js') ?>
