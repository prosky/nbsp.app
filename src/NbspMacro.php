<?php declare(strict_types=1);

namespace App\Utils;

use DOMXPath;
use DOMDocument;
use Latte\Engine;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\PhpWriter;
use Nette\Utils\Strings;
use Latte\Macros\MacroSet;


class NbspMacro extends MacroSet
{
	public const
		ENGLISH_LOCALE = 'en',
		CZECH_LOCALE = 'cs';

	public const LOCALES = [
		self::ENGLISH_LOCALE,
		self::CZECH_LOCALE
	];
	public const TASKS = [
		self::CZECH_LOCALE => [
			'short_words' => [
				'@(\w{1,3}) @i',
				'$1&nbsp;$2',
			],
			'non_breaking_hyphen' => [
				'@(\w{1})-(\w+)@i',
				'$1&#8209;$2'
			],
			'numbers' => [
				'@(\d) (\d)@i',
				'$1&nbsp;$2',
			],
			'spaces_in_scales' => [
				'@(\d) : (\d)@i',
				'$1&nbsp;:&nbsp;$2'
			],
			'ordered_number' => [
				'@(\d\.) ([0-9a-záčďéěíňóřšťúýž])@',
				'$1&nbsp;$2'
			],
			'prepositions ' => [
				'@($|;| |&nbsp;|\(|\n|>)(%keys%) @i',
				'$1$2&nbsp;',
				'do|kromě|od|u|z|ze|za|proti|naproti|kvůli|vůči|nad|pod|před|za|o|pro|mezi|přes|mimo|při|na|po|v|ve|pod|před|s|za|mezi|se|si|k|je'
			],
			'conjunctions' => [
				'@($|;| |&nbsp;|\(|\n|>)(%keys%) @i',
				'$1$2&nbsp;',
				'a|i|o|u'
			],
			'abbreviations' => [
				'@($|;| |&nbsp;|\(|\n|>)(%keys%) @i',
				'$1$2&nbsp;',
				'vč.|cca.|č.|čís.|čj.|čp.|fa|fě|fy|kupř.|mj.|např.|p.|pí|popř.|př.|přib.|přibl.|sl.|str.|sv.|tj.|tzn.|tzv.|zvl.'
			],
			'units' => [
				'@(\d) (%keys%)(^|[;\.!:]| |&nbsp;|\?|\n|\)|<|\010|\013|$)@i',
				'$1&nbsp;$2$3',
				'm|m²|l|kg|h|°C|Kč|lidí|dní|%|mil'
			]
		],

		self::ENGLISH_LOCALE => [
			'short_words' => [
				'@(\w{1,3}) @i',
				'$1&nbsp;$2',
			],
			'non_breaking_hyphen' => [
				'@(\w{1})-(\w+)@i',
				'$1&#8209;$2'
			],
			'numbers' => [
				'@(\d) (\d)@i',
				'$1&nbsp;$2',
			],
			'spaces_in_scales' => [
				'@(\d) : (\d)@i',
				'$1&nbsp;:&nbsp;$2'
			],
			'ordered_number' => [
				'@(\d\.) ([0-9a-záčďéěíňóřšťúýž])@',
				'$1&nbsp;$2'
			],
			'prepositions ' => [
				'@($|;| |&nbsp;|\(|\n|>)(%keys%) @i',
				'$1$2&nbsp;',
				'aboard|about|above|across|after|against|ahead of|along|amid|amidst|among|around|as|as far as|as of|aside from|at|athwart|atop|barring|because of|before|behind|below|beneath|beside|besides|between|beyond|but|by|by means of|circa|concerning|despite|down|during|except|except for|excluding|far from|following|for|from|in|in accordance with|in addition to|in case of|in front of|in lieu of|in place of|in spite of|including|inside|instead of|into|like|minus|near|next to|notwithstanding|of|off|on|on account of|on behalf of|on top of|onto|opposite|out|out of|outside|over|past|plus|prior to|regarding|regardless of|save|since|than|through|throughout|till|to|toward|towards|under|underneath|unlike|until|up|upon|versus|via|with|with regard to|within|without'
			],
			'conjunctions' => [
				'@($|;| |&nbsp;|\(|\n|>)(%keys%) @i',
				'$1$2&nbsp;',
				'a|i|o|u'
			],
			'article' => [
				'@($|;| |&nbsp;|\(|\n|>)(%keys%) @i',
				'$1$2&nbsp;',
				'a|an|the'
			],
			/*'abbreviations' => [
				'@($|;| |&nbsp;|\(|\n|>)(%keys%) @i',
				'$1$2&nbsp;',
				'vč.|cca.|č.|čís.|čj.|čp.|fa|fě|fy|kupř.|mj.|např.|p.|pí|popř.|př.|přib.|přibl.|sl.|str.|sv.|tj.|tzn.|tzv.|zvl.'
			],*/
			'units' => [
				'@(\d) (%keys%)(^|[;\.!:]| |&nbsp;|\?|\n|\)|<|\010|\013|$)@i',
				'$1&nbsp;$2$3',
				'm|m²|l|kg|h|°C|Kč|lidí|dní|%|mil'
			]
		]
	];
	private static $tasks = [];

