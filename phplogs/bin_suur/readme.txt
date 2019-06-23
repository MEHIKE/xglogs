Rakenduste logide arhiveerimine


Koosneb kahest etapist (lühike selgitus):

1etapp. copylogs.php script
	Kuna enamus logisid roteeritakse iga tund, siis tehakse tegevust
	peale igat täistundi, kui lõplikud logifailid on tekkinud.
	koosneb kolmest tegevusest:
	a. Tõmmatakse erinevatest serveritest logid 'incoming' kataloogi XGLOGS masinas.
	b. Kopeeritakse logid 'processed' kataloogi, samal ajal neid veidi
	ümber töödeldes. Töötlemine seisneb selles, et kontrollitakse igal
	real, kellaaja ja serveri nime olemasolu, vajadusel need lisatakse.
	Rohkem logisid ei muudeta - need muudatused on vajalikud arhiveerimise
	scripti (2etapp) logiridade sorteerimiseks ning hiljem logidest
	vajalike ridade otsimisel (mis kelleajal ja mis nodes tegevus tehti).
	Töötlemise käigus leitakse read, mis peavad olema olema
	vigade weebilehel ühel real ja liidetakse need. Nende ridade
	lõppu pannakse '\n' märge, Täisridade lõppu pannakse '\r\n' märge.
	c. kustutatakse töödeldud failid 'incoming' kataloogist.

2etapp. arhiveeri.php script
	koosneb kahest tegevusest:
	a. Iga päeva varahommikul, peale südaööd liidetakse ühe päeva
	ühe rakenduse logid üheks suureks failiks ning samal ajal sorteeritakse 
	read kellaaja järgi. fail tekitatakse 'arhiiv' kataloogi.
	b. eelmise etapi tulemusena saadud fail pakitakse kokku.

        PS! Vastavalt Erki soovile, sai see koht ringi tehtud ja tunnifaile
        enam päevafailideks kokku ei liideta vaid jäetakse tunnifailideks,
        kus on node logide read kokku pandud ja kellaaja järgi sorteeritud.
        Igale päevale tehakse eraldi alamkataloog '/logs/arhiiv/' kataloogi.





Veidi täpsemalt tegevustest ja konfifailidest:

Scriptide tööks vajalikud lisafailid:
	abiscript:
		config.class.php - konfifailide muutujate kasutamiseks lisaxript.
	konfidfailid:
		hosts_conf.php   - siin on kirjas serverid, kasutajanimed ja logikataloogid.
				   muutujate kuju on järgmine:
				   massiivimuutuja $hosts kus on kaks välja iga serveri puhul
				   $hosts[serveri nimi][user]='serveri kasutajanimi'
				   $hosts[serveri nimi][path]='logide kataloog serveris'
				   lisatingimuseks on paroolivaba lugemine remote serverist.
		config.ini.php   - siin kirjas kõik scriptide tööks vajalikud konstantsed muutujad.
				   neid muutujaid loetakse abiscripti (config.class.php) abil.
				   muutujad on jaotatud eraldi sktsioonide abil.
				   [paths] - scriptide tööks vajalikud kataloogid.
					incoming_path  = 'kataloog kuhu remote serverist logid kopeeritakse'
					processed_path = 'kataloog kuhu pannakse töödeldud failid'
					archive_path   = 'kataloog kuhu lähevad kokku pakitud logifailid'
				   [others] - muud parameetrid
					days =     mitu päeva vanasid logisid kopeeritakse remote serevrist
					           ja sellest vanemad logid kustutatakse 'processed' kataloogist.
				   [prefix] - osadel logifailidel puudub nodenimi
					vaartus =  siin on kirjas sõnad, mis failinimes peavad sisalduma,
						   ja nende failinimede ette lisatakse vastava node nimi, et logifailide
						   nimed oleksid hilisemaks töötlemiseks standartsed.
						   sõnad eraldatakse omavahel komaga.
				   [sufix] - osad logifailid ei roteerita iga tnd vaid alles päeva lõpus.
					vaartus =  siin on kirjas sõnad, mis failinimes peavad sisalduma,
						   ja nende failinimede lõppu lisatakse päeva viimane tund '-23',
						   et logifailide nimed oleksid hilisemaks töötlemiseks standartsed.
						   sõnad eraldatakse omavahel komaga.
				   [exclude] - logikataloogides on ka muid meile mitte vaja minevaid faile/logisid.
					vaartus =  siin on kirjas sõnad, mis failinimedes ei tohi sisaldauda
						   ja neid faile ei tõmmata remote serverist.
						   sõnad eraldatakse omavahel komadega.
				   [include] - millised on kindlad sõnad, mis peavad sisalduma failinimedes.
					vaartus =  siin on kirjas sõnad, mis peavad failinimedes sisalduma.
						   üldjuhul piisab eelmisest muutujast ja 'include' pole vajadust
						   kasutada. pigem võib segadust tekitada. igaks juhuks siiski
						   selline võimalus scriptis sees, kui peaks tekkima vajadus.
						   sõnad eraldatakse omavahel komaga.
				   [memory] - mälumuutujad
					mem_size = kasutatav php mälumaht. kuna standartne maht on suhteliselt
						   väike, siis scriptides kasutatavate suurte massiidide jaoks
						   vajalik määrata suurem maht. 
				   [errors] - vead ja veateated. 
					error_mail   = siin kirjas mailiaadressid, kellele saadetakse veateated.
						       mailiaadressid on eraldatud komaga.
					warning_mail = siin kirjas mailiaadressid, kellele saadetakse hoiatuse teated.
						       mailiaadressid on eraldatud komaga.
					max_filesize = faili suurus, mille puhul saadetakse veateade.
					max_filesize_warning = faili suurus, mille puhul saadetakse hoiatus.
					max_filesize_message = veateade, mis saadetakse.
					max_filesize_warningmessage = hoiatus, mis saadetakse


