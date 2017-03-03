<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Language" content="fr" />
		<link rel="stylesheet" href="/css/stylesheets/global.css" media="screen" title="Style par défaut" type="text/css" />
		<link rel="stylesheet" href="/css/stylesheets/zcode.css" media="screen" title="Style par défaut" type="text/css" />
		<title>Vous êtes banni de ce site</title>
	</head>

	<body style="font-family: 'Lucida Sans Unicode', 'Trebuchet MS', Verdana, Geneva, Arial, Helvetica, sans-serif; background: #fff; font-size: 12px; padding: 0; margin: 0;">
		<div style="border-bottom: 5px solid #d6d6d6; background-color: #31CCF7;">
			<h1 style="padding: 5px; padding-top: 8px; border-bottom: none; font-size: 22px; color: white; text-align: center;">
				Vous êtes banni de ce site
			</h1>
		</div>

		<div style="padding: 10px 0; margin-right: 10px; margin-left: 10px; width: 750px; margin: auto;">
			Vous avez été banni de ce site. Son accès vous est en conséquence
			interdit.<br /><br />

			<span class="gras">Début : </span><?php echo dateformat($Debut, MINUSCULE); ?>.<br />
			<span class="gras">Fin : </span>
			<?php echo ($Duree != 0) ? 'dans '.$Duree.' jour'.pluriel($Duree).'.' : 'jamais.' ?>

			<?php if(!empty($Raison)){ ?>
			<br /><span class="gras">Raison : </span>
			<div class="citation2" style="width: 700px; margin: auto;"><?php echo $view['messages']->parse($Raison); ?></div>
			<?php } ?>
		</div>
	</body>
</html>