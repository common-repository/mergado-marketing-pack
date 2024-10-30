<?php declare(strict_types=1);

namespace Mergado\Utils;

class MultisiteUtils
{
    /**
     * Return current blog ID
     */
    public static function getCurrentBlogId() : int
    {
        if (is_multisite()) {
            $currentBlogId = get_current_blog_id();
        } else {
            $currentBlogId = 0;
        }

        return $currentBlogId;
    }
}
