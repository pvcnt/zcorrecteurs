<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Géolocalisation de l'équipe</h1>

<p>
	Cette page affiche dynamiquement notre localisation à toute l'équipe de 
	zCorrection ! Elle se base sur l'adresse que vous avez renseignée dans 
	votre profil. Elle n'est visible que pour les membres de l’équipe actuelle 
	et les anciens.
</p>

<div id="map" style="width: 100%; height: 700px ;margin: auto;"></div>

<?php $view['javelin']->initBehavior('user-markers-on-map', array('id' => 'map', 'markers' => $markers)) ?>
<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css') ?>