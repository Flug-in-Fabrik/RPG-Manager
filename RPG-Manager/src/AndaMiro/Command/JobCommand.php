<?php

namespace AndaMiro\Command;

use pocketmine\command\{Command, CommandSender};

use pocketmine\player\Player;

use pocketmine\form\Form;

use AndaMiro\job\{JobManager, Warrior, Archer, Wizard, Assassin};

use AndaMiro\RpgManager;

class JobCommand extends Command{

  public function __construct(){
    parent::__construct("전직", "전직 관련 명령어입니다.", "전직", []);
  }

  public function execute(CommandSender $sender, string $command, array $args) : void{
    if(JobManager::getJobManager($sender)->getJob()->getName() !== "Citizen"){
      RpgManager::sendMessage($sender, "already-has-job");
      return;
    }
    $sender->sendForm(new class() implements Form{

      public function jsonSerialize(){
        $arr = [];

        array_push($arr, array("text" => "전사"));
        array_push($arr, array("text" => "궁수"));
        array_push($arr, array("text" => "마법사"));
        array_push($arr, array("text" => "암살자"));

        return [
          "type" => "form",
          "title" => "전직",
          "content" => "전직할 직업을 선택해 주세요",
          "buttons" => $arr
        ];
      }

      public function handleResponse(Player $player, $data) : void{
        if(is_null($data))
        return;

        if($data == 0) JobManager::getJobManager($player)->setJob(new Warrior());
        if($data == 1) JobManager::getJobManager($player)->setJob(new Archer());
        if($data == 2) JobManager::getJobManager($player)->setJob(new Wizard());
        if($data == 3) JobManager::getJobManager($player)->setJob(new Assassin());

        RpgManager::sendMessage($player, "set-job");
      }
    });
  }
}
