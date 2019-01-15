<?php

/*
 * (l) 2011 Fabien Potencier
 *
 */

/**
 * Stores all sent emails for further usage.
 *
 */
class Swift_Plugins_MessageLogger implements Swift_Events_SendListener
{
    /**
     * @var Swift_Mime_Message[]
     */
    private $messages;

    public function __construct()
    {
        $this->messages = array();
    }

    /**
     * Get the message list.
     *
     * @return Swift_Mime_Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get the message count.
     *
     * @return int count
     */
    public function countMessages()
    {
        return count($this->messages);
    }

    /**
     * Empty the message list.
     */
    public function clear()
    {
        $this->messages = array();
    }

    /**
     * Invoked immediately before the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $this->messages[] = clone $evt->getMessage();
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
    }
}
