<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class OAuthTest extends TestCase
{
    use DatabaseTransactions;

    protected $client;

    public function setUp()
    {
        parent::setUp();

        $clients = new \Laravel\Passport\ClientRepository();

        $this->client = $clients->create(
            null, 'Test Client', ''
        );
        config(['oauth.scopes.clients.'.$this->client->id => 'read']);
    }

    /**
     * Test requesting oauth token.
     *
     * @return void
     */
    public function testRequestOAuthToken()
    {
        $response = $this->call('POST', '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'scope' => 'create'
        ]);

        $this->assertEquals(200, $response->status());

        $json = json_decode($response->getContent());

        $this->assertEquals('Bearer', $json->token_type);
        $this->assertLessThanOrEqual(3600, $json->expires_in);
        $this->assertTrue(isset($json->access_token));
    }

    /**
     * Test requesting oauth token without scope.
     *
     * @return void
     */
    public function testRequestOAuthTokenWithoutScope()
    {
        $response = $this->call('POST', '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
        ]);

        $this->assertEquals(200, $response->status());

        $json = json_decode($response->getContent());

        $this->assertEquals('Bearer', $json->token_type);
        $this->assertLessThanOrEqual(3600, $json->expires_in);
        $this->assertTrue(isset($json->access_token));
    }

    /**
     * Test requesting oauth token without grant type.
     *
     * @return void
     */
    public function testRequestOAuthTokenWithoutGrantType()
    {
        $response = $this->call('POST', '/oauth/token', [
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'scope' => 'create'
        ]);

        $this->assertEquals(400, $response->status());

        $json = json_decode($response->getContent());

        $this->assertEquals('unsupported_grant_type', $json->error);
    }

    /**
     * Test requesting oauth token without client id.
     *
     * @return void
     */
    public function testRequestOAuthTokenWithoutClientId()
    {
        $response = $this->call('POST', '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_secret' => $this->client->secret,
            'scope' => 'create'
        ]);

        $this->assertEquals(400, $response->status());

        $json = json_decode($response->getContent());

        $this->assertEquals('invalid_request', $json->error);
    }

    /**
     * Test requesting oauth token without client secret.
     *
     * @return void
     */
    public function testRequestOAuthTokenWithoutClientSecret()
    {
        $response = $this->call('POST', '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client->id,
            'scope' => 'create'
        ]);

        $this->assertEquals(401, $response->status());

        $json = json_decode($response->getContent());

        $this->assertEquals('invalid_client', $json->error);
    }
}
