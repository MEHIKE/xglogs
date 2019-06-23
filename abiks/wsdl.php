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
                    'EUR',                    // currency - Makse valuuta (Võimalikud väärtused: ´EUR¡)
#                    '2011-07-15 20:00:00',        // date - Kuupäev ja kellaaeg, millal makse võlgnikult laekus.
#		array("startTime"=>"20040417 20:00:00"),
			'20040417 20:00:00',
                    'YURIY MIKHNO',     // description - Makse kirjeldus.
                    '1107349977',            // paymentId - Makse saatja süsteemi sisemine makse ID.     
                    'Normal',                // paymentType - Makse tüüp: (Võimalikud väärtused: ¥Normal¡, ¥Internal¡)
                    152162,                // requestId - Algse nõude viitenumber, mille abil tuvastatakse klient nii EMT kui ka inkassofirma süsteemis. (Kohustuslik kõikides sõnumites)
                    'EMT'                    // senderCode - Kood tuvastamaks sõnumi saatjat. (Võimalikud väärtused: ´EMT¡, ´CRE¡)
                );


