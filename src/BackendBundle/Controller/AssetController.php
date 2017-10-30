<?php

namespace BackendBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssetController extends Controller
{
    /**
     * @Route("/asset/dump", name="backend_asset_dump")
     *
     * @param Request $request
     * @return Response
     */
    public function dumpAction(): Response
    {
        try {
            $success = true;
            $assetDumper = $this->get('helper.asset_dumper');
            $assetDumper->dumpAssets();
        } catch (Exception $exception) {
            $logger = $this->get('logger');
            $logger->error('Asset dump failed');
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            $success = false;
        }

        return $this->json([
            'success' => $success
        ], $success ? 200 : 500);
    }
}
