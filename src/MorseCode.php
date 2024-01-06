<?php

/**
 * Class MorseCode
 *
 * The MorseCode class provides functionality to convert text to Morse code and vice versa.
 *
 * @category   Text Processing
 * @package    MorseCode
 * @author     Ramazan Çetinkaya <ramazancetinkayadev@outlook.com>
 * @license    MIT License (https://opensource.org/licenses/MIT)
 * @version    1.0.0
 * @link       https://github.com/ramazancetinkaya/morse-code
 */

namespace ramazancetinkaya;

final class MorseCode
{
    /**
     * @var array Morse code mapping for characters.
     */
    private const MORSE_CODE_MAP = [
        'A' => '.-', 'B' => '-...', 'C' => '-.-.', 'D' => '-..', 'E' => '.',
        'F' => '..-.', 'G' => '--.', 'H' => '....', 'I' => '..', 'J' => '.---',
        'K' => '-.-', 'L' => '.-..', 'M' => '--', 'N' => '-.', 'O' => '---',
        'P' => '.--.', 'Q' => '--.-', 'R' => '.-.', 'S' => '...', 'T' => '-',
        'U' => '..-', 'V' => '...-', 'W' => '.--', 'X' => '-..-', 'Y' => '-.--',
        'Z' => '--..', '1' => '.----', '2' => '..---', '3' => '...--', '4' => '....-',
        '5' => '.....', '6' => '-....', '7' => '--...', '8' => '---..', '9' => '----.',
        '0' => '-----',
    ];

    /**
     * Converts a given text to Morse code.
     *
     * @param string $text The text to convert to Morse code.
     * @return string The Morse code representation of the text.
     */
    public static function textToMorse(string $text): string
    {
        $text = strtoupper($text);
        $words = explode(' ', $text);
        $morseCode = [];

        foreach ($words as $word) {
            $chars = str_split($word);
            $morseWord = [];

            foreach ($chars as $char) {
                if (isset(self::MORSE_CODE_MAP[$char])) {
                    $morseWord[] = self::MORSE_CODE_MAP[$char];
                } else {
                    throw new InvalidArgumentException("Invalid character: $char");
                }
            }

            if (!empty($morseWord)) {
                $morseCode[] = implode(' ', $morseWord);
            }
        }

        return implode(' / ', $morseCode);
    }

    /**
     * Converts Morse code to plain text.
     *
     * @param string $morse The Morse code to convert to text.
     * @return string The plain text representation of the Morse code.
     */
    public static function morseToText(string $morse): string
    {
        $morseWords = explode(' / ', $morse);
        $text = [];

        foreach ($morseWords as $morseWord) {
            $morseChars = explode(' ', $morseWord);
            $word = [];

            foreach ($morseChars as $morseChar) {
                $char = array_search($morseChar, self::MORSE_CODE_MAP);
                if ($char !== false) {
                    $word[] = $char;
                } else {
                    throw new InvalidArgumentException("Invalid Morse code: $morseChar");
                }
            }

            if (!empty($word)) {
                $text[] = implode('', $word);
            }
        }

        return implode(' ', $text);
    }

}
