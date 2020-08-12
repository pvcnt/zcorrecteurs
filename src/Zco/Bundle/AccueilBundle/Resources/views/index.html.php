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
            <a href="https://twitter.com/zCorrecteurs">
                <img src="/bundles/zcocore/img/bouton.png"
                     alt="twitter"/>
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
					'Tweets'   => $Tweets,
					'Dictee' => $Dictee
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
		<h2 class="mod_dictees">Dictées</h2>
		<?php if (verifier('dictees_voir')): ?>
			<?php echo $view->render('ZcoDicteesBundle::_bloc_accueil.html.php',
				compact('DicteesAccueil', 'DicteeHasard', 'DicteesLesPlusJouees')) ?>
		<?php else: ?>
			<p>Vous n'avez pas accès aux dictées.</p>
		<?php endif ?>
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
