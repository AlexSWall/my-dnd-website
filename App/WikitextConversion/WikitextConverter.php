<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextConverter
{
	public function __construct()
	{
	}

	public function convertWikitextToHTML( string $wikitext ): string
	{
		$grammarClass = 'App\WikitextConversion\Grammar';
		$wikitextParser = new wikitextParser($grammarClass);
		$htmlBuilder = new HTML5Builder();

		$tokens = $wikitextParser->parse($wikitext);
		$html = $htmlBuilder->build($tokens);

		return $html;
	}
}