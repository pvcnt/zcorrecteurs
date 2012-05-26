/**
 * @requires mootools
 * 			 @ZcoCoreBundle/Resources/public/css/zcode.css
 *		     @ZcoCoreBundle/Resources/public/css/new_zform.css
 */
var Editor = new Class({
	Implements: Options,
	
	initialize: function(id, options)
	{
		this.setOptions(options);
		
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
		var sections = new Element('div', {
			'class': 'zform-sections',
			'style': 'display: none'
		});
		var tabs = new Element('div', {
			'class': 'zform-tabs'
		});
	
		for (section in options)
		{
			var sectionDiv = new Element('div', {
				'class': 'zform-section zform-section-' + section,
				'id': 'zform-section-' + section
			});
		
			for (group in options[section])
			{
				if (group[0] == '_')
				{
					continue;
				}
				var groupDiv = new Element('div', {
					'class': 'zform-group zform-group-' + group
				});
				if (options[section][group]._label)
				{
					groupDiv.grab(new Element('span', {
						'class': 'zform-tool-label',
						'html': options[section][group]._label + ' &rarr;'
					}));
				}
			
				for (tool in options[section][group])
				{
					if (tool[0] == '_')
					{
						continue;
					}
				
					var conf = options[section][group][tool];
					if (conf.type == 'button')
					{
						var span = new Element('span', {
							'class': 'zform-tool-button'
						});
						var link = new Element('a', {
							'title': conf.label,
							'href': '#',
							'data-target': section + '.' + group + '.' + tool
						});
					
						link.addEvent('click', function(e)
						{
							e.preventDefault();
							if (this.get('data-target'))
							{
								var target = this.get('data-target');
								var parts = target.split('.');
								_insertCallback(zform, options[parts[0]][parts[1]][parts[2]].action);
							}
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
							link.addClass('btn');
							link.set('text', conf.label);
						}
						span.grab(link),
						groupDiv.grab(span);
					} /* end of "button" tool type */
					else if (conf.type == 'select')
					{
						var span = new Element('span', {
							'class': 'zform-tool-select'
						});
						var select = new Element('select', {
							'data-target': section + '.' + group + '.' + tool
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
						}
					
						select.addEvent('change', function(e)
						{
							if (this.get('data-target'))
							{
								var target = this.get('data-target');
								var parts = target.split('.');
								_insertCallback(zform, options[parts[0]][parts[1]][parts[2]].list[this.value].action);
								this.value = '_default';
							}
						});
					
						span.grab(select),
						groupDiv.grab(span);
					}
				}
				sectionDiv.grab(groupDiv);
			}
		
			if (section == 'main')
			{
				sectionDiv.addClass('zform-section-visible');
				sectionDiv.removeClass('zform-section');
				main.grab(sectionDiv);
			}
			else
			{
				sectionDiv.addClass('zform-invisible');
				sections.grab(sectionDiv);
			
				var tabDiv = new Element('div', {
					'class': 'zform-tab zform-tab-invisible zform-tab-' + group
				});
				var tabLink = new Element('a', {
					href: '#',
					text: options[section]._label ? options[section]._label : group,
					'data-target': sectionDiv.get('id')
				});
			
				tabLink.addEvent('click', function(e)
				{
					e.preventDefault();
					var tab = e.target.getParent('.zform-tab');
					var section = document.id(e.target.get('data-target'));
					if (tab.hasClass('zform-tab-invisible'))
					{
						$$('.zform-tab').removeClass('zform-tab-visible');
						$$('.zform-tab').addClass('zform-tab-invisible');
						$$('.zform-section').removeClass('zform-visible');
						$$('.zform-section').addClass('zform-invisible');
						
						tab.addClass('zform-tab-visible');
						tab.removeClass('zform-tab-invisible');
						section.removeClass('zform-invisible');
						section.addClass('zform-visible');
						sections.setStyle('display', '');
					}
					else
					{
						tab.addClass('zform-tab-invisible');
						tab.removeClass('zform-tab-visible');
						section.removeClass('zform-visible');
						section.addClass('zform-invisible');
						sections.setStyle('display', 'none');
					}
				});
			
				tabDiv.grab(tabLink);
				tabs.grab(tabDiv);
			}
		}
	
		main.grab(tabs);
		toolbar.grab(main);
		toolbar.grab(sections);
	
		var rawLinks = new Element('div', {
			'class': 'zform-group',
			'style': 'float: right;'
		});
		rawLinks.grab(new Element('a', {
			'class': 'zform-rawlink zform-squeezebox-link',
			'href': '/options/sauvegardes-zcode.html?id=' + id + '&xhr=1',
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
				url: '/informations/ajax-parse-zcode.html',
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
	}
});