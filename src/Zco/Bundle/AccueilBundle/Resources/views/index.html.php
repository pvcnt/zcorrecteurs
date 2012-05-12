<?php $view->extend('::layouts/default.html.php') ?>

<h1>Bienvenue sur le site des zCorrecteurs</h1>

<br />
<table class="UI_boxes home" cellspacing="7px">
	<tr><td rowspan="2">
		<span style="float: right; margin-left: 10px;">
			<a href="/blog/flux.html" title="S'abonner au flux du blog">
				<img src="/pix.gif" class="fff feed" alt="Flux" />
			</a>
		</span>

		<h2 class="mod_blog">Blog</h2>
		<p class="centre italique"><a href="/blog/">Accéder au blog</a></p>
		<?php
		if($ListerBillets)
		{
			$nb = 1;
			foreach($ListerBillets as $billet)
			{

				echo $view->render('ZcoBlogBundle::_intro_module.html.php', array(
					'Auteurs' => $BilletsAuteurs[$billet['blog_id']],
					'InfosBillet' => $billet,
					'nb' => $nb,
				));
				$nb++;
			}
		}
		else
			echo '<p>Aucun billet n\'a encore été publié.</p>';
		?>
	</td>

	<td>
		<span style="float: right; margin-left: 10px;">
			<?php if ($quel_bloc == 'twitter'): ?>
				<a href="<?php echo $view['router']->generate('zco_twitter_index') ?>">
					<img src="/bundles/zcotwitter/img/bouton.png"
					     alt="twitter"/>
				</a>
			<?php endif ?>
			<a href="<?php echo $view['router']->generate('zco_info_feed') ?>" title="S'abonner au flux des annonces">
				<img src="/pix.gif" class="fff feed" alt="Flux" />
			</a>
		</span>
		<?php echo $view->render('ZcoAccueilBundle::_annonces.html.php',
				array(
					'quel_bloc' => $quel_bloc,
					'Informations' => $Informations,
					'QuizSemaine' => $QuizSemaine,
					'SujetSemaine' => $SujetSemaine,
					'BilletSemaine' => $BilletSemaine,
					'BilletHasard' => $BilletHasard,
					'BilletAuteurs' => $BilletAuteurs,
					'ListerRecrutements' => $ListerRecrutements,
					'question' => $question,
					'reponses' => $reponses,
					'sondage'  => $sondage,
					'a_vote'   => $a_vote,
					'Tweets'   => $Tweets
			)) ?>

		<?php if(verifier('gerer_breve_accueil')){ ?>
		<p class="droite">
			<a href="/informations/editer-annonces.html">
				<img src="/pix.gif" class="fff pencil" alt="Éditer" />
				Modifier le bloc annonces
			</a>
		</p>
		<?php } ?>
	</td>
	</tr>

	<tr>
	<td>
		<h2 class="mod_quiz">Quiz</h2>
		<?php if(verifier('quiz_voir')){ ?>
		<?php echo $view->render('ZcoQuizBundle::_bloc_accueil.html.php',
			array(
				'ListerQuizFrequentes' => $ListerQuizFrequentes,
				'ListerQuizNouveaux' => $ListerQuizNouveaux,
				'QuizHasard' => $QuizHasard
			)) ?>
		<?php } else{ ?>
		<p>Vous ne pouvez pas accéder aux quiz.</p>
		<?php } ?>
	</td>

	</tr>

	<tr>
	<td>
		<h2 class="mod_zcorrection">zCorrection</h2>
		<?php echo $view->render('ZcoZcorrectionBundle::_bloc_accueil.html.php',
				array(
					'StatistiquesZcorrection' => $StatistiquesZcorrection,
					'NombreTutosAttente' => $NombreTutosAttente
			)) ?>
	</td>

	<td>
		<?php if(verifier('voir_sujets')){ ?>
		<span style="float: right; margin-left: 10px;">
			<a href="/forum/messages-flux.html" title="S'abonner au flux du forum">
				<img src="/pix.gif" class="fff feed" alt="Flux" />
			</a>
		</span>
		<?php } ?>

		<h2 class="mod_forum">Forum</h2>
		<?php if(verifier('voir_sujets')){ ?>
		<?php echo $view->render('ZcoForumBundle::_bloc_accueil.html.php',
			array(
				'StatistiquesForum' => $StatistiquesForum,
			)) ?>
		<?php } else{ ?>
		<p>Vous ne pouvez pas voir le forum.</p>
		<?php } ?>
	</td>
	</tr>
</table>