kopeerimise script copylogs.php. Sellest täpsemalt ja kuidas see toimib.
	a.Loetakse mällu incoming kataloogi failinimed.
	b.Loetakse mällu processed kataloogist failinimed.
	c.Mergetakse kokku need mälumassiivid. (leitakse kõik juba kopeeritud failid)
	d.Tõmmatakse kõik uued logifailid remote serveritest serverite kaupa.
	  Kontrollitakse, et ei oleks vanemad kui konfis määratud päevade arv.
	  Kontrollitakse, et poleks juba kopeeritud.
	  Kopeeritakse tingimustele vastavad failid (tingmimused konfifailis) ja
	  nimetatakse failid kopeerimise käigus ühtse struktuuriga failinimeks.
	  Saadetakse meil, kui failisuurus ol ootamatult liiga suur.
	e.Tõstetakse sisse tõmmatud failid processed kataloogi.
	  Selle käigus kontrollitakse logifailid rida-realt üle, kas
	  igal real kellaaeg ja serveri nimi ning vajadusel lisatakse.
	  See on vajalik hilisemaks sorteerimiseks kellaaja järgi ning
	  logide vaatamisel grep käsuga, et saaks kuvada ikka kõiki ridu.
	  Read, mis peaksid olema ühel real (veateated, listid, jne)
	  jäetakse ühele reale ja nende ette ei panda andmeid. Selle
	  kontrollimiseks on scriptis eritöötlus.
	f.scripti lõpp. -> töötab tavaliselt loetud sekunditega (sub1min kuni 4-5min). 
	  Peale iga täistundi päevasel ajal.
sub1min 
arhiveerimise script. Sellest täpsemalt ja kuidas see toimib.
	On suhteliselt keeruline aga üldiselt lahti seletatult kulgeb kõik järgmiselt:
	a.Loetakse mällu kõik processed kataloogi failinimed.
	b.Tehakse korduses üks rakendus korraga
	c.Tehakse sisemises korduses üks päev korraga kui päev ei ole tänane päev.
	  Kokku pakitakse ainult vanemad failid kui confifailis märgitud.
	d.arhiveeritakse
	  Kui rakendus on ainult ühel nodel, siis pakitakse tunnifailid lihtsalt kokku.
	  Muidu tehakse kordus üle tundide, kus
	    loetakse rakenduste kaupa read mällu (otse failist lugemine oli ca 2x aeglasem)
	    reakaupa vaadatakse, kas kellaaeg on uuem või vanem ning pannakse read
	    omakorda vastavalt tulemusmassiivi mälus. Iga tunni töötlemisel salvestatakse
	    tulemus faili ning pakitakse see fail kokku. Pakitud fail pannakse
	    arhiivi kataloogis päeva alamkataloogi kujul: '/aasta/kuu/päev/'
	e.kustutatakse vanemad failid processed kataloogist (konfifailis määratud päevade arv)
	  vastavalt siis töödeldud failid (ühe rakenduse ühe päeva tunnifailid)
	f.scripti lõpp. - töötab tavaliselt alla 1tunni (oleneb failide suurustest), 
	  1x päevas, öösel.

