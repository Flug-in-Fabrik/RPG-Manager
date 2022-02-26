<?php

namespace AndaMiro\Command;

use pocketmine\command\{Command, CommandSender};

use pocketmine\form\Form;

use pocketmine\player\Player;

use AndaMiro\stat\{StatManager, Stat};
use AndaMiro\RpgManager;

class StatCommand extends Command{

  public function __construct(){
    parent::__construct("스탯", "스탯 관련 명령어입니다.", "/스탯", []);
  }

  public function execute(CommandSender $sender, string $command, array $args) : void{
    $sender->sendForm(new class($sender) implements Form{

      private $player;

      private $options = [];

      public function __construct(Player $player){
        $this->player = $player;
        $statManager = StatManager::getStatManager($player);
        $this->options = ["공격력" => $statManager->getAtk(), "마법" => $statManager->getAp(), "방어력" => $statManager->getDef(), "민첩" => $statManager->getAa()];
      }

      public function jsonSerialize(){
        $statManager = StatManager::getStatManager($this->player);
        $info = "현재 포인트 : " . $statManager->getPoint();
        $info .= "\n현재 공격력 스탯 : " . $statManager->getAtk()->getLevel();
        $info .= "\n현재 마법 스탯 : " . $statManager->getAp()->getLevel();
        $info .= "\n현재 방어력 스탯 : " . $statManager->getDef()->getLevel();
        $info .= "\n현재 민첩 스탯 : " . $statManager->getAa()->getLevel();
        $info .= "\n\n현재 스탯 데미지 : " . $statManager->getStatDamage();
        $info .= "\n현재 스탯 스킬 데미지 : " . $statManager->getStatAbilityDamage();
        $info .= "\n현재 스탯 방어력 : " . $statManager->getStatDefense();
        $info .= "\n현재 스탯 체력 : " . $statManager->getStatHealth();
        $info .= "\n현재 스탯 크리티컬 데미지 : " . $statManager->getStatCriticalDamage();
        $info .= "\n현재 스탯 크리티컬 확률 : " . $statManager->getStatCriticalPercent();

        return [
          "type" => "custom_form",
          "title" => "스탯",
          "content" => [
            [
              "type" => "dropdown",
              "text" => $info . "\n\n올릴 스탯",
              "options" => array_keys($this->options)
            ],
            [
              "type" => "slider",
              "text" => "올릴 양",
              "min" => 0,
              "max" => StatManager::getStatManager($this->player)->getPoint() / Stat::getPointPerStat()
            ]
          ]
        ];
      }

      public function handleResponse(Player $player, $data) : void{
        if(is_null($data))
        return;

        if($data[1] == 0) return;

        $stat = array_values($this->options)[$data[0]];
        $stat->addLevel($player, $data[1]);
        RpgManager::sendMessage($player, "levelup-stat");
      }
    });
    return;
  }
}
