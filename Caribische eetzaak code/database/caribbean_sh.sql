CREATE DATABASE IF NOT EXISTS caribbean_sh;
USE caribbean_sh;

DROP TABLE IF EXISTS bestelling_regels;
DROP TABLE IF EXISTS bestellingen;
DROP TABLE IF EXISTS inlog_pogingen;
DROP TABLE IF EXISTS inloggegevens;
DROP TABLE IF EXISTS registraties;
DROP TABLE IF EXISTS producten;
DROP TABLE IF EXISTS klanten;

CREATE TABLE klanten (
    klant_id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    telefoon VARCHAR(20),
    straat VARCHAR(100) NOT NULL,
    huisnummer VARCHAR(10) NOT NULL,
    postcode VARCHAR(10) NOT NULL,
    woonplaats VARCHAR(80) NOT NULL,
    aangemaakt_op DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE registraties (
    registratie_id INT AUTO_INCREMENT PRIMARY KEY,
    voornaam_achternaam VARCHAR(120) NOT NULL,
    straat_huisnummer VARCHAR(120) NOT NULL,
    woonplaats VARCHAR(80) NOT NULL,
    telefoon VARCHAR(20),
    postcode VARCHAR(10) NOT NULL,
    iban VARCHAR(34),
    email VARCHAR(120) NOT NULL UNIQUE,
    aangemaakt_op DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE inloggegevens (
    inlog_id INT AUTO_INCREMENT PRIMARY KEY,
    registratie_id INT NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    wachtwoord_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(30) DEFAULT 'klant',
    actief TINYINT(1) DEFAULT 1,
    laatste_login DATETIME NULL,
    FOREIGN KEY (registratie_id) REFERENCES registraties(registratie_id)
);

CREATE TABLE inlog_pogingen (
    poging_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(120) NOT NULL,
    succesvol TINYINT(1) NOT NULL,
    bericht VARCHAR(255),
    geprobeerd_op DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE producten (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(120) NOT NULL,
    soort VARCHAR(50) NOT NULL,
    prijs DECIMAL(8,2) NOT NULL,
    gram INT,
    grootte VARCHAR(30),
    icoon VARCHAR(10),
    ingredienten TEXT
);

CREATE TABLE bestellingen (
    bestelling_id INT AUTO_INCREMENT PRIMARY KEY,
    klant_id INT NOT NULL,
    betaalmethode VARCHAR(50) NOT NULL,
    totaalprijs DECIMAL(8,2) NOT NULL,
    status VARCHAR(30) DEFAULT 'Nieuw',
    besteld_op DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (klant_id) REFERENCES klanten(klant_id)
);

CREATE TABLE bestelling_regels (
    regel_id INT AUTO_INCREMENT PRIMARY KEY,
    bestelling_id INT NOT NULL,
    product_id INT NOT NULL,
    product_naam VARCHAR(120) NOT NULL,
    grootte VARCHAR(30),
    aantal INT NOT NULL,
    prijs_per_stuk DECIMAL(8,2) NOT NULL,
    subtotaal DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (bestelling_id) REFERENCES bestellingen(bestelling_id),
    FOREIGN KEY (product_id) REFERENCES producten(product_id)
);

INSERT INTO producten (naam, soort, prijs, gram, grootte, icoon, ingredienten) VALUES
('Spicy gestoomd vispakketje', 'Gerecht', 14.95, 250, 'Middel', '🐟', 'Kabeljauw, rode paprika, ui, tomaat, knoflook, lente-ui, zwarte peper, zout, tijm, Spaanse peper en paprikapoeder.'),
('Caribische pasteitjes', 'Gerecht', 5.00, 150, 'Groot', '🥟', 'Bladerdeeg, kipgehakt, ui, paprika en Caribische kruiden.'),
('Jerk chicken met vegetarische kip', 'Gerecht', 12.50, 300, 'Middel', '🍗', 'Vegetarische kip, rijst, bonen, jerk kruiden, paprika en saus.'),
('Mango Mouse', 'Toetje', 7.90, 150, 'Klein', '🥭', 'Mango, slagroom, suiker en vanille.'),
('Ananas Kokoscake', 'Toetje', 4.50, 120, 'Middel', '🍍', 'Ananas, kokos, bloem, suiker, boter en ei.'),
('Frozen aardbei daiquiri mocktail', 'Drank', 5.95, 250, 'Middel', '🍓', 'Aardbei, limoen, ijs, suiker en kokos.');

-- Standaard admin account voor de adminpagina
-- Email: admin@caribbeansh.nl
-- Wachtwoord: Admin123!
INSERT INTO registraties (voornaam_achternaam, straat_huisnummer, woonplaats, telefoon, postcode, iban, email)
VALUES ('Admin Caribbean SH', 'Eetzaak Plein 1023', 'Rotterdam', '0173731198', '2142QT', NULL, 'admin@caribbeansh.nl');

INSERT INTO inloggegevens (registratie_id, email, wachtwoord_hash, rol, actief)
VALUES (LAST_INSERT_ID(), 'admin@caribbeansh.nl', '$2y$12$7ZHwq7BxumQR4ZKmlUJ.uOA67bLN7fXo1G7UOKQUG1TNjwVgmCGk2', 'admin', 1);
