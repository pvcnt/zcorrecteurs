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
					<a href="<?php echo $view['router']->generate('zco_options_profile') ?>">
						Modifier mon profil
					</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/modifier_mail.png" alt="" />
					<a href="<?php echo $view['router']->generate('zco_options_email') ?>">
						Changer mon adresse mail
					</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/modifier_pass.png" alt="" />
					<a href="<?php echo $view['router']->generate('zco_options_password') ?>">
						Changer mon mot de passe
					</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/modifier_avatar.png" alt="" />
					<a href="<?php echo $view['router']->generate('zco_options_avatar') ?>">
						Changer mon avatar
					</a>
				</li>

				<li>
					<img src="/img/membres/ch_pseudo.png" alt="" />
					<a href="<?php echo $view['router']->generate('zco_user_newPseudo') ?>">
						Demander un changement de pseudo
					</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/gerer_absence.png" alt="" />
					<a href="<?php echo $view['router']->generate('zco_options_absence') ?>">
						Indiquer une période d'absence
					</a>
				</li>

				<li>
					<img src="/bundles/zcooptions/img/voir_profil.png" alt="" />
					<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $_SESSION['id'], 'slug' => rewrite($_SESSION['pseudo']))) ?>">
						Voir mon profil
					</a>
				</li>
			</ul>
		</td>

		<td>
			<h2>Mon activité sur le site</h2>
			<ul>
				<li>
					<img src="/bundles/zcooptions/img/stats_quiz.png" alt="" />
					<a href="/quiz/mes-statistiques.html">Voir mes statistiques d'utilisation des quiz</a>
				</li>
				<li>
					<img src="/bundles/zcooptions/img/voir_uploads.png" alt="" />
					<a href="<?php echo $view['router']->generate('zco_file_index') ?>">
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
					<a href="<?php echo $view['router']->generate('zco_options_preferences') ?>">
						Modifier mes options de navigation
					</a>
				</li>
			</ul>
		</td>
	</tr>
</table>
