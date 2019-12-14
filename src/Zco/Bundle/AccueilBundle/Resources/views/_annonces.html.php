<?php if ($quel_bloc == 'quiz' && verifier('quiz_voir')){ ?>
	<h2 class="mod_communaute">Quiz du moment</h2>
	<?php if(!empty($QuizSemaine['image'])){ ?>
	<a href="/quiz/quiz-<?php echo $QuizSemaine['id']; ?>-<?php echo rewrite($QuizSemaine['nom']); ?>.html">
		<img class="flot_droite" src="<?php echo htmlspecialchars($QuizSemaine['image']); ?>" alt="" />
	</a>
	<?php } ?>

	Le quiz suivant de la catégorie « <?php echo htmlspecialchars($QuizSemaine['Categorie']['nom']); ?> »
	est actuellement mis en valeur par l'équipe du site :<br /><br />

	<div>
		<strong><a href="/quiz/quiz-<?php echo $QuizSemaine['id']; ?>-<?php echo rewrite($QuizSemaine['nom']); ?>.html">
			<?php echo htmlspecialchars($QuizSemaine['nom']); ?>
		</a></strong>
		<?php if(!empty($QuizSemaine['description'])){ ?><br />
		<?php echo htmlspecialchars($QuizSemaine['description']); ?>
		<?php } ?>
	</div>
<?php } elseif ($quel_bloc == 'sujet' && verifier('voir_sujets')){ ?>
	<h2 class="mod_communaute">Sujet du moment</h2>
	<?php if(!empty($SujetSemaine['image'])){ ?>
	<a href="/forum/sujet-<?php echo $SujetSemaine['sujet_id']; ?>-<?php echo rewrite($SujetSemaine['sujet_titre']); ?>.html">
		<img class="flot_droite" src="<?php echo htmlspecialchars($SujetSemaine['image']); ?>" alt="" />
	</a>
	<?php } ?>

	Le sujet suivant du forum « <?php echo htmlspecialchars($SujetSemaine['cat_nom']); ?> »
	est actuellement mis en valeur par l'équipe du site :<br /><br />

	<div>
		<strong><a href="/forum/sujet-<?php echo $SujetSemaine['sujet_id']; ?>-<?php echo rewrite($SujetSemaine['sujet_titre']); ?>.html">
			<?php echo htmlspecialchars($SujetSemaine['sujet_titre']); ?>
		</a></strong>
		<?php if(!empty($SujetSemaine['sujet_sous_titre'])){ ?><br />
		<?php echo htmlspecialchars($SujetSemaine['sujet_sous_titre']); ?>
		<?php } ?>
	</div>
<?php } else if ($quel_bloc == 'sondage' && verifier('sondages_voir')) { ?>
	<h2 class="mod_sondage">Sondages</h2>
	<?php echo $view->render('ZcoSondagesBundle::_bloc_accueil.html.php',
		array(
			'question' => $question,
			'reponses' => $reponses,
			'sondage'  => $sondage,
			'a_vote'   => $a_vote,
		)) ?>
<?php } elseif ($quel_bloc == 'recrutement' && verifier('recrutements_voir')) { ?>
	<h2 class="mod_communaute">Recrutements en cours</h2>
	<ul>
		<?php foreach($ListerRecrutements as $r){ ?>
			<li><a href="recrutement/recrutement-<?php echo $r['recrutement_id']; ?>-<?php echo rewrite($r['recrutement_nom']); ?>.html">
				<?php echo htmlspecialchars($r['recrutement_nom']); ?></a>
				(<span style="color: <?php echo $r['groupe_class']; ?>"><?php echo htmlspecialchars($r['groupe_nom']); ?></span>)
			</li>
		<?php } ?>
	</ul>
<?php } elseif ($quel_bloc == 'billet') { ?>
	<h2 class="mod_communaute">Billet du moment</h2>
	<div>
		<?php
			echo $view->render('ZcoBlogBundle::_intro_module.html.php', array(
						'Auteurs' => $BilletAuteurs,
						'InfosBillet' => $BilletSemaine,
						'nb' => 0,
						'cote' => 'droite'
					));
		?>
	</div>
<?php } elseif ($quel_bloc == 'billet_hasard') { ?>
	<h2 class="mod_communaute">Billet au hasard</h2>
	<div>
		<?php
			echo $view->render('ZcoBlogBundle::_intro_module.html.php', array(
						'Auteurs' => $BilletAuteurs,
						'InfosBillet' => $BilletHasard,
						'nb' => 0,
						'cote' => 'droite'
					));
		?>
	</div>
<?php } elseif ($quel_bloc == 'dictee' ) { ?>
	<h2 class="mod_dictees">Dictée à la une</h2>
	<div class="accueil_dictee">
	<?php
		echo $view->render('ZcoDicteesBundle::_dictee_en_avant.html.php', compact('Dictee'));
	?>
	</div>
	<?php } else { ?>
	<h2 class="mod_communaute">Annonces</h2>
	<div><?php echo $view['messages']->parse($Informations); ?></div>
<?php } ?>
