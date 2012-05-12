<?php $view->extend('::layouts/default.html.php') ?>

<h1>Historique des versions</h1>

<p>
	Voici la liste de toutes les versions du billet. Pour chacune vous pouvez
	la visualiser, et éventuellement décider de rétablir votre billet tel qu'il
	était à ce moment.<br />
	Un <span class="vertf">élément vert</span> indique qu'il n'y a pas eu de
	modification par rapport à la version précédente (au-dessous).<br />
	Un <span class="rouge">élément rouge</span> indique des modifications par
	rapport à la version précédente (au-dessous).
</p>

<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 5%;">N<sup>o</sup></th>
			<th style="width: 15%;">Date</th>
			<th style="width: 15%;">Pseudo</th>
			<?php if(verifier('ips_analyser')){ ?>
			<th style="width: 5%;">IP</th>
			<?php } ?>
			<th style="width: 40%;">Modifications</th>
			<th style="width: 15%;">Comparer</th>
			<th style="width: 5%;">Revenir</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($ListerVersions as $i => $v){ ?>
		<tr>
			<td class="centre">
				<?php echo $v['version_id_fictif']; ?>
			</td>
			<td class="centre">
				<?php echo dateformat($v['version_date']); ?>
			</td>
			<td>
				<a href="/membres/profil-<?php echo $v['utilisateur_id']; ?>-<?php echo rewrite($v['utilisateur_pseudo']); ?>.html">
					<?php echo htmlspecialchars($v['utilisateur_pseudo']); ?>
				</a>
			</td>
			<?php if(verifier('ips_analyser')){ ?>
			<td class="centre">
				<?php if(!empty($v['version_ip'])){ ?>
				<a href="/ips/analyser.html?ip=<?php echo long2ip($v['version_ip']); ?>">
					<?php echo long2ip($v['version_ip']); ?>
				</a>
				<?php } else echo '-'; ?>
			</td>
			<?php } ?>
			<td>
				<span class="<?php echo $v['titre']; ?>">
					<?php echo htmlspecialchars($v['version_titre']); ?>
				</span> -
				<span class="<?php echo $v['sous_titre']; ?>">
					<?php echo !empty($v['version_sous_titre']) ? htmlspecialchars($v['version_sous_titre']) : '(aucun)'; ?>
				</span> -
				<span class="italique <?php echo $v['texte']; ?>">(contenu)</span> -
				<span class="italique <?php echo $v['intro']; ?>">(intro)</span>
				<?php if(!empty($v['version_commentaire'])){ ?><br />
				<strong>Commentaire :</strong> <?php echo htmlspecialchars($v['version_commentaire']); ?>
				<?php } ?>
			</td>
			<td class="centre">
				<?php if($v['version_id_fictif'] > 0){ ?>
				<a href="comparaison-<?php echo $v['version_id']; ?>-<?php echo $v['id_precedent']; ?>.html">
					Comparer avec n<sup>o</sup>&nbsp;<?php echo $v['version_id_fictif'] - 1; ?>
				</a>
				<?php } else echo '-'; ?>
			</td>
			<td class="centre">
				<?php if($i != 0){ ?>
				<a href="revenir-version-<?php echo $_GET['id']; ?>-<?php echo $v['version_id']; ?>.html">
					Revenir
				</a>
				<?php } else echo '-'; ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>