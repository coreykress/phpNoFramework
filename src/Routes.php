<?php declare(strict_types = 1);

namespace phpNoFramework;

return [
    ['GET', '/', ['phpNoFramework\Controllers\Homepage', 'show']],
    ['GET', '/{slug}', ['phpNoFramework\Controllers\Page', 'show']]
];
