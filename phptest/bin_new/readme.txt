Rakenduste logide arhiveerimine


Koosneb kahest etapist (l�hike selgitus):

1etapp. copylogs.php script
	Kuna enamus logisid roteeritakse iga tund, siis tehakse tegevust
	peale igat t�istundi, kui l�plikud logifailid on tekkinud.
	koosneb kolmest tegevusest:
	a. T�mmatakse erinevatest serveritest logid 'incoming' kataloogi XGLOGS masinas.
	b. Kopeeritakse logid 'processed' kataloogi, samal ajal neid veidi
	�mber t��deldes. T��tlemine seisneb selles, et kontrollitakse igal
	real, kellaaja ja serveri nime olemasolu, vajadusel need lisatakse.
	Rohkem logisid ei muudeta - need muudatused on vajalikud arhiveerimise
	scripti (2etapp) logiridade sorteerimiseks ning hiljem logidest
	vajalike ridade otsimisel (mis kelleajal ja mis nodes tegevus tehti).
	T��tlemise k�igus leitakse read, mis peavad olema olema
	vigade weebilehel �hel real ja liidetakse need. Nende ridade
	l�ppu pannakse '\n' m�rge, T�isridade l�ppu pannakse '\r\n' m�rge.
	c. kustutatakse t��deldud failid 'incoming' kataloogist.

2etapp. arhiveeri.php script
	koosneb kahest tegevusest:
	a. Iga p�eva varahommikul, peale s�da��d liidetakse �he p�eva
	�he rakenduse logid �heks suureks failiks ning samal ajal sorteeritakse 
	read kellaaja j�rgi. fail tekitatakse 'arhiiv' kataloogi.
	b. eelmise etapi tulemusena saadud fail pakitakse kokku.

        PS! Vastavalt Erki soovile, sai see koht ringi tehtud ja tunnifaile
        enam p�evafailideks kokku ei liideta vaid j�etakse tunnifailideks,
        kus on node logide read kokku pandud ja kellaaja j�rgi sorteeritud.
        Igale p�evale tehakse eraldi alamkataloog '/logs/arhiiv/' kataloogi.





Veidi t�psemalt tegevustest ja konfifailidest:

Scriptide t��ks vajalikud lisafailid:
	abiscript:
		config.class.php - konfifailide muutujate kasutamiseks lisaxript.
	konfidfailid:
		hosts_conf.php   - siin on kirjas serverid, kasutajanimed ja logikataloogid.
				   muutujate kuju on j�rgmine:
				   massiivimuutuja $hosts kus on kaks v�lja iga serveri puhul
				   $hosts[serveri nimi][user]='serveri kasutajanimi'
				   $hosts[serveri nimi][path]='logide kataloog serveris'
				   lisatingimuseks on paroolivaba lugemine remote serverist.
		config.ini.php   - siin kirjas k�ik scriptide t��ks vajalikud konstantsed muutujad.
				   neid muutujaid loetakse abiscripti (config.class.php) abil.
				   muutujad on jaotatud eraldi sktsioonide abil.
				   [paths] - scriptide t��ks vajalikud kataloogid.
					incoming_path  = 'kataloog kuhu remote serverist logid kopeeritakse'
					processed_path = 'kataloog kuhu pannakse t��deldud failid'
					archive_path   = 'kataloog kuhu l�hevad kokku pakitud logifailid'
				   [others] - muud parameetrid
					days =     mitu p�eva vanasid logisid kopeeritakse remote serevrist
					           ja sellest vanemad logid kustutatakse 'processed' kataloogist.
				   [prefix] - osadel logifailidel puudub nodenimi
					vaartus =  siin on kirjas s�nad, mis failinimes peavad sisalduma,
						   ja nende failinimede ette lisatakse vastava node nimi, et logifailide
						   nimed oleksid hilisemaks t��tlemiseks standartsed.
						   s�nad eraldatakse omavahel komaga.
				   [sufix] - osad logifailid ei roteerita iga tnd vaid alles p�eva l�pus.
					vaartus =  siin on kirjas s�nad, mis failinimes peavad sisalduma,
						   ja nende failinimede l�ppu lisatakse p�eva viimane tund '-23',
						   et logifailide nimed oleksid hilisemaks t��tlemiseks standartsed.
						   s�nad eraldatakse omavahel komaga.
				   [exclude] - logikataloogides on ka muid meile mitte vaja minevaid faile/logisid.
					vaartus =  siin on kirjas s�nad, mis failinimedes ei tohi sisaldauda
						   ja neid faile ei t�mmata remote serverist.
						   s�nad eraldatakse omavahel komadega.
				   [include] - millised on kindlad s�nad, mis peavad sisalduma failinimedes.
					vaartus =  siin on kirjas s�nad, mis peavad failinimedes sisalduma.
						   �ldjuhul piisab eelmisest muutujast ja 'include' pole vajadust
						   kasutada. pigem v�ib segadust tekitada. igaks juhuks siiski
						   selline v�imalus scriptis sees, kui peaks tekkima vajadus.
						   s�nad eraldatakse omavahel komaga.
				   [memory] - m�lumuutujad
					mem_size = kasutatav php m�lumaht. kuna standartne maht on suhteliselt
						   v�ike, siis scriptides kasutatavate suurte massiidide jaoks
						   vajalik m��rata suurem maht. 
				   [errors] - vead ja veateated. 
					error_mail   = siin kirjas mailiaadressid, kellele saadetakse veateated.
						       mailiaadressid on eraldatud komaga.
					warning_mail = siin kirjas mailiaadressid, kellele saadetakse hoiatuse teated.
						       mailiaadressid on eraldatud komaga.
					max_filesize = faili suurus, mille puhul saadetakse veateade.
					max_filesize_warning = faili suurus, mille puhul saadetakse hoiatus.
					max_filesize_message = veateade, mis saadetakse.
					max_filesize_warningmessage = hoiatus, mis saadetakse


