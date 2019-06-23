#!/usr/local/bin/php -q
<?php

	$login = 'credit.reform';
        $password = '12345';
        //$wsdl_url = dirname(__FILE__) . '/cred_Incasso_prelive.wsdl';
	$wsdl_url = '/home/webapp/test/cred_Incasso_prelive.wsdl';
        $params = array('location'   => 'https://web.emt.ee:30000/services_krk_process/Incasso?WSDL'
                        , 'soap_version' => 'SOAP_1_2');
 
        $time = array("startTime"=>"20040417 20:00:00 ");
                                        
        $client = new SoapClient($wsdl_url, $params);
        
        $vastus = $client->sendDebtPayment(
                    100.00,                    // amount - Makse summa
                    'EUR',                    // currency - Makse valuuta (V�imalikud v��rtused: �EUR�)
#                    '2011-07-15 20:00:00',        // date - Kuup�ev ja kellaaeg, millal makse v�lgnikult laekus.
#		array("startTime"=>"20040417 20:00:00"),
			'20040417 20:00:00',
                    'YURIY MIKHNO',     // description - Makse kirjeldus.
                    '1107349977',            // paymentId - Makse saatja s�steemi sisemine makse ID.     
                    'Normal',                // paymentType - Makse t��p: (V�imalikud v��rtused: �Normal�, �Internal�)
                    152162,                // requestId - Algse n�ude viitenumber, mille abil tuvastatakse klient nii EMT kui ka inkassofirma s�steemis. (Kohustuslik k�ikides s�numites)
                    'EMT'                    // senderCode - Kood tuvastamaks s�numi saatjat. (V�imalikud v��rtused: �EMT�, �CRE�)
                );


