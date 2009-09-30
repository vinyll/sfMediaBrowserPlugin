<?php

/*
 * This file is part of the sfMediaBrowser package.
 *
 * (c) 2009 Vincent Agnano <vincent.agnano@particul.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package sfMediaBrowser
 * @author Vincent Agnano <vincent.agnano@particul.es>
 *
 */
class sfMediaBrowserStringUtils
{
  public static function slugify($text)
  {
    $str = strtolower($text);

    $str = str_replace(
      array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'),
      array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'),
      $str
    );

     // strip all non word chars
    $str = preg_replace('/\W/', ' ', $str);

    // replace all white space sections with a dash
    $str = preg_replace('/\ +/', '-', $str);

    // trim dashes
    $str = preg_replace('/\-$/', '', $str);
    $str = preg_replace('/^\-/', '', $str);

    return $str;
  }
}