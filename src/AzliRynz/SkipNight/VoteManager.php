<?php

declare(strict_types=1);

namespace AzliRynz\NightSkip;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\World;

class VoteManager {

    private SkipNight $plugin;
    private BossBar $bossBar;
    private array $votedPlayers = [];
    private int $agreeVotes = 0;
    private int $disagreeVotes = 0;
    private bool $isVoting = false;

    public function __construct(SkipNight $plugin) {
        $this->plugin = $plugin;
        $this->bossBar = new BossBar("Night Skip Vote", 1.0);
    }

    public function startVote(): void {
        if ($this->isVoting) return;
        $this->isVoting = true;

        $this->agreeVotes = 0;
        $this->disagreeVotes = 0;
        $this->votedPlayers = [];

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $this->bossBar->send($player);
        }

        Server::getInstance()->broadcastMessage("§eThe night has begun! Vote with §a/agree §for §c/disagree");

        $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void {
            $this->endVote();
        }), 2400);
    }

    public function vote(Player $player, bool $agree): void {
        if (isset($this->votedPlayers[$player->getName()])) {
            $player->sendMessage("§cYou have already voted!");
            return;
        }

        $this->votedPlayers[$player->getName()] = true;

        if ($agree) {
            $this->agreeVotes++;
            Server::getInstance()->broadcastMessage("§e" . $player->getName() . " chose to skip the night!");
        } else {
            $this->disagreeVotes++;
            Server::getInstance()->broadcastMessage("§e" . $player->getName() . " wants to keep the night!");
        }

        $this->bossBar->remove($player);
        $this->bossBar->updateProgress($this->agreeVotes / max(1, count(Server::getInstance()->getOnlinePlayers())));
        $this->bossBar->updateTitle("Votes: §a{$this->agreeVotes} §f/ §c{$this->disagreeVotes}");
    }

    private function endVote(): void {
        $this->isVoting = false;
        $players = count(Server::getInstance()->getOnlinePlayers());
        if ($this->agreeVotes > ($players / 2)) {
            Server::getInstance()->broadcastMessage("§aMajority agreed! Skipping the night...");
            foreach (Server::getInstance()->getWorldManager()->getWorlds() as $world) {
                $world->setTime(World::TIME_DAY);
            }
        } else {
            Server::getInstance()->broadcastMessage("§cNot enough votes to skip the night.");
        }

        $this->bossBar->updateTitle("Vote ended.");
        foreach ($this->votedPlayers as $name => $player) {
            $this->bossBar->remove($player);
        }
    }
}
