<?php

namespace WapplerSystems\FormExtended\Event;

use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use TYPO3\CMS\Form\Domain\Finishers\FinisherContext;

final class MailBeforeSendingEvent
{

    protected FluidEmail $mail;

    protected FinisherContext $finisherContext;

    protected AbstractFinisher $finisher;

    public function __construct(FluidEmail $mail, FinisherContext $finisherContext, AbstractFinisher $finisher)
    {
        $this->mail = $mail;
        $this->finisherContext = $finisherContext;
        $this->finisher = $finisher;
    }

    public function getMail() : FluidEmail
    {
        return $this->mail;
    }

    public function setMail(FluidEmail $mail) : MailBeforeSendingEvent
    {
        $this->mail = $mail;
        return $this;
    }

    public function getFinisherContext() : FinisherContext
    {
        return $this->finisherContext;
    }

    public function getFinisher(): AbstractFinisher
    {
        return $this->finisher;
    }

}
