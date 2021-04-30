<?php

namespace Citrus\EncodingConverter;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class Command extends BaseCommand
{
	protected function configure()
	{
		$this
			->setName('encoding:convert')
			->setDescription('Convert encoding of bitrix lang files (*.php in lang/<lang_code>/ subfolders)')
			->addArgument('path', InputArgument::REQUIRED, 'Directory in which to perform conversion (i.e. path to component, template or module)')
			->addArgument('to', InputArgument::REQUIRED, 'Target encoding')
			->addArgument('from', InputArgument::OPTIONAL, 'Source encoding (if ommited, conversion performed between <comment>utf-8</comment> and <comment>windows-1251</comment>)')
			->addOption('lang', 'l', InputOption::VALUE_OPTIONAL, 'Language to convert (for bitrix lang files) (default is <comment>ru</comment>)')
			->addOption('all', 'a', InputOption::VALUE_OPTIONAL, 'Process all php files (not ony bitrix lang files)')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$converter = new Converter($input->getArgument('to'), $input->getArgument('from'), !$input->getOption('all'));
		if ($lang = $input->getOption('lang')) {
			$converter->setBitrixLang($lang);
		}
		$converter->setIo($output);
		$converter->convert($input->getArgument('path'));
	}
}
