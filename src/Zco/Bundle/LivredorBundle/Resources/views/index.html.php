<?php $view->extend('::layouts/default.html.php') ?>

<h1>Livre d'or</h1>

<p class="UI_descr centre">
	Le site vous a rendu un fier service en vous remettant un document sans faute ? Dites-le-nous !<br />
	<em>Note : nous vous rappelons que vous ne pouvez poster qu'un message par semaine.</em>
</p>

<?php if($VerifieDernierPost){ ?>
<form method="post" action="" class="centre">
	<p>
		<input type="submit" name="submit" value="Déposer un message" />
	</p>
</form>
<?php } ?>

<p class="message_info">Il y a <strong><?php echo $pager->countAll(); ?></strong> message<?php echo pluriel($pager->countAll()) ?> dans ce livre d'or.<br />
	Note moyenne attribuée :</p>
	<ul class="star-rating star-centre">
		<li class="current-rating" style="width:<?php echo (int)($NoteMoyenne * 30); ?>px;"><strong><?php echo $NoteMoyenne; ?></strong> étoile<?php if($NoteMoyenne > 1) echo 's'; ?> sur 5.</li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
	</ul>

<br />
<p class="pagination_livreor">
	Page : <?php echo $view['ui']->render($pager) ?>
</p>

<?php
if(count($pager) > 0){
	foreach($pager as $msg){
?>
<div class="ensemble_blocs" id="m<?php echo $msg['id']; ?>">
	<ul class="star-rating">
		<li class="current-rating" style="width: <?php echo ($msg->note * 30); ?>px;"><strong>Note : <?php echo $msg->note; ?>/5</strong></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
	</ul>
	<div class="infos_msg">
		<?php if(verifier('livredor_editer') || verifier('livredor_supprimer') || verifier('ips_analyser')){ ?>
		<span class="msg_moderation">
			<?php if(verifier('ips_analyser')){ ?>
			IP : <a href="/ips/analyser.html?ip=<?php echo long2ip($msg->ip); ?>"><?php echo long2ip($msg->ip); ?></a>
			<?php } if(verifier('livredor_editer')){ ?>
			<a href="editer-<?php echo $msg['id']; ?>.html" title="Éditer ce message"><img src="/img/editer.png" alt="Éditer" /></a>
			<?php } if(verifier('livredor_supprimer')){ ?>
			<a href="supprimer-<?php echo $msg['id']; ?>.html" title="Supprimer ce message"><img src="/img/supprimer.png" alt="Supprimer" /></a>
			<?php } ?>
		</span>
		<?php } ?>
		Par
		<?php if(!is_null($msg->Utilisateur)){ ?>
		<a href="/membres/profil-<?php echo $msg->Utilisateur->id; ?>-<?php echo rewrite($msg->Utilisateur->pseudo); ?>.html"><?php echo htmlspecialchars($msg->Utilisateur->pseudo); ?></a>,
		<?php } else{ ?>
		<?php echo 'Anonyme'; ?>,
		<?php } ?>
		<?php echo dateformat($msg->date, MINUSCULE); ?>
	</div>

	<div class="bloc_msg">
		<?php if(!is_null($msg->Utilisateur) && $msg->Utilisateur->avatar){ ?>
		<a href="/membres/profil-<?php echo $msg->Utilisateur->id; ?>-<?php echo rewrite($msg->Utilisateur->pseudo); ?>.html"><img class="avatar" src="/uploads/avatars/<?php echo htmlspecialchars($msg->Utilisateur->avatar); ?>" alt="Avatar" /></a>
		<?php } ?>

		<p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
	</div>
</div>
<?php
	}
}
elseif(verifier('livredor_ecrire'))
{
	echo '<p class="message_info">Il n\'y a aucun message dans ce livre d\'or. Vous pouvez en <a href="ecrire.html">déposer un</a>.</p>';
}
?>

<p class="pagination_livreor">
	Page : <?php echo $view['ui']->render($pager) ?>
</p>

<?php if($VerifieDernierPost){ ?>
<br />

<form method="post" action="" class="centre">
	<p class="message_info">
		<input type="submit" name="submit" value="Déposer un message" />
	</p>
</form>

<?php } ?>
