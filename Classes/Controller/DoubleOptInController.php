<?php
namespace WapplerSystems\FormExtended\Controller;

/*
 * DoubleOptInController
 */

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use WapplerSystems\FormExtended\Domain\Model\OptIn;
use WapplerSystems\FormExtended\Domain\Repository\OptInRepository;

class DoubleOptInController extends ActionController
{

    /**
     * optInRepository
     *
     * @var OptInRepository
     */
    protected $optInRepository = NULL;

    /**
     * signalSlotDispatcher
     *
     * @var Dispatcher
     */
    protected $signalSlotDispatcher = NULL;


    /**
     * @param OptInRepository $optInRepository
     */
    public function injectOptInRepository(OptInRepository $optInRepository) {
        $this->optInRepository = $optInRepository;
    }


    /**
     * @param Dispatcher $signalSlotDispatcher
     */
    public function injectSignalSlotDispatcher(Dispatcher $signalSlotDispatcher) {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
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

                $this->signalSlotDispatcher->dispatch(__CLASS__, 'afterOptInValidation', [$this,$optIn]);

                if (isset($this->settings['forward']) && (int)$this->settings['forward'] > 0) {
                    $url = $this->uriBuilder->reset()->setCreateAbsoluteUri(true)->setTargetPageUid($this->settings['forward'])->build();
                    $this->redirectToUri($url);
                }

                $this->view->assign('success', true);
                return $this->htmlResponse();
            }
        }

        $this->view->assign('notFound', true);
        return $this->htmlResponse();
    }
}
