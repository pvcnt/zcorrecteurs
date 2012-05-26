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

namespace Zco\Bundle\CoreBundle\Swiftmailer;

/**
 * Transport Swiftmailer faisant passer les courriels par l'API de 
 * Mandrill <https://mandrillapp.com>.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class MandrillTransport implements \Swift_Transport
{
	private $eventDispatcher;
	private $apiKey;

	/**
	 * Constructeur.
	 *
	 * @param \Swift_Events_EventDispatcher $eventDispatcher
	 *        Gestionnaire d'événements de Swiftmailer
	 * @param string $apiKey Clé d'API Mandrill
	 * @param boolean $trackClicks
	 * @param boolean $trackOpens
	 * @param array $tags
	 */
	public function __construct(\Swift_Events_EventDispatcher $eventDispatcher, $apiKey, $trackClicks = false, $trackOpens = false, array $tags = array())
	{
		$this->eventDispatcher = $eventDispatcher;
		$this->apiKey          = $apiKey;
		$this->trackClicks     = (bool) $trackClicks;
		$this->trackOpens      = (bool) $trackOpens;
		$this->tags            = $tags;
	}
		
	/**
	 * {@inheritdoc}
	 */
	public function isStarted()
	{
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function start()
	{
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function stop()
	{
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function registerPlugin(\Swift_Events_EventListener $plugin)
	{
		$this->eventDispatcher->bindEventListener($plugin);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
	{
		if ($evt = $this->eventDispatcher->createSendEvent($this, $message))
		{
			$this->eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
			if ($evt->bubbleCancelled())
			{
				return 0;
			}
		}
		
		$to = array();
		foreach ($message->getTo() as $email => $name)
		{
			$to[] = array('email' => $email, 'name' => $name);
		}
		
		$headers = array();
		foreach ($message->getHeaders() as $header)
		{
			$headerName = strtolower($header->getFieldName());
			if (strpos($headerName, 'x-') === 0 || strpos($headerName, 'reply-to') === 0)
			{
				$headers[$header->getFieldName()] = $header->getFieldBody();
			}
		}
		
		$attachments = array();
		foreach ($message->getChildren() as $child)
		{
			if ($child instanceof \Swift_MimePart && $child->getContentType() === 'text/html')
			{
				$html = $child->getBody();
			}
			/*elseif ($child instanceof \Swift_Mime_Attachment
				&& (strpos($child->getContentType(), 'text/') === 0
					|| strpos($child->getContentType(), 'image/') === 0
					|| $child->getContentType() === 'application/pdf'))
			{
				$attachments[] = array(
					'type' => $child->getContentType(), 
					'name' => $child->getFilename(),
					'content' => $child->getContent(),
				);
			}*/
		}
		
		if (!isset($html))
		{
			$html = $message->getBody();
		}
		
		$fromEmail = array_keys($message->getFrom());
		$fromEmail = $fromEmail[0];
		$fromName = array_values($message->getFrom());
		$fromName = $fromName[0];
		
		$tags = $this->tags;
		$tags[] = 'zcorrecteurs-fr';
		
		$request = array(
			'key' => $this->apiKey,
			'message' => array(
				'html' => $html,
				'subject' => $message->getSubject(),
				'from_email' => $fromEmail,
				'from_name' => $fromName,
				'to' => $to,
				'auto_text' => true,
				'tags' => $tags,
				'track_clicks' => $this->trackClicks,
				'track_opens' => $this->trackOpens,
			)
		);

		$c = curl_init('https://mandrillapp.com/api/1.0/messages/send.json');
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($request));
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
		$result = curl_exec($c);
		curl_close($c);
		
		$retval = 0;
		if ($result === false)
		{
			$success = false;
		}
		else
		{
			$success = true;
			$result = json_decode($result, true);
			
			//Si le champ status est présent et indique une erreur, on envoie 
			//une exception pour signaler cela.
			if (isset($result['status']) && $result['status'] === 'error')
			{
				throw new MandrillException(sprintf(
					'Error while calling Mandrill API (%s). %s.', 
					$result['name'], $result['message']));
			}
			
			//On note les adresses qui ont été rejetées. Un envoi mis dans la file 
			//d'attente est ici considéré comme un succès.
			foreach ($result as $email)
			{
				if ($email['status'] === 'rejected')
				{
					$success = false;
					$failures[] = $email['email'];
				}
				else
				{
					++$retval;
				}
			}
		}
		
		if ($evt)
		{
			$evt->setResult($success ? \Swift_Events_SendEvent::RESULT_SUCCESS : \Swift_Events_SendEvent::RESULT_FAILED);
			$this->eventDispatcher->dispatchEvent($evt, 'sendPerformed');
		}
		
		return $retval;
	}
}
