<?php
/**
 * File containing the LegacyExtensionInstaller class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use InvalidArgumentException;

/**
 * All this class does is to tell composer that extensions have to be installed in a
 * different directory
 */
class LegacyExtensionInstaller extends LegacyInstaller
{
    public function __construct( IOInterface $io, Composer $composer, $type = 'ezpublish-legacy-extension' )
    {
        parent::__construct( $io, $composer, $type );
    }

    public function getInstallPath( PackageInterface $package )
    {
        if ( $package->getType() != $this->type )
        {
            throw new InvalidArgumentException( "Installer only supports {$this->type} package type, got instead: " . $package->getType() );
        }

        return $this->ezpublishLegacyDir . '/extension';
    }

}
