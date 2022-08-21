<?php

namespace Games\Tris\Controller;

use Games\Tris\Bot\RandomBot;
use Games\Tris\Exception\YouLose;
use Games\Tris\Exception\YouWin;
use Games\Tris\Game;
use Laminas\Diactoros\Response;
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpController
{
    private Engine $templates;

    public function __construct(Engine $templates)
    {
        $this->templates = $templates;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ('POST' !== $request->getMethod()) {
            $this->clear();
        }

        try {
            // Load current match
            $data = $this->load();
            $match = new Game($data['board'], $data['turn']);
        } catch (\Throwable $t) {
            $match = new Game();
        }

        if ('POST' === $request->getMethod()) {
            $data = $request->getParsedBody();
            $move = (int) $data['move'];

            # Human turn
            try {
                $match->move($move);
                $this->save($match);
            } catch (YouWin $e) {
                $this->clear();
                $response = new Response;
                $response->getBody()->write($this->templates->render('Tris/win'));
                return $response;
            } catch (YouLose $e) {
                $this->clear();
                $response = new Response;
                $response->getBody()->write($this->templates->render('Tris/lose'));
                return $response;
            }

            # Bot turn
            try {
                $bot = new RandomBot();
                $bot->move($match);

                $this->save($match);
            } catch (YouWin $e) {
                $this->clear();
                $response = new Response;
                $response->getBody()->write($this->templates->render('Tris/win'));
                return $response;
            } catch (YouLose $e) {
                $this->clear();
                $response = new Response;
                $response->getBody()->write($this->templates->render('Tris/lose'));
                return $response;
            }
        }

        if (isset($match)) {
            $response = new Response;
            $response->getBody()->write($this->templates->render('Tris/play', ['board' => $match->getBoard(), 'turn' => $match->getPlayer()]));
            return $response;
        }

        $response = new Response;
        $response->getBody()->write('<h1 style="text-align: center">Tris!</h1>');
        return $response;
    }

    private function clear(): void
    {
        unset($_SESSION['tris_board']);
        unset($_SESSION['tris_turn']);
    }

    private function load()
    {
        return [
            'board' => unserialize($_SESSION['tris_board']),
            'turn' => unserialize($_SESSION['tris_turn']),
        ];
    }

    private function save(Game $match): void
    {
        $_SESSION['tris_board'] = serialize($match->getBoard());
        $_SESSION['tris_turn'] = serialize($match->getPlayer());
    }
}