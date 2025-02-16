<?php

declare(strict_types=1);

namespace AzliRynz\SkipNight;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\world\TimeChangeEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class SkipNight extends PluginBase implements Listener {

    private VoteManager $voteManager;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->voteManager = new VoteManager($this);
    }

    public function onTimeChange(TimeChangeEvent $event): void {
        $world = $event->getWorld();
        if ($world->getTime() == 13000) {
            $this->voteManager->startVote();
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can only be used in-game!");
            return true;
        }

        switch ($command->getName()) {
            case "agree":
                $this->voteManager->vote($sender, true);
                return true;

            case "disagree":
                $this->voteManager->vote($sender, false);
                return true;
        }

        return false;
    }
}
