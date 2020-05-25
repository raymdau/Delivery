<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \DateTime;
use App\Entity\Orders;

class OrdersController extends AbstractController {
    private $validator, $response;
    const GOOGLE_API_APP_KEY = "AIzaSyDJ7kVASWIo7_N1fI_7aBEI9h4d8HLB538";
    const STATUS_UNASSIGNED = "UNASSIGNED";
    const STATUS_TAKEN = "TAKEN";

    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
        $this->response = new Response();
        $this->response->headers->set("Content-Type", "application/json");
    }

    private function getRequestJsonContent($request) {
        $content = $request->getContent();
        return json_decode($content);
    }

    private function generateTransactionId() {
        return uniqid();
    }

    private function getPrice() {
        return 10; // no products data at the monent
    }

    private function getDistance($origin, $destination) {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $origin[0] . "," . $origin[1] . "&destinations=" . $destination[0] . "," . $destination[1] . "&mode=driving&language=en&key=" . self::GOOGLE_API_APP_KEY;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $responseArr = json_decode($response, true);
        $distance = $responseArr["rows"][0]["elements"][0]["distance"]["text"];

        return $distance;
    }

    public function placeAction(Request $request) {
        $requestContent = $this->getRequestJsonContent($request);
        if (
            (isset($requestContent->origin) && count($requestContent->origin) == 2) &&
            (isset($requestContent->destination) && count($requestContent->destination) == 2)
        ) {
            // create new order
            $order = new Orders();
            $transactionId = $this->generateTransactionId(); // create a transaction id for the order
            $order->setTransactionId($transactionId);
            $now = new DateTime();
            $order->setCreateTime($now);
            $order->setOrigin($requestContent->origin[0] . "," . $requestContent->origin[1]);
            $order->setDestination($requestContent->destination[0] . "," . $requestContent->destination[1]);
            $price = $this->getPrice();
            $order->setPrice($price);
            $distance = $this->getDistance($requestContent->origin, $requestContent->destination); // get distance using google distance matrix api
            $order->setDistance($distance);
            $order->setStatus(self::STATUS_UNASSIGNED);

            $orderErr = $this->validator->validate($order);
            if (count($orderErr) > 0) { // to validate order entity
                $errString = (string) $orderErr;
                $responseContent = [
                    "error" => $errString,
                ];
                $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $this->response->setContent(json_encode($responseContent));
            } else {
                $em = $this->getDoctrine()->getManager();
                $em->persist($order);
                $em->flush();
                $responseContent = [
                    "order_id" => $order->getId(),
                    "distance" => $distance,
                    "status" => self::STATUS_UNASSIGNED,
                ];
                $this->response->setStatusCode(Response::HTTP_OK);
                $this->response->setContent(json_encode($responseContent));
            }
        } else {
            $responseContent = [
                "error" => "INVALID ORIGIN OR DESTINATION COORDINATIONS",
            ];
            $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->response->setContent(json_encode($responseContent));
        }

        return $this->response;
    }

    public function takeAction(Request $request, int $id) {
        $requestContent = $this->getRequestJsonContent($request);
        if (isset($requestContent->status) && $requestContent->status == self::STATUS_TAKEN) {
            $order = $this->getDoctrine()->getRepository(Orders::class)->findOneBy(["id" => $id]);
            if ($order) {
                if ($order->getStatus() != self::STATUS_TAKEN) { // check if the order has already been taken
                    $order->setStatus(SELF::STATUS_TAKEN);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($order);
                    $em->flush();
                    $responseContent = [
                        "status" => "SUCCESS",
                    ];
                    $this->response->setStatusCode(Response::HTTP_OK);
                    $this->response->setContent(json_encode($responseContent));
                } else { // if order has already been taken, no action made to database
                    $responseContent = [
                        "error" => "ORDER ALREADY TAKEN",
                    ];
                    $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
                    $this->response->setContent(json_encode($responseContent));
                }
            } else {
                $responseContent = [
                    "error" => "INVALID ORDER ID",
                ];
                $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $this->response->setContent(json_encode($responseContent));
            }
        } else {
            $responseContent = [
                "error" => "INVALID STATUS",
            ];
            $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->response->setContent(json_encode($responseContent));
        }

        return $this->response;
    }

    public function listAction(int $page, int $limit) {
        $offset = ($page - 1) * $limit;
        if ($page > 0 && $limit > 0) { // page and limit should be greater than 0
            $orders = $this->getDoctrine()->getManager()->createQueryBuilder()
                                                                    ->select('O')
                                                                    ->from(Orders::class, 'O')
                                                                    ->orderBy('O.id', 'DESC')
                                                                    ->setFirstResult($offset)
                                                                    ->setMaxResults($limit)
                                                                    ->getQuery()
                                                                    ->getResult();

          $resultLists = [];
          foreach($orders as $key => $order) {
              array_push($resultLists, [
                  "id" => $order->getId(),
                  "distance" => $order->getDistance(),
                  "status" => $order->getStatus(),
              ]);
          }
          $this->response->setStatusCode(Response::HTTP_OK);
          $this->response->setContent(json_encode($resultLists));
        } else {
            $responseContent = [
                "error" => "INVALID PARAMETERS",
            ];
            $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->response->setContent(json_encode($responseContent));
        }

        return $this->response;
    }
}