	public static function install(Compiler $compiler): self
	{
		$me = new static($compiler);
		//$me->addMacro('nbsp', [$me, 'macroNbsp'], [$me, 'macroNbsp']);
		$me->addMacro('nbsp', [$me, 'nbspStart'], [$me, 'nbspEnd']);
		$me->addMacro('nbsphtml', [$me, 'nbspStart'], [$me, 'nbspHTMLEnd']);
		//$me->addMacro('nbsp', [$me, 'nbspMacroStart'], [$me, 'nbspMacroEnd']);
		return $me;
	}

	public static function filterNbspText($s, string $locale = self::CZECH_LOCALE): string
	{
		return self::nbsp($s, $locale);
	}

	public static function filterNbspHtml(string $s, int $phase = null, bool &$strip = true): string
	{
		if ($phase & PHP_OUTPUT_HANDLER_START) {
			$s = ltrim($s);
		}
		if ($phase & PHP_OUTPUT_HANDLER_FINAL) {
			$s = rtrim($s);
		}
		return self::nbspHtml($s);
	}

	public function nbspStart(MacroNode $node, PhpWriter $writer): string
	{
		return 'if (false) {';
	}

	public function nbspEnd(MacroNode $node, PhpWriter $writer): string
	{
		return '};?>' . self::nbsp($node->content, $node->args ?: self::CZECH_LOCALE) . '<?php';
	}

	public static function nbsp(string $content, string $locale = self::CZECH_LOCALE): string
	{
		$content = self::spacelessText($content);
		foreach (self::getTasks($locale) as $key => $TASK) {
			$content = Strings::replace($content, ...$TASK);
		}
		return trim($content);
	}

	/**
	 * Replaces all repeated white spaces with a single space.
	 * @return string text
	 */
	public static function spacelessText(string $s): string
	{
		return preg_replace('#[ \t\r\n]+#', ' ', $s);
	}

	private static function getTasks(string $locale)
	{
		if (!isset(self::$tasks[$locale])) {
			foreach (self::TASKS[$locale] as $key => $TASK) {
				[$regex, $replacement, $keys] = $TASK + [null, null, null];
				if ($keys) {
					$regex = str_replace('%keys%', $keys, $regex);
				}
				self::$tasks[$locale][$key] = [$regex, $replacement];
			}
		}
		return self::$tasks[$locale];
	}

	public function nbspHTMLEnd(MacroNode $node, PhpWriter $writer): string
	{
		return '};?>' . self::nbspHtml($node->content) . '<?php';
	}

	/**
	 * @param string $content
	 * @param string $locale
	 * @return string
	 * @todo hledat atribut lang
	 */
	public static function nbspHtml(string $content, string $locale = self::CZECH_LOCALE): string
	{
		$doc = new DOMDocument('1.1', 'UTF-8');
		$doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		$xpath = new DOMXPath($doc);
		$textNodes = $xpath->query('//text()');
		foreach ($textNodes as $textNode) {
			$textNode->data = self::nbsp(self::spacelessText($textNode->data), $locale);
		}
		$body = $doc->getElementsByTagName('body')->item(0);
		return trim(str_replace(['<body>', '</body>'], ['', ''], $doc->saveHTML($body)));
	}

	public function macroNbsp(MacroNode $node, PhpWriter $writer)
	{
		$node->openingCode = in_array($node->context[0], [Engine::CONTENT_HTML, Engine::CONTENT_XHTML], true)
			? '<?php ob_start(function ($s, $phase) { static $strip = true; return App\Utils\NbspMacro::filterNbspHtml($s, $phase, $strip); }, 4096); ?>'
			: "<?php ob_start('App\\Utils\\NbspMacro::filterNbspText', 4096); ?>";
		$node->closingCode = '<?php ob_end_flush(); ?>';
	}


}
