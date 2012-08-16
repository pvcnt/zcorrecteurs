/**
 * @provides vitesse-behavior-zform
 * @requires vitesse-behavior
 *		     mootools
 *           @ZcoCoreBundle/Resources/public/js/Editor.js
 * 			 @ZcoCoreBundle/Resources/public/css/zcode.css
 *		     @ZcoCoreBundle/Resources/public/css/new_zform.css
 *           vitesse-behavior-squeezebox
 *           vitesse-behavior-twipsy
 *           vitesse-behavior-resizable-textarea
 */
Behavior.create('zform', function(config, statics)
{
	if (!statics.count)
	{
		statics.count = 1;
	}
	else
	{
		statics.count++;
	}
	
	var listCallback = function(zform, action)
	{
		var lines = zform.getSelectedText().split("\n");
		var content = '<puce>' + lines.join('</puce>' + "\n" + '<puce>') + '</puce>';
		content = (action.options.pre ? action.options.pre : '') + content;
		content += (action.options.post ? action.options.post : '');
		zform.insertAtCursor(content, true);
	};
	
	if (!config.options)
	{
		var colors = {};
		var colorsDict = {
			'pink': {'label': 'Rose', value: 'rose'},
			'red': {'label': 'Rouge', value: 'rouge'},
			'orange': {'label': 'Orange', value: 'orange'},
			'yellow': {'label': 'Jaune', value: 'jaune'},
			'dgreen': {'label': 'Vert foncé', value: 'vertf'},
			'lgreen': {'label': 'Vert clair', value: 'vertc'},
			'turquoise': {'label': 'Turquoise', value: 'turquoise'},
			'bluegrey': {'label': 'Bleu-gris', value: 'bleugris'},
			'marine': {'label': 'Marine', value: 'marine'},
			'violet': {'label': 'Violet', value: 'violet'},
			'maroon': {'label': 'Marron', value: 'marron'},
			'black': {'label': 'Noir', value: 'noir'},
			'grey': {'label': 'Gris', value: 'gris'},
			'silver': {'label': 'Argent', value: 'argent'},
			'white': {'label': 'Blanc', value: 'blanc'}
		};
		for (var key in colorsDict)
		{
			colors[key] = {
				label: colorsDict[key].label,
				link_class: 'zform-block-button-color zform-button-' + key,
				action: {
					type: 'encapsulate',
					options: {
						'pre': '<couleur nom="' + colorsDict[key].value + '">',
						'post': '</couleur>'
					}
				}
			};
		}
		
		var smilies = {};
		var smiliesDict = {
			    ':)': 'smile.png',
			    ':D': 'heureux.png',
			    ';)': 'clin.png',
			    ':p': 'langue.png',
			    ':lol:': 'rire.gif',
			    ':euh:': 'unsure.gif',
			    ':(': 'triste.png',
			    ':o': 'huh.png',
			    ':colere2:': 'mechant.png',
			    'o_O': 'blink.gif',
			    '^^': 'hihi.png',
			    ':-°': 'siffle.png',
			    ':diable:': 'diable.png',
			    ':ninja:': 'ninja.png',
			    '>_<': 'pinch.png',
			    ':pirate:': 'pirate.png',
			    ':\'(': 'pleure.png',
			    ':honte:': 'rouge.png',
			    ':soleil:': 'soleil.png',
			    ':waw:': 'waw.png',
			    ':zorro:': 'zorro.png'/*,
				':magicien:': 'magicien.png',
				':ange:': 'ange.png',
				':colere:': 'angry.gif'*/
		};
		var i = 0;
		for (var key in smiliesDict)
		{
			i++;
			smilies['smilie' + i] = {
				label: key,
				icon: '/bundles/zcocore/img/zcode/smilies/' + smiliesDict[key],
				action: {
					type: 'encapsulate',
					options: {
						'pre': key,
					}
				}
			};
		}
		
		config.options = {
			'basic': {
				'bold': {
					type: 'button',
					label: 'Gras',
					icon: '/bundles/zcocore/img/zcode/gras.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<gras>',
							'post': '</gras>'
						}
					}
				},
				'italic': {
					type: 'button',
					label: 'Italique',
					icon: '/bundles/zcocore/img/zcode/italique.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<italique>',
							'post': '</italique>'
						}
					}
				},
				'underline': {
					type: 'button',
					label: 'Souligné',
					icon: '/bundles/zcocore/img/zcode/souligne.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<souligne>',
							'post': '</souligne>'
						}
					}
				},
				'strike': {
					type: 'button',
					label: 'Barré',
					icon: '/bundles/zcocore/img/zcode/barre.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<barre>',
							'post': '</barre>'
						}
					}
				},
			}, /* end of group "basic"" */
			formatting: {
				'color': {
					type: 'block',
					label: 'Couleur',
					icon: '/bundles/zcocore/img/zform/palette.png',
					block: colors,
					per_row: 5
				}
			}, /* end of group "formatting" */
			verticalpos: {
				'sup': {
					type: 'button',
					label: 'Exposant',
					icon: '/bundles/zcocore/img/zcode/exposant.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<exposant>',
							'post': '</exposant>'
						}
					}
				},
				'inf': {
					type: 'button',
					label: 'Indice',
					icon: '/bundles/zcocore/img/zcode/indice.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<indice>',
							'post': '</indice>'
						}
					}
				}
			}, /* end of group "verticalpos"" */
			positions: {
				left: {
					type: 'button',
					label: 'Texte aligné à gauche',
					icon: '/bundles/zcocore/img/zform/align-left.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<position valeur="gauche">',
							'post': '</position>'
						}
					}
				},
				center: {
					type: 'button',
					label: 'Texte centré',
					icon: '/bundles/zcocore/img/zform/align-center.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<position valeur="centre">',
							'post': '</position>'
						}
					}
				},
				right: {
					type: 'button',
					label: 'Texte aligné à droite',
					icon: '/bundles/zcocore/img/zform/align-right.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<position valeur="droite">',
							'post': '</position>'
						}
					}
				},
				justify: {
					type: 'button',
					label: 'Texte justifié',
					icon: '/bundles/zcocore/img/zform/align-justify.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<position valeur="justifie">',
							'post': '</position>'
						}
					}
				}
			}, /* end of group "positions" */
			lists: {
				unordered: {
					type: 'button',
					label: 'Liste avec tirets cadratins',
					icon: '/bundles/zcocore/img/zform/unordered-list.png',
					action: {
						type: 'callback',
						execute: listCallback,
						options: {
							'pre': '<liste>' + "\n",
							'post': "\n" + '</liste>'
						}
					}
				},
				ordered: {
					type: 'button',
					label: 'Liste numérotée',
					icon: '/bundles/zcocore/img/zform/ordered-list.png',
					action: {
						type: 'callback',
						execute: listCallback,
						options: {
							'pre': '<liste type="1">' + "\n",
							'post': "\n" + '</liste>'
						}
					}
				}
			}, /* end of group "lists" */
			semantic: {
				heading1: {
					type: 'button',
					label: 'Titre de premier niveau',
					icon: '/bundles/zcocore/img/zform/heading1.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<titre1>',
							'post': '</titre1>'
						}
					}
				},
				heading2: {
					type: 'button',
					label: 'Titre de second niveau',
					icon: '/bundles/zcocore/img/zform/heading2.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<titre2>',
							'post': '</titre2>'
						}
					}
				}
			}, /* end of group "semantic" */
			objects: {
				link: {
					type: 'button',
					label: 'Lien',
					icon: '/bundles/zcocore/img/zcode/lien.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<lien url="">',
							'post': '</lien>'
						}
					}
				},
				image: {
					type: 'button',
					label: 'Image',
					icon: '/bundles/zcocore/img/zcode/image.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<image>',
							'post': '</image>'
						}
					}
				},
				'blocks': {
					type: 'block',
					label: 'Blocs spéciaux',
					icon: '/bundles/zcocore/img/zform/blocks.png',
					block: {
						info: {
							type: 'button',
							label: 'Information',
							icon: '/bundles/zcocore/img/zcode/info.png',
							action: {
								type: 'encapsulate',
								options: {
									'pre': '<information>',
									'post': '</information>'
								}
							}
						},
						warning: {
							type: 'button',
							label: 'Attention',
							icon: '/bundles/zcocore/img/zcode/attention.png',
							action: {
								type: 'encapsulate',
								options: {
									'pre': '<attention>',
									'post': '</attention>'
								}
							}
						},
						error: {
							type: 'button',
							label: 'Erreur',
							icon: '/bundles/zcocore/img/zcode/erreur.png',
							action: {
								type: 'encapsulate',
								options: {
									'pre': '<erreur>',
									'post': '</erreur>'
								}
							}
						},
						question: {
							type: 'button',
							label: 'Question',
							icon: '/bundles/zcocore/img/zcode/question.png',
							action: {
								type: 'encapsulate',
								options: {
									'pre': '<question>',
									'post': '</question>'
								}
							}
						},
					}
				},
				quote: {
					type: 'button',
					label: 'Citation',
					icon: '/bundles/zcocore/img/zcode/citation.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<citation>',
							'post': '</citation>'
						}
					}
				},
				secret: {
					type: 'button',
					label: 'Secret',
					icon: '/bundles/zcocore/img/zcode/secret.png',
					action: {
						type: 'encapsulate',
						options: {
							'pre': '<secret>',
							'post': '</secret>'
						}
					}
				},
				symbol: {
					type: 'block',
					label: 'Caractères spéciaux',
					icon: '/bundles/zcocore/img/zform/symbol.png',
					block: {
						char1: {
							type: 'button',
							label: 'À',
							action: {
								type: 'encapsulate',
								options: {
									'pre': 'À',
								}
							}
						},
						char2: {
							type: 'button',
							label: 'Ç',
							action: {
								type: 'encapsulate',
								options: {
									'pre': 'Ç',
								}
							}
						},
						char3: {
							type: 'button',
							label: 'É',
							action: {
								type: 'encapsulate',
								options: {
									'pre': 'É',
								}
							}
						},
						char4: {
							type: 'button',
							label: 'È',
							action: {
								type: 'encapsulate',
								options: {
									'pre': 'È',
								}
							}
						},
						char5: {
							type: 'button',
							label: '« »',
							action: {
								type: 'encapsulate',
								options: {
									'pre': '« ',
									'post': ' »'
								}
							}
						},
						char6: {
							type: 'button',
							label: '—',
							title: 'Tiret cadratin',
							action: {
								type: 'encapsulate',
								options: {
									'pre': '—',
								}
							}
						},
						char7: {
							type: 'button',
							label: '–',
							title: 'Tiret demi-cadratin',
							action: {
								type: 'encapsulate',
								options: {
									'pre': '–',
								}
							}
						},
						char8: {
							type: 'button',
							label: '…',
							action: {
								type: 'encapsulate',
								options: {
									'pre': '…',
								}
							}
						},
						char9: {
							type: 'button',
							label: 'œ',
							action: {
								type: 'encapsulate',
								options: {
									'pre': 'œ',
								}
							}
						}
					}
				},
				smilies: {
					type: 'block',
					label: 'Frimousses',
					icon: '/bundles/zcocore/img/zform/smilie.png',
					block: smilies,
					per_row: 7
				}
			} /* end of group "objects" */
		};
