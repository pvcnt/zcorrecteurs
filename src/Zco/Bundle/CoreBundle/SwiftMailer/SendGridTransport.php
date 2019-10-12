<?php

/**
 * Copyright (c) 2017 Sam Anthony
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Zco\Bundle\CoreBundle\SwiftMailer;

use Psr\Log\LoggerInterface;
use SendGrid;
use SendGrid\Mail;

class SendGridTransport implements \Swift_Transport
{
    /**
     * @see https://sendgrid.com/docs/API_Reference/Web_API_v3/Mail/errors.html
     * 2xx responses indicate a successful request. The request that you made is valid and successful.
     */
    const STATUS_SUCCESSFUL_MAX_RANGE = 299;

    /**
     * @see https://sendgrid.com/docs/API_Reference/Web_API_v3/Mail/errors.html
     * ACCEPTED : Your message is both valid, and queued to be delivered.
     */
    const STATUS_ACCEPTED = 202;

    /**
     * @see https://sendgrid.com/docs/API_Reference/Web_API_v3/Mail/errors.html
     * OK : Your message is valid, but it is not queued to be delivered. Sandbox mode only.
     */
    const STATUS_OK_SUCCESSFUL_MIN_RANGE = 200;

    /**
     * Sendgrid api key.
     *
     * @var string
     */
    private $apiKey;

    /**
     * Sendgrid mails categories.
     *
     * @var array
     */
    private $categories;

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Http client options.
     *
     * @var array
     */
    private $httpClientOptions;

    /** Connection status */
    protected $started = false;

    /**
     * @var \Swift_Events_EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Some header keys are reserved. You may not include any of the following reserved keys
     *
     * @see https://sendgrid.com/docs/API_Reference/Web_API_v3/Mail/errors.html#-Headers-Errors
     */
    const RESERVED_KEYWORDS = [
        'X-SG-ID',
        'X-SG-EID',
        'RECEIVED',
        'DKIM-SIGNATURE',
        'CONTENT-TYPE',
        'CONTENT-TRANSFER-ENCODING',
        'TO',
        'FROM',
        'SUBJECT',
        'REPLY-TO',
        'CC',
        'BCC',
    ];

    public function __construct(\Swift_Events_EventDispatcher $eventDispatcher, string $apiKey, array $categories)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->apiKey = $apiKey;
        $this->categories = $categories;
        $this->httpClientOptions = [];
    }

    /**
     * {@inheritDoc}
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * {@inheritDoc}
     */
    public function start()
    {
        if (!$this->started) {
            if ($evt = $this->eventDispatcher->createTransportChangeEvent($this)) {
                $this->eventDispatcher->dispatchEvent($evt, 'beforeTransportStarted');
                if ($evt->bubbleCancelled()) {
                    return;
                }
            }

            //noop (transport does not need initialization)

            if ($evt) {
                $this->eventDispatcher->dispatchEvent($evt, 'transportStarted');
            }

            $this->started = true;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function stop()
    {
        if ($this->started) {
            if ($evt = $this->eventDispatcher->createTransportChangeEvent($this)) {
                $this->eventDispatcher->dispatchEvent($evt, 'beforeTransportStopped');
                if ($evt->bubbleCancelled()) {
                    return;
                }
            }

            //noop (transport does not need to termintated)
            if ($evt) {
                $this->eventDispatcher->dispatchEvent($evt, 'transportStopped');
            }
        }

        $this->started = false;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     *
     * WARNING: $failedRecipients and return value are faked.
     */
    public function send(\Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        if ($evt = $this->eventDispatcher->createSendEvent($this, $message)) {
            $this->eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        // prepare fake data.
        $sent = 0;
        $prepareFailedRecipients = [];

        //Get the first from email (SendGrid PHP library only seems to support one)
        $fromArray = $message->getFrom();
        $fromName = reset($fromArray);
        $fromEmail = key($fromArray);

        $mail = new Mail\Mail(); //Intentionally not using constructor arguments as they are tedious to work with

        // categories can be useful if you use them like tags to, for example, distinguish different applications.
        foreach ($this->categories as $category) {
            $mail->addCategory($category);
        }

        $mail->setFrom(new Mail\From($fromEmail, $fromName));
        $mail->setSubject($message->getSubject());

        // extract content type from body to prevent multi-part content-type error
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $contentType = $finfo->buffer($message->getBody());
        $mail->addContent(new SendGrid\Mail\Content($contentType, $message->getBody()));

        // personalization
        if (!empty($mail->getPersonalizations())) {
            $personalization = $mail->getPersonalizations()[0];
        } else {
            $personalization = new Mail\Personalization();
            $mail->addPersonalization($personalization);
        }

        // process TO
        if ($toArr = $message->getTo()) {
            foreach ($toArr as $email => $name) {
                $personalization->addTo(new Mail\To($email, $name));
                ++$sent;
                $prepareFailedRecipients[] = $email;
            }
        }

        // process CC
        if ($ccArr = $message->getCc()) {
            foreach ($ccArr as $email => $name) {
                $personalization->addCc(new Mail\Cc($email, $name));
                ++$sent;
                $prepareFailedRecipients[] = $email;
            }
        }

        // process BCC
        if ($bccArr = $message->getBcc()) {
            foreach ($bccArr as $email => $name) {
                $personalization->addBcc(new Mail\Bcc($email, $name));
                ++$sent;
                $prepareFailedRecipients[] = $email;
            }
        }

        // process attachment
        if ($attachments = $message->getChildren()) {
            foreach ($attachments as $attachment) {
                if ($attachment instanceof \Swift_Mime_Attachment) {
                    $sAttachment = new Mail\Attachment();
                    $sAttachment->setContent(base64_encode($attachment->getBody()));
                    $sAttachment->setType($attachment->getContentType());
                    $sAttachment->setFilename($attachment->getFilename());
                    $sAttachment->setDisposition($attachment->getDisposition());
                    $sAttachment->setContentId($attachment->getId());
                    $mail->addAttachment($sAttachment);
                } elseif (in_array($attachment->getContentType(), ['text/plain', 'text/html'])) {
                    // add part if any is defined, to avoid error please set body as text and part as html
                    $mail->addContent(new Mail\Content($attachment->getContentType(), $attachment->getBody()));
                }
            }
        }

        // add headers
        if ($headers = $message->getHeaders()->getAll()) {
            foreach ($headers as $name => $value) {
                if (!in_array(strtoupper($name), self::RESERVED_KEYWORDS)) {
                    $mail->addHeader($name, $value);
                }
            }
        }

        $sendGrid = new SendGrid($this->apiKey, $this->httpClientOptions);

        $response = $sendGrid->client->mail()->send()->post($mail);

        // only 2xx status are ok
        if ($response->statusCode() < self::STATUS_OK_SUCCESSFUL_MIN_RANGE ||
            self::STATUS_SUCCESSFUL_MAX_RANGE < $response->statusCode()) {
            // to force big boom error uncomment this line
            //throw new \\Swift_TransportException("Error when sending message. Return status :".$response->statusCode());
            if (null !== $this->logger) {
                $this->logger->error($response->statusCode() . ': ' . $response->body());
            }

            // copy failed recipients
            foreach ($prepareFailedRecipients as $recipient) {
                $failedRecipients[] = $recipient;
            }
            $sent = 0;
        }

        if ($evt) {
            if ($sent == count($toArr ?? []) + count($ccArr ?? []) + count($bccArr ?? [])) {
                $evt->setResult(\Swift_Events_SendEvent::RESULT_SUCCESS);
            } elseif ($sent > 0) {
                $evt->setResult(\Swift_Events_SendEvent::RESULT_TENTATIVE);
            } else {
                $evt->setResult(\Swift_Events_SendEvent::RESULT_FAILED);
            }
            $evt->setFailedRecipients($failedRecipients);
            $this->eventDispatcher->dispatchEvent($evt, 'sendPerformed');
        }

        return $sent;
    }

    /**
     * {@inheritdoc}
     */
    public function registerPlugin(\Swift_Events_EventListener $plugin)
    {
        $this->eventDispatcher->bindEventListener($plugin);
    }

    /**
     * @param array $options
     */
    public function setHttpClientOptions(array $options)
    {
        $this->httpClientOptions = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function ping()
    {
        return true;
    }
}