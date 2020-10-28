<?php

namespace Omnipay\Etherscan\Message;

use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;

abstract class AbstractRequest extends OmnipayAbstractRequest
{
    private $endpoint = 'https://%s.etherscan.io/api';

    protected $responseClass = Response::class;

    abstract protected function getModule(): string;

    abstract protected function getAction(): string;

    public function getEndpoint(): string
    {
        $network = $this->getNetwork();
        return sprintf($this->endpoint, ($network == 'main' ? 'api' : $network));
    }

    public function getApiKey(): string
    {
        return $this->getParameter('api_key');
    }

    public function setApiKey(string $value)
    {
        return $this->setParameter('api_key', $value);
    }

    public function getNetwork(): string
    {
        return $this->getParameter('network');
    }

    public function setNetwork(string $value)
    {
        return $this->setParameter('network', $value);
    }

    /**
     * Validate request parameters
     */
    protected function validateRequest(): void
    {
        //
    }

    /**
     * Get extra request parameters
     *
     * @return array
     */
    protected function getRequestData()
    {
        return [];
    }

    /**
     * Get common request parameters
     *
     * @return array|mixed
     */
    public function getData()
    {
        $this->validateRequest();

        return array_merge(
            [
                'module'  => $this->getModule(),
                'action'  => $this->getAction(),
                'apikey'  => $this->getApiKey()
            ],
            $this->getRequestData()
        );
    }

    public function sendData($data)
    {
        $query = http_build_query($data, '', '&');

        $httpResponse = $this->httpClient->request('GET', $this->getEndpoint() . '?' . $query);

        return $this->createResponse(json_decode($httpResponse->getBody()->getContents()));
    }

    protected function createResponse($data): Response
    {
        return $this->response = new $this->responseClass($this, $data);
    }
}
