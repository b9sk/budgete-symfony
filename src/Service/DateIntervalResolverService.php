<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 31.01.20
 * Time: 16:24
 */

namespace App\Service;


class DateIntervalResolverService
{
    const FORMAT = 'Y-m-d H:i:s';
    
    /**
     * @return string[]
     * @throws \Exception
     */
    public function getLastWeek()
    {
        $dateStart = new \DateTime('midnight');
        $dateStart = $dateStart->format(self::FORMAT);

        $dateEnd = new \DateTime('-7 days midnight');
        $dateEnd = $dateEnd->format(self::FORMAT);
        
        return ['start' => $dateStart, 'end' => $dateEnd];
    }
    
    public function getLastMonth()
    {
        $dateStart = new \DateTime('midnight');
        $dateStart = $dateStart->format(self::FORMAT);
    
        $dateEnd = new \DateTime('last month midnight');
        $dateEnd = $dateEnd->format(self::FORMAT);
    
        return ['start' => $dateStart, 'end' => $dateEnd];
    }
}