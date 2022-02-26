<?php

namespace AndaMiro\Command;

use pocketmine\command\{Command, CommandSender};

use pocketmine\player\Player;

use pocketmine\form\Form;

use AndaMiro\equipment\{EquipmentManager, Equipment, Helmet, ChestPlate, Leggins, Boots, Weapon, AssistantWeapon};

class EquipmentCommand extends Command{

  public function __construct(){
    parent::__construct("장비", "장비 관련 명령어입니다.", "장비", []);
  }

  public function execute(CommandSender $sender, string $command, array $args) : void{
    if(!$sender->getServer()->isOp($sender->getName())) return;
    if(!isset($args[0])){
      $sender->sendMessage("/장비 <생성, 목록>");
      return;
    }
    switch($args[0]){
      case "생성" :
      if($sender->getInventory()->getItemInHand()->getId() == 0){
        $sender->sendMessage("공기는 장비로 생성될 수 없습니다.");
        return;
      }

      $sender->sendForm(new class() implements Form{

        private const RANK = ["레전더리", "유니크", "레어", "일반"];
        private const JOB = ["Warrior", "Archer", "Wizard", "Assassin"];
        private const PARTS = ["Helmet", "ChestPlate", "Leggins", "Boots", "Weapon", "AssistantWeapon"];

        public function jsonSerialize(){
          return [
            "type" => "custom_form",
            "title" => "장비 생성",
            "content" => [
              [
                "type" => "input",
                "text" => "이름"
              ],
              [
                "type" => "dropdown",
                "text" => "부위",
                "options" => ["헬멧", "흉갑", "바지", "부츠", "무기", "장비구"]
              ],
              [
                "type" => "dropdown",
                "text" => "등급",
                "options" => $this::RANK
              ],
              [
                "type" => "input",
                "text" => "설명"
              ],
              [
                "type" => "input",
                "text" => "공격력",
                "default" => "0"
              ],
              [
                "type" => "input",
                "text" => "마법",
                "default" => "0"
              ],
              [
                "type" => "input",
                "text" => "방어력",
                "default" => "0"
              ],
              [
                "type" => "input",
                "text" => "민첩",
                "default" => "0"
              ],
              [
                "type" => "dropdown",
                "text" => "직업전용",
                "options" => ["전사", "궁수", "마법사", "암살자"]
              ],
              [
                "type" => "input",
                "text" => "레벨제한 (MIN)",
                "default" => "0"
              ],
              [
                "type" => "input",
                "text" => "레벨제한 (MAX)",
                "default" => "0"
              ]
            ]
          ];
        }

        public function handleResponse(Player $player, $data) : void{
          if(is_null($data))
          return;

          $item = $player->getInventory()->getItemInHand();

          switch($this::PARTS[$data[1]]){
            case "Helmet" :
            EquipmentManager::addEquipment(new Helmet($item, time(), (string)$data[0], $this::RANK[$data[2]], (string)$data[3], (int)$data[4], (int)$data[5], (int)$data[6], (int)$data[7], $this::JOB[$data[8]], (int)$data[9], (int)$data[10]));
            break;

            case "ChestPlate" :
            EquipmentManager::addEquipment(new ChestPlate($item, time(), (string)$data[0], $this::RANK[$data[2]], (string)$data[3], (int)$data[4], (int)$data[5], (int)$data[6], (int)$data[7], $this::JOB[$data[8]], (int)$data[9], (int)$data[10]));
            break;

            case "Leggins" :
            EquipmentManager::addEquipment(new Leggins($item, time(), (string)$data[0], $this::RANK[$data[2]], (string)$data[3], (int)$data[4], (int)$data[5], (int)$data[6], (int)$data[7], $this::JOB[$data[8]], (int)$data[9], (int)$data[10]));
            break;

            case "Boots" :
            EquipmentManager::addEquipment(new Boots($item, time(), (string)$data[0], $this::RANK[$data[2]], (string)$data[3], (int)$data[4], (int)$data[5], (int)$data[6], (int)$data[7], $this::JOB[$data[8]], (int)$data[9], (int)$data[10]));
            break;

            case "Weapon" :
            EquipmentManager::addEquipment(new Weapon($item, time(), (string)$data[0], $this::RANK[$data[2]], (string)$data[3], (int)$data[4], (int)$data[5], (int)$data[6], (int)$data[7], $this::JOB[$data[8]], (int)$data[9], (int)$data[10]));
            break;

            case "AssistantWeapon" :
            EquipmentManager::addEquipment(new AssistantWeapon($item, time(), (string)$data[0], $this::RANK[$data[2]], (string)$data[3], (int)$data[4], (int)$data[5], (int)$data[6], (int)$data[7], $this::JOB[$data[8]], (int)$data[9], (int)$data[10]));
            break;
          }
          $player->sendMessage($this::PARTS[$data[1]] . " 장비를 생성하였습니다.");
        }
      });
      break;

      case "목록" :
      $sender->sendForm(new class() implements Form{

        public function jsonSerialize(){
          $arr = [];

          foreach(EquipmentManager::getEquipments() as $id => $equipment) array_push($arr, array("text" => $equipment->getName()));

          return [
            "type" => "form",
            "title" => "장비 목록",
            "content" => "",
            "buttons" => $arr
          ];
        }

        public function handleResponse(Player $player, $data) : void{
          if(is_null($data))
          return;

          $player->sendForm(new class(array_values(EquipmentManager::getEquipments())[$data]) implements Form{

            private $equipment;

            public function __construct(Equipment $equipment){
              $this->equipment = $equipment;
            }

            public function jsonSerialize(){
              $arr = [];

              array_push($arr, array("text" => "지급"));
              array_push($arr, array("text" => "제거"));

              $equipment = $this->equipment;

              $content = "아이디 : " . $equipment->getId() . "\n";
              $content .= "부위 : " . $equipment->getPartsName() . "\n";
              $content .= "이름 : " . $equipment->getName() . "\n";
              $content .= "등급 : " . $equipment->getRank() . "\n";
              $content .= "설명 : " . $equipment->getInfo() . "\n";
              $content .= "공격력 : " . $equipment->getDamage() . "\n";
              $content .= "마법 : " . $equipment->getAbilityDamage() . "\n";
              $content .= "방어력 : " . $equipment->getDefense() . "\n";
              $content .= "민첩 : " . $equipment->getSpeed() . "\n";
              $content .= "직업전용 : " . $equipment->getJob() . "\n";
              $content .= "레벨제한 : {$equipment->getMinLevel()} ~ {$equipment->getMaxLevel()}";

              return [
                "type" => "form",
                "title" => $this->equipment->getName(),
                "content" => $content,
                "buttons" => $arr
              ];
            }

            public function handleResponse(Player $player, $data) : void{
              if(is_null($data))
              return;

              if($data == 0){
                $player->getInventory()->addItem($this->equipment->getEquipmentItem());
                $player->sendMessage("장비를 지급하였습니다.");
                return;
              } else{
                EquipmentManager::removeEquipment($this->equipment);
                $player->sendMessage("장비를 제거하였습니다.");
                return;
              }
            }
          });
        }
      });
    }
  }
}
