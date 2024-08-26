<?php

namespace App\Tests\Controller;

use App\Entity\Customer;
use App\Model\Message;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class CustomerControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ?EntityManagerInterface $entityManager;

    public function provideBody(): array
    {
        return [
            'test Email request' => [
                'requestData' => json_encode(['body' => 'Test']),
                'response' => Response::HTTP_OK,
                'responseText' => 'OK',
                'customerCode' => 'customer1'
            ],
            'test not found request' => [
                'requestData' => json_encode(['body' => 'Test']),
                'response' => Response::HTTP_NOT_FOUND,
                'responseText' => 'Customer not found',
                'customerCode' => 'customer2'
            ],
            'test bad request' => [
                'requestData' => '',
                'response' => Response::HTTP_BAD_REQUEST,
                'responseText' => 'Invalid request data',
                'customerCode' => 'customer1'
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::$container->get(EntityManagerInterface::class);

        // Start a transaction for the test
        $this->entityManager->beginTransaction();

        $customer = new Customer();
        $customer->setCode('customer1');
        $customer->setNotificationType(Message::TYPE_EMAIL);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        // Rollback the transaction to clean up database changes
        $this->entityManager->rollback();
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }

    /**
     * @dataProvider provideBody
    */
    public function testNotifyCustomerSuccess(string $requestData, int $response, string $responseText, string $customerCode): void
    {

        $this->client->request('GET', 'api/customer/'.$customerCode.'/notifications', [], [], ['CONTENT_TYPE' => 'application/json'], $requestData);

        $this->assertEquals($response, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString($responseText, $this->client->getResponse()->getContent());
    }
}
