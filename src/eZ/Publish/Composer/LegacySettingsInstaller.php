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

/**
 * This class allows user to deploy eZ LS setting as composer packages
 *
 * @todo ideally we should always remove anyting in settings/siteaccess and settings/override when we install or upgrade
 *       (but of course keep all settings/*.ini)
 */
class LegacySettingsInstaller extends LegacyKernelInstaller
{
    public function __construct( IOInterface $io, Composer $composer, $type = 'ezpublish-legacy-settings' )
    {
        parent::__construct( $io, $composer, $type );
    }

    public function getInstallPath( PackageInterface $package )
    {
        if ( $package->getType() != $this->type )
        {
            throw new InvalidArgumentException( "Installer only supports {$this->type} package type, got instead: " . $package->getType() );
        }

        return $this->ezpublishLegacyDir . '/settings';
    }

    protected function generateTempDirName()
    {
        /// @todo to be extremely safe, we should use PID+time
        return sys_get_temp_dir() . '/' . uniqid( 'composer_ezlegacysetting_' );
    }
}
