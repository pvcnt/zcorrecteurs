<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></h1>

<?php if(!empty($InfosBillet['version_sous_titre'])){ ?>
<h2><?php echo htmlspecialchars($InfosBillet['version_sous_titre']); ?></h2>
<?php } ?>

<div class="UI_column_menu">
	<div class="box">
		<?php if($verifier_editer == true){ ?>
		<a href="editer-<?php echo $_GET['id']; ?>.html">
			<img src="/img/editer.png" alt="" /> Modifier le contenu de ce billet
		</a><br /><br />
		<?php } ?>

		<strong>État actuel : <?php echo mb_strtolower($Etats[$InfosBillet['blog_etat']]); ?></strong><br />

		<?php if(in_array($InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && $createur == true){ ?>
		<a href="proposer-<?php echo $_GET['id']; ?>.html" title="Proposer ce billet à la validation">
			<img src="/bundles/zcoblog/img/proposer.png" alt="" /> Proposer ce billet
		</a><br />
		<?php } ?>

		<?php if(((verifier('blog_choisir_etat') && $autorise == true) || verifier('blog_valider')) && !in_array($InfosBillet['blog_etat'], array(BLOG_VALIDE, BLOG_PROPOSE))){ ?>
		<a href="valider-<?php echo $InfosBillet['blog_id']; ?>.html" title="Valider ce billet">
			<img src="/bundles/zcoblog/img/valider.png" alt="" /> Valider ce billet
		</a><br />
		<?php } elseif(verifier('blog_devalider') && $InfosBillet['blog_etat'] == BLOG_VALIDE){ ?>
		<a href="devalider-<?php echo $InfosBillet['blog_id']; ?>.html" title="Dévalider ce billet">
			<img src="/bundles/zcoblog/img/refuser.png" alt="" /> Dévalider ce billet
		</a><br />
		<?php } ?>

		<?php if((in_array($InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && $createur == true) ||
		verifier('blog_supprimer')){ ?>
		<a href="supprimer-<?php echo $InfosBillet['blog_id']; ?>.html">
			<img src="/img/supprimer.png" alt="" /> Supprimer ce billet
		</a><br />
		<?php } ?>

		<br />
		<a href="billet-<?php echo $_GET['id']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html">
			<img src="/img/misc/zoom.png" alt="" />
			Visualiser le billet<br />
		</a>

		<?php if(verifier('blog_voir_historique') || $autorise == true){ ?>
		<a href="validation-<?php echo $_GET['id']; ?>.html">
			<img src="/bundles/zcoblog/img/validation.png" alt="" />
			Voir l'historique de validation
		</a><br />
		<?php } ?>

		<?php if(verifier('blog_voir_versions') || $redacteur == true){ ?>
		<a href="versions-<?php echo $_GET['id']; ?>.html">
			<img src="/bundles/zcoblog/img/versions.png" alt="" />
			Voir l'historique des modifications
		</a>
		<?php } ?>
	</div>

	<div class="box UI_rollbox">
		<div class="title">Logo de l'article</div>

		<div class="content centre">
			<img src="/<?php echo $InfosBillet['blog_image']; ?>" alt="Logo de l'article" id="image_actuelle" style="width: 70px; height: 70px;" />
		</div>

		<div class="hidden hr">
			<?php if($verifier_editer){ ?>
			<form method="post" action="">
				<label for="image" class="nofloat">Changer le logo :</label><br />
				<input type="text" name="image" id="image" value="<?php echo htmlspecialchars($InfosBillet['blog_image']); ?>" />
				<br />

				<a href="<?php echo $view['router']->path('zco_file_index', array('input' => 'image', 'xhr' => 1)) ?>" id="blog-files-link">
					<img src="/img/popup.png" alt="Ouvre une nouvelle fenêtre" />
					Envoi d'images
				</a>
				
				<?php $view['javelin']->initBehavior('squeezebox', array(
            	    'selector' => '#blog-files-link', 
            	    'options' => array('handler' => 'iframe'),
            	)) ?>

				<span id="ajax_logo"></span>
				<input type="submit" value="Changer" />
			</form>
			<?php } ?>
		</div>
	</div>

	<?php if(verifier('blog_choisir_comms')){ ?>
	<div class="box UI_rollbox">
		<div class="title">Choix des commentaires</div>

		<div class="hidden">
			<form method="post" id="change_comments_form" action="admin-billet-<?php echo $_GET['id'] ?>.html">
				<select name="commentaires" id="commentaires"
				onchange="if(this.value == <?php echo COMMENTAIRES_TOPIC; ?>) $('div_lien').setStyle('display', 'inline'); else $('div_lien').setStyle('display', 'none');">
					<option value="<?php echo COMMENTAIRES_OK; ?>"<?php if($InfosBillet['blog_commentaires'] == COMMENTAIRES_OK) echo ' selected="selected"'; ?>>Activés</option>
					<option value="<?php echo COMMENTAIRES_NONE; ?>"<?php if($InfosBillet['blog_commentaires'] == COMMENTAIRES_NONE) echo ' selected="selected"'; ?>>Désactivés</option>
					<option value="<?php echo COMMENTAIRES_TOPIC; ?>"<?php if($InfosBillet['blog_commentaires'] == COMMENTAIRES_TOPIC) echo ' selected="selected"'; ?>>Sur le forum</option>
				</select>

				<div id="div_lien" <?php if($InfosBillet['blog_commentaires'] != COMMENTAIRES_TOPIC) echo ' style="display: none;"'; ?>>
					<input type="text" name="lien" id="lien" value="<?php echo htmlspecialchars($InfosBillet['blog_lien_topic']); ?>" />
				</div>

				<input type="submit" value="Changer" />
			</form>
			
			<?php $view['javelin']->initBehavior('ajax-form', array('id' => 'change_comments_form')) ?>
		</div>
	</div>
	<?php } ?>

	<?php if($verifier_editer){ ?>
	<div class="box UI_rollbox">
		<div class="title">Redirection</div>

		<div class="hidden">
			<p>
				Cela vous permet de définir un article virtuel référencé dans
				le blog, mais redirigeant vers une ressource externe.
			</p>

			<form method="post" action="admin-billet-<?php echo $_GET['id'] ?>.html" id="change_redirection_form">
				<input type="text" name="redirection" id="redirection"
				value="<?php echo !is_null($InfosBillet['blog_url_redirection']) ? htmlspecialchars($InfosBillet['blog_url_redirection']) : 'http://'; ?>" />
				<input type="submit" value="Changer" />
			</form>
			
			<?php $view['javelin']->initBehavior('ajax-form', array('id' => 'change_redirection_form')) ?>
		</div>
	</div>
	<?php } ?>
</div>


<div class="UI_column_text">
	<div class="UI_rollbox">
		<div class="title">
			Date de publication :
			<?php echo dateformat($InfosBillet['blog_date_publication'], MINUSCULE); ?>
		</div>

		<div class="hidden">
			<?php if(verifier('blog_valider')){ ?>
			<form method="post" action="admin-billet-<?php echo $_GET['id'] ?>.html" id="change_pubdate_form">
				<label for="date_pub">Choisissez une date de publication :</label>
                <input type="text" name="date_pub" id="date_pub" value="<?php echo $InfosBillet['blog_date_publication'] ?>" />
                <?php $view['javelin']->initBehavior('datepicker', ['id' => 'date_pub']) ?>
				<input type="submit" name="changer_date" value="Changer" />
			</form>
			
			<?php $view['javelin']->initBehavior('ajax-form', array('id' => 'change_pubdate_form')) ?>
			<?php } ?>
		</div>
	</div>

	<div class="UI_rollbox">
		<div class="title"><?php echo count($Auteurs); ?> auteur<?php echo pluriel(count($Auteurs)); ?></div>

		<div class="hidden">
			<table class="UI_items simple">
				<tbody>
					<?php foreach($Auteurs as $a){ ?>
					<tr>
						<td>
							<a href="/membres/profil-<?php echo $a['utilisateur_id']; ?>-<?php echo rewrite($a['utilisateur_pseudo']); ?>.html"><?php echo htmlspecialchars($a['utilisateur_pseudo']); ?></a>
						</td>
						<td class="centre">
							Ajouté <?php echo dateformat($a['auteur_date'], MINUSCULE); ?>
						</td>
						<td><?php echo \Zco\Bundle\BlogBundle\Domain\Author::STATUSES[$a['auteur_statut']]; ?></td>
						<?php if($createur == true || verifier('blog_toujours_createur')){ ?>
						<td class="centre">
							<a href="editer-auteur-<?php echo $_GET['id']; ?>-<?php echo $a['utilisateur_id']; ?>.html" title="Modifier cet auteur">
								<img src="/img/editer.png" alt="Modifier" />
							</a>

							<a href="supprimer-auteur-<?php echo $_GET['id']; ?>-<?php echo $a['utilisateur_id']; ?>.html" title="Retirer cet auteur">
								<img src="/img/supprimer.png" alt="Retirer" />
							</a>
						</td>
						<?php } ?>
					</tr>
					<?php } ?>
				</tbody>
			</table>

			<?php if($createur == true || verifier('blog_toujours_createur')){ ?>
			<form action="" method="post">
				<label for="pseudo">Ajouter un auteur : </label>
				<input type="text" name="pseudo" id="pseudo" />
				
				<?php $view['javelin']->initBehavior('autocomplete', array(
				    'id' => 'pseudo', 
				    'callback' => $view['router']->path('zco_user_api_searchUsername'),
				)) ?>

				<select name="statut" id="statut">
					<?php foreach(\Zco\Bundle\BlogBundle\Domain\Author::STATUSES as $cle=>$valeur){ ?>
					<option value="<?php echo $cle; ?>"><?php echo htmlspecialchars($valeur); ?></option>
					<?php } ?>
				</select>

				<input type="submit" name="ajouter_auteur" value="Envoyer" />
			</form>
			<?php } ?>
		</div>
	</div>

	<div class="UI_rollbox">
		<div class="title"><?php echo count($Tags); ?> mot<?php echo pluriel(count($Tags)); ?> clé<?php echo pluriel(count($Tags)); ?></div>

		<div class="hidden">
			<?php if($verifier_editer){ ?>
			<form method="post" action="admin-billet-<?php echo $_GET['id'] ?>.html" id="change_tags_form">
				<label for="tags" class="nofloat">Modifier les mots clés :</label>
				<input type="text" name="tags" id="tags" size="60" value="<?php echo implode(', ', array_values($Tags)); ?>" />
				<input type="submit" name="ajouter_tag" value="Modifier" />
			</form>
			<?php $view['javelin']->initBehavior('ajax-form', array('id' => 'change_tags_form')) ?>
			<?php } else{ ?>
			Modifier les mots clés :
			<input type="text" size="60" value="<?php echo implode(', ', array_values($Tags)); ?>" readonly="readonly" />
			<?php } ?>
		</div>
	</div>

	<?php if(is_null($InfosBillet['blog_url_redirection'])){ ?>
	<div class="UI_box">
		<p>
			<?php if($verifier_editer == true){ ?>
			<span class="flot_droite">
				<a href="editer-<?php echo $_GET['id']; ?>.html#intro" title="Modifier l'introduction">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
			</span>
			<?php } ?>

			<?php echo $view['messages']->parse($InfosBillet['version_intro'], array(
			    'core.anchor_prefix' => $InfosBillet['blog_id'],
			    'files.entity_id' => $InfosBillet['blog_id'],
			    'files.entity_class' => 'Blog',
				'files.part' => 1,
			)); ?>
		</p>
		<br />

		<p>
			<?php if($verifier_editer == true){ ?>
			<span class="flot_droite">
				<a href="editer-<?php echo $_GET['id']; ?>.html#texte" title="Modifier le corps du billet">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
			</span>
			<?php } ?>

			<?php echo $view['messages']->parse($InfosBillet['version_texte'], array(
			    'core.anchor_prefix' => $InfosBillet['blog_id'],
			    'files.entity_id' => $InfosBillet['blog_id'],
			    'files.entity_class' => 'Blog',
				'files.part' => 2,
			)); ?>
		</p>
	</div>
	<?php } else{ ?>
	<div class="cadre centre">
		<p>
			<strong>Redirection.</strong> Cet article est virtuel et renvoie
			vers :<br />
			<a href="<?php echo htmlspecialchars($InfosBillet['blog_url_redirection']); ?>">
				<?php echo htmlspecialchars($InfosBillet['blog_url_redirection']); ?>
			</a>
		</p>
	</div>
	<?php } ?>
</div>
