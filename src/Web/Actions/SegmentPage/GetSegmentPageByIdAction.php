<?php

namespace App\Web\Actions\SegmentPage;

use App\Database\SegmentPage;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetSegmentPageByIdAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \App\Database\Exceptions\UniqueFailedException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);
        if ($segmentPage === null) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        return $this->respond($segmentPage->format());
    }
}