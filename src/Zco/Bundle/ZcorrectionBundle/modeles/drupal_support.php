<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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
 * Exception levée lors d'une erreur de communication avec le serveur REST 
 * embarqué dans Drupal.
 */
class DrupalException extends Exception { }

function CompterTicketsSupportDrupal(array $cond = array())
{
	return count(ListerTicketsSupportDrupal($cond));
}

/**
 * Liste les tickets de support présents sur Drupal et destinés à être traités 
 * par les correcteurs.
 *
 * @param array $cond Une liste de conditions. Sont supportées : etat et assigne (un pseudo)
 * @return array
 */
function ListerTicketsSupportDrupal(array $cond = array())
{

	/** 
	* Etape préliminaire : les tickets ne sont retournés que si les 
	* identifiants du compte drupal sont renseignés.
	*/
	if (!Container::hasParameter('zco_zcorrection.drupal_username') || !Container::hasParameter('zco_zcorrection.drupal_password'))
	{
		return array();
	}
	
	/**
	 * Partie 1 : connexion au compte utilisateur Drupal.
	 */
	$user = EnvoyerRequeteDrupal('user/login', array(), array(
	  'username' => Container::getParameter('zco_zcorrection.drupal_username'),
	  'password' => Container::getParameter('zco_zcorrection.drupal_password'),
	), 'post');
	$cookies = array($user['session_name'] => $user['sessid']);
	
	/**
	 * Partie 2 : récupération des tickets de support correspondant à 
	 * des textes en attente de correction.
	 */
	if (($nids = Container::getService('zco_core.cache')->get('zcorrection-node_nids')) === false)
	{
		$nids = array();
		$nodes = EnvoyerRequeteDrupal('node', $cookies);
		foreach ($nodes as $node)
		{
			if ($node['type'] === 'support_ticket')
			{
				$nids[] = $node['nid'];
			}
		}
		
		Container::getService('zco_core.cache')->set('zcorrection-node_nids', $nids, 60*15);
	}
	
	$retour = array();
	foreach ($nids as $nid)
	{
		if ($node = ConstruireTicketSupportDrupal($nid, $cookies, $cond))
		{
			$retour[] = $node;
		}
	}
	
	return $retour;
}

function ConstruireTicketSupportDrupal($nid, array $cookies, array $cond = array())
{
	if (($node = Container::getService('zco_core.cache')->get('zcorrection-node_'.$nid)) === false)
	{
		$cache = true;
		try
		{
			$node = EnvoyerRequeteDrupal('node/'.$nid, $cookies);
		}
		catch (DrupalException $e)
		{
			//On traite le cas particulier d'une 404 (ticket supprimé). On 
			//supprime alors le cache des ids des noeuds pour le régénérer 
			//et ignore silencieusement l'erreur.
			if ($e->getCode() === 404)
			{
				Container::getService('zco_core.cache')->delete('zcorrection-node_nids');
				return false;
			}
			
			//Pour toute autre exception on la relance.
			throw $e;
		}
	}
	else
	{
		$cache = false;
	}
	
	//Correspondance entre les états de textes et les états de tickets.
	$etats = array(
		ENVOYE                => 1,
		CORRECTION            => 2,
		RECORRECTION_DEMANDEE => 3,
		TERMINE_CORRIGE       => 4,
		RECORRECTION          => 5,
	);
	
	if (!is_array($cond['etat']))
	{
		$cond['etat'] = array($cond['etat']);
	}
	
	//Liste des clients autorisés.
	$clients = array(1 => 'PSB', 6 => 'Autres', 8 => 'Gwaeron', 9 => 'Animasphère');
	
	if (isset($clients[$node['client']]))
	{
		if ($cache)
		{
			$node['state'] = array_search($node['state'], $etats);
		}
		
		if (empty($cond['etat']) || in_array($node['state'], $cond['etat']))
		{
			if ($cache)
			{
				$node['partenaire'] = $clients[$node['client']];
				$node['type'] = 'drupal';
				$node['user'] = EnvoyerRequeteDrupal('user/'.$node['uid'], $cookies);
				$node = FormaterCommentaireTicketSupportDrupal($node);
				if ($node['assigned'])
				{
					$node['assigned'] = EnvoyerRequeteDrupal('user/'.$node['assigned'], $cookies);
				}
			
				Container::getService('zco_core.cache')->set('zcorrection-node_'.$nid, $node, 60*15);
			}
			
			if (empty($cond['assigne']) || (!empty($node['assigned']) && $node['assigned']['name'] == $cond['assigne']))
			{
				return $node;
			}
		}
	}
	
	return false;
}

/**
 * Envoie une requête REST au serveur Drupal hébergeant les tickets de support.
 * Les données sont envoyées au même format que les formulaires et reçues en JSON.
 *
 * @param string $service Le service à appeler (par exemple node ou user/3)
 * @param array $cookies Les cookies à joindre à la requête
 * @param array $data Les données à envoyer
 * @param string $method La méthode à utiliser (get ou post)
 * @return array Les données décodées
 * @throws DrupalException Si le code de retour HTTP est différent de 200
 */
function EnvoyerRequeteDrupal($service, $cookies = array(), $data = array(), $method = 'get')
{
	if (!empty($data))
	{
		$data = array_map(function($key, $value){
			return $key.'='.$value;
		}, array_keys($data), array_values($data));
		$data = ($method === 'get' ? '?' : '').implode('&', $data);
	}
	else
	{
		$data = '';
	}
	
	$url = 'http://tickets.corrigraphie.org/zcorrecteurs/'.$service.'.json';
	$method = strtolower($method);
	if ($method === 'post')
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-type: multipart/form-data',
			'Accept: multipart/form-data',
		));
	}
	else
	{
		$url.= $data;
		$curl = curl_init($url);
	}
	
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FAILONERROR, true);
	
	if (!empty($cookies))
	{
		$cookies = array_map(function($key, $value){
			return $key.'='.$value;
		}, array_keys($cookies), array_values($cookies));
		$cookies = implode('&', $cookies);
		curl_setopt($curl, CURLOPT_COOKIE, $cookies);
	}
	
	$response = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	if ($code == 200)
	{
		return json_decode($response, true);
	}
	else
	{
		throw new DrupalException(curl_error($curl), $code);
	}
}

/**
 * Formate le contenu de base du ticket pour être présentable dans l'interface.
 *
 * @param array $node Le nœud original
 * @return array $node Le nœud avec son contenu prêt à être affiché
 */
function FormaterCommentaireTicketSupportDrupal(array $node)
{
	$texte = strip_tags($node['body']['und'][0]['safe_value']);
	
	//Supprime les signatures de mails.
	if (strpos($texte, '--') !== false)
	{
		$texte = substr($texte, 0, strrpos($texte, '--'));
	}
	
	//Coupe le texte au-delà de 500 caractères
	if (strlen($texte) > 500)
	{
		$texte = substr($texte, 0, strpos($texte, ' ', 500)).'…';
	}
	
	$node['body']['und'][0]['safe_value'] = nl2br($texte);
	
	return $node;
}
