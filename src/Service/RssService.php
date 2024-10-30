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
use Mergado\Traits\SingletonTrait;
use ParseError;
use SimpleXMLElement;

include_once WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';

class RssService
{
    use SingletonTrait;

    public const FEED_BASE_URL = 'https://pack.mergado.cz/custom-platform/woocommerce/';

    public const FEED_LANGUAGES = ['en', 'cs', 'sk', 'pl', 'hu'];

    public const LAST_RSS_FEED_UPDATE = 'mergado_last_rss_feed_download';
    public const RSS_FEED_LOCK = 'unfinished_rss_downloads';

    protected $logger;

    public function __construct()
    {
        //Don't add $this->logger = wc_get_logger() .. Jetpack will shout on you
    }

    public function update(): void
    {
        $now = new DateTime();

        try {
            $lastDownload = $this->getLastDownload();

            if ($lastDownload) {
                $lastDownloadDateTime = new DateTime($lastDownload);

                // If lock
                if ($this->getDownloadLock() === 0) {
                    $dateFormatted = $this->getTimeWithModify($lastDownloadDateTime, '+60 minutes');
                } else {
                    $minutes = 120 * $this->getDownloadLock();
                    $dateFormatted = $this->getTimeWithModify($lastDownloadDateTime, '+' . $minutes . ' minutes');
                }

                if ($dateFormatted <= $now) {
                    foreach (self::FEED_LANGUAGES as $item_lang) {
                        $this->saveFeed($item_lang);
                    }

                    // Set lock on null and download time to now
                    $this->nullDownloadLock();
                    $this->setLastDownload($now->format(NewsService::DATE_FORMAT));
                }
            } else {
                foreach (self::FEED_LANGUAGES as $item_lang) {
                    $this->saveFeed($item_lang);
                }

                // Set lock on null and download time to now
                $this->nullDownloadLock();
                $this->setLastDownload($now->format(NewsService::DATE_FORMAT));
            }
        } catch (ParseError $e) {
            LogService::getInstance()->warning('Error parsing RSS feed - ' . $e->getMessage(), 'news');
            $this->increaseDownloadLock();
            $this->setLastDownload($now->format(NewsService::DATE_FORMAT));
        } catch (Exception $e) {
            LogService::getInstance()->warning('Error getting RSS feed - ' . $e->getMessage(), 'news');
            $this->increaseDownloadLock();
            $this->setLastDownload($now->format(NewsService::DATE_FORMAT));
        }
    }

    /**
     * Save new RSS feed articles to database
     * @throws Exception
     * @throws ParseError
     */
    private function saveFeed($lang): void
    {
        $dbQuery = NewsService::getNews($lang);
        $rssFeed = $this->downloadFeed($lang);

        foreach ($rssFeed as $item) {

            // Transform keys to lowercase
            $itemAr = (array)$item;
            $item = array_change_key_case($itemAr, CASE_LOWER);

            $itemDatetime = new DateTime((string)$item['pubdate']);
            $save = true;

            if (count($dbQuery) > 0) {
                foreach ($dbQuery as $dbItem) {

                    // Fix different APIs ( one with time and second only date ) => Compare only based on date and title
                    $dbTime = new DateTime($dbItem->pubDate);
                    $dbTime = $dbTime->format(NewsService::DATE_COMPARE_FORMAT);

                    if ($itemDatetime->format(NewsService::DATE_COMPARE_FORMAT) === $dbTime && (string)$item['title'] === $dbItem->title) {
                        $save = false;
                        break;
                    }
                }
            }

            if ($save) {
                NewsService::saveArticle($item, $itemDatetime, $lang);
            }
        }
    }

    /**
     * Download RSS feed
     * @throws Exception
     * @throws ParseError
     */
    private function downloadFeed($lang): array
    {
        $lang = NewsService::getMergadoNewsLanguage($lang);

        $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_URL, self::FEED_BASE_URL . $lang);
        curl_setopt($ch, CURLOPT_FAILONERROR,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $feed = curl_exec($ch);

        $errorCount = curl_errno($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($feed === false || $errorCount > 0) {
            throw new Exception('Curl error: ' . $error);
        }

        try {
            $x = new SimpleXMLElement($feed, LIBXML_NOERROR);

            $data = array();
            foreach ($x->item as $item) {
                $data[] = $item;
            }
            return $data;

        } catch (Exception $e) {
            throw new ParseError($e);
        }
    }

    /**
     * Get last download based
     */
    private function getLastDownload()
    {
        return get_option(self::LAST_RSS_FEED_UPDATE, null);
    }

    /**
     * Set last download based
     *
     * @param $now
     */
    private function setLastDownload($now): void
    {
        update_option(self::LAST_RSS_FEED_UPDATE, $now, true);
    }

    /**
     * Set lock for few minutes, if feed is broken
     */
    private function increaseDownloadLock(): void
    {
        $value = $this->getDownloadLock();
        update_option(self::RSS_FEED_LOCK, $value + 1, true);
    }

    /**
     * Set download lock to null
     */
    private function nullDownloadLock(): void
    {
        update_option(self::RSS_FEED_LOCK, 0, true);
    }

    /**
     * Return current download lock number
     * @return int
     */
    private function getDownloadLock(): int
    {
        return (int)get_option(self::RSS_FEED_LOCK, 0);
    }

    private function getTimeWithModify(DateTime $dateTime, string $modifyString)
    {
        return (clone $dateTime)->modify($modifyString);
    }
}
