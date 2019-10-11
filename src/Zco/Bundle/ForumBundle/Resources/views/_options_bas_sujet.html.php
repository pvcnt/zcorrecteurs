<?php
if(verifier('deplacer_sujets', $InfosSujet['sujet_forum_id']))
{
	?>
	<script type="text/javascript">
	<?php
	if(verifier('deplacer_sujets', $InfosSujet['sujet_forum_id']))
	{
	?>
		function afficher_deplacer_sujet(text, xml)
		{
			$('deplacer_sujet').innerHTML = unescape(text);
		}
		function deplacer_sujet(bouton)
		{
			bouton.setStyle('display', 'none');
			$('deplacer_sujet').innerHTML = '<img src="/img/ajax-loader.gif" alt="" />';
			setTimeout(function(){
				xhr = new Request({method: 'post', url: '/forum/ajax-deplacer-sujet.html', onSuccess: afficher_deplacer_sujet});
			xhr.send('id='+escape("<?php echo $InfosSujet['sujet_id']; ?>")+'&fofo_actuel='+escape("<?php echo $InfosSujet['sujet_forum_id']; ?>"));
			}, 500);
		}
	<?php
	}
    ?>
</script>

<?php
}
?>
<fieldset>
	<legend>Contrôles</legend>
	<ul>
		<?php
		//DÉBUT sujet résolu
		if( (verifier('resolu_ses_sujets', $InfosSujet['sujet_forum_id']) OR verifier('resolu_sujets', $InfosSujet['sujet_forum_id']) ) AND $_SESSION['id'] == $InfosSujet['sujet_auteur'])
		{
			if($InfosSujet['sujet_resolu'])
			{
			?>
			<li>
				<img src="/pix.gif" class="fff accept" alt="" />
				<a href="<?php echo $view['router']->path('zco_forum_markSolved', ['id' => $InfosSujet['sujet_id'], 'token' => $_SESSION['token']]) ?>">
					Ne plus indiquer mon problème comme résolu
				</a>
			</li>
			<?php
			}
			else
			{
			?>
			<li>
				<img src="/pix.gif" class="fff accept" alt="" />
				<a href="<?php echo $view['router']->path('zco_forum_markSolved', ['id' => $InfosSujet['sujet_id'], 'token' => $_SESSION['token']]) ?>">
					Indiquer mon problème comme résolu
				</a>
			</li>
			<?php
			}
		}
		elseif(verifier('resolu_sujets', $InfosSujet['sujet_forum_id']) AND $_SESSION['id'] != $InfosSujet['sujet_auteur'])
		{
			if($InfosSujet['sujet_resolu'])
			{
			?>
			<li>
				<img src="/pix.gif" class="fff accept" alt="" />
				<a href="<?php echo $view['router']->path('zco_forum_markSolved', ['id' => $InfosSujet['sujet_id'], 'token' => $_SESSION['token']]) ?>">
					Ne plus indiquer le problème de <strong><?php echo htmlspecialchars($InfosSujet['sujet_auteur_pseudo']); ?></strong> comme résolu.
				</a>
			</li>
			<?php
			}
			else
			{
			?>
			<li>
				<img src="/pix.gif" class="fff accept" alt="" />
				<a href="<?php echo $view['router']->path('zco_forum_markSolved', ['id' => $InfosSujet['sujet_id'], 'token' => $_SESSION['token']]) ?>">
					Indiquer le problème de <strong><?php echo htmlspecialchars($InfosSujet['sujet_auteur_pseudo']); ?></strong> comme résolu.
				</a>
			</li>
			<?php
			}
		}
		//FIN sujet résolu
        ?>
	</ul>
</fieldset><br />

