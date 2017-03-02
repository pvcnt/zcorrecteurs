/**
 * Gestion de la page de soumission d'un quiz. Permet l'affichage
 * des réponses et de la note sans rechargement de la page.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */

$('form_jouer').addEvent('submit', function (e) {
    e.stop();
    var xhr = new Request({
        url: Routing.generate('zco_quiz_api_correction'), method: 'post', onSuccess: function (text, xml) {
            //Sinon on décode et on affiche les réponses.
            var data = JSON.decode(text);
            for (var i in data.reponses) {
                $('correction_' + i).set('html', data.reponses[i]);

                for (var r = 0; r <= 4; r++) {
                    if (data.enfait[i] == r) {
                        $('q' + i + 'r' + r).setStyles({'font-weight': 'bold', 'color': 'green'});
                    }
                    else if (data.achoisi[i] == r && data.achoisi[i] != data.enfait[i]) {
                        $('q' + i + 'r' + r).setStyles({'font-weight': 'bold', 'color': 'red'});
                    }
                }
            }

            //On affiche les corrections et cache les explications.
            $$('.correction').each(function (elem, i) {
                elem.slide('in');
            });
            $('quiz_note').setStyle('display', 'block');
            $('quiz_note').set('html', data.note);
            $('quiz_note').slide('in');

            //On bloque le formulaire pour une éventuelle soumission future.
            $('submit').setStyle('display', 'none');
            $$('input[type=radio]').each(function (elem, i) {
                elem.set('disabled', 'disabled');
            });

            //On remonte en haut pour que l'utilisateur voie sa note.
            new Fx.Scroll(window, {duration: 1000, transition: Fx.Transitions.Quart.easeOut}).toTop();
        }
    });
    xhr.send($('form_jouer').toQueryString() + '&ajax=1');

    return false;
});

window.addEvent('domready', function () {
    $$('.correction').each(function (elem, i) {
        elem.slide('hide');
    });
});
