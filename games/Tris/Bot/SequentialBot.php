<?php

namespace Games\Tris\Bot;

use Games\Tris\Game;

class SequentialBot
{
    private Game $match;

    private $history = [];

    private $goBack = false;

    private $file;

    public function __construct(Game $match, string $logfile)
    {
        $this->match = $match;

        @unlink($logfile);
        $this->file = fopen($logfile, 'ab+') or die('File not writeable');
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    public function move(): void
    {
        if ($this->goBack) {
            do {
                $previousMove = $this->goBack();
            } while ($previousMove < 0);

            $this->match->previousPlayer();

            $validMoves = array_filter($this->match->getMoves(), static fn(int $val) => $val > abs($previousMove) -1);
        } else {
            $validMoves = $this->match->getMoves();
        }

        $nextMove = array_shift($validMoves);

        $this->history[] = match (count($validMoves)) {
            0 => -($nextMove +1),
            default => $nextMove +1,
        };

        $this->match->move($nextMove);
    }

    public function getHistory(): array
    {
        return array_map(static fn (int $val) => abs($val)-1, $this->history);
    }

    public function giveFeedback(Feedback $feedback): void
    {
        $this->goBack = true;

        fwrite($this->file, json_encode([
            'F' => $feedback,
            'H' => $this->getHistory(),
        ], JSON_THROW_ON_ERROR) . "\n");
    }

    /*
     * Return previous reverted move
     */
    private function goBack(): int
    {
        if (!$this->history) {
            throw new SimulationEnd();
        }

        $lastMove = array_pop($this->history);

        $this->match->clearPosition(abs($lastMove) -1);
        $this->match->previousPlayer();

        $this->goBack = false;

        return $lastMove;
    }
}