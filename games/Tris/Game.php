<?php

namespace Games\Tris;

use Games\Tris\Exception\InvalidMove;
use Games\Tris\Exception\YouLose;
use Games\Tris\Exception\YouWin;

class Game
{
    /* The Board
     *  0 │ 1 │ 2
     * ———┼———┼———
     *  3 │ 4 │ 5
     * ———┼———┼———
     *  6 │ 7 │ 8
     */
    private array $board;

    private Pawn $player;
    
    public function __construct(array $board = null, Pawn $player = Pawn::X)
    {
        $this->board = $board ?? array_fill(0, 9, null);
        $this->player = $player;
    }

    public function getPlayer(): Pawn
    {
        return $this->player;
    }

    public function getMoves(): array
    {
        $moves = [];

        foreach ($this->board as $position => $value) {
            if (!$value) {
                $moves[] = $position;
            }
        }

        return $moves;
    }

    public function move(int $position): void
    {
        if ($this->board[$position]) {
            throw new InvalidMove();
        }

        $this->board[$position] = $this->player;

        $this->checkBoard();

        $this->nextPlayer();
    }

    public function getBoard(): array
    {
        return $this->board;
    }

    private function checkBoard(): void
    {
        // Horizontal
        if ($this->board[0] && ($this->board[0] === $this->board[1]) && ($this->board[0] === $this->board[2])) {
            throw new YouWin();
        }
        if ($this->board[3] && ($this->board[3] === $this->board[4]) && ($this->board[3] === $this->board[5])) {
            throw new YouWin();
        }
        if ($this->board[6] && ($this->board[6] === $this->board[7]) && ($this->board[6] === $this->board[8])) {
            throw new YouWin();
        }
        // Horizontal
        if ($this->board[0] && ($this->board[0] === $this->board[3]) && ($this->board[0] === $this->board[6])) {
            throw new YouWin();
        }
        if ($this->board[1] && ($this->board[1] === $this->board[4]) && ($this->board[1] === $this->board[7])) {
            throw new YouWin();
        }
        if ($this->board[2] && ($this->board[2] === $this->board[5]) && ($this->board[2] === $this->board[8])) {
            throw new YouWin();
        }
        // Diagonal
        if ($this->board[4] && ($this->board[4] === $this->board[0]) && ($this->board[4] === $this->board[8])) {
            throw new YouWin();
        }
        if ($this->board[4] && ($this->board[4] === $this->board[2]) && ($this->board[4] === $this->board[6])) {
            throw new YouWin();
        }

        // Check if game is end
        foreach($this->board as $position => $value) {
            if (!$value) return; // <- If at least one cell is free the game is not end yet
        }

        // No more free cells
        throw new YouLose();
    }

    public function clearPosition(int $position): void
    {
        $this->board[$position] = null;
    }

    public function nextPlayer(): void
    {
        $this->player = match($this->player) {
            Pawn::X => Pawn::O,
            Pawn::O => Pawn::X,
        };
    }

    public function previousPlayer(): void
    {
        $this->nextPlayer();
    }
}