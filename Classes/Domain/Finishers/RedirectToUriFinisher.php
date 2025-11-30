<?php

declare(strict_types=1);



namespace WapplerSystems\FormExtended\Domain\Finishers;

use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;

/**
 * This finisher redirects to another Controller.
 *
 * Scope: frontend
 */
class RedirectToUriFinisher extends AbstractFinisher
{
    /**
     * @var array
     */
    protected $defaultOptions = [
        'uri' => '',
        'statusCode' => 303,
    ];

    /**
     * Executes this finisher
     * @throws PropagateResponseException
     * @see AbstractFinisher::execute()
     */
    protected function executeInternal(): void
    {
        $uri = $this->parseOption('uri');
        $statusCode = (int)$this->parseOption('statusCode');

        $this->finisherContext->cancel();

        $this->redirectToUri($uri, $statusCode);
    }


    /**
     * Redirects the web request to another uri.
     *
     * NOTE: This method only supports web requests and will thrown an exception if used with other request types.
     *
     * @param string $uri A string representation of a URI
     * @param int $statusCode (optional) The HTTP status code for the redirect. Default is "303 See Other
     * @throws PropagateResponseException
     */
    protected function redirectToUri(string $uri, int $statusCode = 303): void
    {
        $uri = $this->addBaseUriIfNecessary($uri);
        $response = new RedirectResponse($uri, $statusCode);
        // End processing and dispatching by throwing a PropagateResponseException with our response.
        // @todo: Should be changed to *return* a response instead, but this requires the ContentObjectRender
        // @todo: to deal with responses instead of strings, if the form is used in a fluid template rendered by the
        // @todo: FluidTemplateContentObject and the extbase bootstrap isn't used.
        throw new PropagateResponseException($response, 1477070964);
    }

    /**
     * Adds the base uri if not already in place.
     *
     * @param string $uri The URI
     */
    protected function addBaseUriIfNecessary(string $uri): string
    {
        return GeneralUtility::locationHeaderUrl($uri);
    }
}
