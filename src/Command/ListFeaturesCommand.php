<?php

namespace Stogon\UnleashBundle\Command;

use Stogon\UnleashBundle\FeatureInterface;
use Stogon\UnleashBundle\Repository\FeatureRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListFeaturesCommand extends Command
{
	protected static $defaultName = 'unleash:features:list';

	private FeatureRepository $featureRepository;

	public function __construct(FeatureRepository $featureRepository)
	{
		$this->featureRepository = $featureRepository;

		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->setDescription('List available Unleash features from remote.')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);

		$features = $this->featureRepository->getFeatures();

		if (empty($features)) {
			$io->warning('There is no feature available.');

			return Command::SUCCESS;
		}

		$io->table([
			'Name', 'Description', 'Stategies',
		], array_map(function (FeatureInterface $feature): array {
			$strategiesName = array_map(function (array $strategy): string {
				return $strategy['name'];
			}, $feature->getStrategies());

			return [
				$feature->getName(),
				$feature->getDescription(),
				implode(', ', $strategiesName),
			];
		}, $features));

		return Command::SUCCESS;
	}
}
