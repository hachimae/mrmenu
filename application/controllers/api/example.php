<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
// require APPPATH.'/libraries/REST_Controller.php';

class Example extends REST_Controller
{
    
    public function __construct()
    {
        $this->rest_format = 'json';
        parent::__construct();
    }

	function user_get()
    {
        if(!$this->get('id'))
        {
        	$this->response(NULL, 400);
        }

        // $user = $this->some_model->getSomething( $this->get('id') );
    	$users = array(
			1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
			2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!', array('hobbies' => array('fartings', 'bikes'))),
		);
		
    	$user = @$users[$this->get('id')];
    	
        if($user)
        {
            $this->response($user, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }
    
    function user_post()
    {
        //$this->some_model->updateUser( $this->get('id') );
        $message = array('id' => $this->get('id'), 'name' => $this->post('name'), 'email' => $this->post('email'), 'message' => 'ADDED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function user_delete()
    {
    	//$this->some_model->deletesomething( $this->get('id') );
        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function users_get()
    {
        //$users = $this->some_model->getSomething( $this->get('limit') );
        $users = array(
			array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com'),
			array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => array('hobbies' => array('fartings', 'bikes'))),
		);

        // api/getData
        $users = array(
            'success' => true,
            'last_update' => '2011-09-14 11:32',
            'restaurant' => array(
                'id' => 1,
                'name' => 'MK1'
            ),
            'category' => array(
                '1' => array( 'name'=>'Thai Dishes', 'counting'=>25, 'child'=>array(
                    '1' => array( 'name'=>'Thai Dishes', 'counting'=>5 ),
                    '2' => array( 'name'=>'Curries', 'counting'=>20 ),
                ) ),
                '3' => array( 'name'=>'Drink', 'counting'=>5, 'child'=>array(
                    '1' => array( 'name'=>'Drink', 'counting'=>5 ),
                ) ),
            ),
            'dish' => array(
                array(
                    'id' => '1',
                    'name' => 'Salapoa',
                    'category_id' => '1',
                    'thumbnail' => 'images.png',
                    'detail' => 'xxxxxxxxxxxxxxxxxxxxxxx',
                    'prices' => array(
                        array( 'size'=>'S', 'price'=>'120' ),
                        array( 'size'=>'M', 'price'=>'160' ),
                        array( 'size'=>'L', 'price'=>'200' ),
                    )
                ),
                array(
                    'id' => '2',
                    'name' => 'Salapoa',
                    'category_id' => '1',
                    'thumbnail' => 'images.png',
                    'detail' => 'xxxxxxxxxxxxxxxxxxxxxxx',
                    'prices' => array(
                        array( 'size'=>'S', 'price'=>'120' ),
                        array( 'size'=>'M', 'price'=>'160' ),
                        array( 'size'=>'L', 'price'=>'200' ),
                    )
                ),
                array(
                    'id' => '3',
                    'name' => 'Salapoa',
                    'category_id' => '1',
                    'thumbnail' => 'images.png',
                    'detail' => 'xxxxxxxxxxxxxxxxxxxxxxx',
                    'prices' => array(
                        array( 'size'=>'S', 'price'=>'120' ),
                        array( 'size'=>'M', 'price'=>'160' ),
                        array( 'size'=>'L', 'price'=>'200' ),
                    )
                ),
                array(
                    'id' => '4',
                    'name' => 'Salapoa',
                    'category_id' => '1',
                    'thumbnail' => 'images.png',
                    'detail' => 'xxxxxxxxxxxxxxxxxxxxxxx',
                    'prices' => array(
                        array( 'size'=>'S', 'price'=>'120' ),
                        array( 'size'=>'M', 'price'=>'160' ),
                        array( 'size'=>'L', 'price'=>'200' ),
                    )
                ),
                array(
                    'id' => '5',
                    'name' => 'Salapoa',
                    'category_id' => '1',
                    'thumbnail' => 'images.png',
                    'detail' => 'xxxxxxxxxxxxxxxxxxxxxxx',
                    'prices' => array(
                        array( 'size'=>'S', 'price'=>'120' ),
                        array( 'size'=>'M', 'price'=>'160' ),
                        array( 'size'=>'L', 'price'=>'200' ),
                    )
                ),
            )
        );

        // 
        
        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }


	public function send_post()
	{
		var_dump($this->request->body);
	}


	public function send_put()
	{
		var_dump($this->put('foo'));
	}
}