<?php

namespace App\Utils;


use NumberFormatter;
use Nette\Utils\Html;
use DateTimeInterface;
use IntlDateFormatter;
use Nette\SmartObject;
use Nette\DI\Container;
use Nette\Http\Request;
use Nette\Caching\Cache;
use Nette\Utils\Strings;
use Nette\Utils\DateTime;
use Nette\Caching\IStorage;
use App\Images\ImageStorage;
use Nette\Database\Structure;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Contributte\Translation\Translator;
use Contributte\Translation\Wrappers\NotTranslate;
use Contributte\Translation\Exceptions\InvalidArgument;

class Helpers
{

	use SmartObject;

	public const FA_ICONS = [
		'admins' => 'fas fa-users-cog',
		'clients' => 'fas fa-users',
		'orders' => 'fas fa-scroll',
		'countries' => 'fas fa-flag-usa',
		'languages' => 'fas fa-language',
		'currencies' => 'fas fa-money-bill-wave',
		'products' => 'fas fa-shopping-cart',
		'sites' => 'fas fa-scroll',
		'payments' => 'fas fa-credit-card',
		'devices' => 'fas fa-box',
		'sales' => 'fas fa-coins',
		'sale_codes' => 'fas fa-barcode',

	];
	public const BREAKPOINTS = [
		'xs' => 0,
		'sm' => 576,
		'md' => 768,
		'lg' => 992,
		'xl' => 1200
	];
	public const WIDTHS = [
		'sm' => 540,
		'md' => 720,
		'lg' => 960,
		'xl' => 1140
	];
	public const SPACER = 30;
	public const FILES = [
		'cs' => [
			'alerts' => 'Alerty.cs.v2.pdf',
			'examples' => 'Pouziti.cs.v1.pdf',
			'manual' => 'Manual.cs.v3.pdf',
			'terms' => 'VOP.cs.v2.pdf',
		],
		'sk' => [
			'alerts' => 'Alerty.sk.v2.pdf',
			'examples' => 'Pouziti.cs.v1.pdf',
			'manual' => 'Manual.sk.v3.pdf',
			'terms' => 'VOP.sk.v1.pdf',
		]
	];
	/** @var DateTime */
	private static $now;
	/** @var Container */
	protected $context;
	/** @var Cache */
	protected $cache;
	/** @var Request */
	protected $request;
	/** @var Structure */
	protected $structure;
	/** @var ITranslator */
	private $translator;
	/** @var IntlDateFormatter */
	private $dateFormatter;
	/**  @var IntlDateFormatter */
	private $timeFormatter;
	/**  @var IntlDateFormatter */
	private $dateTimeFormatter;
	/**
	 * @var Config
	 */
	private $config;
	/** @var NumberFormatter */
	private $moneyFormatter;

