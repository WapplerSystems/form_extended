<?php

declare(strict_types=1);


namespace WapplerSystems\FormExtended\EventListener;

use TYPO3\CMS\Form\Domain\Model\FormElements\FileUpload;
use TYPO3\CMS\Form\Event\BeforeRenderableIsAddedToFormEvent;

/**
 * Works around a TYPO3 v14.3 core ordering issue: AbstractSection::createElement()
 * calls FileUpload::initializeFormElement() (which resolves the upload target folder
 * from properties.saveToFileMount) right after applying only the prototype's type
 * defaults - the form-definition-specific properties from the *.form.yaml (set via
 * setOptions() in ArrayFormFactory::addNestedRenderable()) are applied only afterwards.
 * As a result, a saveToFileMount override on an individual FileUpload element in a
 * .form.yaml is silently ignored; every upload always lands in the prototype default
 * '1:/user_upload/', never in the configured per-form subfolder.
 *
 * BeforeRenderableIsAddedToFormEvent fires right after the form-definition-specific
 * properties have been applied to the element, so simply re-running
 * initializeFormElement() here re-resolves the upload folder with the correct value.
 *
 * Scope: frontend
 */
class FileUploadSaveToFileMountListener
{
    public function __invoke(BeforeRenderableIsAddedToFormEvent $event): void
    {
        $renderable = $event->renderable;
        if ($renderable instanceof FileUpload) {
            $renderable->initializeFormElement();
        }
    }
}
