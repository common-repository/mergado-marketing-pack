<?php declare(strict_types=1);

namespace Mergado\Service;

use Mergado\Traits\SingletonTrait;

class AdminNoticeService
{
    use SingletonTrait;

    public function initNotices() : void
    {
        add_action( 'admin_notices', [NewsService::class, 'getAdminNews']);
    }
}
