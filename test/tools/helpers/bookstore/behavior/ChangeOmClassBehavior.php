<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

class ChangeOmClassBehavior extends Behavior
{

    public function extensionPeerFilter(&$script)
    {
        $getOMClass = <<<EOF
\\1
    static public function getOMClass(\$row = 0, \$colnum = 0)
    {
        return 'MyBookExtended';
    }

}
EOF;
        $script = preg_replace('/(.*)}/',$getOMClass, $script);
    }

}
