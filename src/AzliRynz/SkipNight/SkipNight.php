<?php

declare(strict_types=1);

namespace AzliRynz\SkipNight;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\World;

class SkipNight extends PluginBase {

    private VoteManager $voteManager;

    public function onEnable(): void {
        $this->voteManager = new VoteManager($this);

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            $this->checkNightTime();
        }), 100);
    }

    private function checkNightTime(): void {
        foreach ($this->getServer()->getWorldManager()->getWorlds() as $world) {
            if ($this->isNightTime($world) && !$this->voteManager->isVoting()) {
                $this->voteManager->startVote($world);
            }
        }
    }

    private function isNightTime(World $world): bool {
        $time = $world->getTime();
        return $time >= 12500 && $time < 23000; // Night in Minecraft
    }

    public function getVoteManager(): VoteManager {
        return $this->voteManager;
    }
}
