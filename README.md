# Morse Code

A simple PHP library for converting text to Morse code and vice versa

## Installation

This library can be easily installed using [Composer](https://getcomposer.org/), a modern PHP dependency manager.

### Step 1: Install Composer

If you don't have Composer installed, you can download and install it by following the instructions on the [official Composer website](https://getcomposer.org/download/).

### Step 2: Install the Library

Once Composer is installed, you can install the `morse-code` library by running the following command in your project's root directory:

```bash
composer require ramazancetinkaya/morse-code
```

## Usage

```php
require_once 'vendor/autoload.php'; // Include Composer's autoloader

use ramazancetinkaya\MorseCode;
```

```php
try {
    $text = "Hello World";
    $morse = MorseCode::textToMorse($text);
    echo "Text to Morse: " . $morse . "\n";

    $originalText = MorseCode::morseToText($morse);
    echo "Morse to Text: " . $originalText . "\n";
} catch (InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```
