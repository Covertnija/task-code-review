<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Model\Message;
use App\Service\EmailSender;
use App\Service\Messenger;
use App\Service\SMSSender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class CustomerController extends AbstractController
{

    /**
     *
     * @Route("/customer/{code}/notifications", name="customer_notifications", methods={"GET"})
     */
    public function notifyCustomer(string $code, Request $request): Response
    {
        $requestData = json_decode($request->getContent());

        /** @var Customer $customer */
        $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(['code' => $code]);

        if (!$customer) {
            return new Response('Customer not found', Response::HTTP_NOT_FOUND);
        }

        if (!isset($requestData->body)) {
            return new Response('Invalid request data', Response::HTTP_BAD_REQUEST);
        }

        $message = new Message();
        $message->setBody($requestData->body);
        $message->setType($customer->getNotificationType());

        $messenger = new Messenger([new EmailSender(), new SMSSender()]);
        $messenger->send($message);

        return new Response('OK');
    }
}
