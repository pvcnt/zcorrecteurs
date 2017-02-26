/**
 * @requires mootools
 * 			 @ZcoCoreBundle/Resources/public/css/zcode.css
 *		     @ZcoCoreBundle/Resources/public/css/new_zform.css
 */
var Editor = new Class({
	Implements: Options,
	
	actions: [],
	_textareaId: null,
	_lastBackup: null,
	
	initialize: function(id, options)
	{
		this._textareaId = id;
		this.setOptions(options);
		var actions = this.actions;
		
		var _insertCallback = function(zform, action)
		{
			if (action.type == 'encapsulate')
			{
				zform.insertAroundCursor({
					before: action.options.pre ? action.options.pre : '',
					after: action.options.post ? action.options.post : '',
					defaultMiddle: action.options.middle ? action.options.middle : ''
				});
			}
			else if (action.type == 'callback')
			{
				action.execute(zform, action);
			}
		};
		
		var _createLink = function(conf, group, tool, subtool)
		{
			var link = new Element('a', {
				'title': conf.label,
				'href': '#',
				'data-target': (group && tool) ? group + '-' + tool + (subtool ? '-' + subtool : '') : ''
			});
			
			if (conf.icon)
			{
				link.grab(new Element('img', {
					'src': conf.icon,
					'alt': conf.label
				}));
			}
			else
			{
				link.addClass(conf['link_class'] != undefined ? conf['link_class'] : 'btn');
				link.set('text', conf.label);
				link.set('title', conf.title != undefined ? conf.title : conf.label)
			}
			
			return link;
		};
		
		var _createButton = function(conf, group, tool, subtool)
		{
			var span = new Element('span', {
				'class': conf['wrapper_class'] != undefined ? conf['wrapper_class'] : 'zform-tool-button'
			});
			var link = _createLink(conf, group, tool, subtool);
			
			if (group && tool)
			{
				actions[group + '-' + tool + (subtool ? '-' + subtool : '')] = conf.action;
			}
			
			link.addEvent('click', function(e)
			{
				e.preventDefault();
				if (this.get('data-target'))
				{
					var target = this.get('data-target');
					_insertCallback(zform, actions[target]);
				}
			});
			
			span.grab(link);
			
			return span;
		}
		
		var zform = document.id(id);
		var toolbar = new Element('div', {
			'class': 'zform-toolbar'
		});
		var postbar = new Element('div', {
			'class': 'zform-postbar'
		});
		var main = new Element('div', {
			'class': 'zform-main'
		})
	
		for (group in options)
		{
			if (group[0] == '_')
			{
				continue;
			}
			var groupDiv = new Element('div', {
				'class': 'zform-group zform-group-' + group
			});
			if (options[group]._label)
			{
				groupDiv.grab(new Element('span', {
					'class': 'zform-tool-label',
					'html': options[group]._label + ' &rarr;'
				}));
			}
		
			for (tool in options[group])
			{
				if (tool[0] == '_')
				{
					continue;
				}
				
				var conf = options[group][tool];
				if (conf.type == 'button')
				{
					groupDiv.grab(_createButton(conf, group, tool));
				} /* end of "button" tool type */
				else if (conf.type == 'select')
				{
					var span = new Element('span', {
						'class': 'zform-tool-select'
					});
					var select = new Element('select', {
						'data-target': group + '-' + tool
					});
					select.grab(new Element('option', {
						'class': 'zform-tool-select-title',
						'text': conf.label,
						'value': '_default',
					}));
				
					for (option in conf.list)
					{
						select.grab(new Element('option', {
							'value': option,
							'text': conf.list[option].label
						}));
						actions[group + '-' + tool + '-' + option] = conf.list[option].action;
					}
				
					select.addEvent('change', function(e)
					{
						if (this.get('data-target'))
						{
							var target = this.get('data-target');
							_insertCallback(zform, actions[target + '-' + this.value].action);
							this.value = '_default';
						}
					});
				
					span.grab(select),
					groupDiv.grab(span);
				} /* end of "select"" tool type */
				else if (conf.type == 'block')
				{
					var span = new Element('span', {
						'class': 'zform-tool-button'
					});
					var link = _createLink(conf, group, tool);
					
					link.addEvent('mouseenter', function(e)
					{
						if (this.tagName.toLowerCase() == 'a')
						{
							$$('.zform-tool-block').removeClass('zform-visible').addClass('zform-invisible');
							
							var target = this.getParent('span').getElement('.zform-tool-block');
							var main = target.getParent('.zform-main');
							target.setStyle('top', (main.getPosition().y + main.getSize().y) + 'px');
							target.setStyle('left', (this.getPosition().x - 11) + 'px');
							target.removeClass('zform-invisible').addClass('zform-visible');
						}
					});
					link.addEvent('click', function(e){
						event.preventDefault();
						return false;
					});
					
					span.grab(link),
					groupDiv.grab(span);
					
					var block = new Element('span', {
						//'id': 'zform-block-button-' + group + '-' + tool,
						'class': 'zform-tool-block zform-invisible'
					});
					var i = 0;
					var perRow = conf.per_row != undefined ? conf.per_row : 0;
					for (symbol in conf.block)
					{						
						conf.block[symbol]['link_class'] = conf.block[symbol]['link_class'] != undefined ? conf.block[symbol]['link_class'] : '';
						conf.block[symbol]['wrapper_class'] = conf.block[symbol]['wrapper_class'] != undefined ? conf.block[symbol]['wrapper_class'] : 'zform-block-button';
						var button = _createButton(conf.block[symbol], group, tool, symbol);
						block.grab(button);
						
						i++;
						if ((i % perRow) == 0)
						{
							block.grab(new Element('br'));
						}
					}
					block.inject(link, 'after');
				} /* end of "block" tool type */
			}
			main.grab(groupDiv);
		}
	
		document.addEvent('click', function(e)
		{
			$$('.zform-tool-block').removeClass('zform-visible').addClass('zform-invisible');
		});
		toolbar.grab(main);
	
		var rawLinks = new Element('div', {
			'class': 'zform-group',
			'style': 'float: right;'
		});
		rawLinks.grab(new Element('a', {
			'class': 'zform-rawlink zform-squeezebox-link',
			'href': Routing.generate('zco_user_zformBackups', {'textarea': id, 'xhr': 1}),
			'text': 'Sauvegardes automatiques',
			'style': 'background-image: url(/bundles/zcooptions/img/sauvegardes_zcode.png)'
		}));
		rawLinks.grab(new Element('a', {
			'class': 'zform-rawlink zform-squeezebox-link',
			'href': '/fichiers?xhr=1&textarea=' + id,
			'text': 'Envoi de fichiers',
			'style': 'background-image: url(/bundles/zcofile/img/zform_icon.png)'
		}));
	
		var previewButton = new Element('a', {
			'text': 'Prévisualiser',
			'href': '#',
			'class': 'btn'
		});
		var previewArea = zform.getParent('.zform').getElement('.zform-preview-area');
		
		previewButton.addEvent('click', function(e)
		{
			e.preventDefault();
			var xhr = new Request({
				method: 'post',
				url: Routing.generate('zco_parser_api_parse'),
				onSuccess: function(text)
				{
					previewArea.removeClass('zform-invisible');
					previewArea.set('html', text);
				}
			});
			xhr.send('texte='+encodeURIComponent(zform.value));
		});
		
		var postbarPreview = new Element('div', {
			'class': 'zform-postbar-preview zform-group'
		}).grab(previewButton);
		postbar.grab(postbarPreview);
		postbar.grab(rawLinks);
		
		toolbar.inject(zform.getParent('.zform-wrapper'), 'before');
		postbar.inject(zform.getParent('.zform-wrapper'), 'after');

		//Initialise la sauvegarde automatique du formulaire.
		this._lastBackup = zform.value;
		this.autoBackup();
	},

	backup: function() {
		var textarea = document.id(this._textareaId);
		var content = textarea.value;
		var xhr = new Request({
			method: 'post', 
			url: Routing.generate('zco_user_api_saveZform'),
			onSuccess: function(text, xml){
				this._lastBackup = content;
				textarea.highlight('#b3ffb3');
			}
		});
		xhr.send('texte=' + encodeURIComponent(content) + '&url=' + encodeURIComponent(document.location.pathname));
	},

	autoBackup: function() {
		var shortTime = 10000;
		var longTime = 30000;
		var content = document.id(this._textareaId).value;
		var that = this;

		if (content == '') {
	        setTimeout(function() { that.autoBackup(); }, shortTime);
	    } else {
			if (content != this._lastBackup) {
				this.backup();
			}
			setTimeout(function() { that.autoBackup(); }, longTime);
		}
	}
});