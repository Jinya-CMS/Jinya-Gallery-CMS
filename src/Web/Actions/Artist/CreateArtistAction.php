<?php

namespace App\Web\Actions\Artist;

use App\Database\Artist;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateArtistAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws JsonException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $artist = new Artist();
        $artist->setPassword($body['password']);
        $artist->enabled = $body['enabled'] ?? false;
        $artist->roles = $body['roles'] ?? [];
        $artist->email = $body['email'];
        $artist->artistName = $body['artistName'];
        try {
            $artist->create();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Email exists');
        }

        return $this->respond($artist->format(), Action::HTTP_CREATED);
    }
}