<?php
namespace common\components;

class DurationConverter
{
    /**
     * @param $stringTime
     *
     * @return int
     */
    public static function calculateTime($stringTime)
    {
        $totalTime = 0;
        $unitTypes = [];
        $unitTypes['m'] = 60;
        $unitTypes['h'] = $unitTypes['m'] * 60;
        $unitTypes['d'] = $unitTypes['h'] * 24;
        $unitTypes['w'] = $unitTypes['d'] * 7;

        $timeVariables = explode(' ', $stringTime);
        foreach ($timeVariables as $variable) {
            $value = (int)$variable;
            $units = trim(str_replace($value, '', $variable));

            if (!empty($unitTypes[$units])) {
                $totalTime += $value * $unitTypes[$units];
            }
        }

        return $totalTime;
    }

    public static function shortDuration($duration)
    {
        return str_replace([' seconds', ' second', ' minutes', ' minute', ' hours', ' hour', ' days', ' day', ','],
            ['s', 's', 'm', 'm', 'h', 'h', 'd', 'd', ''], $duration);
    }

    public static function convertArrayString($array)
    {
        return empty($array) ? null : implode(', ', $array);
    }

}