<?php if(verifier('epingler_sujets', $InfosSujet['sujet_forum_id']) || verifier('fermer_sujets', $InfosSujet['sujet_forum_id']) || verifier('deplacer_sujets', $InfosSujet['sujet_forum_id']) || verifier('corbeille_sujets', $InfosSujet['sujet_forum_id']) || verifier('suppr_sujets', $InfosSujet['sujet_forum_id'])){ ?>
<fieldset>
	<legend>Options de modération</legend>
	<ul>
		<?php
		//DÉBUT annonce
		if(verifier('epingler_sujets', $InfosSujet['sujet_forum_id']))
		{
			if($InfosSujet['sujet_annonce'])
			{
			?>
			<li><span><img src="/pix.gif" class="fff flag_yellow" alt="Enlever des annonces" title="Enlever des annonces" /></span>
			<a href="<?php echo $view['router']->path('zco_forum_markPinned', ['id' => $InfosSujet['sujet_id'], 'token' => $_SESSION['token']]) ?>">
				Enlever des annonces
			</a></li>
			<?php
			}
			else
			{
			?>
			<li><span><img src="/pix.gif" class="fff flag_red" alt="Transformer en annonce" title="Mettre ce sujet en annonce" /></span>
			<a href="<?php echo $view['router']->path('zco_forum_markPinned', ['id' => $InfosSujet['sujet_id'], 'token' => $_SESSION['token']]) ?>">
				Mettre le sujet en annonce
			</a></li>
			<?php
			}
		}
		//FIN annonce

		//DÉBUT fermer/ouvrir sujet
		if(verifier('fermer_sujets', $InfosSujet['sujet_forum_id']))
		{
			if($InfosSujet['sujet_ferme'])
			{
			?>
			<li><span><img src="/pix.gif" class="fff lock_open" alt="Ouvrir" title="Ouvrir le sujet" /></span>
                <a href="<?php echo $view['router']->path('zco_forum_markClosed', ['id' => $InfosSujet['sujet_id'], 'token' => $_SESSION['token']]) ?>">
				Ouvrir le sujet
			</a></li>
			<?php
			}
			else
			{
			?>
			<li><span><img src="/pix.gif" class="fff lock" alt="Fermer" title="Fermer le sujet" /></span>
                <a href="<?php echo $view['router']->path('zco_forum_markClosed', ['id' => $InfosSujet['sujet_id'], 'token' => $_SESSION['token']]) ?>">
				Fermer le sujet
			</a></li>
			<?php
			}
		}
		//FIN fermer/ouvrir sujet

		//DÉBUT déplacer sujet
		if(!$InfosSujet['sujet_corbeille'] AND verifier('deplacer_sujets', $InfosSujet['sujet_forum_id']))
		{
		?>
		<li>
			<img src="/pix.gif" class="fff folder_go" alt="" />
			Déplacer le sujet vers :

            <form action="<?php echo $view['router']->path('zco_forum_move', ['id' => $InfosSujet['sujet_id']]) ?>" method="post">
                <select name="forum_cible">
                    <?php foreach($CategoriesForums as $i => $valeur) { ?>
                    <?php if($valeur['cat_niveau'] == 2 && $InfosSujet['sujet_forum_id'] != $valeur['cat_id']) { ?>
                        <?php if($i > 1) { ?></optgroup><?php } ?>
                        <optgroup label="<?php echo htmlspecialchars($valeur['cat_nom']) ?>">
                    <?php } else { ?>
                        <option value="<?php echo $valeur['cat_id'] ?>"><?php echo htmlspecialchars($valeur['cat_nom']) ?></option>
                        <?php if (!empty($valeur['sous_forums'])) { ?>
                            <?php foreach ($valeur['sous_forums'] as $forum) { ?>
                                <option value="<?php echo $forum['cat_id'] ?>"><?php echo str_pad('', ($forum['cat_niveau']-3)*3, '...').htmlspecialchars($forum['cat_nom']) ?></option>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                </optgroup></select>
                <input type="submit" value="Déplacer" />
            </form>
		</li>
		<?php
		}
		//FIN déplacer sujet

		//DÉBUT mise en corbeille / restauration
		if(verifier('corbeille_sujets', $InfosSujet['sujet_forum_id']))
		{
			if($InfosSujet['sujet_corbeille'])
			{
			?>
			<li>
				<img src="/pix.gif" class="fff bin" alt="" />
				<a href="<?php echo $view['router']->path('zco_forum_trash', ['id' =>  $InfosSujet['sujet_id'], 'status' => 0, 'token' => $_SESSION['token']]) ?>">
					Restaurer le sujet
				</a>
			</li>
			<?php
			}
			else
			{
			?>
			<li>
				<img src="/pix.gif" class="fff bin" alt="" />
				<a href="<?php echo $view['router']->path('zco_forum_trash', ['id' => $InfosSujet['sujet_id'], 'status' => 1, 'token' => $_SESSION['token']]) ?>">
					Mettre le sujet à la corbeille
				</a>
			</li>
			<?php
			}
		}
		//FIN mise en corbeille / restauration

		//DÉBUT supprimer sujet
		if(verifier('suppr_sujets', $InfosSujet['sujet_forum_id']))
		{
			?>
			<li>
				<img src="/pix.gif" class="fff cross" alt="" />
				<a href="<?php echo $view['router']->path('zco_forum_delete', ['id' => $InfosSujet['sujet_id']]) ?>">
					Supprimer le sujet
				</a>
			</li>
			<?php
		}
		//FIN supprimer sujet
		?>
	</ul>
</fieldset>
<?php } ?>
