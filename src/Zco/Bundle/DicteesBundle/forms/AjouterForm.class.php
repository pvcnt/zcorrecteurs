<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Formulaire d'ajout d'une dictée
 *
 * @copyright   Copyright (c) www.zcorrecteurs.fr 2011
 * @author mwsaz@zcorrecteurs.fr
 */

class AjouterForm extends Form
{
	protected function configure()
	{
		$cfg = Config::get('messages');
		$difficultes = array();
		foreach($cfg['DicteeDifficultes'] as $k => &$diff)
			$difficultes[$k] = array($diff, ' style="color:'.$cfg['DicteeCouleurs'][$k].'"');
		$this->addFieldset('Dictée');
		$this->addWidget('titre', new Widget_Input_Text(array(), array('size' => 60, 'maxlength' => 255)));
		$this->addWidget('difficulte', new Widget_Select(array('choices' => $difficultes)));

		$choixTemps = array(); $step = 5;
		foreach (range(5, 55, 5) as $choix)
			$choixTemps[$choix] = $choix.' minutes';

		$this->addWidget('temps_estime', new Widget_Select(array('choices' => $choixTemps)));
		$this->addWidget('texte', new Widget_Textarea);

		$this->setHelpText('difficulte', 'Choisissez la difficulté de votre dictée.');
		$this->setHelpText('temps_estime', 'Temps indicatif pour faire cette dictée.');
		$this->setLabels(array(
			'titre' => 'Titre de la dictée',
			'difficulte' => 'Difficulté',
			'temps_estime' => 'Durée estimée'
		));
		$this->attachFieldset(array(
			'titre' => 'Dictée',
			'difficulte' => 'Dictée',
			'temps_estime' => 'Dictée',
		));

		if(verifier('dictees_publier'))
		{
			$this->addWidget('publique', new Widget_Checkbox);
			$this->setHelpText('publique', 'Cochez cette case pour que la dictée soit rendue publique.');
			$this->setLabel('publique', 'Mettre en ligne');
			$this->attachFieldset('publique', 'Dictée');
			$this->setValidator('publique', new Validator_Boolean);
		}

		$this->attachFieldset('texte', 'Dictée');

		$this->addFieldset('Voix de lecture de la dictée');
		/*$voix = array(
			DICTEE_SON_PERSO  => 'Envoyer',
			DICTEE_SON_AUTO => 'Générer'
		);
		$this->addWidget('type_son', new Widget_Radio(array('choices' => $voix)));
		*/
		$this->addWidget('MAX_FILE_SIZE', new Widget_Input_Hidden);
		$this->addWidget('lecture_rapide', new Widget_Input_File);
		$this->addWidget('lecture_lente', new Widget_Input_File);

		$this->setDefault('MAX_FILE_SIZE', sizeint(ini_get('upload_max_filesize')));
		$this->setHelpText(array(
			//'type_son' => 'Choisissez si vous voulez envoyer un fichier audio ou s\'il faut le générer.',
			'lecture_rapide' => 'Au format ogg ou mp3, taille maximale : '.sizeformat(ini_get('upload_max_filesize')).'.',
			'lecture_lente' => 'Au format ogg ou mp3, taille maximale : '.sizeformat(ini_get('upload_max_filesize')).'.'
		));
		//$this->setLabel('type_son', 'Type de voix');
		$this->attachFieldset(array(
			//'type_son' => 'Voix de lecture de la dictée',
			'MAX_FILE_SIZE' => 'Voix de lecture de la dictée',
			'lecture_rapide' => 'Voix de lecture de la dictée',
			'lecture_lente' => 'Voix de lecture de la dictée'
		));


		$fname = 'Informations supplémentaires (facultatif)';
		$this->addFieldset($fname);

		$this->addWidget('auteur', new Widget_Auteur);
		$this->addWidget('source', new Widget_Input_Text(array(), array('size' => 60, 'maxlength' => 255)));
		$this->addWidget('tags', new Widget_Tags);
		$this->addWidget('MAX_FILE_SIZE', new Widget_Input_Hidden);
		$this->addWidget('icone', new Widget_Input_File);
		$this->setHelpText('auteur', 'Choisissez un auteur dans la liste, ou créez-en un s\'il n\'existe pas déjà.');
		$this->setHelpText('source', 'Indiquez l\'origine du texte.');
		$this->setHelpText('tags', 'Liez des mots clés à votre dictée (séparés par des virgules).');
		$this->setHelpText('icone','Icône pour votre dictée, au format jpg ou png.');
		$this->addWidget('description', new Widget_zForm);
		$this->addWidget('indications', new Widget_zForm);
		$this->addWidget('commentaires', new Widget_zForm);
		$this->setHelpText('indications', 'Indications au membre, comme l\'orthographe des noms propres.');
		$this->setHelpText('commentaires', 'Ce texte sera affiché avec la correction.');
		$this->setDefault('MAX_FILE_SIZE', sizeint(ini_get('upload_max_filesize')));

		$this->setLabel('tags', 'Mots-clés');
		$this->setLabel('icone', 'Icône');

		$this->attachFieldset(array(
			'auteur'       => $fname,
			'source'       => $fname,
			'tags'         => $fname,
			'icone'		   => $fname,
			'description'  => $fname,
			'indications'  => $fname,
			'commentaires' => $fname,	
		));

		$this->setValidators(array(
			'titre'      => new Validator_String(array('max_length' => 40)),
			'difficulte' => new Validator_Choices(array('choices' => array_keys($difficultes))),
			'temps_estime' => new Validator_Choices(array('choices' => array_keys($choixTemps))),
			'texte'      => new Validator_String,
			//'type_son' => new Validator_Choices(array('choices' => array_keys($voix)))
		));
	}
}
