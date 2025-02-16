<?php

declare(strict_types=1);

namespace AzliRynz\SkipNight;

use pocketmine\world\World;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

class VoteManager {

    private bool $isVoting = false;
    private int $agreeVotes = 0;
    private int $disagreeVotes = 0;
    private array $votedPlayers = [];
    private ?World $world = null;
    private BossBar $bossBar;
    private SkipNight $plugin;

    public function __construct(SkipNight $plugin) {
        $this->plugin = $plugin;
        $this->bossBar = new BossBar("Vote to skip the night!");
    }

    public function startVote(World $world): void {
        $this->isVoting = true;
        $this->agreeVotes = 0;
        $this->disagreeVotes = 0;
        $this->votedPlayers = [];
        $this->world = $world;

        Server::getInstance()->broadcastMessage("§eNight has fallen! Use §b/agree §eor §c/disagree §ewithin 2 minutes!");

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $this->bossBar->send($player);
        }

        $this->plugin->getScheduler()->scheduleDelayedTask(new class($this) extends Task {
            private VoteManager $voteManager;
            public function __construct(VoteManager $voteManager) {
                $this->voteManager = $voteManager;
            }
            public function onRun(): void {
                $this->voteManager->endVote();
            }
        }, 2400);
    }

    public function vote(Player $player, bool $agree): void {
        if (isset($this->votedPlayers[$player->getName()])) {
            return;
        }

        $this->votedPlayers[$player->getName()] = true;

        if ($agree) {
            $this->agreeVotes++;
        } else {
            $this->disagreeVotes++;
        }

        $this->bossBar->remove($player);
        Server::getInstance()->broadcastMessage("§eVote: §a{$this->agreeVotes} agreed §f| §c{$this->disagreeVotes} disagreed");
    }

    public function endVote(): void {
        $totalPlayers = count(Server::getInstance()->getOnlinePlayers());
        $majorityNeeded = (int) ceil($totalPlayers / 2);

        if ($this->agreeVotes >= $majorityNeeded) {
            Server::getInstance()->broadcastMessage("§aMajority agreed! Night skipped.");
            $this->world?->setTime(0);
        } else {
            Server::getInstance()->broadcastMessage("§cVote failed! Night continues.");
        }

        $this->isVoting = false;
    }

    public function isVoting(): bool {
        return $this->isVoting;
    }
}
