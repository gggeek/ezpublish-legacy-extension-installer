<?php
/**
 * File containing the LegacyKernelInstaller class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;

class LegacyKernelInstaller extends LegacyInstaller
{
    public function __construct( IOInterface $io, Composer $composer, $type = 'ezpublish-legacy' )
    {
        parent::__construct( $io, $composer, $type );
    }

    /**
     * We override this because if install dir is '.', existence is not enough - we add for good measure a check for
     * existence of the "settings" folder
     * @param InstalledRepositoryInterface $repo
     * @param PackageInterface $package
     * @return bool
     */
    public function isInstalled( InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        return parent::isInstalled( $repo, $package ) && is_dir( $this->ezpublishLegacyDir . '/settings' );
    }

    /**
     * If composer tries to install into a non-empty folder, we risk to effectively erase an existing installation.
     * This is not a composer limitation we can fix - it happens because composer might be using git to download the
     * sources, and git can not clone a repo into a non-empty folder.
     *
     * To prevent this, we adopt the following strategy:
     * - install in a separate, temporary directory
     * - then move over the installed files copying on top of the existing installation
     *
     * @param InstalledRepositoryInterface $repo
     * @param PackageInterface $package
     */
    public function install( InstalledRepositoryInterface $repo, PackageInterface $package )
    {
        $downloadPath = $this->getInstallPath( $package );
        $fileSystem = new Filesystem();
        if ( !is_dir( $downloadPath ) || $fileSystem->isDirEmpty( $downloadPath ) )
        {
            return parent::install( $repo, $package );
        }

        $actualLegacyDir = $this->ezpublishLegacyDir;
        $this->ezpublishLegacyDir = $this->generateTempDirName();

        parent::install( $repo, $package );

        /// @todo the following function does not warn of any failures in copying stuff over. We should probably fix it...
        $fileSystem->copyThenRemove( $this->ezpublishLegacyDir, $downloadPath );

        // if parent::install installed binaries, then the resulting shell/bat stubs will not work. We have to redo them
        $this->removeBinaries( $package );
        $this->ezpublishLegacyDir = $actualLegacyDir;
        $this->installBinaries( $package );
    }

    /**
     * Same as install(): we need to insure there is no removal of actual eZ code.
     * updateCode is called by update()
     */
    public function updateCode( PackageInterface $initial, PackageInterface $target )
    {
        $downloadPath = $this->getInstallPath( $package );

        $actualLegacyDir = $this->ezpublishLegacyDir;
        $this->ezpublishLegacyDir = $this->generateTempDirName();

        $this->installCode( $target );

        $fileSystem = new Filesystem();
        /// @todo the following function does not warn of any failures in copying stuff over. We should probably fix it...
        $fileSystem->copyThenRemove( $this->ezpublishLegacyDir, $downloadPath );

        $this->ezpublishLegacyDir = $actualLegacyDir;
    }

    /**
     * @return string a unique temporary directory (full path)
     */
    protected function generateTempDirName()
    {
        /// @todo to be extremely safe, we should use PID+time
        return sys_get_temp_dir() . '/' . uniqid( 'composer_ezlegacykernel_' );
    }
}
