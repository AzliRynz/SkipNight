<?php

declare(strict_types=1);

namespace AzliRynz\SkipNight;

use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\types\BossBarColor;
use pocketmine\player\Player;

class BossBar {

    private int $entityUniqueId;
    private string $title;
    private float $progress;
    private array $players = [];

    public function __construct(string $title, float $progress = 1.0) {
        $this->entityUniqueId = mt_rand(10000, 99999);
        $this->title = $title;
        $this->progress = $progress;
    }

    public function send(Player $player): void {
        if (isset($this->players[$player->getName()])) return;
        $this->players[$player->getName()] = $player;

        $pk = BossEventPacket::show(
            $this->entityUniqueId,
            $this->title,
            $this->progress,
            false,
            BossBarColor::BLUE,
            0
        );

        $player->getNetworkSession()->sendDataPacket($pk);
    }

    public function updateProgress(float $progress): void {
        $this->progress = $progress;
        foreach ($this->players as $player) {
            $pk = BossEventPacket::healthPercent($this->entityUniqueId, $this->progress);
            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }

    public function updateTitle(string $title): void {
        $this->title = $title;
        foreach ($this->players as $player) {
            $pk = BossEventPacket::title($this->entityUniqueId, $this->title);
            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }

    public function remove(Player $player): void {
        if (!isset($this->players[$player->getName()])) return;
        unset($this->players[$player->getName()]);

        $pk = BossEventPacket::hide($this->entityUniqueId);
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
