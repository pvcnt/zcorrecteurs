<?php
$_visiteurs = 0;
$_membres = 0;
foreach($ListerVisiteurs as $v)
{
	!empty($v['utilisateur_id']) ? $_membres++ : $_visiteurs++;
}
?>

<?php echo $_membres; ?> membre<?php echo pluriel($_membres); ?> et
<?php echo $_visiteurs; ?> visiteur<?php echo pluriel($_visiteurs);

echo $_membres ? ' :' : '.';

$i = 1; foreach($ListerVisiteurs as $v) { ?>
	<?php if(!empty($v['utilisateur_id'])) { ?>
		<a	href="/membres/profil-<?php
			echo $v['utilisateur_id']; ?>-<?php
			echo rewrite($v['utilisateur_pseudo']); ?>.html"
			style="color: <?php
			echo $v['groupe_class']; ?>;"<?php
			if($v['connecte_nom_action'] === 'ZcoForumBundle:repondre')
				echo ' class="italique" title="En train de répondre à un sujet"';
			elseif($v['connecte_nom_action'] === 'ZcoForumBundle:nouveau')
				echo ' class="gras" title="En train de rédiger un nouveau sujet"';
			echo '>'.htmlspecialchars($v['utilisateur_pseudo']); ?></a><?php
				if($i != $_membres) echo ', '; $i++;
	}
} ?>
