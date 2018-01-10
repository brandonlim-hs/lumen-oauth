<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class OAuthRouteTest extends TestCase
{
    use DatabaseTransactions;

    protected $authorisedClient;

    protected $unauthorisedClient;

    protected $uriToTest = '/test';

    public function setUp()
    {
        parent::setUp();

        $clients = new \Laravel\Passport\ClientRepository();

        $this->authorisedClient = $clients->create(
            null, 'Test Client', ''
        );
        config(['oauth.scopes.clients.'.$this->authorisedClient->id => 'read']);

        $this->unauthorisedClient = $clients->create(
            null, 'Test Client', ''
        );
        config(['oauth.scopes.clients.'.$this->unauthorisedClient->id => '']);
    }

    /**
     * Get an access token that is authorized to access the URI to be tested.
     *
     * @return string
     */
    protected function getAuthorizedAccessToken()
    {
        $response = $this->call('POST', '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->authorisedClient->id,
            'client_secret' => $this->authorisedClient->secret
        ]);

        $this->assertEquals(200, $response->status());

        $json = json_decode($response->getContent());
        return $json->access_token;
    }

    /**
     * Get an access token that is not authorized to access the URI to be tested.
     *
     * @return string
     */
    protected function getUnauthorizedAccessToken()
    {
        $response = $this->call('POST', '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->unauthorisedClient->id,
            'client_secret' => $this->unauthorisedClient->secret
        ]);

        $this->assertEquals(200, $response->status());

        $json = json_decode($response->getContent());
        return $json->access_token;
    }

    /**
     * Test request with an authorized OAuth token.
     *
     * @return void
     */
    public function testAccessWithAuthorizedToken()
    {
        $server = [
            'HTTP_Authorization' => 'Bearer '.$this->getAuthorizedAccessToken()
        ];

        $response = $this->call('GET', $this->uriToTest, [], [], [], $server);

        $this->assertEquals(200, $response->status());
    }

    /**
     * Test request with an unauthorized OAuth token.
     *
     * @return void
     */
    public function testAccessWithUnauthorizedToken()
    {
        $server = [
            'HTTP_Authorization' => 'Bearer '.$this->getUnauthorizedAccessToken()
        ];

        $response = $this->call('GET', $this->uriToTest, [], [], [], $server);

        $this->assertEquals(403, $response->status());
    }
}
