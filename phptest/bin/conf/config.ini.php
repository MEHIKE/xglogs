
; logifailide töötlemiseks vajalikud kataloogid
; vajalikud kataloogid processed kataloog kaob hiljem. temp on scriptide progemisel vajalik ainult, muidu ei kasutata
[paths]
incoming_path = /testlogs/incoming/
archive_path = /testlogs/arhiiv/
processed_path = /testlogs/incoming/processed/
temp_path = /home/webapp/phptest/bin/



; vajalikud erinevad confimuutujad siin. 
; days - mitu päeva vanu logisid pumbatakse sisse ja arhiveerimisel, vanemad pakkimata logifailid kustutatakse
; mitu päeva tagasi vaadatakse faile remote serveritest tõmbamiseks
[others]
days = 2



; failinime eesliide (hosts name) milline sona peab sisalduma failinimes
; logide sisse pumpamise e. kopimise script. Kui ei peaks logifaili nimes alguses olema node nime, 
; siis lisatakse see, kui logi nimes sisaldub vaartus
[prefix]
vaartus = xgate,tva,mdp



; failinime lõpu (-23) lisamine. kui pole tunni vaid päevafailid näiteks. milline sõna peab sisalduma
; logide sisse pumpamise e. copymise script. kuna osadel logidel ei teki tunnifaile, siis lisatakse "-23" (tund) failinime lõppu
; kui logifaili nimes sisaldub väärtus
[sufix]
vaartus = servicegate,services_bus,mdp,tva



; kui failinimes sees, siis seda ei kopita. logise sisse pumpamise e. copymise script
[exclude]
vaartus = message_center,AutoProbe,Introscope,banklink,errorlog,usertrack,compressed,static_content,tva,ccm_processor,OracleDBAgent,errors,mdp,mdp.log



; millised failinimed tulevad ainult sisse (failinimes sisaldub). logide sisse pumpamise e. copymise script
;[included]
;vaartus = dealgate,witb,kit,www_admin,web_krk,mdp,xgate_krk,xgate_wli,wapmail,xgate,servicegate,pop,diilitb


; mälu parameetrid
[memory]
mem_size = 2G


; vead ja veateated
; hetkel veel pole realiseeritud see koht
[errors]
error_mail = rynno.ruul@emt.ee,mehike@sms.emt.ee
warning_mail = rynno.ruul@emt.ee
max_filesize = 1073741824
;max_filesize = 100
max_filesize_warning = 536870912
;max_filesize_warning = 100
max_filesize_message = "Faili '%1$s' suurus [%3$s] on suurem lubatavast [%2$s], kontrolli asja!"
max_filesize_warningmessage = "Faili '%1$s' suurus [%3$s] on suurem kui keskmine: [%2$s], võib kontrollida asja!"



