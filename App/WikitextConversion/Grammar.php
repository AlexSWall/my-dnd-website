<?php





namespace App\WikitextConversion;

use App\WikitextConversion\Tokens\ClosingTagToken;
use App\WikitextConversion\Tokens\EndOfFileToken;
use App\WikitextConversion\Tokens\MetaToken;
use App\WikitextConversion\Tokens\NewLineToken;
use App\WikitextConversion\Tokens\OpeningTagToken;
use App\WikitextConversion\Tokens\SelfClosingTagToken;
use App\WikitextConversion\Tokens\TextToken;

class Grammar
{
	static function getNewGrammarParser()
	{
		return new PEGParser();
	}
}


class PEGParser extends \WikiPEG\PEGParserBase {
  // initializer
  
  
  function array_flatten($array = null) {
  	$result = array();
  
  	if (!is_array($array)) {
  		$array = func_get_args();
  	}
  
  	foreach ($array as $key => $value) {
  		if (is_array($value))
  			$result = array_merge($result, array_flatten($value));
  		else
  			$result = array_merge($result, array($key => $value));
  	}
  
  	return $result;
  }
  
  private function startOffset() {
  	return $this->savedPos;
  }
  
  private function endOffset() {
  	return $this->currPos;
  }
  
  

  // cache init
  

  // expectations
  protected $expectations = [
    0 => ["type" => "end", "description" => "end of input"],
    1 => ["type" => "other", "description" => "start"],
    2 => ["type" => "literal", "value" => "\x0a", "description" => "\"\\n\""],
    3 => ["type" => "literal", "value" => "\x0d\x0a", "description" => "\"\\r\\n\""],
    4 => ["type" => "class", "value" => "[ \\t]", "description" => "[ \\t]"],
    5 => ["type" => "literal", "value" => "==", "description" => "\"==\""],
    6 => ["type" => "class", "value" => "[^\\r\\n]", "description" => "[^\\r\\n]"],
    7 => ["type" => "class", "value" => "[^\\n\\r]", "description" => "[^\\n\\r]"],
    8 => ["type" => "literal", "value" => "=", "description" => "\"=\""],
    9 => ["type" => "class", "value" => "[*#:;]", "description" => "[*#:;]"],
    10 => ["type" => "literal", "value" => "[[", "description" => "\"[[\""],
    11 => ["type" => "class", "value" => "[^\\]|]", "description" => "[^\\]|]"],
    12 => ["type" => "literal", "value" => "|", "description" => "\"|\""],
    13 => ["type" => "class", "value" => "[^\\]]", "description" => "[^\\]]"],
    14 => ["type" => "literal", "value" => "]]", "description" => "\"]]\""],
    15 => ["type" => "literal", "value" => "'''", "description" => "\"'''\""],
    16 => ["type" => "literal", "value" => "''", "description" => "\"''\""],
  ];

