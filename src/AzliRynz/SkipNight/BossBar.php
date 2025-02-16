<?php

declare(strict_types=1);

namespace AzliRynz\SkipNight;

use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

class BossBar implements PluginOwned {
    use PluginOwnedTrait;

    private int $entityUniqueId;
    private string $title;
    private array $players = [];

    public function __construct(string $title) {
        $this->entityUniqueId = mt_rand(10000, 99999);
        $this->title = $title;
    }

    public function send(Player $player): void {
        if (isset($this->players[$player->getName()])) return;
        $this->players[$player->getName()] = $player;

        $pk = new BossEventPacket();
        $pk->bossEid = $this->entityUniqueId;
        $pk->eventType = BossEventPacket::TYPE_SHOW;
        $pk->title = $this->title;
        $pk->healthPercent = 1.0;
        $player->getNetworkSession()->sendDataPacket($pk);
    }

    public function remove(Player $player): void {
        if (!isset($this->players[$player->getName()])) return;
        unset($this->players[$player->getName()]);

        $pk = new BossEventPacket();
        $pk->bossEid = $this->entityUniqueId;
        $pk->eventType = BossEventPacket::TYPE_HIDE;
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
