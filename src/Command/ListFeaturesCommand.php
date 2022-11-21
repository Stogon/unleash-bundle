<?php

namespace Stogon\UnleashBundle\Command;

use Stogon\UnleashBundle\FeatureInterface;
use Stogon\UnleashBundle\Repository\FeatureRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// @phpstan-ignore-next-line
#[\Symfony\Component\Console\Attribute\AsCommand('unleash:features:list', 'List available Unleash features from remote.')]
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

			if (\defined(Command::class.'::SUCCESS')) {
				// @phpstan-ignore-next-line
				return Command::SUCCESS;
			}

			return 0;
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

		if (\defined(Command::class.'::SUCCESS')) {
			// @phpstan-ignore-next-line
			return Command::SUCCESS;
		}

		return 0;
	}
}
