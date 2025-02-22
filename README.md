<h1 align="center">Morse Code Library</h1>

<p align="center">A modern PHP library for encoding and decoding Morse code with extended configuration options.</p>

<p align="center">
  <a href="https://github.com/ramazancetinkaya/morse-code">
    <img src="logo.webp" alt="Logo">
  </a>

  <p align="center">
    <a href="https://github.com/ramazancetinkaya/morse-code/issues">Report a Bug</a>
    ·
    <a href="https://github.com/ramazancetinkaya/morse-code/pulls">New Pull Request</a>
  </p>
</p>

## Features

- Encode and decode Morse code seamlessly.
- Customizable delimiters for letters and words.
- Multiple handling options for unknown characters.
- Configurable case preservation.
- Structured error handling with custom exceptions.
- Fully object-oriented and extensible.

## Installation

This library can be easily installed using [Composer](https://getcomposer.org/), a modern PHP dependency manager.

### Step 1: Install Composer

If you don't have Composer installed, you can download and install it by following the instructions on the [official Composer website](https://getcomposer.org/download/).

### Step 2: Install the Library

Once Composer is installed, you can install the `morse-code` library by running the following command in your project's root directory:

```bash
composer require ramazancetinkaya/morse-code
```

_Alternatively, download the source code and include it in your project manually._

### Requirements

- PHP 8.0 or higher.
- No additional dependencies.

## Usage

```php
require 'vendor/autoload.php'; // Include Composer's autoloader

use ramazancetinkaya\{MorseTranslator, MorseCodeConfig, UnknownCharHandling};

// Create a configuration where unknown characters are replaced with '?'
// and we separate letters with a single space, words with ' / ', 
// and DO NOT preserve original case (defaults to uppercase).
$config = new MorseCodeConfig(
    unknownCharHandling: UnknownCharHandling::REPLACE,
    replacementChar: '?',
    preserveCase: false,
    letterDelimiter: ' ',  // single space between letters
    wordDelimiter: ' / '   // slash and spaces between words
);

// Create the translator
$translator = new MorseTranslator();

// Sample text to encode
$text = "Hello, World!";

try {
    // Encoding
    $encoded = $translator->encode($text, $config);
    echo "Original: {$text}\n";
    echo "Encoded:  {$encoded}\n";

    // Decoding
    $decoded = $translator->decode($encoded, $config);
    echo "Decoded:  {$decoded}\n";
} catch (MorseCodeException $exception) {
    // Handle or log the exception
    echo "Morse Code Error: " . $exception->getMessage() . "\n";
}
```

## Configuration Options

| Option               | Description |
|----------------------|-------------|
| `unknownCharHandling` | Defines how unknown characters are handled (`IGNORE`, `REPLACE`, `THROW_EXCEPTION`). |
| `replacementChar`    | Specifies the character used when `REPLACE` mode is enabled. |
| `preserveCase`       | If `true`, preserves original case; otherwise, converts text to uppercase. |
| `letterDelimiter`    | Defines the separator between Morse code letters. |
| `wordDelimiter`      | Defines the separator between Morse code words. |

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue for any enhancements or bug fixes.

## Author

Developed by [Ramazan Çetinkaya](https://github.com/ramazancetinkaya).
