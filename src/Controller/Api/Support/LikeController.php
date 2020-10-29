<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 15.05.18
 * Time: 17:40
 */

namespace Jinya\Controller\Api\Support;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Jinya\Entity\Artist\User;
use Jinya\Framework\BaseApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends BaseApiController
{
    /** @var string */
    private string $jinyaVersion;

    /**
     * @Route("/api/support/like", methods={"POST"}, name="api_support_like")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function submitAction(Client $client): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($client) {
            /** @var User $user */
            $user = $this->getUser();
            $like = [
                'who' => $user->getArtistName(),
                'message' => $this->getValue('message'),
            ];

            $response = $client->request('POST', 'https://api.jinya.de/tracker/like', [
                RequestOptions::JSON => $like,
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    public function setJinyaVersion(string $jinyaVersion): void
    {
        $this->jinyaVersion = $jinyaVersion;
    }
}
