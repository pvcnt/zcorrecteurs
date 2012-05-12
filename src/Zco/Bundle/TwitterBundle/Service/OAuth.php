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

namespace Zco\Bundle\TwitterBundle\Service;

/**
 * Gestion du processus d'identification auprès d'un service OAuth.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
*/
class OAuth
{
	private $endpoint;
	private $parameters;
	private $consumerKey;
	private $requestToken = array(null, null);
	private $accessToken = array(null, null);

	public function __construct($endpoint, array $consumerKey)
	{
		$this->endpoint     = rtrim($endpoint, '/');
		$this->consumerKey  = $consumerKey;
		$this->parameters   = array(
			'oauth_consumer_key' => $this->consumerKey[0],
			'oauth_version'      => '1.0',
		);
	}
	
	public function setTokens(array $token)
	{
		$this->requestToken = $token;
		$this->accessToken  = $token;
	}

	public function addApplication()
	{
		$verifier = $this->getRequestToken();
		if ($verifier)
		{
			$verifier = $this->allowed();
		}
		
		if (!$verifier)
		{
			return false;
		}
		else
		{
			return array_merge($this->accessToken, $this->getAccessToken($verifier));
		}
	}
	
	protected function send($method, $url, array $parameters = array(), array $nonSignedParameters = array())
	{
		$post = array_keys($parameters);
		$appendParams = true;
		if ($method === 'GET_noAppend')
		{
			$method = 'GET';
			$appendParams = false;
		}

		if($method === 'GET' && $appendParams)
		{
			$out = '';
			foreach ($parameters as $k => $v)
			{
				$out .= '&'.rawurlencode($k).'='.rawurlencode($v);
			}
			if ($out)
			{
				$out = substr($out, 1);
				$url .= '?'.$out;
			}
		}

		$parameters = array_merge($this->parameters, $parameters);
		$parameters['oauth_timestamp'] = time();
		$parameters['oauth_nonce'] = md5(uniqid());

		if (
			!isset($parameters['oauth_token'])
			&& $this->accessToken[0] !== null
		)
		{
			$parameters['oauth_token'] = $this->accessToken[0];
		}
		
		$this->generateSignature($method, $url, $parameters);

		$parameters = array_merge($nonSignedParameters, $parameters);
		$header = 'Authorization: OAuth ';
		foreach ($parameters as $k => $v)
		{
			$header .= rawurlencode($k).'='.rawurlencode($v).', ';
		}
		
		$header = substr($header, 0, -2);
		$req = curl_init($url);
		if (!$req)
		{
			return false;
		}

		if ($method === 'POST')
		{
			curl_setopt($req, CURLOPT_POST, 1);
			$postvars = '';
			foreach ($post as $v)
			{
				$postvars .= rawurlencode($v).'='
				            .rawurlencode($parameters[$v]).'&';
			}
			$postvars = substr($postvars, 0, -1);
			curl_setopt($req, CURLOPT_POSTFIELDS, $postvars);
		}
		
		curl_setopt($req, CURLOPT_HTTPHEADER, array($header));
		curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
		$retval = curl_exec($req);
		curl_close($req);
		
		return $retval;
	}

	protected function getTokens($texte)
	{
		$taille = strlen($texte);
		$tokens = array();

		$args = explode('&', $texte);
		foreach($args as $arg)
		{
			$cle = explode('=', $arg);
			if(!isset($cle[0], $cle[1]))
			{
				$tokens[] = implode(' ', $cle);
				continue;
			}
			$tokens[$cle[0]] = ($cle[1] === 'true' ?
				true : ($cle[1] === 'false' ? false : $cle[1]));
		}
		
		return $tokens;
	}

	private function getRequestToken()
	{
		if (isset($_SESSION['oauth_requestToken']))
		{
			$this->requestToken = $_SESSION['oauth_requestToken'];
			unset($_SESSION['oauth_requestToken']);
			
			return true;
		}

		$tok = $this->getTokens($this->send(
			'GET_noAppend',
			$this->endpoint.'/request_token',
			array('oauth_callback' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])
		));

		if (!isset($tok['oauth_token'], $tok['oauth_token_secret']))
		{
			return false;
		}

		$this->requestToken = array(
			$tok['oauth_token'],
			$tok['oauth_token_secret']
		);
		
		return true;
	}

	private function allowed()
	{
		if (
			isset($_GET['oauth_token'], $_GET['oauth_verifier']) 
			&& $_GET['oauth_token'] == $this->requestToken[0]
		)
		{
			return $_GET['oauth_verifier'];
		}
		
		$_SESSION['oauth_requestToken'] = $this->requestToken;
		header('Location: '.$this->endpoint.'/authorize'
		      .'?oauth_token='.$this->requestToken[0]);
		exit();
	}

	private function getAccessToken($verifier)
	{
		$url = $this->endpoint.'/access_token';
		$tok = $this->getTokens($this->send('POST', $url,  array(
			'oauth_token'    => $this->requestToken[0],
			'oauth_verifier' => $verifier
		)));
		if (!isset($tok['oauth_token'], $tok['oauth_token_secret']))
		{
			return false;
		}
		
		$this->accessToken = array(
			$tok['oauth_token'],
			$tok['oauth_token_secret']
		);
		
		return array($tok['user_id'], $tok['screen_name']);
	}

	private function generateSignature($method, $url, &$parameters)
	{
		$parameters['oauth_signature_method'] = 'HMAC-SHA1';
		ksort($parameters);
		$params = array();
		foreach ($parameters as $k => &$v)
		{
			$params[] = rawurlencode($k).'='.rawurlencode($v);
		}

		$url = substr($url, 0, ($p = strrpos($url, '?')) ? $p : strlen($url));

		$args = array(
			$method,
			rawurlencode($url),
			rawurlencode(implode('&', $params))
		);

		$sig = implode('&', $args);
		$cle = rawurlencode($this->consumerKey[1]).'&'
		      .rawurlencode($this->requestToken[1]);
		$sig = base64_encode(hash_hmac('sha1', $sig, $cle, true));
		$parameters['oauth_signature'] = $sig;
	}
}