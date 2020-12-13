<?php
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Mvc\Micro;
use Phalcon\Loader;
use Models\BaseModel;
use Models\User;
use Models\City;

$di->setShared('config', function () {
    $config = include __DIR__ . '/config.php';
    
    return $config;
});

// Set up the database service
$di->set(
    'db',
    function () {
        $config = $this->getConfig();
            
        return new PdoMysql(
            [
                'host'     => $config->DB->host,
                'username' => $config->DB->username,
                'password' => $config->DB->password,
                'dbname'   => $config->DB->dbname
            ]
        );
    }
);

$config        = $di->getConfig();
$loader        = new Loader(); // Use Loader() to autoload
$app           = new Micro($di); // Create and bind the DI to the application
$authorization = $app->request->getHeader('authorization');
$token         = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $authorization));
$token         = !empty($token) ? $token : $_COOKIE['access_token'];

$loader->registerDirs(
    [
        $config->application->modelsDir
    ]
)->registerNamespaces(
    [
        'Models' => $config->application->modelsDir
    ]
)->register();

/**
 * Function to get an access token
 *
 * @return string         The access token
 * @since  1.0
 */
function getToken()
{
    GLOBAL $config;
    
    $token_data = [
        'client_id'     => $config->api->client_id,
        'client_secret' => $config->api->client_secret,
        'grant_type'    => 'client_credentials'
    ];
    
    $token_result = BaseModel::sendRequest($config->api->auth_url, $token_data, 'POST');
    
    if ( $token_result === false ) {
        throw new Exception('Could not connect to the API.');
        
    } else if ( $token_result->status == 'error' ) {
        throw new Exception('Could not get the data from the API. - Error: ' . $token_result->message . ' - Code: '.$token_result->code);
    }
    
    return $token_result->data->access_token;
}

/**
 * Function to validate the provided access token
 *
 * @return string           True if valid, false otherwise
 * @since  1.0
 */
function validateToken()
{
    GLOBAL $config, $token;
    
    $token_result = BaseModel::sendRequest($config->api->token_url, [], 'POST', ['Authorization: Bearer ' . $token]);
    
    if ( $token_result === false ) {
        throw new Exception('Could not connect to the API.');
        
    } else if ( $token_result->status == 'success' ) {
        return true;
        
    } else if ( $token_result->status == 'error' ) {
        throw new Exception('Error: ' . $token_result->message . ' - Code: '.$token_result->code);
    }   
    
    return false;
}

/**
 * Function to get the user oauth data
 *
 * @return array      The user oauth data
 * @since  1.0
 */
function getUserAuthData()
{
    GLOBAL $config, $token;
    
    if ( empty($token) ) {
        throw new Exception('Invalid Request!', 1);
    }
    
    $result = BaseModel::sendRequest($config->api->user_url, [], 'GET', ['Authorization: Bearer ' . $token]);
    
    if ( $result === false ) {
        throw new Exception('Could not connect to the Authentication API.');
        
    } else if ( $result->status == 'error' ) {
        throw new Exception('Could not get the data from the Authentication API. - Error: ' . $result->message . ' - Code: '.$result->code);
    }
    
    $user_data = json_decode(json_encode($result->data->data), true);
    
    setcookie('user_auth_id', $user_data['id'], time() + (24 * 3600));
    
    return $user_data;
}

/**
 * Function to get the user profile data by its email
 *
 * @param  string      $email      The given user email
 * @return array                   The user profile data
 * @since  1.0
 */
function getUserProfileData($email)
{
    $user = User::findFirst("email = '".$email."'");
    
    if ( !is_object($user) ) {
        throw new Exception('Could not find the user!', 3);
    }
    
    $user_data             = $user->toArray();
    $user_data['city_ids'] = [];
    $city_ids              = explode(',', $user->city_ids);
    
    if ( count($city_ids) ) {
        foreach ( $city_ids as $cid ) {
            $cid = (int) $cid;
            
            if ( $cid > 0 ) {
                $user_data['city_ids'][] = $cid;
            }
        }
    }
    
    return $user_data;
}

/**
 * Method to retrieve the cities and their weather state
 *
 * @param  string      $token     The given access token
 * @return string                 True if valid, false otherwise
 * @since  1.0
 */
$app->get(
    '/api/cities',
    function () {
        try {
            $data           = [];
            $data['data']   = [];
            $data['status'] = 'failed';
            
            if ( !validateToken() ) {
                throw new Exception('Invalid Request!', 3);
            }
            
            $user_data = getUserAuthData();
            $cities    = City::find("status = 'A'");
            
            if ( !is_object($cities) || $cities === false ) {
                throw new Exception('Could not get the cities information!', 3);
            }
            
            $profile_data = getUserProfileData($user_data['username']);
            $data['data'] = $cities->toArray();
            
            if ( count($data['data']) ) {
                foreach ( $data['data'] as $k => $city ) {
                    $data['data'][$k]['selected'] = false;
                    
                    if ( in_array((int) $city['id'], $profile_data['city_ids']) ) {
                        $data['data'][$k]['selected'] = true;
                    }
                }
            }
            
            $data['status'] = 'success';
            
        } catch (Exception $e) {
            $data['msg'] = $e->getMessage();
        }
        
        echo json_encode($data);
    }
);

