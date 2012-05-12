<?php $view->extend('::layouts/default.html.php') ?>

<h1>Mes options</h1>

<p>Bienvenue sur l'accueil de vos options. Depuis cette page, vous pouvez toucher à tout ce qui concerne le paramétrage de votre compte.</p>

<p><a href="/aide/page-5-options-du-compte.html">
    <img src="/img/misc/aide.png" alt="" />
    Plus d'informations sur mes options.
</a></p>

<table class="UI_boxes" cellspacing="7px">
	<tr>
		<td rowspan="2">
			<h2>Mon compte</h2>
			<ul>
				<li>
					<img src="/bundles/zcooptions/img/modifier_profil.png" alt="" />
					<a href="modifier-profil.html">Modifier mon profil</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/modifier_mail.png" alt="" />
					<a href="modifier-mail.html">Changer mon adresse mail</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/modifier_pass.png" alt="" />
					<a href="modifier-mot-de-passe.html">Changer mon mot de passe</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/modifier_avatar.png" alt="" />
					<a href="modifier-avatar.html">Changer mon avatar</a>
				</li>

				<li>
					<img src="/img/membres/ch_pseudo.png" alt="" />
					<a href="<?php echo $view['router']->generate('zco_user_newPseudo') ?>">
						Demander un changement de pseudo
					</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/gerer_absence.png" alt="" />
					<a href="gerer-absence.html">Indiquer une période d'absence</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/voir_profil.png" alt="" />
					<a href="/membres/profil-<?php echo $_SESSION['id']; ?>-<?php echo rewrite($_SESSION['pseudo']); ?>.html">Voir mon profil</a>
				</li>
			</ul>
		</td>

		<td>
			<h2>Mon activité sur le site</h2>
			<ul>
				<?php if(verifier('quiz_ses_stats')){ ?>
				<li>
					<img src="/bundles/zcooptions/img/stats_quiz.png" alt="" />
					<a href="/quiz/mes-statistiques.html">Voir mes statistiques d'utilisation des quiz</a>
				</li>
				<?php }?>

				<li>
					<img src="/bundles/zcooptions/img/voir_uploads.png" alt="" />
					<a href="/fichiers/">
						Accéder au gestionnaire de fichiers
					</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/sauvegardes_zcode.png" alt="" />
					<a href="sauvegardes-zcode.html">Voir les sauvegardes automatiques de zCode</a>
				</li>

				<?php if(verifier('publicite_proposer')){ ?>
				<li>
					<img src="/bundles/zcooptions/img/publicites.png" alt="" />
					<a href="/publicite/gestion.html">
						Voir mes campagnes publicitaires
					</a>
				</li>
				<?php } ?>
			</ul>
		</td>
	</tr>

	<tr>
		<td>
			<h2>Ma navigation</h2>
			<ul>
				<li>
					<img src="/bundles/zcooptions/img/navigation.png" alt="" />
					<a href="navigation.html">Modifier mes options de navigation</a>
				</li>
				<li>
					<img src="/pix.gif" class="fff time" alt="" />
					<a href="decalage.html">Modifier mon décalage horaire</a>
				</li>
				<?php if (verifier('admin')): ?>
				<li>
					<img src="/bundles/zcooptions/img/tests.png" alt="" />
					<a href="tests.html">Participer aux tests en avant-première</a>
				</li>
				<?php endif; ?>
			</ul>
		</td>
	</tr>
</table>