	/**
	 * Helpers constructor.
	 * @param Container $context
	 * @param Request $request
	 * @param ITranslator|Translator $translator
	 * @param Config $config
	 */
	public function __construct(Container $context, Request $request, ITranslator $translator, Config $config)
	{
		$this->context = $context;
		$this->request = $request;
		$this->translator = $translator;
		$this->dateFormatter = new IntlDateFormatter($translator->getLocale(), IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
		$this->timeFormatter = new IntlDateFormatter($translator->getLocale(), IntlDateFormatter::NONE, IntlDateFormatter::SHORT);
		$this->dateTimeFormatter = new IntlDateFormatter($translator->getLocale(), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
		$this->moneyFormatter = new NumberFormatter($translator->getLocale(), NumberFormatter::CURRENCY);
		//$formatter->setPattern('E d.M.yyyy');

		$this->config = $config;
	}

	public static function notTranslate($data)
	{
		foreach ($data as $key => &$value) {
			$value = new NotTranslate($value);
		}
		return $data;
	}

	public static function clientEmail(ActiveRow $client): string
	{
		return "{$client->first_name} {$client->last_name} <{$client->email}>";
	}

	public static function now(): DateTime
	{
		return self::$now ?? (self::$now = new DateTime());
	}

	public static function json($text): string
	{
		return json_encode($text);
	}

	public static function icon($key): string
	{
		return static::FA_ICONS[$key] ?? 'fa fa-question anim-blink';
	}

	public static function address(?ActiveRow $row): ?string
	{
		return $row ? "$row->street $row->city $row->zip $row->country" : null;
	}

	public static function values(string $name, array $values): array
	{
		return array_combine($values, self::prefix($values, $name . '.values.'));
	}

	/**
	 * @param array $array
	 * @param string $prefix
	 * @return array
	 */
	public static function prefix(array $array, string $prefix): array
	{
		return preg_filter('/^/', $prefix, $array);
	}

	public static function nbsp(?string $content): ?string
	{
		return $content ? NbspMacro::nbsp($content) : $content;
	}

	public static function width($width): string
	{
		$width = (string)$width;
		return strpos($width, 'x') ? Strings::before($width, 'x') : $width;
	}

	public static function underScoreToCamelCase(string $input): string
	{
		return lcfirst(implode('', array_map('ucfirst', explode('_', $input))));
	}

	public static function camelCaseToUnderScore(string $input): string
	{
		return self::camelCaseTo($input, '_');
	}

	public static function camelCaseTo(string $input, string $separator): string
	{
		return strtolower(preg_replace('/(?<!^)[A-Z]/', $separator . '$0', $input));
	}

	public static function transformKeys($array, callable $callable): array
	{
		$result = [];
		foreach ($array as $key => $value) {
			$result[$callable($key)] = $value;
		}
		return $result;
	}

	public static function files(string $locale, string $name): string
	{
		return '/files/' . $locale . '/NEDELEJTEKOMPROMISY.' . self::FILES[$locale][$name];
	}

	public static function merge(array $items, string $column): Html
	{
		return Html::el()->setHtml(\App\Utils\Strings::mergeCommon(array_column($items, $column), '/<wbr>'));
	}

	public static function unique(array $items, string ...$columns): array
	{

		if (count($columns) > 1) {
			$x = [];
			foreach (Arrays::columns($items, $columns) as $data) {
				$x[implode('/', $data)] = array_values($data) + $data;
			}
			return array_values($x);
		}
		return array_unique(array_column($items, $columns[0]));
	}

	/**
	 * @param Structure $structure
	 */
	public function injectStructure(Structure $structure): void
	{
		$this->structure = $structure;
	}

	/**
	 * @param IStorage $cache
	 */
	public function injectCache(IStorage $cache): void
	{
		$this->cache = new Cache($cache, 'helpers');
	}

	public function name(?ActiveRow $admin): string
	{
		if ($this->hasColumn($admin, 'name')) {
			return $admin->name;
		}
		return "$admin->first_name $admin->last_name";
	}

	public function hasColumn(ActiveRow $row, string $column): bool
	{
		$table = $row->getTable()->getName();
		return $this->cache->load("columns-$table-$column", function () use ($table, $column) {
			$columns = $this->structure->getColumns($table);
			$names = array_column($columns, 'name');
			return in_array($column, $names, true);
		});
	}

	public function signature(?ActiveRow $row): ?string
	{
		if (!$row) {
			return null;
		}
		if ($this->hasColumn($row, 'hash')) {
			$id = $row->hash;
		} elseif ($this->hasColumn($row, 'number')) {
			$id = $row->number;
		} else {
			$id = $row->getSignature();
		}
		return '#' . implode(' ', array_unique([(string)$id, $this->title($row)]));
	}

	public function title(?ActiveRow $row): ?string
	{
		if (!$row) {
			return null;
		}
		$table = $row->getTable()->getName();
		if ($table === 'devices') {
			return $row->hash;
		}
		if ($table === 'orders') {
			return $row->number;
		}
		if ($this->hasColumn($row, 'title')) {
			return $row->title;
		}
		if ($this->hasColumn($row, 'name')) {
			return $row->name;
		}
		if ($this->hasColumn($row, 'first_name') && $this->hasColumn($row, 'last_name')) {
			return "$row->first_name $row->last_name";
		}
		return $row->getSignature();
	}

	public function cookieClass(string $key, ?string $classOn = 'show', ?string $classOff = null, bool $default = false): ?string
	{
		return $this->cookieToggle($key, $default) ? $classOn : $classOff;
	}

	public function cookieToggle(string $key, $default = null)
	{
		$cookies = $this->request->getCookies();
		if (isset($cookies[$key])) {
			return filter_var($cookies[$key], FILTER_VALIDATE_BOOLEAN);
		}
		return $default;
	}

	public function sizes(int $width): string
	{
		$s = self::SPACER;
		$sizes = [];
		foreach (array_reverse(self::BREAKPOINTS, true) as $bp => $w) {
			if ($_w = self::WIDTHS[$bp] ?? null) {
				$_w = $_w / 100 * $width - $s;
				$sizes[] = "(min-width: {$w}px) {$_w}px";
			}
		}
		$sizes[] = "calc({$width}vw - {$s}px)";
		return implode(', ', $sizes);
	}

	public function ld($date): string
	{
		return $this->formatDateTime($date);
	}

	public function formatDateTime($date): string
	{
		return $this->dateTimeFormatter->format($date);
	}

	public function dateHtml($date): Html
	{
		return Html::el('div')
			->addText($this->formatDate($date))
			->addHtml(' ')
			->addHtml(Html::el('sup')->addText($this->formatTime($date)))
			->title($this->ago($date));
	}

	public function formatDate($date): string
	{
		return $this->dateFormatter->format($date);
	}

	public function formatTime($date): string
	{
		return $this->timeFormatter->format($date);
	}

	public function ago(?DateTimeInterface $dateTime): string
	{
		if (!$dateTime) {
			return '';
		}
		$interval = DateTime::from('now')->diff($dateTime);
		foreach ($interval as $part => $value) {
			if ($value !== 0) {
				break;
			}
		}
		if ($value < 1) { // now diff now má rok -1
			$prefix = 'now';
			$part = 0;
		} else {
			$prefix = $interval->invert ? 'before' : 'after';
		}
		return $this->translator->translate("content.time.$prefix.$part", $value);
	}

	/**
	 * @param $val
	 * @return mixed
	 * @throws InvalidArgument
	 */
	public function unknown($val)
	{
		return $val ?: Html::el('small')->setText($this->translator->translate('content.unknown'))->class('text-muted');
	}

	public function money($value): string
	{
		return number_format($value, 0, ',', ' ');
	}

	public function price($price, string $currency): string
	{
		return $this->moneyFormatter->formatCurrency($price, $currency);
		/*
		$options = $this->config->shop->currencies[$currency];
		// $price = number_format($price, 0, ',', '.');
		$price = self::money($price);
		if ($options->before) {
			return Html::el()->addText($options->sign)->addHtml('&nbsp;')->addText($price);
		}
		return Html::el()->addText($price)->addHtml('&nbsp;')->addText($options->sign);*/
	}

}

