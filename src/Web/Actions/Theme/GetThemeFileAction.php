<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Theme;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;
use stdClass;

#[JinyaAction('/api/theme/{id}/file', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action gets the given theme files')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully got the theme files', ref: File::class, map: true)]
#[OpenApiResponse('Theme not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetThemeFileAction extends ThemeAction
{
    /**
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $files = $theme->getFiles();
        $result = [];

        foreach ($files as $key => $file) {
            $result[$key] = $file->format();
        }
        if (empty($result)) {
            $result = new stdClass();
        }

        return $this->respond($result);
    }
}
