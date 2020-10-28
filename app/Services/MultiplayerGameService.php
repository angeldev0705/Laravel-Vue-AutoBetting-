<?php

namespace App\Services;

use App\Events\GamePlayed;
use App\Events\MultiplayerGameStateChanged;
use App\Models\Account;
use App\Models\Game;
use App\Models\GameRoom;
use App\Models\ProvablyFairGame;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class MultiplayerGameService
{
    private $user;
    private $room;
    private $provablyFairGame;
    private $game;
    private $gameable;
    private $isGameEventBroadcastingDisabled = FALSE;

    protected $gameableClass;

    public function __construct(User $user)
    {
        if (!$this->gameableClass)
            throw new \Exception('Gameable model should be explicitly set in the child class before calling MultiplayerGameService constructor.');

        // check if a specific user is passed in, otherwise get the user from the request
        $this->user = $user->getKey() ? $user : auth()->user();
    }

    private function init(): MultiplayerGameService
    {
        // get a reference to a GameRoomPlayer model (player, which already started the game)
        $otherPlayer = $this->room->activePlayers->first();

        // Obtain references to ProvablyFairGame and Gameable models if the game is already started
        if ($otherPlayer) {
            $this->provablyFairGame = $otherPlayer->game->provablyFairGame;
            $this->gameable = $otherPlayer->game->gameable;
        }

        return $this;
    }

    /**
     * Create a new game
     *
     * @return MultiplayerGameService
     */
    public final function createGame(): MultiplayerGameService
    {
        $this->init();

        if (!$this->getProvablyFairGame()) {
            $this->createProvablyFairGame();
        }

        if (!$this->getGameable()) {
            $this->createGameable();
        }

        // create Game model instance
        $this->game = new Game();
        $this->game->account()->associate($this->getUserAccount());
        $this->game->provablyFairGame()->associate($this->getProvablyFairGame());
        $this->game->bet = $this->getRoom()->parameters->bet;
        $this->game->win = 0;
        $this->game->status = Game::STATUS_IN_PROGRESS;
        $this->getGameable()->game()->save($this->game);

        $this->game->setRelation('gameable', $this->getGameable());

        // link current player to the created game model
        $player = $this->room->player($this->getUser());
        $player->game()->associate($this->game);
        $player->save();

        // create a new account transaction
        $accountService = new AccountService($this->getUserAccount());
        $accountService->createTransaction($this->game, -$this->game->bet);

        return $this;
    }

    /**
     * Initialize the game and related objects before making any game actions
     *
     * @return MultiplayerGameService
     */
    public final function loadGame(): MultiplayerGameService
    {
        $player = $this->room->player($this->getUser());

        $this->game = $player->game;
        $this->provablyFairGame = $this->game->provablyFairGame;
        $this->gameable = $this->game->gameable;

        return $this;
    }

    /**
     * Complete the game
     *
     * @param Game $game
     * @param array $attributes
     * @return MultiplayerGameService
     */
    protected final function completeGame(Game $game, array $attributes): MultiplayerGameService
    {
        // if the loaded game is passed it's important to change the reference, so the completed game passed to frontend
        if ($game->id == $this->game->id) {
            $game = $this->game;
        }

        // unlink current player from the game model, so a new game can be started
        $player = $this->room->player($game);
        $player->game()->dissociate();
        $player->save();

        // fill in game attributes
        foreach ($attributes as $key => $value) {
            $game->{$key} = $value;
        }
        $game->is_completed = TRUE;

        // save the game model
        $game->save();

        // create a new account transaction if the game is finished
        if ($game->is_completed && $game->win > 0) {
            $accountService = new AccountService($game->account); // important to pass $game->account, not $this->getUserAccount()
            $accountService->createTransaction($game, $game->win);
        }

        // throw new GamePlayed event
        event(new GamePlayed($game));

        return $this;
    }

    /**
     * Cancel the game
     *
     * @return MultiplayerGameService
     */
    protected final function cancelGame(Game $game, array $attributes): MultiplayerGameService
    {
        // if the loaded game is passed it's important to change the reference, so the completed game passed to frontend
        if ($game->id == $this->game->id) {
            $game = $this->game;
        }

        // unlink current player from the game model, so a new game can be started
        $player = $this->room->player($game);
        $player->game()->dissociate();
        $player->save();

        // fill in game attributes
        foreach ($attributes as $key => $value) {
            $game->{$key} = $value;
        }
        $game->is_cancelled = TRUE;

        // save the game model
        $game->save();

        // create a new account transaction and return bet
        $accountService = new AccountService($game->account); // important to pass $game->account, not $this->getUserAccount()
        $accountService->createTransaction($game, $game->bet);

        return $this;
    }

    /**
     * Create ProvablyFairGame model instance
     *
     * @param string|NULL $clientSeed
     * @return MultiplayerGameService
     * @throws \Exception
     */
    public final function createProvablyFairGame(string $clientSeed = NULL): MultiplayerGameService
    {
        $this->provablyFairGame = new ProvablyFairGame();
        $this->provablyFairGame->secret = $this->makeSecret();
        $this->provablyFairGame->client_seed = $clientSeed ?: random_int(10000000, 99999999);
        $this->provablyFairGame->gameable_type = $this->gameableClass;
        $this->provablyFairGame->save();

        return $this;
    }

    /**
     * Get ProvablyFairGame model
     *
     * @return ProvablyFairGame|null
     */
    public final function getProvablyFairGame(): ?ProvablyFairGame
    {
        return $this->provablyFairGame;
    }

    /**
     * Get GameRoom model
     *
     * @return GameRoom|null
     */
    public final function getRoom(): ?GameRoom
    {
        return $this->room;
    }

    /**
     * Set GameRoom model
     *
     * @param GameRoom $room
     * @return MultiplayerGameService
     */
    public final function setRoom(GameRoom $room): MultiplayerGameService
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get Game model instance
     *
     * @return Game
     */
    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function getGameable(): ?Model
    {
        return $this->gameable;
    }

    public function setGameable(Model $gameable): MultiplayerGameService
    {
        $this->gameable = $gameable;

        return $this;
    }

    public function isGameCompleted(): bool
    {
        return $this->getGame()->is_completed ?? FALSE;
    }

    public function isGameCancelled(): bool
    {
        return $this->getGame()->is_cancelled ?? FALSE;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserAccount(): Account
    {
        return $this->getUser()->account;
    }

    /**
     * Broadcast game event to all subscribers via pusher
     *
     * @param array $event
     * @return MultiplayerGameService
     */
    protected final function broadcastGameEvent(array $event): MultiplayerGameService
    {
        if (!$this->isGameEventBroadcastingDisabled) {
            event(new MultiplayerGameStateChanged($this->getRoom(), $event));
        }

        return $this;
    }

    protected final function disableGameEventBroadcasting(): MultiplayerGameService
    {
        $this->isGameEventBroadcastingDisabled = TRUE;

        return $this;
    }

    /**
     * Make a game secret (reels positions, shuffled cards deck) - before applying client seed
     *
     * @return string
     */
    abstract public function makeSecret(): string;

    /**
     * Create a gameable model instance. setGameable() method should be called after creation
     *
     * @return MultiplayerGameService
     */
    abstract protected function createGameable(): MultiplayerGameService;

    /**
     * Run a game action
     *
     * @param string $action
     * @return MultiplayerGameService
     */
    abstract public function action(string $action): MultiplayerGameService;
}
