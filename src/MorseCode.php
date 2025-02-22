<?php

/**
 * Morse Code Library
 *
 * Morse code is a method of encoding text into a series of dots and dashes,
 * used historically for long-distance communication. This library offers a
 * modern, structured, and extensible approach to encoding and decoding Morse
 * code in PHP 8, prioritizing security and maintainability.
 *
 * @category Communication
 * @package  MorseCode
 * @author   Ramazan Çetinkaya
 * @license  MIT License <https://opensource.org/licenses/MIT>
 * @version  1.0.0
 * @link     https://github.com/ramazancetinkaya/morse-code
 *
 * @see https://en.wikipedia.org/wiki/Morse_code
 */

namespace ramazancetinkaya;

/**
 * Enum that defines how to handle unknown characters when encoding/decoding.
 *
 * @package MorseCode
 */
enum UnknownCharHandling: string
{
    /**
     * If an unknown character is encountered, skip it entirely (omit).
     */
    case IGNORE = 'ignore';

    /**
     * If an unknown character is encountered, replace it with a placeholder character.
     */
    case REPLACE = 'replace';

    /**
     * If an unknown character is encountered, throw an exception.
     */
    case THROW_EXCEPTION = 'throw_exception';
}

/**
 * Custom exception class for Morse Code errors.
 *
 * @package MorseCode
 */
class MorseCodeException extends \Exception
{
    // Reserved for future enhancements or custom exception behavior.
}

/**
 * This class provides the core mapping between characters and Morse code representations.
 * It also generates the reverse mapping from Morse code to characters.
 *
 * @package MorseCode
 */
class MorseCodeDictionary
{
    /**
     * @var array<string, string> Maps individual characters (A, B, 1, etc.) to Morse code (.-, -..., etc.)
     */
    private static array $charToMorseMap = [
        'A' => '.-',    'B' => '-...',  'C' => '-.-.',  'D' => '-..',
        'E' => '.',     'F' => '..-.',  'G' => '--.',   'H' => '....',
        'I' => '..',    'J' => '.---',  'K' => '-.-',   'L' => '.-..',
        'M' => '--',    'N' => '-.',    'O' => '---',   'P' => '.--.',
        'Q' => '--.-',  'R' => '.-.',   'S' => '...',   'T' => '-',
        'U' => '..-',   'V' => '...-',  'W' => '.--',   'X' => '-..-',
        'Y' => '-.--',  'Z' => '--..',
        '0' => '-----', '1' => '.----', '2' => '..---', '3' => '...--',
        '4' => '....-', '5' => '.....', '6' => '-....', '7' => '--...',
        '8' => '---..', '9' => '----.',
        '.' => '.-.-.-', ',' => '--..--', '?' => '..--..', ':' => '---...',
        ';' => '-.-.-.', '!' => '-.-.--', '-' => '-....-', '/' => '-..-.',
        '@' => '.--.-.', '(' => '-.--.',  ')' => '-.--.-', '&' => '.-...',
        '=' => '-...-',  '+' => '.-.-.'
    ];

    /**
     * @var array<string, string> Maps Morse code (.-, -..., etc.) back to individual characters (A, B, 1, etc.)
     */
    private static array $morseToCharMap = [];

    /**
     * Initializes the reverse mapping from Morse code to character.
     * This should be called once, typically on library load.
     *
     * @return void
     */
    public static function init(): void
    {
        if (empty(self::$morseToCharMap)) {
            foreach (self::$charToMorseMap as $char => $morse) {
                self::$morseToCharMap[$morse] = $char;
            }
        }
    }

    /**
     * Returns the Morse code representation of a character.
     *
     * @param string $char The character to map (already uppercase recommended).
     * @return string|null The corresponding Morse code or null if not found.
     */
    public static function getMorseCodeForChar(string $char): ?string
    {
        return self::$charToMorseMap[$char] ?? null;
    }

    /**
     * Returns the character representation of a Morse code token.
     *
     * @param string $morse The Morse code token (e.g. "-.-").
     * @return string|null The corresponding character or null if not found.
     */
    public static function getCharForMorseCode(string $morse): ?string
    {
        return self::$morseToCharMap[$morse] ?? null;
    }
}

