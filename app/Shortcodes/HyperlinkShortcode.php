<?php

namespace App\Shortcodes;

class HyperlinkShortcode {

    public function register($shortcode, $content, $compiler, $name, $viewData)
    {
        return "<a {$shortcode->get('href', '#')}>{$content}</a>";
    }
  
}