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
        $json = $request->query->get('json');
        
        // $phone = $json->phone;
        $phone = $request->query->get('phone');
        $code = is_numeric($request->query->get('code')) ? random_int(1000, 9999) : $request->query->get('code');
        
        $data = date('Y-m-d H:i', strtotime("+1 min"));
        
        if ($phone) {
            
            $users = $connection->fetchAllAssociative("SELECT * FROM users WHERE phone='{$phone}'");
            $code_ = $connection->fetchAllAssociative("SELECT * FROM codes WHERE user_id='{$users[0]['id']}'");
            // var_dump();
            if ($code_[0]['block_time'] == null || $code_[0]['block_time'] < date('Y-m-d H:i')) {
                if ($users[0]['id']) {

                    if (!isset($code_[0]['user_id'])) {
                        $sql = "INSERT INTO codes ( id, code, user_id, datetime) VALUES ( 0 ,{$code}, '{$users[0]['id']}', '{$data}')";
                    } else {
                        $sql = "UPDATE codes SET code = '{$code}', datetime = '{$data}' WHERE user_id = '{$users[0]['id']}'";    
                    }

                    $connection->prepare($sql)->execute();

                    if (date('Y-m-d H:i') < $code_[0]['datetime']) {
                        $code = $request->query->get('code');
                    } else {

                        $code = random_int(1000, 9999);
                        if ($code_[0]['upd_count']+1 > 3 ){
                            $sql = "UPDATE codes SET code = '{$code}', datetime = '{$data}',block_time = '". ( date('Y-m-d H:i', strtotime('+1 hour')) ) ."' WHERE user_id = '{$users[0]['id']}'";
                        } esle {
                            $sql = "UPDATE codes SET code = '{$code}', datetime = '{$data}',upd_count = '{$code_[0][upd_count]+1}' WHERE user_id = '{$users[0]['id']}'";   
                        } 

                    }

                    $connection->prepare($sql)->execute();

                } else {
                    return $this->json([
                        'message' => 'Phone number not in DB',
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
                'message' => 'Sended you phone number',
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        //var_dump(date('Y-m-d H:i') > $data);

        //var_dump($phone);
     
        echo '<pre>';
        var_dump($users);
        
        //if (null === $user) {
        //    return $this->json([
        //        'message' => 'missing credentials',
        //    ], Response::HTTP_UNAUTHORIZED);
        //}
        
        return $this->json([
            'message' => 'Welcome to your authorized!',
            'code' => $code,
            'phone' => $users[0]['phone']
        ]);
    }
}
