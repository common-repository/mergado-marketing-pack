<?php declare(strict_types=1);

namespace Mergado\Helper;

use Mergado\Request\Request;

class ResponseHelper
{
    public static function downloadFile(string $file, string $missingFileRedirectUrl = ''): void
    {
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            echo readfile($file);
            exit;
        } else {
            // TODO: REMOVE THIS WHOLE SECTION?
            // If not set redirect to same page without parameters
            if ($missingFileRedirectUrl = '') {
                $missingFileRedirectUrl = UrlHelper::getAdminRoute(Request::getPage());
            }

            wp_redirect($missingFileRedirectUrl);
        }
    }
}
