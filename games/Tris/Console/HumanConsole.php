<?php

namespace Games\Tris\Console;

use Games\Tris\Exception\YouLose;
use Games\Tris\Exception\YouWin;
use Games\Tris\Game;
use Games\Tris\Pawn;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class HumanConsole extends AbstractConsole
{
    protected static $defaultName = 'tris';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Tris game');

        $match = new Game();

        $section = $output->section();
        $io = new SymfonyStyle($input, $section);

        try {
            while (true) {
                $section->clear();
                static::loop($section, $match, $io);
            }
        } catch (YouWin|YouLose $exception) {
            $section->overwrite(static::displayBoard($match->getBoard()));
            $section->writeln('');

            if ($exception instanceof YouWin) {
                $section->writeln(sprintf('<info>Player %s win</info>', static::displayPawn($match->getPlayer())));
            } else {
                $section->writeln('<comment>Nobody win</comment>');
            }
        }

        return Command::SUCCESS;
    }

    private static function loop(ConsoleSectionOutput $section, Game $match, SymfonyStyle $io): void
    {
        $section->write('Turn player ' . static::displayPawn($match->getPlayer()));
        $section->write(static::displayBoard($match->getBoard()));

        $myMove = $io->ask('Your move', null, function ($value) use ($match): int {
            if (!in_array($value, $match->getMoves())) {
                throw new \RuntimeException('Invalid move');
            }

            return $value;
        });

        $match->move($myMove);
    }
}