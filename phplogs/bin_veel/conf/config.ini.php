
; logifailide töötlemiseks vajalikud kataloogid
; vajalikud kataloogid processed kataloog kaob hiljem. temp on scriptide progemisel vajalik ainult, muidu ei kasutata
[paths]
incoming_path = /logs/incoming1/
archive_path = /logs/arhiiv/
processed_path = /logs/incoming/processed/
temp_path = /home/webapp/phplogs/bin/
log_path = /var/www/log/



; vajalikud erinevad confimuutujad siin. 
; days - mitu päeva vanu logisid pumbatakse sisse ja arhiveerimisel, vanemad pakkimata logifailid kustutatakse
; mitu päeva tagasi vaadatakse faile remote serveritest tõmbamiseks
[others]
days = 5



; failinime eesliide (hosts name) milline sona peab sisalduma failinimes
; logide sisse pumpamise e. kopimis escript. Kui ei peaks logifaili nimes alguses olema node nime,
; siis lisatakse see, kui logi nimes sisaldub vaartus
[prefix]
vaartus = xgate,tva,friendnumber,krrstat,mediator,policegate,tbcis,mml,travelsim,server,accumulator,shokkarve,www_liferay,static_content,eshopProductsSync,imeiBlacklist,maacRecommendations,mobileid,monitor,repricing,risProductsFullUpdate,serviceAgeCheck,susgRecommendations,telcocrm,wapmailUpdate,wapmailPoller,dataServiceState,mahtNotification,mapp_mml,smsDataLoad,rating,elionbundle,fnrOptimize,prepCampChanger,shoppingCartCleanup,simpelCampaign,papi,papiStatisticsJob,paym2esb,ppbalance,retailSerialNumbers,unsignedContractsSync,notif,nameReplication,vembuts,tembuts,emt_texts,map-bolt,mapp-ds,liferay,midcert,coca,debtorsInfo,dealgate,servicegate,mapp-bsloader,mapp-cache,mapp-eedusync,mapp-incloader,mapp-logstats,mapp-prov,mapp_bsloader,mapp_cache,mapp_eedusync,mapp_incloader,mapp_logstats,mapp_prov,map_bolt,mapp_ds,kiDataImport,mapp-energia,mapp-retailletters,services_bus,mapp-cndb,mapp-kml,infogate,ccm_processor,mapp-cfg,mapp-extra,mapp_extra,mapp-nbo,mapp_nbo,mapp-numbriliikuvus,mapp_numbriliikuvus,mapp_cndb,mapp_energia,mapp_kml,mapp-mml,mapp_retailletters,cssdkprod1,cssdkprod2,cssdk-prod1,cssdk-prod2,cssdk_prod1,cssdk_prod2,kreedo_message_router,kreedo-message-router,mapp-suhtlus,mapp_suhtlus,mapp-vcard,mapp_vcard,mapp-mhrfraud,mapp_mhrfraud,mapp_eshopsync,mapp-eshopsync,papiStatisticsJob,paym2esb,mapp-bankport-audit,mapp_bankport_audit,mapp-bankport,mapp_bankport,mapp-mediator,mapp_mediator,mapp-prepaid,mapp_prepaid,jdbc,cssnoprod1,cssnoprod2,cssno-prod1,cssno-prod2,cssno_prod1,cssno_prod2,mapp-mobileid,mapp_mobileid,mapp-krediidiregister,mapp_krediidiregister,mapp_prepaid,mapp-cfg,mapp_cfg,mapp-logstats,mapp_logstats,mapp-mhrfraud,mapp_mhrfraud,mapp-watchdog,mapp_watchdog,mapp-nbm,mapp_nbm,mapp-welcome,mapp_welcome,mapp-myab,mapp_myab,mapp-finder,mapp_finder,mapp-bankport-audit,mapp_bankport-audit,mapp_bankport_audit,mapp-anam,mapp_anam,mapp-aliens,mapp_aliens,mapp-spotify,mapp_spotify,mapp-simorder,mapp_simorder,mapp-ingate,mapp_ingate,mapp-parkimine,mapp_parkimine,dealgate_jdbc,dealgate_debug,dealgate-jdbc,dealgate-debug


