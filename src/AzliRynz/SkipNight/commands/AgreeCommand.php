<?php

declare(strict_types=1);

namespace AzliRynz\SkipNight\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use AzliRynz\SkipNight\SkipNight;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

class AgreeCommand extends Command implements PluginOwned {
    use PluginOwnedTrait;

    public function __construct(SkipNight $plugin) {
        parent::__construct("agree", "Agree to skip the night", "/agree");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            $this->plugin->getVoteManager()->vote($sender, true);
        }
        return true;
    }
}
