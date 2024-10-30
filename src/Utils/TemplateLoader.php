<?php

namespace Mergado\Utils;

class TemplateLoader {

	public static function getTemplate(string $path, array $variables = null)
	{
        if ($variables) {
            extract($variables); // Extract variables for template
        }

		ob_start();

		include $path;

        return ob_get_clean();
	}
}