; failinime lõpu (-23) lisamine. kui pole tunni vaid päevafailid näiteks. milline sõna peab sisalduma
; logide sisse pumpamise e. copymise script. kuna osadel logidel ei teki tunnifaile, siis lisatakse "-23" (tund) failinime lõppu
; kui logifaili nimes sisaldub väärtus
[sufix]
vaartus = servicegate,services_bus,mdp,tva,friendnumber,krrstat,mediator,policegate,tbcis,mml,travelsim,server,accumulator,shokkarve,static_content,eshopProductsSync,imeiBlacklist,maacRecommendations,mobileid,monitor,repricing,risProductsFullUpdate,serviceAgeCheck,susgRecommendations,telcocrm,wapmailUpdate,wapmailPoller,rating,dataServiceState,mahtNotification,mapp_mml,smsDataLoad,elionbundle,fnrOptimize,prepCampChanger,shoppingCartCleanup,simpelCampaign,papi,papiStatisticsJob,paym2esb,ppbalance,retailSerialNumbers,unsignedContractsSync,notif,nameReplication,map-bolt,mapp-ds,midcert,coca,debtorsInfo,mapp-bsloader,mapp-cache,mapp-eedusync,mapp-incloader,mapp-logstats,mapp-prov,mapp_bsloader,mapp_cache,mapp_eedusync,mapp_incloader,mapp_logstats,mapp_prov,map_bolt,mapp_ds,kiDataImport,mapp-energia,mapp_retailletters,mapp-cndb,mapp-kml,ccm_processor,infogate,mapp-cfg,mapp-extra,mapp_extra,mapp-nbo,mapp_nbo,mapp-numbriliikuvus,mapp_numbriliikuvus,mapp_cndb,mapp_energia,mapp_kml,mapp-mml,mapp-retailletters,kreedo_message_router,kreedo-message-router,mapp-suhtlus,mapp_suhtlus,mapp-vcard,mapp_vcard,mapp-mhrfraud,mapp_mhrfraud,mapp_eshopsync,mapp-eshopsync,papiStatisticsJob,paym2esb,mapp-bankport-audit,mapp_bankport_audit,mapp-bankport,mapp_bankport,mapp-mediator,mapp_mediator,mapp-prepaid,mapp_prepaid,jdbc,mapp-mobileid,mapp_mobileid,mapp-krediidiregister,mapp_krediidiregister,mapp_prepaid,mapp-cfg,mapp_cfg,mapp-logstats,mapp_logstats,mapp-mhrfraud,mapp_mhrfraud,mapp-watchdog,mapp_watchdog,mapp-nbm,mapp_nbm,mapp-welcome,mapp_welcome,mapp-myab,mapp_myab,mapp-finder,mapp_finder,mapp-bankport-audit,mapp_bankport-audit,mapp_bankport_audit,mapp-anam,mapp_anam,mapp-aliens,mapp_aliens,mapp-spotify,mapp_spotify,mapp-simorder,mapp_simorder,mapp-ingate,mapp_ingate,mapp-parkimine,mapp_parkimine,dealgate_jdbc,dealgate_debug,dealgate-jdbc,dealgate-debug



; logide sisse pumpamise e. copymise script. kuna osad logide on eraldi kataloogis (messaging e. /log/messaging kataloogis), need rakendused
; kui logifaili nimes sisaldub väärtus
[messaging]
vaartus = friendnumber,rating,mediator,mml,policegate,tbcis,travelsim,accumulator,shokkarve,eshopProductsSync,imeiBlacklist,maacRecommendations,monitor,repricing,risProductsFullUpdate,serviceAgeCheck,susgRecommendations,telcocrm,wapmailUpdate,wapmailPoller,dataServiceState,mahtNotification,smsDataLoad,elionbundle,fnrOptimize,prepCampChanger,shoppingCartCleanup,simpelCampaign,papiStatisticsJob,paym2esb,ppbalance,retailSerialNumbers,unsignedContractsSync,notif,nameReplication,midcert,coca,debtorsInfo,kiDataImport,papiStatisticsJob,paym2esb


; kui failinimes sees, siis seda ei kopita. logise sisse pumpamise e. copymise script
[exclude]
vaartus = message_center,AutoProbe,Introscope,banklink,errorlog,usertrack,compressed,tva,OracleDBAgent,dlg,dgerror,gz,map-rcs,Tomcat,access-logstats,.out,.pid,epa.log,gc
;,jdbc
; vaartus = message_center,AutoProbe,Introscope,banklink,errorlog,usertrack,compressed,static_content,tva,ccm_processor,OracleDBAgent,dlg,dgerror,gz


; kui logi LEVEL pole 3 vaid 4s koht, siis vahetatakse need siinsete puhul
[change_level]
vaartus = mdp



; millised failinimed tulevad ainult sisse (failinimes sisaldub). logide sisse pumpamise e. copymise script
;[include]
;vaartus = dealgate,witb,kit,www_admin,web_krk,mdp,xgate_krk,xgate_wli,wapmail,xgate,servicegate,pop,diilitb

[gzip]
vaartus = ccm_processor,dealgate_jdbc,dealgate_debug

; mitu päeva tagasi vaadatakse faile remote serveritest tõmbamiseks
[addpre]
vaartus = -ts,-bolt,dk-prod1,dk-prod2,no-prod1,no-prod2

; kui vaja ara vahetada kuupaev.log kujult failinimi kopeerimise kaigus log.kuupaev
[replkp]
vaartus = map-bolt

; mälu parameetrid
[memory]
mem_size = 2G


; vead ja veateated
[errors]
error_mail = rynno.ruul@emt.ee,mehike@sms.emt.ee
warning_mail = rynno.ruul@emt.ee
max_filesize = 1073741824
;max_filesize = 100
max_filesize_warning = 536870912
;max_filesize_warning = 100
max_filesize_message = "Faili '%1$s' suurus [%3$s] on suurem lubatavast [%2$s], kontrolli asja!"
max_filesize_warningmessage = "Faili '%1$s' suurus [%3$s] on suurem kui keskmine: [%2$s], võib kontrollida asja!"