/*			}
			formatting: {
				_label: 'Mise en forme',
				formatting: {
					font: {
						type: 'select',
						label: 'Police',
						list: {
							arial: {
								label: 'Arial',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<police nom="arial">',
										'post': '</police>'
									}
								}
							},
							times: {
								label: 'Times',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<police nom="times">',
										'post': '</police>'
									}
								}
							},
							courier: {
								label: 'Courier',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<police nom="courier">',
										'post': '</police>'
									}
								}
							},
							impact: {
								label: 'Impact',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<police nom="impact">',
										'post': '</police>'
									}
								}
							},
							geneva: {
								label: 'Geneva',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<police nom="geneva">',
										'post': '</police>'
									}
								}
							},
							optima: {
								label: 'Optima',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<police nom="optima">',
										'post': '</police>'
									}
								}
							}
						}
					}, /* end of tool "font" */
					/*size: {
						type: 'select',
						label: 'Taille',
						list: {
							vvsmall: {
								label: 'Très très petit',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<taille valeur="ttpetit">',
										'post': '</taille>'
									}
								}
							},
							vsmall: {
								label: 'Très petit',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<taille valeur="tpetit">',
										'post': '</taille>'
									}
								}
							},
							small: {
								label: 'Petit',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<taille valeur="petit">',
										'post': '</taille>'
									}
								}
							},
							big: {
								label: 'Gros',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<taille valeur="gros">',
										'post': '</taille>'
									}
								}
							},
							vbig: {
								label: 'Très gros',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<taille valeur="tgros">',
										'post': '</taille>'
									}
								}
							},
							vvbig: {
								label: 'Très très gros',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<taille valeur="ttgros">',
										'post': '</taille>'
									}
								}
							},
						}
					}
				} 
			} */
	}
	
	new Editor(config.id, config.options);
	
	var additionalBehaviors = {
		'resizable-textarea': [{'id': config.id}],
		'squeezebox': [{'selector': '#' + config.id + '_zform .zform-squeezebox-link', 'options': {'handler': 'iframe'}}],
		'twipsy': [{'selector': '#' + config.id + '_zform .zform-tool-button > a'}]
	};
	Behavior.init(additionalBehaviors);
});