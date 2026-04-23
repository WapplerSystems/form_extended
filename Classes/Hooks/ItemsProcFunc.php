<?php

declare(strict_types=1);

namespace WapplerSystems\FormExtended\Hooks;

use TYPO3\CMS\Core\Site\Entity\Site;

/**
 */
class ItemsProcFunc
{


    /**
     *
     * @param array &$config configuration array
     */
    public function getSiteSenders(array &$config): void
    {

        /** @var Site $site */
        $site = $config['site'];
        $senders = $site->getAttribute('senders');
        if ($senders !== null) {
            foreach ($senders as $key => $sender) {
                $config['items'][] = [$sender['name'] . ' <' . $sender['email'] . '>', $sender['email']];
            }
        }

    }

}
