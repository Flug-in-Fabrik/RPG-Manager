<?php

namespace AndaMiro;

final class Manager{

  public static function getAllFiles(string $src) : array{
    $folders = [];
    $dir = opendir($src);
    while(!empty($filename = readdir($dir))){
      if($filename != "." && $filename != ".."){
        $folders[] = $filename;
      }
    }
    closedir($dir);
    return $folders;
  }
}
