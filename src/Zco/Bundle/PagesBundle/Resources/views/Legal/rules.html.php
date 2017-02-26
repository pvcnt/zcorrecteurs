<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPagesBundle:Legal:_tabs.html.php', array('currentTab' => 'rules')) ?>

<h1>Règlement</h1>

<p class="good">
	Ce site possède un ensemble de règles afin d’offrir à nos membres la meilleure 
	navigation possible, dans le respect d’autrui, le calme et la convivialité. 
	Toutes ces règles tombent sous le sens, mais elles sont néanmoins répertoriées 
	ici de sorte à constituer une référence. En vous inscrivant et en utilisant 
	les services communautaires du site, vous acceptez implicitement les règles 
	qui le régissent.
</p>

<ol>
	<li>
		Tout message est sujet aux <strong>règles élémentaires de politesse</strong>. 
		Un nouveau sujet doit commencer par un « Bonjour » et se terminer par 
		une petite formule de politesse telle que « Merci par avance ». Les 
		messages de façon générale ne doivent pas comporter d’insulte visant un 
		autre membre ou bien une idée. Si vous avez un problème avec un membre, 
		contactez les administrateurs, mais ne réglez pas vos comptes en public.
	</li>
	<li>
		Vous devez faire un <strong>effort sur l’orthographe</strong> étant donné 
		qu’il s’agit du thème principal du site. Nous sommes conscients que tout 
		le monde ne possède pas la même facilité à manier la langue française, 
		mais vous devez montrer que vous faites des efforts. Il ne vous est bien 
		entendu pas demandé de ne pas commettre d’erreurs (on est tous là pour 
		apprendre et partager), mais vous devez vous relire si vous possédez 
		des difficultés. Bien évidemment, tout langage de type SMS est strictement 
		prohibé.
	</li>
	<li>
		<strong>Il est interdit de se créer plusieurs comptes sur le site.</strong> 
		Si vous avez oublié votre mot de passe, utilisez la fonction de récupération 
		du mot de passe ; si vous avez oublié votre pseudonyme, 
		<a href="<?php echo $view['router']->path('zco_about_contact') ?>">contactez un administrateur</a>
		et expliquez-lui votre problème, il fera son possible pour vous aider. 
		Tout double compte est susceptible d’être banni sans préavis.
	</li>
	<li>
		<strong>Tout sujet à caractère illégal ou offensant est interdit.</strong> 
		Cela inclut de façon non exhaustive la pornographie, le racisme, le piratage 
		informatique, la diffamation, etc. Tout sujet ne respectant pas cette 
		règle pourra être supprimé à tout moment et son auteur sanctionné.
	</li>
	<li>
		<strong>Le <em>flood</em> n’est pas toléré.</strong> Ce terme désigne
		un afflux de messages sans aucun intérêt pour le site ou ses visiteurs. 
		Les messages de ce type seront supprimés (et selon les cas, leur auteur 
		sanctionné).
	</li>
	<li>
		<strong>La publicité est interdite</strong>, cela signifie que tout 
		sujet/message/article trop ouvertement publicitaire pourra être supprimé.
	</li>
	<li>
		<strong>Tout texte ou image</strong> (avatar, image dans un message, 
		signature, etc.) pouvant <strong>choquer les membres et visiteurs de 
		ce site est interdit</strong> (et, selon les cas, peut être passible 
		de sanctions).
	</li>
	<li>
		<strong>Le plagiat est interdit.</strong> Si vous souhaitez poster 
		un texte écrit par quelqu’un d’autre sur ce site ou une image ne vous 
		appartenant pas, assurez-vous que la licence du contenu en question 
		vous y autorise. Dans le cas contraire, abstenez-vous.
	</li>
</ol>

<p class="good">
	Notez que si vous constatez sur le site un comportement vous semblant 
	contraire au règlement ci-dessus énoncé, n’intervenez pas directement. 
	Contactez un administrateur (soit en utilisant les systèmes d’alerte 
	pour les sections du site en étant munies, telles que le forum ou les 
	messages privés, soit par message privé si vous possédez un compte 
	sur le site ou au moyen du <a href="<?php echo $view['router']->path('zco_about_contact') ?>">formulaire de contact </a>
	dans le cas contraire). L’administrateur se chargera d’étudier le 
	problème et, s’il est avéré, de le régler.
</p>