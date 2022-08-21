<?php

namespace Games\Tris\Console;

use Games\Tris\Pawn;
use Symfony\Component\Console\Command\Command;

abstract class AbstractConsole extends Command
{
    protected static function displayBoard(array $board): string
    {
        $table = <<<END
   %s │ %s │ %s
  ———┼———┼———
   %s │ %s │ %s
  ———┼———┼———
   %s │ %s │ %s
END;

        return sprintf($table,
            static::displayPawn($board[0], 0),
            static::displayPawn($board[1], 1),
            static::displayPawn($board[2], 2),
            static::displayPawn($board[3], 3),
            static::displayPawn($board[4], 4),
            static::displayPawn($board[5], 5),
            static::displayPawn($board[6], 6),
            static::displayPawn($board[7], 7),
            static::displayPawn($board[8], 8),
        );
    }

    protected static function displayPawn(Pawn $pawn = null, int $position = null): string
    {
        return match($pawn) {
            Pawn::X => '<fg=red>X</>',
            Pawn::O => '<fg=blue>O</>',
            null => "<fg=gray>$position</>",
        };
    }
}