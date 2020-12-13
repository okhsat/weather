<?php
namespace App\Controllers;

use App\Library\Response;
use App\Library\HttpStatusCodes;
use App\Library\ResponseCodes;
use League\OAuth2\Server\Exception\OAuthServerException;

use Phalcon\Mvc\Controller;

/**
 * Class BaseController
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package App\Controllers
 * @property Response $response
 */
class BaseController extends Controller
{
    /**
     * @param \Exception $exception
     * @return mixed
     */
    protected function sendResponseFromException(\Exception $exception)
    {
        if ($exception instanceof OAuthServerException) {
            $payload = $exception->getPayload();
            
            return $this->response->sendError($payload['error'], $exception->getHttpStatusCode(), $payload['message']);
        }
        
        return $this->response->sendError(
            ResponseCodes::INTERNAL_SERVER_ERROR,
            HttpStatusCodes::INTERNAL_SERVER_ERROR_CODE,
            $exception->getMessage()
        );
    }
    
    /**
     * Check if payload is empty
     * @author Tega Oghenekohwo <tega@cottacush.com>
     * @return bool
     */
    public function isPayloadEmpty()
    {
        $postData = $this->getPayload();
        return empty((array)$postData);
    }

    /**
     * Get payload of current request
     * @author Tega Oghenekohwo <tega@cottacush.com>
     * @return array|bool|\stdClass
     */
    public function getPayload()
    {
        return $this->request->getJsonRawBody();
    }
}
