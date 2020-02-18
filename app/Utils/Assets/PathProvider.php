<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: prosky
 * Date: 25.07.18
 * Time: 15:31
 */

namespace App\Utils\Assets;


use stdClass;
use Generator;
use Nette\Utils\Json;
use Nette\IOException;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\FileSystem;

class PathProvider
{
	/** @var  string */
	protected $publicPath;

	/** @var  string */
	protected $wwwDir;

	/** @var  string|null */
	protected $devServer;

	/** @var  string */
	protected $manifest;

	/** @var  stdClass */
	protected $manifestData;

	/** @var bool|null */
	protected $isAvailable;

	/** @var Cache */
	protected $cache;

	/**
	 * AssetsPathProvider constructor.
	 * @param bool $debugMode
	 * @param string $publicPath
	 * @param string $wwwDir
	 * @param null|string $devServer
	 * @param string $manifest
	 * @param IStorage $storage
	 * @internal param string $buildDir
	 */
	public function __construct(bool $debugMode, string $publicPath = null, string $wwwDir, string $devServer = null, string $manifest, IStorage $storage)
	{
		$this->debugMode = $debugMode;
		$this->wwwDir = $wwwDir;
		$this->publicPath = $publicPath;
		$this->devServer = $devServer;
		$this->manifest = $manifest;
		$this->isAvailable = (bool)$devServer;
		$this->cache = new Cache($storage, 'assets');
	}

	/**
	 * @param string $name
	 * @return string
	 * @throws IOException
	 */
	public function locate(?string $name = null): ?string
	{
		if (!$this->manifestData) {
			$this->initData();
		}
		if (!$name) {
			return ($this->isAvailable ? $this->devServer : null) . $this->publicPath . '/';
		}
		return $this->manifestData->$name ?? null;
	}

	private function initData(): void
	{
		if (!$this->manifestData && $this->isAvailable) {
			try {
				$this->manifestData = $this->loadManifest();
			} catch (IOException $exception) {
				$this->isAvailable = false;
			}
		}
		if (!$this->manifestData && !$this->isAvailable) {
			$this->manifestData = $this->cache->load('manifest', [$this, 'loadManifest']);
		}
	}

	public function loadManifest(?array &$dep = [])
	{
		$path = $this->getAbsolutePath() . $this->publicPath . '/' . $this->manifest;
		if ($this->debugMode && !$this->isAvailable) {
			$dep[Cache::FILES] = $path;
		}
		if (!$this->debugMode && !$this->devServer) {
			$dep[Cache::EXPIRATION] = '999 years';
		}
		if ($this->isAvailable) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $path);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10);
			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
			$data = curl_exec($ch);
		} else {
			$data = FileSystem::read($path);
		}
		if ($data === false) {
			throw new IOException('Data not loaded');
		}
		return Json::decode($data);
	}

	public function getAbsolutePath(): ?string
	{
		return ($this->isAvailable ? $this->devServer : $this->wwwDir);
	}

	public function preload(string $mime): Generator
	{
		if (!$this->manifestData) {
			$this->initData();
		}
		foreach ($this->manifestData as $file => $patch) {
			if (preg_match($mime, $file)) {
				yield $patch;
			}
		}
	}

}
