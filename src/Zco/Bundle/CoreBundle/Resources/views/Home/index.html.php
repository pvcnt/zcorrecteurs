<?php $view->extend('::layouts/default.html.php') ?>

<h1>Bienvenue sur le site des zCorrecteurs</h1>

<br />
<table class="UI_boxes home" cellspacing="7px">
	<tr><td rowspan="2">
		<span style="float: right; margin-left: 10px;">
			<a href="<?php echo $view['router']->path('zco_blog_feed') ?>" title="S'abonner au flux du blog">
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

				echo $view->render('ZcoContentBundle:Blog:_intro_module.html.php', array(
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
		<h2 class="mod_quiz">Quiz</h2>
		<?php echo $view->render('ZcoQuizBundle::_bloc_accueil.html.php',
			array(
				'ListerQuizFrequentes' => $ListerQuizFrequentes,
				'ListerQuizNouveaux' => $ListerQuizNouveaux,
				'QuizHasard' => $QuizHasard
			)) ?>
	</td>

	</tr>

	<tr>
	<td>
		<h2 class="mod_dictees">Dictées</h2>
        <?php echo $view->render('ZcoDicteesBundle::_bloc_accueil.html.php',
            compact('DicteesAccueil', 'DicteeHasard', 'DicteesLesPlusJouees')) ?>
	</td>

	</tr>
</table>
