<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<title>Redirection en cours…</title>
		<meta http-equiv="Content-Language" content="fr" />

		<link rel="icon" type="image/png" href="/favicon.png" />
		
		<?php foreach ($view['vitesse']->stylesheets() as $assetUrl): ?>
		    <link rel="stylesheet" href="<?php echo $assetUrl ?>" media="screen" type="text/css" />
		<?php endforeach ?>
		
		<?php if ($time > 0): ?>
		    <meta http-equiv="refresh" content="<?php echo $time ?>;url=<?php echo $url ?>" />
		<?php endif; ?>
	</head>

	<body>
		<div id="message_<?php echo $type; ?>">
			<p>
				<?php if(!is_null($idMsg)){ ?>
				Message n<sup>o</sup>&nbsp;<?php echo $idMsg ?> :<br />
				<?php } ?>
				<span style="font-weight: bold;"><?php echo $message ?></span>
			</p><br />

			<?php if ($time > 0) { ?><p>Vous allez être redirigé dans <?php echo $time ?> seconde<?php if ($time > 1) echo 's'; ?>.</p><br /><?php } ?>

			<p id="pas_attendre">
				<?php if ($type == MSG_ERROR){ ?>
				<a onclick="history.back();" href="#">&larr; Précédent</a> - <a href="<?php echo $url ?>">Suivant &rarr;</a>
				<?php } else{ ?>
				<a href="<?php echo $url; ?>">Ne pas attendre</a>
				<?php } ?>
			</p>
		</div>
	</body>
</html>
