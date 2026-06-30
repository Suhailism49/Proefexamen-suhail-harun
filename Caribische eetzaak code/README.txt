Caribbean SH - website met admin inlogsysteem

Installatie:
1. Zet deze map in C:\xampp\htdocs\
2. Start Apache en MySQL in XAMPP
3. Ga naar http://localhost/phpmyadmin
4. Importeer database/caribbean_sh.sql
5. Open http://localhost/eetzaak_admin_login_db/index.php

Adminpagina:
- Open: http://localhost/eetzaak_admin_login_db/admin_login.php
- Email: admin@caribbeansh.nl
- Wachtwoord: Admin123!

Wat is toegevoegd:
- admin_login.php: aparte loginpagina voor admin
- admin_auth.php: controleert of de admin is ingelogd
- admin_logout.php: admin uitloggen
- admin.php is beveiligd
- gebruikers.php is beveiligd
- bestellingen.php is beveiligd
- database heeft een standaard admin account met rol 'admin'

Let op:
Dit is een simpele schoolproject-versie. Voor een echte website moet je het standaard wachtwoord veranderen.
