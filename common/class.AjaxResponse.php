<?php

class common_AjaxResponse
{
    public function __construct($success=true, $type='json', $data=null, $message='')
    {
        $context = Context::getInstance();
        $context->getResponse()->setContentHeader('text/json');
        $response = array(
            'success'           => $success
            , 'type'            => $type
            , 'message'         => $message
            , 'data'            => $data
        );
        echo json_encode($response);
    }
}
?>
