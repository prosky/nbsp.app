<?php

declare(strict_types=1);


namespace App\Utils\Assets;

use Nette;
use Nette\Schema\Expect;
use Nette\Utils\Strings;
use Nette\DI\CompilerExtension;
use Nette\InvalidArgumentException;

class Extension extends CompilerExtension
{

	protected $provider;

	public function getConfigSchema(): Nette\Schema\Schema
	{
		$params = $this->getContainerBuilder()->parameters;
		return Expect::structure([
			'debugMode' => Expect::bool($params['debugMode']),
			'wwwDir' => Expect::string($params['wwwDir'])->assert('is_dir'),
			'publicPath' => Expect::string()->nullable(),
			'devServer' => Expect::string()->nullable()->default(null),
			'manifest' => Expect::string('manifest.json')
		])->castTo('array');
	}


	public function loadConfiguration()
	{
		parent::loadConfiguration();
		$config = $this->getConfig();
		if (Strings::endsWith($config['publicPath'], '/')) {
			throw new InvalidArgumentException('Please provide public path without ending slash.');
		}
		$builder = $this->getContainerBuilder();
		$this->provider = $builder->addDefinition($this->prefix('provider'))
			->setFactory(PathProvider::class, $config);

	}

	public function beforeCompile()
	{
		parent::beforeCompile();
		$builder = $this->getContainerBuilder();
		$builder->getDefinition('latte.latteFactory')->getResultDefinition()
			->addSetup('$service->addProvider("assetsPathProvider",?)', [$this->provider]);

	}


}
