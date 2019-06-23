
; logifailide töötlemiseks vajalikud kataloogid
; vajalikud kataloogid processed kataloog kaob hiljem. temp on scriptide progemisel vajalik ainult, muidu ei kasutata
[paths]
incoming_path = /logstest/incoming/
archive_path = /logstest/arhiiv/
processed_path = /logstest/incoming/processed/
temp_path = /home/webapp/phplogs/bin/



; vajalikud erinevad confimuutujad siin. 
; days - mitu päeva vanu logisid pumbatakse sisse ja arhiveerimisel, vanemad pakkimata logifailid kustutatakse
; mitu päeva tagasi vaadatakse faile remote serveritest tõmbamiseks
[others]
days = 2



; failinime eesliide (hosts name) milline sona peab sisalduma failinimes
; logide sisse pumpamise e. kopimis escript. Kui ei peaks logifaili nimes alguses olema node nime, 
; siis lisatakse see, kui logi nimes sisaldub vaartus
[prefix]
vaartus = xgate,tva,friendnumber,krrstat,mediator,policegate,tbcis,travelsim,welcome,server



; failinime lõpu (-23) lisamine. kui pole tunni vaid päevafailid näiteks. milline sõna peab sisalduma
; logide sisse pumpamise e. copymise script. kuna osadel logidel ei teki tunnifaile, siis lisatakse "-23" (tund) failinime lõppu
; kui logifaili nimes sisaldub väärtus
[sufix]
vaartus = servicegate,services_bus,mdp,tva,friendnumber,krrstat,mediator,policegate,tbcis,travelsim,welcome,server



; kui failinimes sees, siis seda ei kopita. logise sisse pumpamise e. copymise script
[exclude]
vaartus = message_center,AutoProbe,Introscope,banklink,errorlog,usertrack,compressed,static_content,tva,ccm_processor,OracleDBAgent,dlq



; millised failinimed tulevad ainult sisse (failinimes sisaldub). logide sisse pumpamise e. copymise script
; hetkel veel pole realiseeritud see koht
;[include]
;vaartus = dealgate,witb,kit,www_admin,web_krk,mdp,xgate_krk,xgate_wli,wapmail,xgate,servicegate,pop,diilitb



; vead ja veateated
; hetkel veel pole realiseeritud see koht
;[errors]
;error_mail = rynno.ruul@emt.ee
;max_size = 2GB
;max_size_errormessage = "Faili suurus on suurem lubatavast (2GB) = %1, vaata fail %2 üle!"




