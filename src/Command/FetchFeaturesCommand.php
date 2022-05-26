<?php

namespace Stogon\UnleashBundle\Command;

use Stogon\UnleashBundle\Repository\FeatureRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchFeaturesCommand extends Command
{
	protected static $defaultName = 'unleash:features:fetch';

	private FeatureRepository $featureRepository;

	public function __construct(FeatureRepository $featureRepository)
	{
		$this->featureRepository = $featureRepository;

		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->setDescription('Fetch Unleash features from remote and store them in the cache for later usage.')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);

		$io->text('Fetching features from remote...');

		$this->featureRepository->getFeatures();

		$io->success('Features fetched from remote and stored in the cache.');

		if (\defined(Command::class.'::SUCCESS')) {
			// @phpstan-ignore-next-line
			return Command::SUCCESS;
		}

		return 0;
	}
}