// Initialize the dictionary mapping
MorseCodeDictionary::init();

/**
 * Configuration class for customizing the encoding/decoding process.
 *
 * @package MorseCode
 */
class MorseCodeConfig
{
    /**
     * @param UnknownCharHandling $unknownCharHandling How to handle unknown characters.
     * @param string              $replacementChar     Character used when unknownCharHandling = REPLACE.
     * @param bool                $preserveCase        Whether to preserve the original case of the text.
     *                                                 (If false, everything is converted to uppercase for encoding.)
     * @param string              $letterDelimiter     Delimiter inserted between letters in Morse code.
     * @param string              $wordDelimiter       Delimiter inserted between words in Morse code.
     */
    public function __construct(
        private UnknownCharHandling $unknownCharHandling = UnknownCharHandling::THROW_EXCEPTION,
        private string $replacementChar = '?',
        private bool $preserveCase = false,
        private string $letterDelimiter = ' ',
        private string $wordDelimiter = ' / '
    ) {
    }

    /**
     * @return UnknownCharHandling
     */
    public function getUnknownCharHandling(): UnknownCharHandling
    {
        return $this->unknownCharHandling;
    }

    /**
     * @return string
     */
    public function getReplacementChar(): string
    {
        return $this->replacementChar;
    }

    /**
     * @return bool
     */
    public function shouldPreserveCase(): bool
    {
        return $this->preserveCase;
    }

    /**
     * @return string
     */
    public function getLetterDelimiter(): string
    {
        return $this->letterDelimiter;
    }

    /**
     * @return string
     */
    public function getWordDelimiter(): string
    {
        return $this->wordDelimiter;
    }
}

/**
 * A class responsible for encoding plain text into Morse code.
 *
 * @package MorseCode
 */
class MorseEncoder
{
    /**
     * Encode a plain text string into Morse code.
     * This method splits text into words, encodes each word's letters,
     * and joins them using configured delimiters.
     *
     * @param string          $text   The plain text to encode.
     * @param MorseCodeConfig $config Configuration controlling behavior and delimiters.
     *
     * @return string The resulting Morse code.
     *
     * @throws MorseCodeException If an unknown character is encountered and THROW_EXCEPTION is set.
     */
    public function encode(string $text, MorseCodeConfig $config): string
    {
        // If case is not preserved, convert to uppercase
        $preparedText = $config->shouldPreserveCase() ? $text : mb_strtoupper($text);

        // Split the text into words by whitespace
        $words   = preg_split('/\s+/', trim($preparedText)) ?: [];
        $encoded = [];

        foreach ($words as $word) {
            $letters = $this->encodeWord($word, $config);
            // Join letters with the letter delimiter
            $encoded[] = implode($config->getLetterDelimiter(), $letters);
        }

        // Join words with the word delimiter
        return implode($config->getWordDelimiter(), $encoded);
    }

    /**
     * Encode a single word into an array of Morse code tokens (one per letter).
     *
     * @param string          $word   Word to encode (already trimmed).
     * @param MorseCodeConfig $config Configuration controlling unknown characters.
     *
     * @return string[] Array of Morse code tokens.
     *
     * @throws MorseCodeException If an unknown character is encountered and THROW_EXCEPTION is set.
     */
    private function encodeWord(string $word, MorseCodeConfig $config): array
    {
        $morseTokens = [];

        for ($i = 0, $length = mb_strlen($word); $i < $length; $i++) {
            $char       = mb_substr($word, $i, 1);
            $morseValue = MorseCodeDictionary::getMorseCodeForChar($char);

            if ($morseValue === null) {
                // Handle unknown char scenario
                switch ($config->getUnknownCharHandling()) {
                    case UnknownCharHandling::IGNORE:
                        // Skip entirely
                        continue 2;

                    case UnknownCharHandling::REPLACE:
                        // Use the replacement character
                        $morseTokens[] = $config->getReplacementChar();
                        continue 2;

                    case UnknownCharHandling::THROW_EXCEPTION:
                        throw new MorseCodeException(
                            sprintf('Unknown character encountered during encoding: "%s"', $char)
                        );
                }
            }

            $morseTokens[] = $morseValue;
        }

        return $morseTokens;
    }
}

