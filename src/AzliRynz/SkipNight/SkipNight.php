<?php

declare(strict_types=1);

namespace AzliRynz\SkipNight;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\world\TimeChangeEvent;
use pocketmine\player\Player;

class SkipNight extends PluginBase implements Listener {

    private VoteManager $voteManager;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->voteManager = new VoteManager($this);
    }

    public function getVoteManager(): VoteManager {
        return $this->voteManager;
    }

    public function onTimeChange(TimeChangeEvent $event): void {
        $world = $event->getWorld();
        $time = $world->getTime();

        if ($time >= 12500 && !$this->voteManager->isVoting()) {
            $this->voteManager->startVote($world);
        }
    }
}
