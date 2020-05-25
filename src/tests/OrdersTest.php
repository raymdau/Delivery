<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\Client;

class OrdersTest extends WebTestCase {
    private $client;
    private static $orderId, $distance;

    protected function setUp(): void {
        parent::setUp();
        $this->client = new Client(['base_uri' => 'http://nginx:80/']);
    }

    protected function tearDown(): void {
        parent::tearDown();
        $this->client = null;
    }

    public function testPlacePost() {
        $headers['Content-Type'] = "application/json";
        $response = $this->client->request("POST", "orders", [
            "headers" => $headers,
            "body" => json_encode([
                "origin" => ["22.331791", "114.224403"],
                "destination" => ["22.336612", "114.176191"],
            ]),
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        // convert response string content to stdClass instance
        $responseJsonStr = $response->getBody()->getContents();
        $responseJson = json_decode($responseJsonStr); // convert response string content to stdClass instance

        // save order id and distance for tests afterward
        $this->static = static::$orderId = $responseJson->order_id;
        $this->static = static::$distance = $responseJson->distance;

        $this->assertEquals($responseJson->status, "UNASSIGNED");
    }

    public function testTakePatch() {
        $headers['Content-Type'] = "application/json";
        $response = $this->client->request("PATCH", "orders/" . static::$orderId, [
            "headers" => $headers,
            "body" => json_encode([
                "status" => "TAKEN",
            ]),
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $responseJsonStr = $response->getBody()->getContents();
        $responseJson = json_decode($responseJsonStr);
        $this->assertEquals($responseJson->status, "SUCCESS");
    }

    public function testListGet() {
        $headers['Content-Type'] = "application/json";
        $response = $this->client->request("GET", "orders/list/1/10", [
            "headers" => $headers,
            "body" => json_encode([]),
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $responseJsonStr = $response->getBody()->getContents();
        $responseJson = json_decode($responseJsonStr);

        // assertion for the newly created order
        $this->assertEquals(static::$orderId, $responseJson[0]->id);
        $this->assertEquals(static::$distance, $responseJson[0]->distance);
        $this->assertEquals("TAKEN", $responseJson[0]->status);

        // assertion for not more than 10 records
        $this->assertLessThanOrEqual(10, count($responseJson));
    }
}
