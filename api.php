<?php
class Api extends Rest
{


    public $dbConn;

    public function __construct()
    {
        parent::__construct();

        $db = new DbConnect;
        $this->dbConn = $db->connect();
    }

    public function generateToken()
    {
        // print_r($this->param);

        // Passo la email alla funzione validateParameter per verificare se è vuota e generare un errore

        $email = $this->validateParameter('email', $this->param['email'], STRING);

        $pass = $this->validateParameter('pass', $this->param['pass'], STRING);

        try {
            $stmt = $this->dbConn->prepare("SELECT * from users WHERE email = :email AND password = :pass");

            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":pass", $pass);

            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!is_array($user)) {
                $this->returnResponse(INVALID_USER_PASS, "Email or password is incorrect");
            }

            if ($user['active'] == 0) {
                $this->returnResponse(USER_NOT_ACTIVE, "User is not activivated. Please contact to admin.");
            }

            $payload = [
                'iat' => time(),
                'iss' => 'localhost',
                'exp' => time() + (15 * 60),
                'userId' => $user['id']
            ];

            $token = JWT::encode($payload, SECRETE_KEY);

            $data = ['token' => $token];

            $this->returnResponse(SUCCESS_RESPONSE, $data);
        } catch (Exception $e) {

            $this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
        }
    }

    public function addCustomer()
    {
        $name = $this->validateParameter('name', $this->param['name'], STRING, false);
        $email = $this->validateParameter('email', $this->param['email'], STRING, false);

        $addr = $this->validateParameter('addr', $this->param['addr'], STRING, false);
        $mobile = $this->validateParameter('mobile', $this->param['mobile'], STRING, false);

        try {
            $token = $this->getBearerToken();
            $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
            print_r($payload->userId);


            $stmt = $this->dbConn->prepare("SELECT * from users WHERE id = :userId");

            $stmt->bindParam(":userId", $payload->userId);

            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!is_array($user)) {
                $this->returnResponse(INVALID_USER_PASS, "This user is not found in our database");
            }

            if ($user['active'] == 0) {
                $this->returnResponse(USER_NOT_ACTIVE, "This user may be deactivivated. Please contact to admin.");
            }

            $cust = new Customer;
            $cust->setName($name);
            $cust->setEmail($email);
            $cust->setAddress($addr);
            $cust->setMobile($mobile);
            $cust->setCreatedBy($payload->userId);
            $cust->setCreatedOn(date('Y-m-d'));

            $booStatus = true;

            if (!$cust->insert()){
                $errMsg = 'Failed to insert';
                $booStatus = false;
            } else {
                $message = 'Insertd successfully';
            }

            $this->returnResponse(SUCCESS_RESPONSE, $message);


        } catch (Exception $e) {
            $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        }
    }
}