// Retrieves user 
$app->get(
    '/api/user',
    function () use ($config) {
        try {
            $data           = [];
            $data['data']   = [];
            $data['status'] = 'failed';
            $user_data      = getUserAuthData();
            $user           = User::findFirst("email = '".$user_data['username']."'");
            
            if ( !is_object($user) ) {
                $user                  = new User();
                $user_data['email']    = $user_data['username'];
                $user_data['password'] = 'especial-auto-pass';
                $user_data['name']     = $user_data['username'];
                $user_data['role']     = 'general_user';
                
                if ( !$user->bind($user_data) ) {
                    throw new Exception('Could not bind the given data to the model.', 235, $user->getCurExc());
                }
                
                if ( !$user->check() ) {
                    throw new Exception('The given data are not valid.', 235, $user->getCurExc());
                }
                
                if ( !$user->save() ) {
                    throw new Exception('Could not save the data into the database.', 235, $user->getCurExc());
                }
            }
            
            $data['data']   = getUserProfileData($user->email);
            $data['status'] = 'success';
            
        } catch (Exception $e) {
            $data['msg'] = $e->getMessage();
        }
        
        echo json_encode($data);
    }
);

// Updates user based on primary key
$app->put(
    '/api/user',
    function () use ($config, $token) {
        try {
            $data           = [];
            $data['data']   = [];
            $data['status'] = 'failed';
            $post           = [];
            $user_data      = getUserAuthData();
            $user           = User::findFirst("email = '".$user_data['username']."'");
            
            if ( !is_object($user) ) {
                throw new Exception('Could not find the user!', 3);
            }
                        
            parse_str($this->request->getPut('form'), $post);
            
            unset($post['role']);
            
            if ( isset($post['password']) && isset($post['password_confirm']) && !empty(trim($post['password'])) && trim($post['password']) != trim($post['password_confirm']) ) {
                throw new Exception('The confirmation of your password is not the same as it!', 3);
            }
            
            $email = $user->email;
            
            if ( !$user->bind($post) ) {
                throw new Exception('Could not bind the given data to the model.', 235, $user->getCurExc());
            }
            
            if ( !$user->check() ) {
                throw new Exception('The given data are not valid.', 235, $user->getCurExc());
            }
            
            if ( $user->email != $email || (isset($post['password']) && !empty(trim($post['password']))) ) {
                $user_data['username'] = $user->email;
                $user_data['password'] = $post['password'];
                $api_result            = BaseModel::sendRequest($config->api->user_url, $user_data, 'PUT', ['Authorization: Bearer ' . $token]);
                
                if ( $api_result === false ) {
                    throw new Exception('Could not connect to the Authentication API.');
                    
                } else if ( $api_result->status == 'error' ) {
                    throw new Exception('Could not get the data from the Authentication API. - Error: ' . $api_result->message . ' - Code: '.$api_result->code);
                    
                } else if ( $api_result->status != 'success' ) {
                    throw new Exception('Could not update the user authentication data!');
                }
            }
            
            if ( !$user->save() ) {
                throw new Exception('Could not save the data into the database.', 235, $user->getCurExc());
            }
            
            $data['data']   = $user->toArray();
            $data['status'] = 'success';
            
        } catch (Exception $e) {
            $data['msg'] = $e->getMessage();
        }
        
        echo json_encode($data);
    }
);

// Adds a new user
$app->post(
    '/api/user',
    function () use ($config) {
        try {
            $data           = [];
            $data['data']   = [];
            $data['status'] = 'failed';
            $post           = [];
            
            parse_str($this->request->getPost('form'), $post);
            
            if ( !isset($post['email']) || empty(trim($post['email'])) ) {
                throw new Exception('Email is not given! User email is required for registeration.', 1);
            }
            
            if ( isset($post['password']) && isset($post['password_confirm']) && !empty(trim($post['password'])) && trim($post['password']) != trim($post['password_confirm']) ) {
                throw new Exception('The confirmation of your password is not the same as it!', 3);
            }
            
            $user = User::findFirst("email = '".$post['email']."'");
            
            if ( is_object($user) ) {
                throw new Exception('User with this email is already registered with our system!', 2);
            }
            
            $access_token = getToken();
            $user         = new User();
            $post['role'] = 'general_user';
            
            if ( !$user->bind($post) ) {
                throw new Exception('Could not bind the given data to the model.', 235, $user->getCurExc());
            }
            
            if ( !$user->check() ) {
                throw new Exception('The given data are not valid.', 235, $user->getCurExc());
            }
            
            $user_data = [
                'username' => $user->email,
                'password' => $post['password'],
                'scope'    => 'password'
            ];
            
            $api_result = BaseModel::sendRequest($config->api->user_url, $user_data, 'POST', ['Authorization: Bearer ' . $access_token]);
            
            if ( $api_result === false ) {
                throw new Exception('Could not connect to the Authentication API.');
                
            } else if ( $api_result->status == 'error' ) {
                throw new Exception('Could not get the data from the Authentication API. - Error: ' . $api_result->message . ' - Code: '.$api_result->code);
            }
            
            setcookie('user_auth_id', $api_result->data->user_id, time() + (24 * 3600));
            
            if ( !$user->save() ) {
                throw new Exception('Could not save the data into the database.', 235, $user->getCurExc());
            }
            
            $data['data'] = $user->toArray();
            
            unset($data['data']['password']);
            
            $data['status'] = 'success';
            
        } catch (Exception $e) {
            $data['msg'] = $e->getMessage();
        }
        
        echo json_encode($data);
    }
);

// Deletes the current user
$app->delete(
    '/api/user',
    function () {
        try {
            $data           = [];
            $data['data']   = [];
            $data['status'] = 'failed';
            $user_data      = getUserAuthData();
            $user           = User::findFirst("email = '".$user_data['username']."'");
            
            if ( !is_object($user) ) {
                throw new Exception('Could not find the user!', 3);
            }
            
            if ( !$user->delete() ) {
                throw new Exception('Could not delete the user data from the database!', 235, $user->getCurExc());
            }
            
            $data['status'] = 'success';
            
        } catch (Exception $e) {
            $data['msg'] = $e->getMessage();
        }
        
        echo json_encode($data);
    }
);
        
$app->handle($uri_path);
        