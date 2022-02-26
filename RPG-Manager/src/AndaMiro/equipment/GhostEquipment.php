<?php

namespace AndaMiro\equipment;

use pocketmine\item\ItemFactory;

class GhostEquipment extends Equipment{

  public function __construct(){
    parent::__construct(ItemFactory::getInstance()->get(0, 0), 0, "", "", "", 0, 0, 0, 0, "", 0, 0);
  }

  public function getPartsName() : string{
    return "GhostEquipment";
  }
}
