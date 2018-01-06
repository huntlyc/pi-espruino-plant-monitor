<?php
    define('AUTH_TOKEN', 'YOUR_MADE_UP_TOKEN');

    class PlantSystem{
        public $status = array(
            'badReq' => 400,
            'ok' => 200,
            'unauth' => 401,
            'err' => 520
        );

        protected $config = null;

        public function __construct(){

            $this->config = json_decode(file_get_contents('config.json'));


            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                
                if(isset($_POST['auth'],$_POST['moisture'],$_POST['temperature'])){

                    if($this->authenticateRequest($_POST['auth'])){
                        
                        $t = $_POST['temperature'];
                        $t = doubleval($t);

                        $m = $_POST['moisture'];
                        $m = intval($m);

                        if(is_numeric($t) && is_numeric($m) && $m >= 0){

                            if($this->savePlantData($t,$m)){
                                $this->sendResponse(
                                    $msg = "Data received",
                                    $respCode = $this->status['ok']
                                );
                            }else{
                                $this->sendResponse(
                                    $msg = "Couldnt save data",
                                    $respCode = $this->status['err']
                                );
                            }
                        }else{
                            $this->sendResponse(
                                $msg = "Invalid data - temp and moisture must be numeric and moisture must be greater than or equal to 0",
                                $respCode = $this->status['badReq']
                            );
                        }
                    }else{
                        $this->sendUnauth();
                    }
                }else{
                    $this->sendResponse(
                        $msg = "Missing required data", 
                        $respCode = $this->status['badReq']
                    );
                }
            }else if($_SERVER['REQUEST_METHOD'] === 'GET'){
                if(isset($_GET['auth']) && $this->authenticateRequest($_GET['auth'])){
                    $this->sendResponse(
                        $msg = $this->getPlantData(),
                        $respCode = $this->status['ok']
                    );
                }else{
                    $this->sendUnauth();
                }

            }else{
                $respCode = $this->status['badReq'];
                $msg = "Hello, World!";
            }

        }

        public function sendUnauth(){
            $this->sendResponse('Not Authorized', $this->status['unauth']);
        }

        public function sendResponse($msg, $httpRespCode){
            echo "{$msg}\n";
            http_response_code($httpRespCode); 
        }


        /**
         * authenticateRequest($token)
         *
         * checks if $token is valid
         *
         * @param str $token - token to be authenticated
         * @return book $isValid 
         **/
        public function authenticateRequest($token){
            $isValid = false;
            if($token === hash('sha256', $this->config->token)){
                $isValid = true;
            }

            return $isValid;
        }


        /**
         * savePlantData($t, $m)
         *
         * Saves data to a file called 'sensor_data'
         *
         * @param $t - temperature data
         * @param $m - moisture data
         * @reutn void;
         **/
        public function savePlantData($t, $m){
            $saved = true;
            $data = json_encode(array('date' => date('Y-m-d H:i'), 't' => $t, 'm' => $m));
            $saveStatus = file_put_contents('sensor_data', $data);  
            if($saveStatus === FALSE){
                $saved = FALSE;
                $err = 'ERR: could not save plant info';
                error_log($err);
                echo "\n\n{err}\n\n";
            }
            return $saved;
        }

        public function getPlantData(){
            $ret = 'no data available';

            if(file_exists('sensor_data')){
                $jsonStr = file_get_contents('sensor_data');

                if(!empty($jsonStr)){
                    $ret = $jsonStr;
                }
            }

            return $ret;
        }
    } 

    try{
        $ps = new PlantSystem();
    }catch(Exception $e){
        echo "Error: {$e}";
    }

    exit;


