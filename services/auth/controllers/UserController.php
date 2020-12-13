<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\AccessToken;
use App\Repositories\AccessTokenRepository;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use Phalcon\Http\Request;

/**
 * Class UserController
 * 
 * @property \League\OAuth2\Server\AuthorizationServer oauth2Server injected into DI
 * @package App\Controllers
 */
class UserController extends BaseController
{
    /**
     * Get user data
     * 
     * @return mixed
     */
    public function get()
    {
        $serverResponse        = new \GuzzleHttp\Psr7\Response();
        $accessTokenRepository = new AccessTokenRepository();
        $bearerTokenValidator  = new BearerTokenValidator($accessTokenRepository);
        
        $bearerTokenValidator->setPublicKey(new CryptKey($this->config->oauth->public_key_path, null, false));
        
        try {
            $request = ServerRequest::fromGlobals();
            $request = $bearerTokenValidator->validateAuthorization($request);
            $user    = User::findFirst("id = '".(int) $request->getAttribute('oauth_user_id')."'");
            
            if ( !is_object($user) ) {
                throw new \Exception('Could not find the user data!', 3);                
            }
            
            $user_data = $user->toArray();
            
            unset($user_data['password']);
                        
            return $this->response->sendSuccess(['data' => $user_data]);
            
        } catch (OAuthServerException $exception) {
            return $this->sendResponseFromException($exception);
            
        } catch (\Exception $exception) {
            return $this->sendResponseFromException($exception);
        }
    }
    
    /**
     * Save user
     * 
     * @return mixed
     */
    public function save()
    {
        $serverResponse        = new \GuzzleHttp\Psr7\Response();
        $accessTokenRepository = new AccessTokenRepository();
        $bearerTokenValidator  = new BearerTokenValidator($accessTokenRepository);
        
        $bearerTokenValidator->setPublicKey(new CryptKey($this->config->oauth->public_key_path, null, false));

        try {
            $request = ServerRequest::fromGlobals();
            
            $bearerTokenValidator->validateAuthorization($request);
            
            if ( strtolower($request->getMethod()) == 'put' ) {
                $put_request = new Request();                
                $post        = $put_request->getPut();
                
            } else {
                $post = $request->getParsedBody();
            }            
            
            $post['id']       = isset($post['id'])       ? (int) $post['id']       : 0;
            $post['username'] = isset($post['username']) ? trim($post['username']) : null;
            $post['password'] = isset($post['password']) ? trim($post['password']) : null;
            $date             = date('Y-m-d H:i:s', time()); // Greenwich Mean Date and Time To MySQL
            
            if ( !empty($post['username']) ) {
                $user = User::findFirst("username = '".$post['username']."'");
            }
            
            if ( !is_object($user) && $post['id'] > 0 ) {
                $user = User::findFirst("id = '".$post['id']."'");
            }
            
            if ( is_object($user) ) {
                $user->updated_at = $date;                
                
            } else if ( $post['id'] < 1 ) {
                $user             = new User();
                $user->created_at = $date;
                $user->scope      = 'password';
                
                if ( empty($post['username']) || empty($post['password']) ) {
                    throw new Exception('Username or password are not given!', 3);
                }
                
            } else {
                throw new Exception('Invalid user data!', 3);
            }
            
            $user->username = !empty($post['username']) ? $post['username'] : $user->username;
            $user->password = !empty($post['password']) ? $post['password'] : $user->password;
            $user->scope    = isset($post['scope'])     ? $post['scope']    : $user->scope;
            
            if ( !$user->save() ) {
                throw new Exception('Could not save the user data!', 3);
            }
            
            return $this->response->sendSuccess($user->toArray());
            
        } catch (OAuthServerException $exception) {
            return $this->sendResponseFromException($exception);
            
        } catch (\Exception $exception) {
            return $this->sendResponseFromException($exception);
        }
    }
}
