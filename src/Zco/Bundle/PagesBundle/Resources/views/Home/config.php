<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier le bloc des annonces</h1>

<p>
	Le bloc « à tout faire » est le bloc en haut à gauche du module d'accueil. Il vous
	permet d'afficher diverses informations. Vous pouvez configurer sur cette page quelle
	information précise afficher aux visiteurs.
</p>

<form action="" method="post">
	<fieldset>
		<legend>Configuration du bloc « à tout faire »</legend>

		<label for="choix_bloc">Information à afficher : </label>
		<select name="choix_bloc">
			<option value="annonce"<?php echo ($bloc_accueil == 'annonce') ? ' selected="selected"' : ''; ?>>
				Afficher une annonce personnalisée
			</option>
			<option value="quiz"<?php echo ($bloc_accueil == 'quiz') ? ' selected="selected"' : ''; ?>>
				Mettre en avant un quiz
			</option>
			<option value="sujet"<?php echo ($bloc_accueil == 'sujet') ? ' selected="selected"' : ''; ?>>
				Mettre en avant un sujet
			</option>
			<option value="billet"<?php echo ($bloc_accueil == 'billet') ? ' selected="selected"' : ''; ?>>
				Mettre en avant un billet
			</option>
                        <option value="dictee"<?php echo ($bloc_accueil == 'dictee') ? ' selected="selected"' : ''; ?>>
				Mettre en avant une dictée
			</option>

			<option value="billet_hasard"<?php echo ($bloc_accueil == 'billet_hasard') ? ' selected="selected"' : ''; ?>>
				Afficher un billet au hasard
			</option>
		</select>
		<input type="submit" value="Envoyer" />
	</fieldset>
</form>

<p class="centre" id="toggle_links" style="display: none">
	<a href="#bloc_annonce">Modifier l'annonce personnalisée</a> |
	<a href="#bloc_sujet">Modifier le sujet mis en valeur</a> |
	<a href="#bloc_quiz">Modifier le quiz mis en valeur</a> |
	<a href="#bloc_billet">Modifier le billet mis en valeur</a> |
	<a href="#bloc_billet_hasard">Modifier les paramètres du billet au hasard</a> |
	<a href="#bloc_dictee">Modifier les paramètres de la dictée</a>
</p>

<div id="bloc_annonce">
	<form action="" method="post">
		<fieldset>
			<legend>Modifier l'annonce personnalisée</legend>
			<div class="send">
				<input type="submit" value="Envoyer" />
			</div>

			<label for="texte">Contenu de la brève :</label>
			<?php echo $view->render('::zform.html.php', array('texte' => $texte_zform)); ?>

			<div class="send">
				<input type="submit" value="Envoyer" />
			</div>
		</fieldset>
	</form>
</div>

