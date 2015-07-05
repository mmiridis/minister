<?php

namespace AppBundle\Event;

use AppBundle\Entity\Contact;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class ContactEvent extends SymfonyEvent
{

    /** @var  Contact $contact */
    private $contact;
    /** @var  int $returnCode */
    private $returnCode;
    /** @var  string $errorMessage */
    private $errorMessage;

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function setReturnCode($returnCode)
    {
        $this->returnCode = $returnCode;
    }

    public function getReturnCode()
    {
        return $this->returnCode;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }
}