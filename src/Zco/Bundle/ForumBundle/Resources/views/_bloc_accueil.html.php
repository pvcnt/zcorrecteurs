<p class="centre italique"><a href="/forum/">Accéder au forum</a></p>
<ul>
	<li>Statistiques
		<ul class="forum_stats">
			<li>Nombre de sujets : <?php echo $StatistiquesForum['nb_topics']; ?></li>
			<li>Nombre de messages : <?php echo $StatistiquesForum['nb_posts']; ?></li>
			<li>Nombre moyen de sujets par jour : <?php echo $view['humanize']->numberformat($StatistiquesForum['nb_topics_jour']); ?></li>
			<li>Nombre moyen de messages par jour : <?php echo $view['humanize']->numberformat($StatistiquesForum['nb_posts_jour']); ?></li>
		</ul>
	</li>
	<?php
	if(!empty($StatistiquesForum['last_posts']))
	{
		echo '<li>Derniers sujets actifs<ul class="lightning">';
		foreach($StatistiquesForum['last_posts'] as $s)
		{
			echo '<li>';
			if(!empty($s['lunonlu_message_id']) && $s['lunonlu_message_id'] != $s['sujet_dernier_message'])
				if (verifier('connecte')) echo '<a href="/forum/sujet-'.$s['sujet_id'].'-'.$s['lunonlu_message_id'].'-'.rewrite($s['sujet_titre']).'.html"><img src="/pix.gif" class="fff bullet_go" title="Aller au dernier message lu" alt="Dernier message lu" /></a>';
			echo '<a href="/forum/sujet-'.$s['sujet_id'].'-'.rewrite($s['sujet_titre']).'.html" title="Forum &laquo; '.htmlspecialchars($s['cat_nom']).' &raquo;">'.htmlspecialchars($s['sujet_titre']).'</a></li>';
		}
		echo '</ul></li>';
	}

	if(!empty($StatistiquesForum['last_topics']))
	{
		echo '<li>Derniers sujets créés<ul class="add">';
		foreach($StatistiquesForum['last_topics'] as $s)
		{
			echo '<li>';
			if(!empty($s['lunonlu_message_id']) && $s['lunonlu_message_id'] != $s['sujet_dernier_message'])
				if (verifier('connecte')) echo '<a href="/forum/sujet-'.$s['sujet_id'].'-'.$s['lunonlu_message_id'].'-'.rewrite($s['sujet_titre']).'.html"><img src="/bundles/zcoforum/img/fleche.png" title="Aller au dernier message lu" alt="Dernier message lu" /></a>';
			echo '<a href="/forum/sujet-'.$s['sujet_id'].'-'.rewrite($s['sujet_titre']).'.html" title="Forum &laquo; '.htmlspecialchars($s['cat_nom']).' &raquo;">'.htmlspecialchars($s['sujet_titre']).'</a></li>';
		}
		echo '</ul></li>';
	}

	if(!empty($StatistiquesForum['topics_coup_coeur']))
	{
		echo '<li>Sujets coup de c&oelig;ur<ul class="heart">';
		foreach($StatistiquesForum['topics_coup_coeur'] as $s)
		{
			echo '<li>';
			if(!empty($s['lunonlu_message_id']) && $s['lunonlu_message_id'] != $s['sujet_dernier_message'])
				if (verifier('connecte')) echo '<a href="/forum/sujet-'.$s['sujet_id'].'-'.$s['lunonlu_message_id'].'-'.rewrite($s['sujet_titre']).'.html"><img src="/bundles/zcoforum/img/fleche.png" title="Aller au dernier message lu" alt="Dernier message lu" /></a>';
			echo '<a href="/forum/sujet-'.$s['sujet_id'].'-'.rewrite($s['sujet_titre']).'.html" title="Forum &laquo; '.htmlspecialchars($s['cat_nom']).' &raquo;">'.htmlspecialchars($s['sujet_titre']).'</a></li>';
		}
		echo '</ul></li>';
	}
	?>
</ul>