<div id="bloc_sujet">
	<fieldset>
		<legend>Choix du sujet à mettre en avant</legend>
		<?php if(!empty($infos_sujet) && !empty($infos_sujet['sujet_titre'])){ ?>
		<p>
			Le sujet actuellement mis en valeur est
			<strong><a href="/forum/sujet-<?php echo $infos_sujet['sujet_id']; ?>.html">
				<?php echo htmlspecialchars($infos_sujet['sujet_titre']); ?>
			</a></strong> du forum « <?php echo htmlspecialchars($infos_sujet['cat_nom']); ?> ».
		</p>
		<?php } ?>

		<form action="" method="post">
			<label for="sujet">Entrez un fragment du titre du sujet :</label>
			<input type="text" name="sujet" id="sujet" value="<?php if(!empty($_POST['sujet'])) echo htmlspecialchars($_POST['sujet']); ?>" />
			<input type="submit" value="Choisir" />
		</form>

		<?php if(!empty($choix_sujets)){ ?>
		<p>Plusieurs sujets correspondent au titre indiqué, cliquez sur le bon :</p>
		<ul>
			<?php foreach($choix_sujets as $suj){ ?>
			<li>
				<a href="?sujet=<?php echo $suj['sujet_id']; ?>">
					<?php echo htmlspecialchars($suj['sujet_titre']); ?>
				</a>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>

		<br />
		<form action="" method="post">
			<label for="image_sujet">Choisissez une image à afficher :</label>
			<input type="text" name="image_sujet" id="image_sujet" value="<?php echo htmlspecialchars($image_sujet); ?>" />
			<input type="submit" value="Choisir" />
			
			<a id="topic-files-link" href="<?php echo $view['router']->path('zco_file_index', array('xhr' => 1, 'input' => 'image_sujet')) ?>">
				<img src="/img/popup.png" alt="Ouvre une nouvelle fenêtre" />
				Envoi de fichiers
			</a>
			<?php $view['javelin']->initBehavior('squeezebox', array(
        	    'selector' => '#topic-files-link', 
        	    'options' => array('handler' => 'iframe'),
        	)) ?>
		</form>
	</fieldset>
</div>

<div id="bloc_quiz">
	<fieldset>
		<legend>Choix du quiz à mettre en avant</legend>
		<?php if (!empty($infos_quiz) && !empty($infos_quiz['nom'])){ ?>
		<p>
			Le quiz actuellement mis en valeur est
			<strong><a href="<?php echo $view['router']->path('zco_quiz_show', ['id' => $infos_quiz['id'], 'slug' => rewrite($infos_quiz['nom'])]) ?>">
				<?php echo htmlspecialchars($infos_quiz['nom']); ?>
			</a></strong> dans la catégorie « <?php echo htmlspecialchars($infos_quiz['Categorie']['nom']); ?> ».
		</p>
		<?php } ?>

		<form action="" method="post">
			<label for="quiz">Entrez un fragment du nom du quiz&nbsp;:</label>
			<input type="text" name="quiz" id="quiz" value="<?php if(!empty($_POST['quiz'])) echo htmlspecialchars($_POST['quiz']); ?>" />
			<input type="submit" value="Choisir" />
		</form>

		<?php if (isset($choix_quiz) && count($choix_quiz) > 0){ ?>
		<p>Plusieurs quiz correspondent au titre indiqué, cliquez sur le bon :</p>
		<ul>
			<?php foreach ($choix_quiz as $quiz){ ?>
			<li>
				<a href="?quiz=<?php echo $quiz['id']; ?>">
					<?php echo htmlspecialchars($quiz['nom']); ?>
				</a>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>

		<br />
		<form method="post" action="">
			<label for="image_quiz">Choisissez une image à afficher :</label>
			<input type="text" name="image_quiz" id="image_quiz" value="<?php echo htmlspecialchars($image_quiz); ?>" />
			<input type="submit" value="Choisir" />

		    <a id="quiz-files-link" href="<?php echo $view['router']->path('zco_file_index', array('xhr' => 1, 'input' => 'image_quiz')) ?>">
				<img src="/img/popup.png" alt="Ouvre une nouvelle fenêtre" />
				Envoi de fichiers
			</a>
			<?php $view['javelin']->initBehavior('squeezebox', array(
        	    'selector' => '#quiz-files-link', 
        	    'options' => array('handler' => 'iframe'),
        	)) ?>
		</form>
	</fieldset>
</div>

<div id="bloc_billet">
	<fieldset>
		<legend>Choix du billet à mettre en avant</legend>
		<?php if(!empty($infos_billet) && !empty($infos_billet['billet_nom'])){ ?>
		<p>
			Le billet actuellement mis en valeur est
			<strong><a href="/blog/billet-<?php echo $infos_billet['billet_id']; ?>.html">
				<?php echo htmlspecialchars($infos_billet['billet_nom']); ?>
			</a></strong> dans la catégorie « <?php echo htmlspecialchars($infos_billet['cat_nom']); ?> ».
		</p>
		<?php } ?>

		<form action="" method="post">
			<label for="billet">Entrez un fragment du nom du billet&nbsp;:</label>
			<input type="text" name="billet" id="billet" value="<?php if(!empty($_POST['billet'])) echo htmlspecialchars($_POST['billet']); ?>" />
			<input type="submit" value="Choisir" />
		</form>

		<?php if(!empty($choix_billet)){ ?>
		<p>Plusieurs billets correspondent au titre indiqué, cliquez sur le bon :</p>
		<ul>
			<?php foreach($choix_billet as $billet){ ?>
			<li>
				<a href="?billet=<?php echo $billet['blog_id']; ?>">
					<?php echo htmlspecialchars($billet['version_titre']); ?>
				</a>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
	</fieldset>
</div>

<div id="bloc_billet_hasard">
	<fieldset>
		<legend>Paramères des billets au hasard</legend>
		<p>Un billet est pris au hasard toutes les <?php echo TEMPS_BILLET_HASARD; ?> minutes, dans une sélection de catégories.</p>

		<p class="gras centre"><a href="editer-annonces.html?supprimer_cache">Forcer la régénération du billet au hasard</a></p>
		<form action="" method="post" class="centre">
			<label for="categories" class="nofloat">Catégories sélectionnées :</label><br />
			<select name="categories[]" id="categories" multiple="multiple" style="min-width: 250px;">
				<?php
				if ( is_array($categories_actuelles) )
				{
					foreach($categories as $categorie)
					{
						if(in_array($categorie['cat_id'], $categories_actuelles))
							echo '<option selected="selected" value="' . $categorie['cat_id'] . '">' . $categorie['cat_nom'] . '</option>';
						else
							echo '<option value="' . $categorie['cat_id'] . '">' . $categorie['cat_nom'] . '</option>';
					}
				}
				?>
			</select><br />

			<div class="send"><input type="submit" value="Choisir" /></div>
		</form>
	</fieldset>
</div>

<div id="bloc_dictee">
	<fieldset>
		<legend>Choix de la dictée à mettre en avant :</legend>
		<?php if ( !is_null($selectDictee) ) : ?>
			<p>
				La dictée actuellement mise en valeur est :<br/>
				<strong>
                    <a href="<?php echo $view['router']->path('zco_dictation_show', ['id' => $selectDictee->id, 'slug' => rewrite($selectDictee->titre)]) ?>">
						<?php echo $selectDictee->titre; ?>
					</a>
				</strong>
			</p><br/>
		<?php endif; ?>
		
		<form action="" method="post">
			<label for="dictee" style="width: 250px;">Entrez un fragment du nom de la dictée&nbsp;:</label>
			<input type="text" name="dictee" value="<?php if(!empty($_POST['dictee'])) echo htmlspecialchars($_POST['dictee']); ?>" />
			<input type="submit" value="Choisir" />
		</form>
		
		<?php if ( sizeof($listDictees) > 1 ) : ?>
		<p>Plusieurs dictées correspondent au titre indiqué, cliquez sur le bon :</p>
		<ul>
			<?php foreach($listDictees as $dictee) : ?>
			<li>
				<a href="?dictee=<?php echo $dictee['id']; ?>">
					<?php echo htmlspecialchars($dictee['titre']); ?>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</fieldset>
</div>

<?php $view['javelin']->initBehavior('toggle-blocks', array('current_block' => $bloc_accueil)) ?>