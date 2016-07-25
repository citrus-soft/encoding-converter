<?php

namespace Citrus\EncodingConverter;

use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Ddeboer\Transcoder\Transcoder;

class Converter
{
    /** @var IOInterface|OutputInterface */
    protected $io;
    protected $toEncoding;
    protected $fromEncoding;

    protected $bitrixLang = 'ru';
    protected $bitrixLangOnly = true;

    protected $transcoder;

    public function __construct($toEncoding, $fromEncoding = null, $bitrixLangOnly = true) {

        $this->io = new NullIO();
        $this->bitrixLangOnly = $bitrixLangOnly;
        $this->toEncoding = $toEncoding;

        if (isset($fromEncoding)) {
            $this->fromEncoding = $fromEncoding;
        }
        else {
            $this->fromEncoding = strtolower($this->toEncoding) == 'windows-1251' ? 'utf-8' : 'windows-1251';
        }

        $this->transcoder = Transcoder::create($this->toEncoding);
    }

    /**
     * Convert files encoding
     *
     * @param string $path
     */
    public function convert($path) {

        $finder = Finder::create()->files()->ignoreDotFiles(false)->in($path)->name('/\.php$/');
        if ($this->bitrixLangOnly) {
            $finder->path('/\blang\/' . $this->getBitrixLang() .'\//');
        }
        foreach ($finder as $file) {
            if ($this->io->isVerbose()) {
                $this->io->write('Converting file <info>' . $file->getRelativePathname() . '</info>', true);
            }
            file_put_contents($file->getRealPath(), $this->transcoder->transcode($file->getContents(), $this->fromEncoding));
        }
    }

    /**
     * Bitrix framework-specific: will convert only language files located at lang/<language code>/ subfolders
     *
     * @see Converter::setBitrixLang()
     * @return boolean
     */
    public function isBitrixLangOnly()
    {
        return $this->bitrixLangOnly;
    }

    /**
     * Bitrix framework-specific: will convert only language files located at lang/<language code>/ subfolders
     *
     * @see Converter::setBitrixLang()
     * @param bool $bitrixLangOnly
     */
    public function setBitrixLangOnly($bitrixLangOnly = true)
    {
        $this->bitrixLangOnly = $bitrixLangOnly;
    }

    /**
     * @return string
     */
    public function getBitrixLang()
    {
        return $this->bitrixLang;
    }

    /**
     * Sets bitrix language code
     *
     * Only language files located at lang/<language code>/ subfolders
     *
     * @param string $bitrixLang
     */
    public function setBitrixLang($bitrixLang = 'ru')
    {
        if (!preg_match('/^[a-z]{2}$/', $bitrixLang)) {
            throw new \InvalidArgumentException('$bitrixLang should contain 2-letter language code');
        }
        $this->bitrixLang = $bitrixLang;
    }

    /**
     * @param IOInterface|OutputInterface $io
     */
    public function setIo($io)
    {
        $this->io = $io;
    }

    /**
     * @return string
     */
    public function getFromEncoding()
    {
        return $this->fromEncoding;
    }

    /**
     * @param string $fromEncoding
     */
    public function setFromEncoding($fromEncoding)
    {
        $this->fromEncoding = $fromEncoding;
    }

    /**
     * @return mixed
     */
    public function getToEncoding()
    {
        return $this->toEncoding;
    }

    /**
     * @param mixed $toEncoding
     */
    public function setToEncoding($toEncoding)
    {
        $this->toEncoding = $toEncoding;
    }
}