<?php declare(strict_types=1);

namespace Mergado\Service;
use Mergado\Traits\SingletonTrait;

class AdminBarService
{
    use SingletonTrait;

    public function init() : void
    {
        add_action( 'admin_bar_menu', function( \WP_Admin_Bar $bar )
        {
            $icon = '<span class="mergado-custom-icon"></span>';
            $title = '';

            $bar->add_menu( array(
                'id'     => 'wpse',
                'title'  => $icon.$title,
                'href'   => admin_url('admin.php?page=mergado-news&showNews=true'),
                'meta'   => array(
                    'target'   => '_self',
                    'html'     => '<!-- Custom HTML that goes below the item -->',
                ),
            ) );
        }, 510 );
    }
}
