/**
 * Gère l'ajout dynamique d'un formulaire pour justifier ses réponses.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 * @provides vitesse-behavior-quiz-comment-answers
 * @requires vitesse-behavior
 *           mootools
 */
Behavior.create('quiz-comment-answers', function(config)
{
	$$('.qz_justification').each(function(el)
	{
		var lien = new Element('a', {
			'href': '#',
			'text': 'Je souhaite justifier ma réponse »',
			'style': 'font-style: italic'
		});
		lien.addEvent('click', function(e)
		{
		    e.preventDefault();
			lien.dispose();
			el.getElementsByTagName('textarea')[0].focus();
			el.slide('in');
			return false;
		});
		lien.inject(el, 'before');
		new Element('br').inject(lien, 'before');
		el.slide('hide');
	});
});