<?php

namespace WapplerSystems\FormExtended\DataType;



class Time
{


    public static function createFromFormat($dateFormat, $dateAsString)
    {

        list($hours, $minutes) = explode(':', $dateAsString);
        $timeAsInt = (int)$hours * 100 + (int)$minutes;

        return $timeAsInt;

    }

}
