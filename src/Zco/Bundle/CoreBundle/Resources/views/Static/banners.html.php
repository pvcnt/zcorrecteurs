<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoCoreBundle:Static:aboutTabs.html.php', array('currentTab' => 'banners')) ?>

<h1>Nous aider à promouvoir le site.</h1>

<p class="intro-text">
	Nous mettons à votre disposition diverses bannières représentant le site que 
	vous pouvez mettre dans vos signatures sur les <em>fora</em> que vous 
	fréquentez, ou bien encore sur votre propre site web si vous le souhaitez.
	Et n’oubliez pas de parler du site autour de vous, cela peut grandement 
	nous aider !
</p>

<table class="table">
	<?php foreach ($bannieres as $url => $info): ?>
	<tr>
		<td class="center span8">
			<a href="/<?php echo $url ?>"><img src="/<?php echo $url ?>" alt="Bannière" /></a>
			
			<?php if (!empty($info['dimensions'])): ?>
				<p class="italic">
					Dimensions : <?php echo $info['dimensions'] ?> pixels
				</p>
			<?php endif; ?>
		</td>
		<td class="span4">
			<div>
				<strong>Code HTML :</strong><br />
				<input type="text" onclick="this.select()" style="width: 95%;" 
					value="&lt;a href=&quot;<?php echo URL_SITE ?>&quot;&gt;&lt;img src=&quot;<?php echo URL_SITE ?>/<?php echo $url ?>&quot; alt=&quot;zCorrecteurs.fr&quot; /&gt;&lt;a&gt;" 
					/>
			</div>
			
			<div>
				<strong>zCode :</strong><br />
				<input type="text" onclick="this.select()" style="width: 95%;" 
					value="&lt;lien url=&quot;<?php echo URL_SITE ?>&quot;&gt;&lt;image legende=&quot;zCorrecteurs.fr&quot;&gt;<?php echo URL_SITE ?>/<?php echo $url ?>&lt;/image&gt;&lt;/lien&gt;"
					/>
			</div>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
