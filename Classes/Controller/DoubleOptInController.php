<?php

namespace WapplerSystems\FormExtended\Controller;

/*
 * DoubleOptInController
 */

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use WapplerSystems\FormExtended\Domain\Model\OptIn;
use WapplerSystems\FormExtended\Domain\Repository\OptInRepository;
use WapplerSystems\FormExtended\Event\AfterOptInValidationEvent;

class DoubleOptInController extends ActionController
{


    public function __construct(readonly
                                OptInRepository $optInRepository,
                                EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * action validation
     *
     * @param string $hash
     * @return void
     */
    public function validationAction($hash = ''): ResponseInterface
    {
        if ($hash !== '') {
            /** @var OptIn $optIn */
            $optIn = $this->optInRepository->findOneByValidationHash($hash);

            if ($optIn) {

                if ($optIn->getIsValidated()) {
                    $this->view->assign('alreadyConfirmed', true);
                    return $this->htmlResponse();
                }

                $optIn->setIsValidated(TRUE);
                $optIn->setValidationDate(new \DateTime);
                $this->optInRepository->update($optIn);

                $this->eventDispatcher->dispatch(
                    new AfterOptInValidationEvent($optIn)
                );

                if (isset($this->settings['forward']) && (int)$this->settings['forward'] > 0) {
                    $url = $this->uriBuilder->reset()->setCreateAbsoluteUri(true)->setTargetPageUid($this->settings['forward'])->build();
                    return $this->redirectToUri($url);
                }

                $this->view->assign('success', true);
                return $this->htmlResponse();
            }
        }

        $this->view->assign('notFound', true);

        return $this->htmlResponse();
    }
}
