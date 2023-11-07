<?php
namespace App\Service;
use DateTime;
class DateFormatter
{
    public function format(DateTime $created): string
    {
        $now = new DateTime();//heure actuelle
        $diff = $now->diff($created);//fonction intégré en php 
        if($diff->y > 0 )
        {
            return $diff->y.' '.($diff->y > 1 ? 'years':'year');
        }
        if($diff->m > 0 )
        {
            return $diff->m.' '.($diff->m > 1 ? 'months':'month');
        }
        if($diff->d > 0 )
        {
            return $diff->d.' '.($diff->d > 1 ? 'days':'day');
        }
        if($diff->h > 0 )
        {
            return $diff->h.' '.($diff->h > 1 ? 'hours':'hour');
        }
        if($diff->i > 0 )
        {
            return $diff->i.' '.($diff->i > 1 ? 'minutes':'minute');
        }
return 'Now';
    } 
}
?>