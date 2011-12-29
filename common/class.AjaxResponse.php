<?php

class common_AjaxResponse
{
    public function __construct($options)
    {
        $success    = isset($options['success'])    ?$options['success']    :true;
        $type       = isset($options['type'])       ?$options['type']       :'json';
        $data       = isset($options['data'])       ?$options['data']       :null;
        $message    = isset($options['message'])    ?$options['message']    :'';
        
        //position the header of the response
        $context = Context::getInstance();
        $context->getResponse()->setContentHeader('text/json');
        //set the response object
        $response = array(
            'success'           => $success
            , 'type'            => $type
            , 'message'         => $message
            , 'data'            => $data
        );
        //write the response
        echo json_encode($response);
    }
}
?>
