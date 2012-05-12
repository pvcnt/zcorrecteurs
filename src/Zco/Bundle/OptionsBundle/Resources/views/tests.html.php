<?php $view->extend('::layouts/default.html.php') ?>

<h1>Participer aux tests en avant-première</h1>

<p>
	De façon ponctuelle, l'équipe du site pourra convier tous les visiteurs à 
	expérimenter <strong>en avant-première</strong> une fonctionnalité majeure, 
	avant qu'elle ne soit 
	rendue disponible à tout le monde. C'est une façon pour nous de mettre au point 
	les derniers détails avant une sortie importante, et pour vous de participer 
	simplement à la vie du site de façon amusante !
</p>
<p>
	En tant que membre, vous pouvez demander à accéder automatiquement à ces versions 
	expérimentales. Lorsqu'une telle période sera en cours, vous aurez alors automatiquement 
	accès à la fonctionnalité en cours de test, sans qu'aucune action ne soit requise.
</p>

<form method="get" action="">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>" />
	<fieldset>
		<legend>Inscription aux programmes de tests</legend>
		<p>En cochant la case suivante, vous serez automatiquement redirigé vers la version de test dès
		qu'un programme de test sera lancé. Vous pourrez à tout moment repasser sur la version classique du site.</p>
		
		<div class="centre">
			<input type="checkbox" name="participer" value="1" id="participer"<?php if ($participer) echo ' checked="checked"' ?> />
			<label class="nofloat" for="participer">Je veux participer automatiquement aux futurs tests en avant-première</label>
			<br /><br />
		
			<input type="submit" value="Envoyer" />
		</div>
	</fieldset>
</form>
