<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur gérant la récupération d'un tutoriel du SdZ et la mise en BDD
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class GetTutoSdzAction extends ZcorrectionActions
{
	public function execute()
	{
	    return new Response('La liaison avec le SdZ est fermée. Désolé !');
	    
		$domaine = explode('.', $_SERVER['HTTP_HOST']);
		$ss_domaine = $domaine[0];
		$domaine_name = $domaine[1];
		$tld = $domaine[2];
		if(!in_array($ss_domaine, array('dev', 'www')) OR $domaine_name !== 'zcorrecteurs' OR ($tld !== 'fr:80' AND $tld !== 'net:80'))
		{
			exit('Erreur de sous-domaine, de domaine ou de TLD');
		}
		if($ss_domaine == 'www')
		{
			$rep = 'prod';
		}
		else
		{
			$ss_domaine = 'dev';
			$rep = 'dev';
		}

		$urlsdz = 'http://'.$ss_domaine.'.siteduzero.com/Templates/xml/xml_zco/';
		//80.248.219.123
		if(isset($_POST['token']) AND strlen($_POST['token']) == 40 AND $_SERVER['REMOTE_ADDR'] == gethostbyname($ss_domaine.'.siteduzero.com'))
		{
			$nom_fichier = time().'_'.$_POST['token'];
			$push_xml = file_get_contents($urlsdz.$_POST['token'].'.xml');
			if($push_xml)
			{
				//Décryptage du XML
				$key = $this->container->getParameter('zco_zcorrection.mcrypt_key');
				$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
				$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
				$push_xml = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $push_xml, MCRYPT_MODE_ECB, $iv);
				$debut_fichier = substr($push_xml, 0, 43);

				if(preg_match('`.?<\?xml version="1.0" encoding="([a-z0-9_-]+)"\?>`is', $debut_fichier, $encodage))
				{
					/* Encodage utf-8 obligé (SimpleXml le modifie lui-même) */
					$encodage = strtolower($encodage[1]);

					if (in_array($encodage, array('utf-8', 'iso-8859-1', 'iso-8859-15')))
					{
						file_put_contents(BASEPATH.'/web/tutos/'.$nom_fichier.'.xml', utf8_encode(utf8_decode($push_xml)));
						$push = new DomDocument();
						$push->loadXML($push_xml);
						$elements = $push->getElementsByTagName('push');
						if($elements->length > 0)
						{
							//On récupère les enfants de push
							$element = $elements->item(0);
							$enfants = $element->childNodes;
							$children = array();
							foreach($enfants as $enfant)
							{
								$nom = $enfant->nodeName;
								$children[] = $nom;
								if($nom == 'informations')
								{
									$informations = $enfant;
								}
								elseif($nom == 'minituto' OR $nom == 'bigtuto')
								{
									$tuto = $enfant;
									$type_tuto = $nom;
								}
							}
							//On récupère le token
							$token_xml = $informations->getAttribute('token');

							//On récupère les enfants validateur et mbr
							$informations = $informations->childNodes;
							foreach($informations as $enfant)
							{
								$nom = $enfant->nodeName;
								if($nom == 'validateur')
								{
									$validateur = $enfant;
								}
								elseif($nom == 'mbr')
								{
									$mbr = $enfant;
								}
							}
							//On récupère les infos sur le validateur
							$infos_valido = array();
							$infos_valido['pseudo'] = htmlspecialchars($validateur->getAttribute('pseudo'));
							$infos_valido['idsdz'] = (int) $validateur->getAttribute('idsdz');

							//On récupère le message du valido
							$msg_valido = trim($validateur->nodeValue);

							//On récupère les infos sur le membre
							$infos_mbr = array();
							$infos_mbr['pseudo'] = htmlspecialchars($mbr->getAttribute('pseudo'));
							$infos_mbr['idsdz'] = (int) $mbr->getAttribute('idsdz');

							if(!empty($infos_valido['pseudo']) AND !empty($infos_valido['idsdz']) AND !empty($infos_mbr['idsdz']) AND !empty($infos_mbr['pseudo']))
							{
								if ($type_tuto == 'minituto')
								{
									if($id = SoumettreTuto($nom_fichier.'.xml', MINI_TUTO, $tuto, $infos_valido, $msg_valido, $infos_mbr, $token_xml, $encodage, 0))
									{
										return new Response('OK '.$id);
									}
									else
									{
										return new Response('[zCos]Erreur1');
									}
								}
								elseif ($type_tuto == 'bigtuto')
								{
									if($id = SoumettreTuto($nom_fichier.'.xml', BIG_TUTO, $tuto, $infos_valido, $msg_valido, $infos_mbr, $token_xml, $encodage, 0))
									{
										return new Response('OK '.$id);
									}
									else
									{
										return new Response('[zCos]Erreur2');
									}
								}
								else
								{
									return new Response('[zCos]Erreur3');
								}
							}
							else
							{
								return new Response('[zCos]Erreur4');
							}
						}
						else
						{
							return new Response('[zCos]Erreur5');
						}
					}
					else
					{
						return new Response('[zCos]Erreur6');
					}
				}
				else
				{
					return new Response('[zCos]Erreur7');
				}
			}
			else
			{
				return new Response('[zCos]Erreur8');
			}
		}
		else
		{
			return new Response('[zCos]Erreur9');
		}
	}
}
