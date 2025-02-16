<?php

declare(strict_types=1);

namespace AzliRynz\SkipNight\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use AzliRynz\SkipNight\SkipNight;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

class DisagreeCommand extends Command implements PluginOwned {
    use PluginOwnedTrait;

    private SkipNight $plugin;

    public function __construct(SkipNight $plugin) {
        parent::__construct("disagree", "Disagree to skip the night", "/disagree");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            $this->plugin->getVoteManager()->vote($sender, false);
        }
        return true;
    }
}
