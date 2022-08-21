<?php

namespace Games\Tris\Console;

use Games\Tris\Bot\Feedback;
use Games\Tris\Bot\RandomBot;
use Games\Tris\Bot\SequentialBot;
use Games\Tris\Bot\SimulationEnd;
use Games\Tris\Exception\YouLose;
use Games\Tris\Exception\YouWin;
use Games\Tris\Game;
use Games\Tris\Pawn;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BotConsole extends AbstractConsole
{
    protected static $defaultName = 'tris:bot';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Tris game');

        $match = new Game();

        $player = new SequentialBot($match, PROJECT_ROOT.'/output/history.log');

        $sectionCounter = $output->section();
        $sectionBoard = $output->section();

        $matchCounter = 1;

        while (true) {
            $sectionCounter->overwrite(sprintf('Match number <fg=cyan>%s</>', $matchCounter));
            $sectionBoard->clear();

            try {
                while (true) {
                    $player->move();
                    $sectionCounter->overwrite(sprintf('Path [%s]', implode(', ', array_map(static fn(int $pos) => sprintf('<fg=yellow>%s</>', $pos), $player->getHistory()))));
                }
            } catch (YouWin|YouLose $exception) {
                $sectionBoard->write(static::displayBoard($match->getBoard()));
                $sectionBoard->writeln('');

                if ($exception instanceof YouWin) {
                    $sectionBoard->writeln(sprintf('<info>Player %s win!</info>', static::displayPawn($match->getPlayer())));
                    $feedback = $match->getPlayer() === Pawn::X ? Feedback::POSITIVE : Feedback::NEGATIVE;
                } else {
                    $sectionBoard->writeln('<comment>Nobody win</comment>');
                    $feedback = Feedback::NEUTRAL;
                }

                ++$matchCounter;

                $player->giveFeedback($feedback);
            } catch (SimulationEnd) {
                $output->writeln('<error>SIMULATION END !!!</error>');
                break;
            }
        }

        return Command::SUCCESS;
    }
}