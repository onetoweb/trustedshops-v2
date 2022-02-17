<?php

namespace Onetoweb\TrustedshopsV2;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Onetoweb\TrustedshopsV2\Token;
use DateTime;

/**
 * Client.
 * 
 * @author Jonathan van 't Ende <jvantende@onetoweb.nl>
 * 
 * @copyright Onetoweb B.V.
 * 
 * @see https://developers.etrusted.com/api-documentation/main.html
 */
class Client
{
    /**
     * Base uris.
     */
    const BASE_URI = 'https://api.etrusted.com';
    const BASE_URI_LOGIN = 'https://login.etrusted.com';
    
    /**
     * Methods.
     */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    
    /**
     * @var string
     */
    private $clientId;
    
    /**
     * @var string
     */
    private $clientSecret;
    
    /**
     * @var Token
     */
    private $token;
    
    /**
     * @var callable
     */
    private $updateTokenCallback;
    
    /**
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }
    
    /**
     * @param Token $token
     * 
     * @return void
     */
    public function setToken(Token $token): void
    {
        $this->token = $token;
    }
    
    /**
     * @return Token
     */
    public function getToken(): ?Token
    {
        return $this->token;
    }
    
    /**
     * @param callable $updateTokenCallback
     * 
     * @return void
     */
    public function setUpdateTokenCallback(callable $updateTokenCallback): void
    {
        $this->updateTokenCallback = $updateTokenCallback;
    }
    
    /**
     * @return void
     */
    private function requestAccessToken(): void
    {
        // make token request
        $accessToken = $this->post('/oauth/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
            'audience' => 'https://api.etrusted.com'
        ], [], false);
        
        // calculate expires dated
        $expires = (new DateTime())->modify("+{$accessToken['expires_in']} second");
        
        // update token
        $this->token = new Token($accessToken['access_token'], $expires);
        
        // fire update token callback
        ($this->updateTokenCallback)($this->token);
    }
    
    /**
     * @param string $endpoint
     * @param array $query = []
     * 
     * @return array|null
     */
    public function get(string $endpoint, array $query = []): ?array
    {
        return $this->request(self::METHOD_GET, $endpoint, [], $query);
    }
    
    /**
     * @param string $endpoint
     * @param array $data = []
     * @param array $query = []
     * @param bool $json = true
     * 
     * @return array|null
     */
    public function post(string $endpoint, array $data = [], array $query = [], bool $json = true): ?array
    {
        return $this->request(self::METHOD_POST, $endpoint, $data, $query, $json);
    }
    
    /**
     * @param string $endpoint
     * @param array $data = []
     * @param array $query = []
     * @param bool $json = true
     * 
     * @return array|null
     */
    public function put(string $endpoint, array $data = [], array $query = [], bool $json = true): ?array
    {
        return $this->request(self::METHOD_PUT, $endpoint, $data, $query, $json);
    }
    
    /**
     * @param string $endpoint
     * @param array $query = []
     *
     * @return array|null
     */
    public function delete(string $endpoint, array $query = []): ?array
    {
        return $this->request(self::METHOD_DELETE, $endpoint, [], $query);
    }
    
    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data = []
     * @param array $query = []
     * 
     * @return array|null
     */
    public function request(string $method, string $endpoint, array $data = [], array $query = [], bool $json = true): ?array
    {
        // check credentials
        if ($endpoint !== '/oauth/token') {
            
            if ($this->token == null or $this->token->isExpired()) {
                
                $this->requestAccessToken();
            }
            
            $baseUri = self::BASE_URI;
            
        } else {
            
            $baseUri = self::BASE_URI_LOGIN;
        }
        
        // add authorization headers
        if ($this->token) {
            
            $options[RequestOptions::HEADERS] = [
                'Authorization' => "Bearer {$this->token->getAccessToken()}"
            ];
        }
        
        // add data to request
        if (count($data) > 0) {
            
            if ($json) {
                $options[RequestOptions::JSON] = $data;
            } else {
                $options[RequestOptions::FORM_PARAMS] = $data;
            }
        }
        
        // add query
        if (count($query) > 0) {
            $options[RequestOptions::QUERY] = $query;
        }
        
        // setup client
        $guzzleClient = new GuzzleClient([
            'http_errors' => false
        ]);
        
        // make request
        $response = $guzzleClient->request($method, $baseUri.$endpoint, $options);
        
        // get contents
        $contents = $response->getBody()->getContents();
        
        // decode json
        $result = json_decode($contents, true);
        
        return $result;
    }
}