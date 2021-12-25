<?php 

require_once('./constants.php');


class Rest {

    protected $request;
    protected $serviceName;
    protected $param;

    public function __construct(){
        
        //Controllo se il metodo è post altrimenti invio un errore
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            $this->throwError(REQUEST_METHOD_NOT_VALID, 'Request method is not valid');
        };

        //recupero il contenuto del post
        $handler = fopen('php://input', 'r');
        $this->request = stream_get_contents($handler);

        //Valido il post arrivato
        $this->validateRequest();

        
    }

    public function validateRequest(){
        //Controllo il type arrivato
        
        // echo $_SERVER['CONTENT_TYPE'];

        if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
            $this->throwError(REQUEST_CONTENT_TYPE_NOT_VALID, 'Request content type is not valid');
        }

        $data = json_decode($this->request, true);

        if (!isset($data['name']) || $data['name']==""){
            $this->throwError(API_NAME_REQUIRED, "API name is required");
        }

        $this->serviceName = $data['name'];
        
        if (!is_array($data['param'])){
            $this->throwError(API_PARAM_REQUIRED, "API PARAM required");
        }

        $this->param = $data['param'];

        print_r($this->param);



    }

    public function processApi(){

    }

    public function validateParameter($fieldname, $value, $dataType, $required){

    }

    public function throwError($code, $message){
        header("content-type: application/json");
        $errorMsg = json_encode(['error'=>['status'=>$code, 'message'=>$message]]);
        echo $errorMsg; 
        exit;
    }

    public  function returnResponse()
    {
        
    }



        
    
}






?>