  // actions
  private function a0($blocks) {
  
  		return array_flatten([ $blocks, new EndOfFileToken() ]);
  	
  }
  private function a1($nl) {
  
  		return $nl;
  	
  }
  private function a2() {
   return [ new NewLineToken() ]; 
  }
  private function a3($start, $block) {
  
  		return array_merge( $start, $block );
  	
  }
  private function a4($start, $content) {
  
  		return [$start, $content];
  	
  }
  private function a5($lines) {
  
  		$tokens = array();
  
  		$firstLine = $lines[0];
  		$innerLines = array_slice($lines, 1, -1);
  		$lastLine = end($lines);
  
  		$tokens[] = $firstLine[0];
  		$tokens[] = new OpeningTagToken('p');
  		$tokens[] = $firstLine[1];
  
  		foreach( $innerLines as $innerLinePair )
  		{
  			$paragraph[] = $innerLinePair[0];
  			$paragraph[] = $innerLinePair[1];
  		}
  
  		if( sizeof($lines) > 1 )
  		{
  			$tokens[] = $lastLine[0];
  			$tokens[] = $lastLine[1];
  		}
  		$tokens[] = new ClosingTagToken('p');
  
  		return $tokens;
  	
  }
  private function a6($newline1, $newline2) {
  
  		return array_merge( $newline1, $newline2 );
  	
  }
  private function a7() {
   return $this->endOffset() === 0; 
  }
  private function a8() {
  
  		return [];
  	
  }
  private function a9($inner) {
  
  		$level = $inner['level'];
  		$text = $inner['text'];
  		$trimmedText = trim(trim($text, '='));
  
  		return [
  			new OpeningTagToken('h' . $level),
  			new TextToken($trimmedText),
  			new ClosingTagToken('h' . $level)
  		];
  	
  }
  private function a10($bullets, $content) {
  
  		return array_merge(
  			new MetaToken('ListItem', [ 'bullets' => bullets ]),
  			$content ?: []);
  	
  }
  private function a11($element) {
   return $element; 
  }
  private function a12($content) {
  
  		$lineContents = [];
  		$textBuffer = '';
  		foreach( $content as $element )
  		{
  			if ( is_string( $element ) )
  				$textBuffer .= $element;
  			else
  			{
  				if ( $textBuffer !== '' )
  				{
  					$lineContents[] = new TextToken($textBuffer);
  					$textBuffer = '';
  				}
  				$lineContents[] = $element;
  			}
  		}
  		if ( $textBuffer !== '' )
  			$lineContents[] = new TextToken($textBuffer);
  
  		return $lineContents;
  	
  }
  private function a13($text) {
  
  		return [ new TextToken(implode('', $text)) ];
  	
  }
  private function a14($inner) {
  
  		$inner['level'] += 1;
  		return $inner;
  	
  }
  private function a15($innerText) {
  
  		return [ 'level' => 1, 'text' => $innerText ];
  	
  }
  private function a16($target, $text) {
   return $text; 
  }
  private function a17($target, $exactText) {
  
  		$linkTarget = '/#' . ucwords(str_replace(' ', '_', trim($target)), '_-');
  		$linkText = trim($exactText) ?: trim($target);
  		return [
  			new OpeningTagToken('a', [
  				'href' => $linkTarget
  			]),
  			new TextToken($linkText),
  			new ClosingTagToken('a')
  		];
  	
  }
  private function a18($content) {
  
  		return array_merge(
  			[ new OpeningTagToken('b') ],
  			$content,
  			[ new ClosingTagToken('b') ]
  		);
  	
  }
  private function a19($content) {
  
  		return array_merge(
  			[ new OpeningTagToken('i') ],
  			$content,
  			[ new ClosingTagToken('i') ]
  		);
  	
  }