kopeerimise script copylogs.php. Sellest t�psemalt ja kuidas see toimib.
	a.Loetakse m�llu incoming kataloogi failinimed.
	b.Loetakse m�llu processed kataloogist failinimed.
	c.Mergetakse kokku need m�lumassiivid. (leitakse k�ik juba kopeeritud failid)
	d.T�mmatakse k�ik uued logifailid remote serveritest serverite kaupa.
	  Kontrollitakse, et ei oleks vanemad kui konfis m��ratud p�evade arv.
	  Kontrollitakse, et poleks juba kopeeritud.
	  Kopeeritakse tingimustele vastavad failid (tingmimused konfifailis) ja
	  nimetatakse failid kopeerimise k�igus �htse struktuuriga failinimeks.
	  Saadetakse meil, kui failisuurus ol ootamatult liiga suur.
	e.T�stetakse sisse t�mmatud failid processed kataloogi.
	  Selle k�igus kontrollitakse logifailid rida-realt �le, kas
	  igal real kellaaeg ja serveri nimi ning vajadusel lisatakse.
	  See on vajalik hilisemaks sorteerimiseks kellaaja j�rgi ning
	  logide vaatamisel grep k�suga, et saaks kuvada ikka k�iki ridu.
	  Read, mis peaksid olema �hel real (veateated, listid, jne)
	  j�etakse �hele reale ja nende ette ei panda andmeid. Selle
	  kontrollimiseks on scriptis erit��tlus.
	f.scripti l�pp. -> t��tab tavaliselt loetud sekunditega (sub1min kuni 4-5min). 
	  Peale iga t�istundi p�evasel ajal.
sub1min 
arhiveerimise script. Sellest t�psemalt ja kuidas see toimib.
	On suhteliselt keeruline aga �ldiselt lahti seletatult kulgeb k�ik j�rgmiselt:
	a.Loetakse m�llu k�ik processed kataloogi failinimed.
	b.Tehakse korduses �ks rakendus korraga
	c.Tehakse sisemises korduses �ks p�ev korraga kui p�ev ei ole t�nane p�ev.
	  Kokku pakitakse ainult vanemad failid kui confifailis m�rgitud.
	d.arhiveeritakse
	  Kui rakendus on ainult �hel nodel, siis pakitakse tunnifailid lihtsalt kokku.
	  Muidu tehakse kordus �le tundide, kus
	    loetakse rakenduste kaupa read m�llu (otse failist lugemine oli ca 2x aeglasem)
	    reakaupa vaadatakse, kas kellaaeg on uuem v�i vanem ning pannakse read
	    omakorda vastavalt tulemusmassiivi m�lus. Iga tunni t��tlemisel salvestatakse
	    tulemus faili ning pakitakse see fail kokku. Pakitud fail pannakse
	    arhiivi kataloogis p�eva alamkataloogi kujul: '/aasta/kuu/p�ev/'
	e.kustutatakse vanemad failid processed kataloogist (konfifailis m��ratud p�evade arv)
	  vastavalt siis t��deldud failid (�he rakenduse �he p�eva tunnifailid)
	f.scripti l�pp. - t��tab tavaliselt alla 1tunni (oleneb failide suurustest), 
	  1x p�evas, ��sel.

