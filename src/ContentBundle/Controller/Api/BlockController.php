<?php

namespace Opifer\ContentBundle\Controller\Api;

use JMS\Serializer\SerializationContext;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class BlockController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function blockAction(Request $request)
    {
        $service = $request->get('service');
        $id = $request->get('id');

        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');

        $block = $manager->find($id);

        /** @var AbstractBlockService $service */
        $service = $manager->getService($service, $block);
        $service->handleRequest($request);

        if ($block) {
            return new JsonResponse(['view' => $service->execute($block)->getContent()]);
        }

        return new JsonResponse(['success' => true]);
    }
}
