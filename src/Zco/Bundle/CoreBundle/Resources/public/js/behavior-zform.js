/**
 * @provides vitesse-behavior-zform
 * @requires vitesse-behavior
 *		     mootools
 *           @ZcoCoreBundle/Resources/public/js/Editor.js
 * 			 @ZcoCoreBundle/Resources/public/css/zcode.css
 *		     @ZcoCoreBundle/Resources/public/css/new_zform.css
 */
Behavior.create('zform', function(config)
{
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
		config.options = {
			'main': {
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
					}
				}, /* end of group "basic"" */
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
					}
				} /* end of group "objects" */
			}, /* end of section "main" */
			advanced: {
				_label: 'Avancé',
				verticalpos: {
					_label: 'Position',
					sup: {
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
					inf: {
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
				}, /* end of group "verticalpos" */
				blocks: {
					_label: 'Blocs',
					unorderedList: {
						type: 'button',
						label: 'Liste non ordonnée',
						icon: '/bundles/zcocore/img/zcode/liste.png',
						action: {
							type: 'callback',
							execute: listCallback,
							options: {
								'pre': '<liste>' + "\n",
								'post': "\n" + '</liste>'
							}
						}
					},
					orderedList: {
						type: 'button',
						label: 'Liste ordonnée',
						icon: '/bundles/zcocore/img/zform/ordered_list.png',
						action: {
							type: 'callback',
							execute: listCallback,
							options: {
								'pre': '<liste type="1">' + "\n",
								'post': "\n" + '</liste>'
							}
						}
					},
					sup: {
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
					inf: {
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
					}
				}, /* end of group "blocks" */
				remarks: {
					_label: 'Remarques',
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
					attention: {
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
					erreur: {
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
					}
				} /* end of group "remarks" */
			}, /* end of section "advanced" */
			formatting: {
				_label: 'Mise en forme',
				formatting: {
					semantic: {
						type: 'select',
						label: 'Sémantique',
						list: {
							title1: {
								label: 'Titre 1',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<titre1>',
										'post': '</titre1>'
									}
								}
							},
							title2: {
								label: 'Titre 2',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<titre2>',
										'post': '</titre2>'
									}
								}
							}
						}
					}, /* end of tool "semantic" */
					position: {
						type: 'select',
						label: 'Position',
						list: {
							left: {
								label: 'Gauche',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<position valeur="gauche">',
										'post': '</position>'
									}
								}
							},
							right: {
								label: 'Droite',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<position valeur="droite">',
										'post': '</position>'
									}
								}
							},
							center: {
								label: 'Centré',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<position valeur="centre">',
										'post': '</position>'
									}
								}
							},
							justify: {
								label: 'Justifié',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<position valeur="justifie">',
										'post': '</position>'
									}
								}
							},
							floatLeft: {
								label: 'Flottant gauche',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<flottant valeur="gauche">',
										'post': '</flottant>'
									}
								}
							},
							floatRight: {
								label: 'Flottant droit',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<flottant valeur="droite">',
										'post': '</flottant>'
									}
								}
							}
						}
					}, /* end of tool "position" */
					color: {
						type: 'select',
						label: 'Couleur',
						list: {
							pink: {
								label: 'Rose',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="rose">',
										'post': '</couleur>'
									}
								}
							},
							red: {
								label: 'Rouge',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="rouge">',
										'post': '</couleur>'
									}
								}
							},
							orange: {
								label: 'Orange',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="orange">',
										'post': '</couleur>'
									}
								}
							},
							yellow: {
								label: 'Jaune',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="jaune">',
										'post': '</couleur>'
									}
								}
							},
							darkGreen: {
								label: 'Vert foncé',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="vertf">',
										'post': '</couleur>'
									}
								}
							},
							lightGreen: {
								label: 'Vert clair',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="vertc">',
										'post': '</couleur>'
									}
								}
							},
							olive: {
								label: 'Olive',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="olive">',
										'post': '</couleur>'
									}
								}
							},
							turquoise: {
								label: 'Turquoise',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="turquoise">',
										'post': '</couleur>'
									}
								}
							},
							blueGrey: {
								label: 'Bleu-gris',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="bleugris">',
										'post': '</couleur>'
									}
								}
							},
							blue: {
								label: 'Bleu',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="bleu">',
										'post': '</couleur>'
									}
								}
							},
							marine: {
								label: 'Marine',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="marine">',
										'post': '</couleur>'
									}
								}
							},
							violet: {
								label: 'Violet',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="violet">',
										'post': '</couleur>'
									}
								}
							},
							maroon: {
								label: 'Marron',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="marron">',
										'post': '</couleur>'
									}
								}
							},
							black: {
								label: 'Noir',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="noir">',
										'post': '</couleur>'
									}
								}
							},
							grey: {
								label: 'Gris',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="gris">',
										'post': '</couleur>'
									}
								}
							},
							silver: {
								label: 'Argent',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="argent">',
										'post': '</couleur>'
									}
								}
							},
							white: {
								label: 'Blanc',
								action: {
									type: 'encapsulate',
									options: {
										'pre': '<couleur nom="blanc">',
										'post': '</couleur>'
									}
								}
							}
						}
					}, /* end of tool "color" */
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
					size: {
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
					} /* end of tool "size" */
				} /* end of group "formatting" */
			}, /* end of section "formatting" */
			characters: {
				_label: 'Caractères spéciaux',
				accents: {
					_label: 'Majuscules',
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
					}
				}, /* end of group "majuscules" */
				typography: {
					_label: 'Typographie',
					char5: {
						type: 'button',
						label: '« »',
						action: {
							type: 'encapsulate',
							options: {
								'pre': '« ',
								'post': ' »'
							}
						}
					},
					char6: {
						type: 'button',
						label: '–',
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
				} /* end of group "typography" */
			} /* end of section "characters" */
		};
	}
	
	new Editor(config.id, config.options);
});