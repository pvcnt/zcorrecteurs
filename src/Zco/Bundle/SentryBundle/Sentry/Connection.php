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

namespace Zco\Bundle\SentryBundle\Sentry;

/**
 * Ouvre une connexion vers notre instance de Sentry et envoie des données.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Connection
{
	protected $publicKey;
	protected $secretKey;
	
	/**
	 * Constructeur.
	 *
	 * @param string $publicKey Clé publique
	 * @param string $secretKey Clé privée
	 */
	public function __construct($publicKey, $secretKey)
	{
		$this->publicKey = $publicKey;
		$this->secretKey = $secretKey;
	}
	
	/**
	 * Envoie des données vers un serveur. Le protocole à utiliser est déduit 
	 * de l'URL.
	 *
	 * @param  string $url Point d'entrée du serveur
	 * @param  string $data Données à envoyer
	 * @return boolean
	 */
	public function send($url, $data)
	{
		$message   = base64_encode(gzcompress(json_encode($data)));
		$timestamp = microtime(true);
		$signature = $this->getSignature($message, $timestamp);
		$headers = array(
			'X-Sentry-Auth' => $this->getAuthHeader($signature, $timestamp),
			'Content-Type'  => 'application/octet-stream'
		);

		$parts = parse_url($url);
		$parts['netloc'] = $parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : null);

		if ('udp' === $parts['scheme'])
		{
			return $this->sendViaUdp($parts['netloc'], $message, $headers['X-Sentry-Auth']);
		}

		return $this->sendViaHttp($url, $message, $headers);
	}

	/**
	 * Envoie des données vers un serveur en UDP.
	 *
	 * @param  string $url Adresse
	 * @param  string $data Données à envoyer
	 * @param  string $headers En-têtes
	 * @return boolean
	 */
	protected function sendViaUdp($url, $data, $headers)
	{
		list($host, $port) = explode(':', $url);
		$raw_data = $headers."\n\n".$data;

		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_sendto($sock, $raw_data, strlen($raw_data), 0, $host, $port);
		socket_close($sock);

		return true;
	}

	/**
	 * Send the message over http to the sentry url given
	 */
	/**
	 * Envoie des données vers un serveur en HTTP.
	 *
	 * @param  string $url Adresse
	 * @param  string $data Données à envoyer
	 * @param  array $headers En-têtes
	 * @return boolean
	 */
	protected function sendViaHttp($url, $data, array $headers = array())
	{
		$newHeaders = array();
		foreach($headers as $key => $value)
		{
			array_push($newHeaders, $key .': '. $value);
		}
		$parts = parse_url($url);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $newHeaders);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_VERBOSE, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$retval = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		if ($code != 200)
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Crée une signature pour les données envoyées.
	 *
	 * @param  string $message Message allant être envoyé
	 * @param  integer $timestamp Horodatage de l'envoi
	 * @return string
	 */
	protected function getSignature($message, $timestamp)
	{
		return hash_hmac('sha1', sprintf('%F', $timestamp) .' '. $message, $this->secretKey);
	}
	
	/**
	 * Crée un en-tête servant à l'authentification.
	 *
	 * @param  string $signature Signature
	 * @param  integer $timestamp Horodatage de l'envoi
	 * @return string
	 */
	protected function getAuthHeader($signature, $timestamp, $client)
	{
		$header = array(
			sprintf("sentry_timestamp=%F", $timestamp),
			"sentry_signature={$signature}",
			"sentry_client=raven-php/0.1",
			"sentry_version=2.0",
			"sentry_key={$this->publicKey}",
		);

		return sprintf('Sentry %s', implode(', ', $header));
	}
}