  // generated
  private function parsestart($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = [];
    for (;;) {
      $r5 = $this->parseblock(true);
      if ($r5!==self::$FAILED) {
        $r4[] = $r5;
      } else {
        break;
      }
    }
    if (count($r4) === 0) {
      $r4 = self::$FAILED;
    }
    // blocks <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $r5
    for (;;) {
      $r6 = $this->discardnewLine(true);
      if ($r6===self::$FAILED) {
        break;
      }
    }
    // free $r6
    $r5 = true;
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $r5
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a0($r4);
    } else {
      if (!$silence) {$this->fail(1);}
    }
    // free $p3
    return $r1;
  }
  private function parseblock($silence) {
    // start choice_1
    $r1 = $this->parseblockLines($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseparagraphLines($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsenewLine($silence);
    // nl <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardanySpacing($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a1($r4);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function discardnewLine($silence) {
    // start choice_1
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "\x0a") {
      $this->currPos++;
      $r1 = "\x0a";
      $this->savedPos = $p2;
      $r1 = $this->a2();
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(2);}
      $r1 = self::$FAILED;
    }
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "\x0d\x0a", $this->currPos, 2, false) === 0) {
      $r1 = "\x0d\x0a";
      $this->currPos += 2;
      $this->savedPos = $p3;
      $r1 = $this->a2();
    } else {
      if (!$silence) {$this->fail(3);}
      $r1 = self::$FAILED;
    }
    choice_1:
    return $r1;
  }
  private function parseblockLines($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsestartOfLine($silence);
    // start <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardanySpacing($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseblockLine($silence);
    // block <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a3($r4, $r6);
    }
    // free $p3
    return $r1;
  }
  private function parseparagraphLines($silence) {
    $p2 = $this->currPos;
    $r3 = [];
    for (;;) {
      $p5 = $this->currPos;
      // start seq_1
      $p6 = $this->currPos;
      $p7 = $this->currPos;
      $r8 = $this->discardparagraphBreak(true);
      if ($r8 === self::$FAILED) {
        $r8 = false;
      } else {
        $r8 = self::$FAILED;
        $this->currPos = $p7;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      // free $p7
      $r9 = $this->parsestartOfLine($silence);
      // start <- $r9
      if ($r9===self::$FAILED) {
        $this->currPos = $p6;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      $r10 = $this->discardanySpacing($silence);
      if ($r10===self::$FAILED) {
        $this->currPos = $p6;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      $r11 = $this->parseinlineContent($silence);
      // content <- $r11
      if ($r11===self::$FAILED) {
        $this->currPos = $p6;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      $r4 = true;
      seq_1:
      if ($r4!==self::$FAILED) {
        $this->savedPos = $p5;
        $r4 = $this->a4($r9, $r11);
        $r3[] = $r4;
      } else {
        break;
      }
      // free $p6
    }
    if (count($r3) === 0) {
      $r3 = self::$FAILED;
    }
    // lines <- $r3
    // free $r4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a5($r3);
    }
    return $r1;
  }
  private function parsenewLine($silence) {
    // start choice_1
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "\x0a") {
      $this->currPos++;
      $r1 = "\x0a";
      $this->savedPos = $p2;
      $r1 = $this->a2();
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(2);}
      $r1 = self::$FAILED;
    }
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "\x0d\x0a", $this->currPos, 2, false) === 0) {
      $r1 = "\x0d\x0a";
      $this->currPos += 2;
      $this->savedPos = $p3;
      $r1 = $this->a2();
    } else {
      if (!$silence) {$this->fail(3);}
      $r1 = self::$FAILED;
    }
    choice_1:
    return $r1;
  }
  private function discardanySpacing($silence) {
    $p1 = $this->currPos;
    for (;;) {
      $r3 = $this->discardspacing($silence);
      if ($r3===self::$FAILED) {
        break;
      }
    }
    // free $r3
    $r2 = true;
    if ($r2!==self::$FAILED) {
      $r2 = substr($this->input, $p1, $this->currPos - $p1);
    } else {
      $r2 = self::$FAILED;
    }
    // free $p1
    return $r2;
  }
  private function parsestartOfLine($silence) {
    // start choice_1
    $r1 = $this->parsenewLine($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parsestartOfFile($silence);
    choice_1:
    return $r1;
  }
  private function parseblockLine($silence) {
    // start choice_1
    $r1 = $this->parseheading($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parselistItem($silence);
    choice_1:
    return $r1;
  }
  private function discardparagraphBreak($silence) {
    // start choice_1
    $p2 = $this->currPos;
    $r1 = $this->discardblockLines(true);
    if ($r1!==self::$FAILED) {
      $r1 = false;
      $this->currPos = $p2;
      goto choice_1;
    }
    // free $p2
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsenewLine($silence);
    // newline1 <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardanySpacing($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parsenewLine($silence);
    // newline2 <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a6($r4, $r6);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parseinlineContent($silence) {
    // start choice_1
    $r1 = $this->parseinlineLine($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseinlineText($silence);
    choice_1:
    return $r1;
  }
  private function discardspacing($silence) {
    $r1 = $this->input[$this->currPos] ?? '';
    if ($r1 === " " || $r1 === "\x09") {
      $this->currPos++;
    } else {
      $r1 = self::$FAILED;
      if (!$silence) {$this->fail(4);}
    }
    return $r1;
  }
  private function parsestartOfFile($silence) {
    $p2 = $this->currPos;
    $this->savedPos = $this->currPos;
    $r1 = $this->a7();
    if ($r1) {
      $r1 = false;
      $this->savedPos = $p2;
      $r1 = $this->a8();
    } else {
      $r1 = self::$FAILED;
    }
    return $r1;
  }
  private function parseheading($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->discardanySpacing($silence);
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "==", $this->currPos, 2, false) === 0) {
      $r5 = "==";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(5);}
      $r5 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseinnerHeadingContent($silence);
    // inner <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "==", $this->currPos, 2, false) === 0) {
      $r7 = "==";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(5);}
      $r7 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->discardanySpacing($silence);
    if ($r8===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a9($r6);
    }
    // free $p3
    return $r1;
  }
  private function parselistItem($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = [];
    for (;;) {
      $r5 = $this->parselistCharacter($silence);
      if ($r5!==self::$FAILED) {
        $r4[] = $r5;
      } else {
        break;
      }
    }
    if (count($r4) === 0) {
      $r4 = self::$FAILED;
    }
    // bullets <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $r5
    $r5 = $this->parseinlineContent($silence);
    if ($r5===self::$FAILED) {
      $r5 = null;
    }
    // content <- $r5
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a10($r4, $r5);
    }
    // free $p3
    return $r1;
  }
  private function discardblockLines($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsestartOfLine($silence);
    // start <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardanySpacing($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseblockLine($silence);
    // block <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a3($r4, $r6);
    }
    // free $p3
    return $r1;
  }
  private function parseinlineLine($silence) {
    $p2 = $this->currPos;
    $r3 = [];
    for (;;) {
      $p5 = $this->currPos;
      // start seq_1
      $p6 = $this->currPos;
      $p7 = $this->currPos;
      $r8 = $this->discardinlineBreak(true);
      if ($r8 === self::$FAILED) {
        $r8 = false;
      } else {
        $r8 = self::$FAILED;
        $this->currPos = $p7;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      // free $p7
      // start choice_1
      $r9 = $this->parseinlineElement($silence);
      if ($r9!==self::$FAILED) {
        goto choice_1;
      }
      $r9 = self::charAt($this->input, $this->currPos);
      if ($r9 !== '' && !($r9 === "\x0d" || $r9 === "\x0a")) {
        $this->currPos += strlen($r9);
      } else {
        $r9 = self::$FAILED;
        if (!$silence) {$this->fail(6);}
      }
      choice_1:
      // element <- $r9
      if ($r9===self::$FAILED) {
        $this->currPos = $p6;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      $r4 = true;
      seq_1:
      if ($r4!==self::$FAILED) {
        $this->savedPos = $p5;
        $r4 = $this->a11($r9);
        $r3[] = $r4;
      } else {
        break;
      }
      // free $p6
    }
    if (count($r3) === 0) {
      $r3 = self::$FAILED;
    }
    // content <- $r3
    // free $r4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a12($r3);
    }
    return $r1;
  }
  private function parseinlineText($silence) {
    $p2 = $this->currPos;
    $r3 = [];
    for (;;) {
      $r4 = self::charAt($this->input, $this->currPos);
      if ($r4 !== '' && !($r4 === "\x0a" || $r4 === "\x0d")) {
        $this->currPos += strlen($r4);
        $r3[] = $r4;
      } else {
        $r4 = self::$FAILED;
        if (!$silence) {$this->fail(7);}
        break;
      }
    }
    if (count($r3) === 0) {
      $r3 = self::$FAILED;
    }
    // text <- $r3
    // free $r4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a13($r3);
    }
    return $r1;
  }
  private function parseinnerHeadingContent($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "=") {
      $this->currPos++;
      $r4 = "=";
    } else {
      if (!$silence) {$this->fail(8);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseinnerHeadingContent($silence);
    // inner <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === "=") {
      $this->currPos++;
      $r6 = "=";
    } else {
      if (!$silence) {$this->fail(8);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a14($r5);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    $p8 = $this->currPos;
    $r7 = $this->discardinlineText($silence);
    // innerText <- $r7
    if ($r7!==self::$FAILED) {
      $r7 = substr($this->input, $p8, $this->currPos - $p8);
    } else {
      $r7 = self::$FAILED;
    }
    // free $p8
    $r1 = $r7;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a15($r7);
    }
    choice_1:
    return $r1;
  }
  private function parselistCharacter($silence) {
    if (strspn($this->input, "*#:;", $this->currPos, 1) !== 0) {
      $r1 = $this->input[$this->currPos++];
    } else {
      $r1 = self::$FAILED;
      if (!$silence) {$this->fail(9);}
    }
    return $r1;
  }
  private function discardinlineBreak($silence) {
    // start choice_1
    $p2 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "[[", $this->currPos, 2, false) === 0) {
      $r1 = "[[";
      $this->currPos += 2;
      $r1 = false;
      $this->currPos = $p2;
      goto choice_1;
    } else {
      $r1 = self::$FAILED;
    }
    // free $p2
    $p2 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "'''", $this->currPos, 3, false) === 0) {
      $r1 = "'''";
      $this->currPos += 3;
      $r1 = false;
      $this->currPos = $p2;
      goto choice_1;
    } else {
      $r1 = self::$FAILED;
    }
    // free $p2
    $p2 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "''", $this->currPos, 2, false) === 0) {
      $r1 = "''";
      $this->currPos += 2;
      $r1 = false;
      $this->currPos = $p2;
      goto choice_1;
    } else {
      $r1 = self::$FAILED;
    }
    // free $p2
    $p2 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "{{", $this->currPos, 2, false) === 0) {
      $r1 = "{{";
      $this->currPos += 2;
      $r1 = false;
      $this->currPos = $p2;
      goto choice_1;
    } else {
      $r1 = self::$FAILED;
    }
    // free $p2
    $p2 = $this->currPos;
    $r1 = $this->discardnewLine(true);
    if ($r1!==self::$FAILED) {
      $r1 = false;
      $this->currPos = $p2;
    }
    // free $p2
    choice_1:
    return $r1;
  }
  private function parseinlineElement($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p4 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "[") {
      $this->currPos++;
      $r5 = "[";
      $r5 = false;
      $this->currPos = $p4;
    } else {
      $r5 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p4
    $r6 = $this->parsewikilink($silence);
    // element <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a11($r6);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p4 = $this->currPos;
    $p7 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "'") {
      $this->currPos++;
      $r8 = "'";
      $r8 = false;
      $this->currPos = $p7;
    } else {
      $r8 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    // free $p7
    $r9 = $this->parsequotedContent($silence);
    // element <- $r9
    if ($r9===self::$FAILED) {
      $this->currPos = $p4;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a11($r9);
    }
    // free $p4
    choice_1:
    return $r1;
  }
  private function discardinlineText($silence) {
    $p2 = $this->currPos;
    $r3 = [];
    for (;;) {
      $r4 = self::charAt($this->input, $this->currPos);
      if ($r4 !== '' && !($r4 === "\x0a" || $r4 === "\x0d")) {
        $this->currPos += strlen($r4);
        $r3[] = $r4;
      } else {
        $r4 = self::$FAILED;
        if (!$silence) {$this->fail(7);}
        break;
      }
    }
    if (count($r3) === 0) {
      $r3 = self::$FAILED;
    }
    // text <- $r3
    // free $r4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a13($r3);
    }
    return $r1;
  }
  private function parsewikilink($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "[[", $this->currPos, 2, false) === 0) {
      $r4 = "[[";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(10);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p6 = $this->currPos;
    for (;;) {
      $r7 = self::charAt($this->input, $this->currPos);
      if ($r7 !== '' && !($r7 === "]" || $r7 === "|")) {
        $this->currPos += strlen($r7);
      } else {
        $r7 = self::$FAILED;
        if (!$silence) {$this->fail(11);}
        break;
      }
    }
    // free $r7
    $r5 = true;
    // target <- $r5
    if ($r5!==self::$FAILED) {
      $r5 = substr($this->input, $p6, $this->currPos - $p6);
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p6
    $p6 = $this->currPos;
    // start seq_2
    $p8 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "|") {
      $this->currPos++;
      $r9 = "|";
    } else {
      if (!$silence) {$this->fail(12);}
      $r9 = self::$FAILED;
      $r7 = self::$FAILED;
      goto seq_2;
    }
    $p11 = $this->currPos;
    $r10 = self::charAt($this->input, $this->currPos);
    // text <- $r10
    if ($r10 !== '' && !($r10 === "]")) {
      $this->currPos += strlen($r10);
      $r10 = substr($this->input, $p11, $this->currPos - $p11);
    } else {
      $r10 = self::$FAILED;
      if (!$silence) {$this->fail(13);}
      $r10 = self::$FAILED;
      $this->currPos = $p8;
      $r7 = self::$FAILED;
      goto seq_2;
    }
    // free $p11
    $r7 = true;
    seq_2:
    if ($r7!==self::$FAILED) {
      $this->savedPos = $p6;
      $r7 = $this->a16($r5, $r10);
    } else {
      $r7 = null;
    }
    // free $p8
    // exactText <- $r7
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "]]", $this->currPos, 2, false) === 0) {
      $r12 = "]]";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(14);}
      $r12 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a17($r5, $r7);
    }
    // free $p3
    return $r1;
  }
  private function parsequotedContent($silence) {
    // start choice_1
    $r1 = $this->parsebold($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseitalics($silence);
    choice_1:
    return $r1;
  }
  private function parsebold($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "'''", $this->currPos, 3, false) === 0) {
      $r4 = "'''";
      $this->currPos += 3;
    } else {
      if (!$silence) {$this->fail(15);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseinlineContent($silence);
    // content <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "'''", $this->currPos, 3, false) === 0) {
      $r6 = "'''";
      $this->currPos += 3;
    } else {
      if (!$silence) {$this->fail(15);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a18($r5);
    }
    // free $p3
    return $r1;
  }
  private function parseitalics($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "''", $this->currPos, 2, false) === 0) {
      $r4 = "''";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(16);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->parseinlineContent($silence);
    // content <- $r5
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "''", $this->currPos, 2, false) === 0) {
      $r6 = "''";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(16);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a19($r5);
    }
    // free $p3
    return $r1;
  }

  public function parse($input, $options = []) {
    $this->initInternal($input, $options);
    $startRule = $options['startRule'] ?? '(DEFAULT)';
    $result = null;

    if (!empty($options['stream'])) {
      switch ($startRule) {
        
        default:
          throw new \WikiPEG\InternalError("Can't stream rule $startRule.");
      }
    } else {
      switch ($startRule) {
        case '(DEFAULT)':
        case "start":
          $result = $this->parsestart(false);
          break;
        default:
          throw new \WikiPEG\InternalError("Can't start parsing from rule $startRule.");
      }
    }

    if ($result !== self::$FAILED && $this->currPos === $this->inputLength) {
      return $result;
    } else {
      if ($result !== self::$FAILED && $this->currPos < $this->inputLength) {
        $this->fail(0);
      }
      throw $this->buildParseException();
    }
  }
}