/**
 * A class responsible for decoding Morse code into plain text.
 *
 * @package MorseCode
 */
class MorseDecoder
{
    /**
     * Decode a Morse code string back into plain text.
     * This method splits Morse code into word tokens, then splits those word tokens into
     * letter tokens, and reconstructs the original text.
     *
     * @param string          $morseCode The Morse code string to decode.
     * @param MorseCodeConfig $config    Decoding configuration (delimiters, unknown handling, etc.).
     *
     * @return string Plain text decoded from the Morse code.
     *
     * @throws MorseCodeException If an unknown Morse token is encountered and THROW_EXCEPTION is set.
     */
    public function decode(string $morseCode, MorseCodeConfig $config): string
    {
        $trimmed = trim($morseCode);

        if ($trimmed === '') {
            return '';
        }

        // Split Morse code into word blocks
        $wordTokens = explode($config->getWordDelimiter(), $trimmed);
        $decodedWords = [];

        foreach ($wordTokens as $wordToken) {
            $decodedWords[] = $this->decodeWord($wordToken, $config);
        }

        // Join the decoded words with a space (to form a sentence)
        return implode(' ', $decodedWords);
    }

    /**
     * Decode a single word's Morse code (e.g. multiple letters joined by letterDelimiter).
     *
     * @param string          $wordToken Morse code representing a single word.
     * @param MorseCodeConfig $config    Configuration for decoding.
     *
     * @return string Decoded plain text word.
     *
     * @throws MorseCodeException If an unknown Morse token is encountered and THROW_EXCEPTION is set.
     */
    private function decodeWord(string $wordToken, MorseCodeConfig $config): string
    {
        $letterTokens  = explode($config->getLetterDelimiter(), $wordToken);
        $decodedLetters = [];

        foreach ($letterTokens as $token) {
            $char = MorseCodeDictionary::getCharForMorseCode($token);

            // Unknown Morse code sequence handling
            if ($char === null) {
                switch ($config->getUnknownCharHandling()) {
                    case UnknownCharHandling::IGNORE:
                        // Skip this token
                        continue 2;

                    case UnknownCharHandling::REPLACE:
                        $decodedLetters[] = $config->getReplacementChar();
                        continue 2;

                    case UnknownCharHandling::THROW_EXCEPTION:
                        throw new MorseCodeException(
                            sprintf('Unknown Morse code token encountered: "%s"', $token)
                        );
                }
            }

            $decodedLetters[] = $char;
        }

        return implode('', $decodedLetters);
    }
}

/**
 * A unified facade that provides easy access to encoding and decoding functionalities.
 *
 * @package MorseCode
 */
class MorseTranslator
{
    /**
     * @var MorseEncoder
     */
    private MorseEncoder $encoder;

    /**
     * @var MorseDecoder
     */
    private MorseDecoder $decoder;

    /**
     * MorseTranslator constructor.
     *
     * @param MorseEncoder|null $encoder Custom encoder; if null, a default encoder is used.
     * @param MorseDecoder|null $decoder Custom decoder; if null, a default decoder is used.
     */
    public function __construct(MorseEncoder $encoder = null, MorseDecoder $decoder = null)
    {
        $this->encoder = $encoder ?? new MorseEncoder();
        $this->decoder = $decoder ?? new MorseDecoder();
    }

    /**
     * Encode plain text into Morse code.
     *
     * @param string          $text   The text to encode.
     * @param MorseCodeConfig $config Configuration object.
     *
     * @return string The resulting Morse code.
     *
     * @throws MorseCodeException
     */
    public function encode(string $text, MorseCodeConfig $config): string
    {
        return $this->encoder->encode($text, $config);
    }

    /**
     * Decode Morse code into plain text.
     *
     * @param string          $morseCode The Morse code to decode.
     * @param MorseCodeConfig $config    Configuration object.
     *
     * @return string The decoded plain text.
     *
     * @throws MorseCodeException
     */
    public function decode(string $morseCode, MorseCodeConfig $config): string
    {
        return $this->decoder->decode($morseCode, $config);
    }
}
