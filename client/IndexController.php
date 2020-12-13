<?php
use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    /**
     * Method to do the default action
     *
     * @param  int        $id      The item id
     * @return void
     * @since  1.0
     */
    public function indexAction($id = null)
    {
        try {
            $this->view->pick('index');
            
            $this->view->setVar('prf_upload_url', isset($this->config->api->prf_upload_url) ? $this->config->api->prf_upload_url : '');
            $this->view->setVar('coupon_url',     isset($this->config->api->coupon_url)     ? $this->config->api->coupon_url     : '');
            $this->view->setVar('cities_url',     isset($this->config->api->cities_url)     ? $this->config->api->cities_url     : '');
            $this->view->setVar('user_url',       isset($this->config->api->user_url)       ? $this->config->api->user_url       : '');
            $this->view->setVar('token_url',      isset($this->config->api->token_url)      ? $this->config->api->token_url      : '');
            $this->view->setVar('auth_url',       isset($this->config->api->auth_url)       ? $this->config->api->auth_url       : '');
            $this->view->setVar('base_url',       isset($this->config->api->base_url)       ? $this->config->api->base_url       : '');
            $this->view->setVar('client_id',      isset($this->config->api->client_id)      ? $this->config->api->client_id      : '');
            $this->view->setVar('client_secret',  isset($this->config->api->client_secret)  ? $this->config->api->client_secret  : '');

        } catch (Exception $e) {
            $this->flash->error( $e->getMessage() );
            return $this->response->redirect('');
        }
    }
}
