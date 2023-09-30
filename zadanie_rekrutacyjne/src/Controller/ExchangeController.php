<?php

namespace App\Controller;

use App\Entity\History;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExchangeController extends AbstractController
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine) {

        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/exchange/values", name="exchange_values", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function exchangeValues(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['first']) && isset($data['second'])) {
            $first = $data['first'];
            $second = $data['second'];

            $em = $this->doctrine->getManager();

            $history = new History();

            $history->setFirstIn($data['first']);
            $history->setSecondIn($data['second']);
            $history->setCreatedAt(new \DateTimeImmutable());
            $history->setUpdatedAt(new \DateTime());

            $em->persist($history);
            $em->flush();

            [$first, $second] = [$second, $first];

            $history->setFirstOut($first);
            $history->setSecondOut($second);
            $history->setUpdatedAt(new \DateTime());

            $em->persist($history);
            $em->flush();

            return new JsonResponse(['message' => 'Wartości zapisane w bazie danych']);
        } else {
            return new JsonResponse(['message' => 'Brak wymaganych parametrów.'], 400);
        }
    }
}
