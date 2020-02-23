<?php

namespace Jinya\Controller\Api\SegmentPage;

use Jinya\Framework\BaseApiController;
use Jinya\Services\SegmentPages\SegmentPageServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SegmentPagePreviewController extends BaseApiController
{
    /**
     * @Route("/api/segment_page/{slug}/preview")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param SegmentPageServiceInterface $segmentPageService
     * @return Response
     */
    public function getAction(
        string $slug,
        SegmentPageServiceInterface $segmentPageService
    ): Response {
        $page = $segmentPageService->get($slug);

        return $this->render('@Theme/SegmentPage/detail.html.twig', [
            'page' => $page,
        ]);
    }
}