<?php declare(strict_types=1);

namespace Mergado\Helper;

class UrlHelper
{
    public static function getAdminRoute(string $page, array $params = []): string
    {
        $paramsModified = array_merge(['page' => $page], $params);

        return sprintf("admin.php?%s", http_build_query($paramsModified));
    }

    public static function getCurrentUrl() : string {
        if ( isset( $_SERVER['HTTP_HOST'] ) ) {
            $host = wp_unslash( $_SERVER['HTTP_HOST'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        } else {
            $host = wp_parse_url( home_url(), PHP_URL_HOST );
        }
        if ( isset( $_SERVER['REQUEST_URI'] ) ) {
            $path = wp_unslash( $_SERVER['REQUEST_URI'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        } else {
            $path = '/';
        }
        return esc_url_raw( ( is_ssl() ? 'https' : 'http' ) . '://' . $host . $path );
    }
}
