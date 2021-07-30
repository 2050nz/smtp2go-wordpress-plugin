<?php

declare (strict_types=1);
namespace SMTP2GOWPPlugin;

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
return ['meta' => [
    'title' => 'Namespaced function call statement in the global scope',
    // Default values. If not specified will be the one used
    'prefix' => 'Humbug',
    'whitelist' => [],
    'whitelist-global-constants' => \true,
    'whitelist-global-classes' => \false,
    'whitelist-global-functions' => \false,
    'registered-classes' => [],
    'registered-functions' => [],
], 'Namespaced function call' => <<<'PHP'
<?php

PHPUnit\main();
----
<?php

namespace Humbug;

PHPUnit\main();

PHP
, 'FQ namespaced function call' => <<<'PHP'
<?php

\PHPUnit\main();
----
<?php

namespace Humbug;

\Humbug\PHPUnit\main();

PHP
, 'Whitelisted namespaced function call' => ['whitelist' => ['SMTP2GOWPPlugin\\PHPUnit\\main'], 'registered-functions' => [['SMTP2GOWPPlugin\\PHPUnit\\main', 'SMTP2GOWPPlugin\\Humbug\\PHPUnit\\main']], 'payload' => <<<'PHP'
<?php

PHPUnit\main();
----
<?php

namespace Humbug;

\Humbug\PHPUnit\main();

PHP
], 'FQ whitelisted namespaced function call' => ['whitelist' => ['SMTP2GOWPPlugin\\PHPUnit\\main'], 'registered-functions' => [['SMTP2GOWPPlugin\\PHPUnit\\main', 'SMTP2GOWPPlugin\\Humbug\\PHPUnit\\main']], 'payload' => <<<'PHP'
<?php

\PHPUnit\main();
----
<?php

namespace Humbug;

\Humbug\PHPUnit\main();

PHP
]];