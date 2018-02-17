<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 17:39
 */

namespace Jinya\Controller\Api;


use Jinya\Entity\Gallery;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Galleries\GalleryServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class GalleryController extends BaseApiController
{
    /**
     * @Route("/api/gallery", methods={"GET"}, name="api_gallery_get_all")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @param GalleryServiceInterface $galleryService
     * @param Request $request
     * @param RouterInterface $router
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function getAllAction(Request $request, GalleryServiceInterface $galleryService, RouterInterface $router, LabelServiceInterface $labelService): Response
    {
        return $this->getAllArt($request, $galleryService, $router, $labelService);
    }

    /**
     * @Route("/api/gallery/{slug}", methods={"GET"}, name="api_gallery_get")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @param string $slug
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function getAction(string $slug, GalleryServiceInterface $galleryService): Response
    {
        return $this->getArt($slug, $galleryService);
    }

    /**
     * @Route("/api/gallery", methods={"POST"}, name="api_gallery_post")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param GalleryServiceInterface $galleryService
     * @return Response
     */
    public function postAction(Request $request, GalleryServiceInterface $galleryService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService) {
            $name = $this->getValue('name');
            $description = $this->getValue('description', '');
            $orientation = $this->getValue('orientation', 'horizontal');
            $labels = $this->getValue('labels', []);

            if (!$name) {
                throw new MissingFieldsException(['name' => 'api.gallery.field.name.missing']);
            }

            $gallery = new Gallery();
            $gallery->setName($name);
            $gallery->setDescription($description);
            $gallery->setOrientation($orientation);

            $gallery = $galleryService->saveOrUpdate($gallery);

            return $galleryService->setLabels($gallery, $labels);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/gallery/{slug}/background", methods={"PUT"}, name="api_gallery_put_background")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param Request $request
     * @param GalleryServiceInterface $galleryService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function putBackgroundImageAction(string $slug, Request $request, GalleryServiceInterface $galleryService, MediaServiceInterface $mediaService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $galleryService, $mediaService, $slug) {
            $background = $request->getContent(true);
            $backgroundPath = $mediaService->saveMedia($background, MediaServiceInterface::GALLERY_BACKGROUND);

            $gallery = $galleryService->get($slug);

            if ($background) {
                $gallery->setBackground($backgroundPath);
            }

            $galleryService->saveOrUpdate($gallery);

            return $backgroundPath;
        }, 201);

        return $this->json($data, $status);
    }
}