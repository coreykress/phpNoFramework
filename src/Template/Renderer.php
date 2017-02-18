<?php declare(strict_types = 1);

namespace phpNoFramework\Template;

interface Renderer
{
    public function render($template, $data = []) : string ;
}