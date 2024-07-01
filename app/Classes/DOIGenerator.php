<?php

namespace App\Classes;

use Dflydev\Base32\Crockford\Crockford;

class DOIGenerator
{
    // 32 by the factor of 6
    protected const UPPER_LIMIT = 1073741823;

    public static function generate(): string
    {
        $prefix = app()->getCurrentConference()->getMeta('doi_prefix');
        $suffix = static::encodeSuffix();

        return $prefix . '/' . $suffix;
    }

    /**
     * Constructs a DOI with an 8-character suffix, using a Crockford Base 32 algorithm
     * to generate the suffix.
     *
     */
    public static function encodeSuffix(): string
    {
        $number = random_int(1, static::UPPER_LIMIT);

        return static::base32EncodeSuffix($number);
    }

    /**
     * Returns the decoded int used to generate the suffix after validating the two-digit checksum
     *
     * @return int|null Returns null if checksum is invalid
     */
    public static function decodeSuffix(string $suffix): ?int
    {
        $suffix = strtoupper($suffix);
        $suffixParts = str_split($suffix, 6);
        $encodedString = $suffixParts[0];
        $checksum = $suffixParts[1];

        $decodedSuffix = Crockford::decode($encodedString);

        $isSuffixValid = static::verifySuffixChecksum($decodedSuffix, $checksum);

        return $isSuffixValid ? $decodedSuffix : null;
    }

    /**
     * Encodes suffix as 8-digit base32 encoded string where the final two numbers are a checksum.
     *
     * E.g. DDDDDDYY where 'D' is a base32 encoded character and 'YY' is the checksum.
     *
     * @param int $number A random number between 1 and 1073741823 (UPPER_LIMIT). Used as seed for encoding suffix.
     */
    protected static function base32EncodeSuffix(int $number): string
    {
        // Initial base32 encoded string (up to 6 characters max)
        $encodedNumber = strtolower(Crockford::encode($number));

        // Add checksum at end of string, calculated as modulo 97-10 (ISO 7064)
        $remainder = static::calculateChecksum($number);
        $payload = $encodedNumber . sprintf('%02d', $remainder);

        return str_pad($payload, 8, '0', STR_PAD_LEFT);
    }


    /**
     * Verifies the provided checksum was generated from the number provided.
     *
     * @param int $number The integer decoded form the base 32 suffix. Original number used to generate suffix.
     * @param int $checksum The two-digit checksum (last two digits of suffix)
     */
    protected  static function verifySuffixChecksum(int $number, int $checksum): bool
    {
        return $checksum === static::calculateChecksum($number);
    }

    /**
     * Checksum calculated as modulo 97-10 (ISO 7064).
     */
    protected static function calculateChecksum(int $number): int
    {
        return 98 - (($number * 100) % 97);
    }
}
