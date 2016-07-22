<?php

namespace Citrus\EncodingConverter;

use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;

/**
 * @todo rollback encoding changes before package update or deletion to suppress warning for changes in vcs repositories
 *
 * @package Citrus\EncodingConverter
 */
class Plugin implements PluginInterface, EventSubscriberInterface, Capable
{
    /** @var Composer */
    protected $composer;
    /** @var IOInterface */
    protected $io;

    protected $packageEncodings = array();

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            PackageEvents::POST_PACKAGE_INSTALL => 'process',
            PackageEvents::POST_PACKAGE_UPDATE => 'process',
        );
    }

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;

        $extra = $composer->getPackage()->getExtra();
        if (isset($extra['encoding-convert']) && is_array($extra['encoding-convert'])) {
            $this->packageEncodings = $extra['encoding-convert'];
        }
    }

    public function process(PackageEvent $event)
    {
        $operation = $event->getOperation();
        if ($operation instanceof InstallOperation) {
            /** @var InstallOperation $operation */
            $package = $operation->getPackage();
        } elseif ($operation instanceof UpdateOperation) {
            /** @var UpdateOperation $operation */
            $package = $operation->getTargetPackage();
        } else {
            throw new \ErrorException('Unexpected operation: ' . get_class($operation));
        }

        if (isset($this->packageEncodings[$package->getName()])) {
            $encoding = $this->packageEncodings[$package->getName()];
            if (is_string($encoding)) {
                $encoding = array('to' => $encoding);
            }

            $converter = new Converter($encoding['to'], isset($encoding['from']) ? $encoding['from'] : null);
            $this->io->write('Converting package <info>' . $package->getName() . '</info> from <comment>' . $converter->getFromEncoding() . '</comment> to <comment>' . $converter->getToEncoding() . '</comment>');

            $converter->setIo($this->io);
            $converter->convert($this->getPackagePath($package));
        }
    }

    /**
     * @param PackageInterface $package
     * @param string $toEncoding
     * @param string $fromEncoding
     * @throws \ErrorException
     */
    protected function convert(PackageInterface $package, $toEncoding, $fromEncoding = null)
    {
    }

    /**
     * Get the install path for a package
     *
     * @param PackageInterface $package
     * @return string
     */
    protected function getPackagePath(PackageInterface $package)
    {
        return $this->composer->getInstallationManager()->getInstallPath($package);
    }

    /**
     * @inheritdoc
     */
    public function getCapabilities()
    {
        return array(
            'Composer\Plugin\Capability\CommandProvider' => 'Citrus\EncodingConverter\CommandProvider',
        );
    }
}