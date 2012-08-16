/**
 * Fichier permettant de gérer l'affichage de messages en Javascript, de
 * façon élégante.
 *
 * @author   Savageman <savageman@zcorrecteurs.fr>
 * @requires mootools
 *           @ZcoCoreBundle/Resources/public/css/zMessage.css
 */
var zMessage = new Class({
	Implements: Options,

	options: {
		className : 'zMessage',
		duration: 2000,
		fadeInValue: '1'
	},

	initialize: function(options) {
		this.setOptions(options);
		this.container = new Element('div', {'class': this.options.className});
		this.container.fade('hide');
		this.container.inject($$('body')[0], 'bottom');
	},

	affichage: function(chaine, classe) {
		$clear(this.fadeOut);
		this.container.set('html', chaine);
		this.container.set('class', this.options.className);
		if (classe != 'undefined') {
			this.container.addClass('msg_'+classe);
		}
		this._afficher();
		if(this.options.duration > 0)
			this.fadeOut = this._cacher.delay(this.options.duration, this);
	},

	_afficher: function() {
		this._reposition();
		this.container.fade(this.options.fadeInValue);
	},

	_cacher: function() {
		this.container.fade('out');
	},

	_reposition: function() {
		this.container.setStyles({
			'position'	: 'fixed',
			'top'		: '50%',
			'left'		: '50%',
			'margin-top'	: '-' + this.container.getSize().y / 2 + 'px',
			'margin-left'	: '-' + this.container.getSize().x / 2 + 'px'
		});
	}
});

zMessage.getInstance = function(options) {
	if (typeof this.instance == 'undefined') this.instance = new zMessage();
	if (options != 'undefined') this.instance.setOptions(options);
	return this.instance;
}

zMessage.display = function(chaine, type, options) {
	zMessageClass = zMessage.getInstance(options);
	zMessageClass.affichage(chaine, type);
}

zMessage.info = function(chaine, options) {
	zMessage.display(chaine, 'info', options);
}

zMessage.error = function(chaine, options) {
	zMessage.display(chaine, 'error', options);
}
zMessage.cacher = function() {
	zMessageClass = zMessage.getInstance();
	zMessageClass._cacher();
}
