<?php

/**
 * NOTICE OF LICENSE.
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    www.mergado.cz
 * @copyright 2016 Mergado technologies, s. r. o.
 * @license   LICENSE.txt
 */

namespace Mergado\Service;

use DateTime;
use Exception;
use Mergado;
use Mergado\Helper\LanguageHelper;
use Mergado\Manager\TokenManager;

class NewsService
{
    public const TABLE_NEWS_NAME = 'mergado_news';

    public const DATE_FORMAT = 'Y-m-d H:i:s';
    public const DATE_COMPARE_FORMAT = 'Y-m-d';
    public const DATE_OUTPUT_FORMAT = 'd.m.Y';

    // Languages
    public const LANG_CS = 'cs';
    public const LANG_SK = 'sk';
    public const LANG_EN = 'en';
    public const LANG_PL = 'pl';
    public const LANG_HU = 'hu';

    public const LANG_AVAILABLE = array(
        self::LANG_EN,
        self::LANG_CS,
        self::LANG_SK,
        self::LANG_PL,
        self::LANG_HU,
    );

    /*******************************************************************************************************************
     * GET
     *******************************************************************************************************************/

    /**
     * Return news from DB by lang and limit (ifset)
     *
     * @param $lang
     * @param null $limit
     * @return array|null
     */
    public static function getNews($lang, $limit = null): ?array
    {
        global $wpdb;

        $query = self::getNewsBase($lang, $limit);

        return $wpdb->get_results($query, OBJECT);
    }

    /**
     * Return news with formated date
     *
     * @param $lang
     * @param null $limit
     * @return array|object|\stdClass[]|null
     */
    public static function getNewsWithFormatedDate($lang, $limit = null)
    {
        try {
            global $wpdb;

            $query = self::getNewsBase($lang, $limit);
            $return = $wpdb->get_results($query, OBJECT);

            foreach ($return as $item => $val) {
                $date = new DateTime($return[$item]->pubDate);
                $formatted = $date->format('d.m.Y H:m:s');

                $return[$item]->pubDate = $formatted;
            }

            return $return;
        } catch (Exception $e) {
            $logger = LogService::getInstance();
            $logger->error('Error during getNewsWithFormatedDate - ' . $e->getMessage(), 'news');

            return [];
        }
    }


    /**
     * Base query for returning news
     *
     * @param $lang
     * @param null $limit
     * @return string
     */
    private static function getNewsBase($lang, $limit = null): string
    {
        global $wpdb;

        $lang = self::getMergadoNewsLanguage($lang);

        $query = 'SELECT * FROM ';
        $query .= $wpdb->prefix . self::TABLE_NEWS_NAME;
        $query .= ' WHERE `language`="' . $lang . '"';
        $query .= ' ORDER BY `id` DESC';

        if ($limit) {
            $query .= ' LIMIT ' . $limit;
        }

        return $query;
    }


    /**
     * Return shown/new news from DB
     * @param $shown
     * @param $lang
     * @param null $category
     * @param null $limit
     * @param bool $excludeTop
     * @param bool $order
     * @return array|null
     */
    public static function getNewsByStatusAndLanguageAndCategory($shown, $lang, $category = null, $limit = null, bool $excludeTop = false, $order = false): ?array
    {
        global $wpdb;

        $lang = self::getMergadoNewsLanguage($lang);

        $query = 'SELECT * FROM ';
        $query .= $wpdb->prefix . self::TABLE_NEWS_NAME;
        $query .= ' WHERE `language`="' . $lang . '"';


        if ($shown) {
            $query .= ' AND `shown`="' . 1 . '"';
        } else {
            $query .= ' AND `shown`="' . 0 . '"';
        }

        if (($category || $category != '') && $category !== null) {
            $query .= ' AND `category`="' . $category . '"';
        }

        if ($excludeTop) {
            $query .= ' AND `category`!="top"';
        }

        if ($order) {
            $query .= ' ORDER BY `pubDate` ' . $order;
        } else {
            $query .= ' ORDER BY `pubDate`';
        }

        if ($limit) {
            $query .= ' LIMIT ' . $limit;
        }

        return $wpdb->get_results($query, OBJECT);
    }

    /**
     * @param $lang
     * @return string|string
     */
    public static function getMergadoNewsLanguage($lang): string
    {
        $lang = substr($lang, 0, 2);

        // Set default English news if language not available
        if (!in_array($lang, self::LANG_AVAILABLE)) {
            $lang = self::LANG_EN;
        }

        return $lang;
    }

    /**
     * @throws Exception
     */
    public static function getFormattedDate($date): string
    {
        $date = new DateTime($date);
        $date = $date->format(self::DATE_OUTPUT_FORMAT);
        return $date;
    }

    /*******************************************************************************************************************
     * SET
     *******************************************************************************************************************/

    /**
     * Save article to DB
     */
    public static function saveArticle(array $item, DateTime $date, $lang): void
    {
        global $wpdb;

        $lang = self::getMergadoNewsLanguage($lang);

        $data = [
            'title' => (string)$item['title'],
            // Preg replace not worked .. others solutions either .. so .. sorry code ..
            'description' => (string)str_replace(']]>', '', str_replace('<![CDATA[', '', $item['description'])),
            'pubDate' => $date->format(self::DATE_FORMAT),
            'category' => (string)$item['category'],
            'link' => (string)str_replace(']]>', '', str_replace('<![CDATA[', '', $item['link'])),
            'language' => $lang,
            'shown' => 0];

        $wpdb->insert($wpdb->prefix . self::TABLE_NEWS_NAME, $data);
    }

    /**
     * Set Article shown by user
     */
    public static function setArticlesShown(array $ids = null, $all = false): void
    {
        global $wpdb;

        if ($all) {
            $wpdb->update($wpdb->prefix . self::TABLE_NEWS_NAME, ['shown' => 1], ['shown' => 0]);
        } elseif ($ids) {
            foreach ($ids as $id) {
                $wpdb->update($wpdb->prefix . self::TABLE_NEWS_NAME, ['shown' => 1], ['id' => $id]);
            }
        }
    }

    /**
     * @throws Exception
     */
    public static function getAdminNews(): void
    {
        global $pagenow;
        if ($pagenow !== 'index.php') {
            return;
        }

        $unreadedNews = NewsService::getNewsByStatusAndLanguageAndCategory(0, LanguageHelper::getLocale(), '', 2, false, 'DESC');
        $token = TokenManager::getToken();

        if (NewsBannerService::shouldBeVisible()) {
            if ($unreadedNews && $unreadedNews != []) {
                echo '<div class="wrap">';
                echo '<div class="mergado-updated-notice news">';
                echo '<div class="mmp-news__holder">';
                foreach ($unreadedNews as $item) {
                    echo '<a href="' . admin_url('admin.php?page=mergado-news') . '" class="mergado-link mmp-news__item">';
                    echo '<p class="mmp-news__title">' . $item->title . '</p>';
                    echo '<p><span class="mmp-badge mmp-badge--' . $item->category . '">' . $item->category . '</span> <span class="mmp-news__date">' . NewsService::getFormattedDate($item->pubDate) . '</span></p></a>';
                }
                echo '</div>';
                echo '<span data-cookie="mmp-cookie-news" data-token="' . $token . '" class="mmp-cross mmp-close-cross">🞩</span>';
                echo '</div>';
                echo '</div>';
            }
        }
    }
}
