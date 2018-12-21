<?php if ($dictation['format']): ?>
<?php foreach(['fast', 'slow'] as $n => $kind): ?>
<div style="float: left; width: 49%<?php if($n % 2) echo '; text-align: right' ?>">
	<p><em>
	<?php if ($field === 'fast'): ?>
		Lecture <acronym title="Lecture fluide, sans temps de pose.
		Écoutez une première fois pour vous familiariser avec le texte,
		puis une seconde fois en vous relisant.">rapide</acronym>
	<?php else: ?>
		Lecture <acronym title="Lecture à vitesse réduite, avec ponctuation explicite.
		Écrivez dans le champ
		ci-dessous ce que vous entendez.">lente</acronym>
	<?php endif ?>
        </em>
		-
		<a href="/uploads/dictees/<?php echo $dictation[$kind . '_sound_filename'] ?>"
		   onclick="window.open(this.href, 'Audio',
			   'height=100,width=300,location=no,menubar=no,'
			   + 'status=no,titlebar=no,toolbar=no'); return false"
		   title="Vous pouvez directement télécharger le fichier si vous avez des problèmes avec le lecteur flash."
		>Fichier audio</a>
	</p>

	<?php if ($dictation['format'] === 'mp3'): ?>
		<object type="application/x-shockwave-flash" data="/swf/player_mp3.swf" width="200" height="20">
			<param name="movie" value="/swf/player_mp3.swf" />
			<param name="bgcolor" value="#efefef" />
			<param name="FlashVars" value="mp3=/uploads/dictees/<?php echo $dictation[$kind . '_sound_filename']
			?>&amp;bgcolor1=e0e0e0&amp;bgcolor2=aaaaaa&amp;loadingcolor=aaaaaa&amp;<?php
			?>buttonovercolor=eeeeee&amp;sliderovercolor=aaaaaa" />
			Afin de lire ce fichier audio, vous devez installer Flash Player, qui est gratuit.<br/>
			Si vous ne pouvez pas installer ce logiciel, vous pouvez toujours écouter le fichier
			dans votre lecteur favori en cliquant sur le lien ci-contre.
		</object>
	<?php elseif ($dictation['format'] === 'ogg'): ?>
		<audio src="/uploads/dictees/<?php echo $dictation[$kind . '_sound_filename'] ?>" controls="controls">
		</audio>
	<?php endif ?>
</div>
<?php endforeach ?>
<?php endif ?>
