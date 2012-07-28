<?php

/**
 * Copyright (c) 2012 Sentry Team and individual contributors. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 *	1. Redistributions of source code must retain the above copyright notice, 
 *	   this list of conditions and the following disclaimer.
 *	2. Redistributions in binary form must reproduce the above copyright notice, 
 *	   this list of conditions and the following disclaimer in the documentation 
 *	   and/or other materials provided with the distribution.
 *	3. Neither the name of the Raven nor the names of its contributors may be 
 *	   used to endorse or promote products derived from this software without 
 *	   specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Zco\Bundle\SentryBundle\Sentry;

use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Client PHP pour Sentry.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Client
{
	protected $server;
	protected $secretKey;
	protected $publicKey;
	protected $project = 1;
	protected $autoLogStacks = false;
	protected $name;
	
	protected $resque;
	protected $connection;
	protected $container;

	/**
	 * Constructeur.
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
		$this->parseDSN($container->getParameter('zco_sentry.client.dsn'));
		$this->autoLogStacks = $container->getParameter('zco_sentry.client.auto_log_stacks');
		$this->name          = $container->getParameter('zco_sentry.client.server_name') ?: gethostname();
		
		$resque = $container->getParameter('zco_sentry.client.resque');
		if ($resque)
		{
			$this->resque = $resque;
		}
		else
		{
			$this->connection = new Connection($this->publicKey, $this->secretKey);
		}
	}

	/**
	 * Capture un enregistrement Monolog.
	 *
	 * @param  array $record
	 * @return string L'identifiant de l'événement capturé
	 */
	public function captureRecord(array $record)
	{
		static $levels = array(
			Logger::DEBUG => 'debug',
			Logger::INFO => 'info',
			//Logger::NOTICE => 'warning',
			Logger::WARNING => 'warning',
			Logger::ERROR => 'error',
			Logger::CRITICAL => 'fatal',
			Logger::ALERT => 'fatal',
			//Logger::EMERGENCY => 'fatal',
		);
			
		$data = array(
			'message' => $record['message'],
			'level'   => $levels[$record['level']],
			'tags'	  => array(
				'channel'     => $record['channel'], 
				'environment' => $this->container->getParameter('kernel.environment'),
			),
			'timestamp' => $record['datetime']->format(\DateTime::ISO8601),
			'sentry.interfaces.Message' => array(
				'message' => $record['message'],
				'params'  => array(),
			),
		);
		
		if (isset($record['extra']['user.is_authenticated']) && isset($record['extra']['user.id']))
		{
			foreach ($record['extra'] as $key => $value)
			{
				if (0 === strpos($key, 'user.'))
				{
					$data['sentry.interfaces.User'][substr($key, 5)] = $value;
					unset($record['extra'][$key]);
				}
			}
		}

		if (!empty($record['extra']))
		{
			$data['extra'] = $record['extra'];
		}

		$tags = $this->container->getParameter('zco_sentry.client.tags');
		if (!empty($tags))
		{
			$data['tags'] = array_merge($tags, $data['tags']);
		}
		
		return $this->capture($data, array());
	}

	/**
	 * Capture une exception.
	 *
	 * @param Exception $exception
	 * @param string|null $culprit
	 * @return string L'identifiant de l'événement capturé
	 */
	public function captureException(Exception $exception, $culprit = null)
	{
		$exc_message = $exception->getMessage();
		if (empty($exc_message)) {
			$exc_message = '<unknown exception>';
		}

		$data = array(
			'message' => $exc_message,
			'level' => 'error',
			'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
		);

		$data['sentry.interfaces.Exception'] = array(
				'value' => $exc_message,
				'type' => $exception->getCode(),
				'module' => $exception->getFile() .':'. $exception->getLine(),
		);

		if ($culprit){
			$data["culprit"] = $culprit;
		}

		/**'sentry.interfaces.Exception'
		 * Exception::getTrace doesn't store the point at where the exception
		 * was thrown, so we have to stuff it in ourselves. Ugh.
		 */
		$trace = $exception->getTrace();
		$frame_where_exception_thrown = array(
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
		);
		array_unshift($trace, $frame_where_exception_thrown);
		return $this->capture($data, $trace);
	}

	/**
	 * Complète et transmet un jeu de données à sentry.
	 *
	 * @param  array $data Données à transmettre
	 * @param  boolean|array $stack Trace d'exécution
	 * @return string L'identifiant de l'événement capturé
	 */
	protected function capture(array $data, $stack)
	{
		$eventId = $this->uuid4();
		$request = $this->container->get('request');
		
		$data = array_merge($data, array(
			'server_name' => $this->name,
			'event_id'    => $eventId,
			'project'     => $this->project,
			'site'        => $request->getHost(),
			'sentry.interfaces.Http' => array(
				'method'       => $request->getMethod(),
				'url'          => $request->getUriForPath($request->getRequestUri()),
				'query_string' => $request->getQueryString(),
				'data'         => $request->request->all(),
				'cookies'      => $request->cookies->all(),
				'headers'      => $request->headers->all(),
				'env'          => $request->server->all(),
			)
		));

		if ((!$stack && $this->autoLogStacks) || true === $stack)
		{
			$stack = debug_backtrace();
			array_shift($stack);
		}

		if (!empty($stack))
		{
			/**
			 * PHP's way of storing backstacks seems bass-ackwards to me
			 * 'function' is not the function you're in; it's any function being
			 * called, so we have to shift 'function' down by 1. Ugh.
			 */
			for ($i = 0; $i < count($stack) - 1; $i++) {
				$stack[$i]['function'] = $stack[$i + 1]['function'];
			}
			$stack[count($stack) - 1]['function'] = null;

			if ($stack && !isset($data['sentry.interfaces.Stacktrace'])) {
				$data['sentry.interfaces.Stacktrace'] = array(
					'frames' => Stacktrace::getStacktrace($stack)
				);
			}
		}
		$data = $this->removeInvalidUtf8($data);
		
		if ($this->resque)
		{
			\Resque::enqueue(
				$this->resque, 
				'Zco\Bundle\SentryBundle\Resque\SentryJob', 
				array(
					'data'       => serialize($data), 
					'server'     => $this->server,
					'public_key' => $this->publicKey,
					'secret_key' => $this->secretKey, 
				)
			);
		}
		else
		{
			$this->connection->send($this->server, $data);
		}

		return $eventId;
	}
	
	/**
	 * Parse un DSN compatible et stocke le résultat dans les attributs du client.
	 *
	 * @param string $dsn DSN
	 */
	protected function parseDSN($dsn)
	{
		$url = parse_url($dsn);
		$scheme = (isset($url['scheme']) ? $url['scheme'] : '');
		if (!in_array($scheme, array('http', 'https', 'udp')))
		{
			throw new \InvalidArgumentException('Unsupported Sentry DSN scheme: ' . $scheme);
		}
		$netloc = (isset($url['host']) ? $url['host'] : null);
		$netloc.= (isset($url['port']) ? ':'.$url['port'] : null);
		$rawpath = (isset($url['path']) ? $url['path'] : null);
		if ($rawpath)
		{
			$pos = strrpos($rawpath, '/', 1);
			if ($pos !== false)
			{
				$path = substr($rawpath, 0, $pos);
				$project = substr($rawpath, $pos + 1);
			}
			else
			{
				$path = '';
				$project = substr($rawpath, 1);
			}
		}
		else
		{
			$project = null;
			$path = '';
		}
		$username = (isset($url['user']) ? $url['user'] : null);
		$password = (isset($url['pass']) ? $url['pass'] : null);
		if (empty($netloc) || empty($project) || empty($username) || empty($password))
		{
			throw new \InvalidArgumentException('Invalid Sentry DSN: ' . $dsn);
		}
		
		$this->server    = sprintf('%s://%s%s/api/store/', $scheme, $netloc, $path);
		$this->project   = $project;
		$this->publicKey = $username;
		$this->secretKey = $password;
	}

	/**
	 * Génère un identifiant uuid4 unique.
	 */
	protected function uuid4()
	{
		$uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
		
		return str_replace('-', '', $uuid);
	}

	/**
	 * Retire d'éventuelles données incompatibles pour l'UTF8 d'un jeu de données.
	 *
	 * @param  array $data Données
	 * @return array Données nettoyées
	 */
	protected function removeInvalidUtf8($data)
	{
		foreach ($data as $key => $value)
		{
			if (is_string($key))
			{
				$key = mb_convert_encoding($key, 'UTF-8', 'UTF-8');
			}
			if (is_string($value))
			{
				$value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
			}
			if (is_array($value))
			{
				$value = $this->removeInvalidUtf8($value);
			}
			$data[$key] = $value;
		}

		return $data;
	}
}
