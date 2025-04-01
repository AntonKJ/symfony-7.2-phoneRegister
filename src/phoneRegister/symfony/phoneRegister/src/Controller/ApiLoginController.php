<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiLoginController extends AbstractController
{
    public $block_time;
    
    #[Route('/api/login', name: 'api_login')]
    public function index(#[CurrentUser] ?User $user, Connection $connection): Response
    {
        
        $request = Request::createFromGlobals();
        $json = json_decode($request->getContent());  // $request->query->get('phone');
        

        
        $phone = isset($json->phone) ? $json->phone : false; // $request->query->get('phone'); $request->query->get('code')
        
        if (isset($json->phone)) {
            $code = (!isset($json->code) || !is_numeric($json->code)) && (isset($json->phone) && $this->number_validate($json->phone)) ? random_int(1000, 9999) : ($json->code ? $json->code : false) ; 
        }
        if (!isset($code) || !$code) {
            return $this->json([
                'message' => 'You must send you number',
            ], Response::HTTP_UNAUTHORIZED);;
        }
        
        if (!is_numeric($json->code) && $this->number_validate($json->phone)) {
            
            // code sended to user phone 
            // $this->sendSMS($phone,$code);
            
            return $this->json([
                        'message' => 'System Creates new code and send sms to your phone, you must sended code and phone  back with API',
                        //'code' => $code
                    ], Response::HTTP_ACCEPTED);
        }
        
        $date = date('Y-m-d H:i', strtotime("+1 min"));
        
        if ($phone) {
            
            $users = $connection->fetchAllAssociative("SELECT * FROM users WHERE phone='{$phone}'");
            $code_ = $connection->fetchAllAssociative("SELECT * FROM codes WHERE user_id='{$users[0]['id']}'");
            // var_dump();
            if ($code_[0]['block_time'] == null || $code_[0]['block_time'] < date('Y-m-d H:i')) {
                if ($users[0]['id']) {
                    
                    if ($code_[0]['code'] == $code) {
                        
                        /** User authorisided
                        // some
                            // action
                            // after comparison
                         * 
                         */
                        
                        return $this->json([
                            'message' => 'Welcome to your authorized!',
                            'code' => $code,
                            'phone' => $users[0]['phone']
                        ]);
                    }

                    if (!isset($code_[0]['user_id'])) {
                        $sql = "INSERT INTO codes ( id, code, user_id, datetime) VALUES ( 0 ,{$code}, '{$users[0]['id']}', '{$date}')";
                    } else {
                        $sql = "UPDATE codes SET code = '{$code}', datetime = '{$date}' WHERE user_id = '{$users[0]['id']}'";    
                    }

                    $connection->prepare($sql)->execute();

                    if (date('Y-m-d H:i') < $code_[0]['datetime']) {
                        //$code = $request->query->get('code');
                        
                    } else {

                        $code = random_int(1000, 9999);
                        if ($code_[0]['upd_count']+1 > 3 ){
                            $sql = "UPDATE codes SET code = '{$code}', datetime = '{$data}',block_time = '". ( date('Y-m-d H:i', strtotime('+1 hour')) ) ."' WHERE user_id = '{$users[0]['id']}'";
                        } else {
                            $sql = "UPDATE codes SET code = '{$code}', datetime = '{$data}',upd_count = '{($code_[0][upd_count]+1)}' WHERE user_id = '{$users[0]['id']}'";   
                        } 

                    }

                    $connection->prepare($sql)->execute();

                } else {
                    return $this->json([
                        'message' => 'Phone number not exist in DB',
                    ], Response::HTTP_UNAUTHORIZED);
                }
            } else {
                    return $this->json([
                        'message' => 'You limit sms requests is exceeded, '
                        . 'you can come back after ' . $code_[0]['block_time'],
                    ], Response::HTTP_UNAUTHORIZED);
                }
            
        } else {
            return $this->json([
                'message' => 'You must send you number',
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        //var_dump(date('Y-m-d H:i') > $data);
        //var_dump($phone);

        
        //if (null === $user) {
        //    return $this->json([
        //        'message' => 'missing credentials',
        //    ], Response::HTTP_UNAUTHORIZED);
        //}
        
    }
    
    function number_validate($phone) {
        // Удаляем все не символы кроме цифр
        $phone = preg_replace('/\D/', '', $phone);

        // Номер должен начинается на цифру 7
        if (substr($phone, 0, 1) !== '7') {
          return false;
        }

        // Длиной 10 символов
        if (strlen($phone) !== 10) {
          return false;
        }
  
        return true;
    }
}
