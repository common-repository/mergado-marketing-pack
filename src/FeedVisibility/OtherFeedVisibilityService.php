<?php

namespace Mergado\FeedVisibility;

use Mergado\Manager\DatabaseManager;
use Mergado\Traits\SingletonTrait;

class OtherFeedVisibilityService extends AbstractFeedVisibilityService
{
    use SingletonTrait;
    private const FEED_TYPE = 'other';

    protected function __construct()
    {
        parent::__construct(self::FEED_TYPE);
    }

    /*******************************************************************************************************************
     * SAVE FIELDS
     ******************************************************************************************************************/

    /**
     * @param $post
     */
    public static function saveFields($post): void
    {
        $checkboxes = [];

        foreach(self::getInstance()->baseVisibilityOptions as $item) {
            $checkboxes[] = $item['databaseName'];
        }

        foreach(self::getInstance()->catalogVisibilityOptions as $item) {
            $checkboxes[] = $item['databaseName'];
        }

        DatabaseManager::saveOptions($post,
            $checkboxes
        );
    }
}
