<?php
/**
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use InvalidArgumentException;

/**
 * Base eZ Publish legacy installer.
 * Provides the right directory to install files into.
 */
class LegacyInstaller extends LibraryInstaller
{
    /**
     * eZ Publish legacy base dir.
     *
     * @var string
     */
    protected $ezpublishLegacyDir;

    public function __construct( IOInterface $io, Composer $composer, $type = '' )
    {
        parent::__construct( $io, $composer, $type );
        $options = $composer->getPackage()->getExtra();
        $this->ezpublishLegacyDir = isset( $options['ezpublish-legacy-dir'] ) ? rtrim( $options['ezpublish-legacy-dir'], '/' ) : '.';
        if ( $io->isVerbose() )
        {
            $io->write( "eZ Publish legacy base directory is '$this->ezpublishLegacyDir'" );
        }
    }

    public function getInstallPath( PackageInterface $package )
    {
        return $this->ezpublishLegacyDir;
    }
}
