<?php

namespace App\Helpers;

use Carbon\Carbon;
use Exception;

class DateHelper
{
    /**
     * Parse date from multiple formats and return in dd-mm-yyyy format
     *
     * @param string $value
     * @return string|null
     */
    public static function parseToddmmyyyy($value)
    {
        // Handle various empty/null values
        if (is_null($value) || $value === '' || $value === '0000-00-00' || $value === 'null' || trim($value) === '') {
            return null;
        }

        try {
            // Special handling for common 2-digit year patterns like 28-01-28
            if (preg_match('/^\d{2}-\d{2}-\d{2}$/', $value)) {
                return self::handleTwoDigitYearDate($value);
            }

            // Try multiple date formats
            $formats = [
                'd-m-Y',        // 28-02-2027
                'Y-m-d',        // 2027-02-28
                'd/m/Y',        // 28/02/2027
                'Y-m-d H:i:s',  // 2027-02-28 00:00:00
                'd-m-Y H:i:s',  // 28-02-2027 00:00:00
                'm/d/Y',        // 02/28/2027
                'Y/m/d',        // 2027/02/28
                'd.m.Y',        // 28.02.2027
                'Y.m.d',        // 2027.02.28
                'd-m-y',        // 28-01-28 (2-digit year)
                'y-m-d',        // 28-01-28 (2-digit year)
                'd/m/y',        // 28/01/28 (2-digit year)
                'y/m/d',        // 28/01/28 (2-digit year)
                'm/d/y',        // 01/28/28 (2-digit year)
                'd.m.y',        // 28.01.28 (2-digit year)
                'y.m.d',        // 28.01.28 (2-digit year)
            ];

            foreach ($formats as $format) {
                try {
                    $date = Carbon::createFromFormat($format, $value);
                    if ($date) {
                        // For 2-digit years, apply smart interpretation
                        if (strpos($format, 'y') !== false && !strpos($format, 'Y')) {
                            $date = self::adjustTwoDigitYear($date);
                        }
                        return $date->format('d-m-Y');
                    }
                } catch (Exception $e) {
                    continue;
                }
            }

            // If all specific formats fail, try Carbon's general parsing
            try {
                $date = Carbon::parse($value);
                return $date->format('d-m-Y');
            } catch (Exception $e) {
                return null;
            }

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Handle specific 2-digit year date format like 28-01-28
     *
     * @param string $value
     * @return string|null
     */
    private static function handleTwoDigitYearDate($value)
    {
        try {
            // Split the date parts
            $parts = explode('-', $value);
            if (count($parts) !== 3) {
                return null;
            }

            $day = intval($parts[0]);
            $month = intval($parts[1]);
            $twoDigitYear = intval($parts[2]);

            // Apply smart year interpretation
            // Years 00-50 assume 21st century (2000-2050)
            // Years 51-99 assume 20th century (1951-1999)
            if ($twoDigitYear <= 50) {
                $fullYear = 2000 + $twoDigitYear;
            } else {
                $fullYear = 1900 + $twoDigitYear;
            }

            // Validate the date
            if (checkdate($month, $day, $fullYear)) {
                return sprintf('%02d-%02d-%04d', $day, $month, $fullYear);
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Adjust 2-digit year interpretation intelligently
     * Years 00-50 -> 20xx, Years 51-99 -> 19xx
     *
     * @param Carbon $date
     * @return Carbon
     */
    private static function adjustTwoDigitYear($date)
    {
        $year = $date->year;

        // Carbon may interpret 2-digit years differently
        // Check if year needs adjustment (less than 1970 or very large)
        if ($year < 1970 || $year > 2050) {
            // Extract the 2-digit year from the original format
            $twoDigitYear = $year % 100;

            // Years 00-50 assume 21st century (2000-2050)
            // Years 51-99 assume 20th century (1951-1999)
            if ($twoDigitYear <= 50) {
                $date->year = 2000 + $twoDigitYear;
            } else {
                $date->year = 1900 + $twoDigitYear;
            }
        }

        return $date;
    }

    /**
     * Validate if a string is a valid date
     *
     * @param string $value
     * @return bool
     */
    public static function isValidDate($value)
    {
        return self::parseToddmmyyyy($value) !== null;
    }
}