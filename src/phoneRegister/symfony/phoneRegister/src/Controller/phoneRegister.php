<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class of realisation methods 
 * for phoneRegister processes
 *
 * @author root
 */
class phoneRegister {
    //method of random generate
    public function number(): Response
    {
        $number = random_int(1000, 9999);

        return new Response(
            '<html><body>Lucky number: '.$number.' Phone '.$_GET['phone'].'</body></html>'
        );
    }
}
