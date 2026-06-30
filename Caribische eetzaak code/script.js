// Producten komen nu uit de database via index.php
const producten = productenUitDatabase;

let winkelwagen = [];
let gekozenProduct = null;

// Wissel tussen pagina's door CSS-klasse 'active' toe te voegen
function showPage(paginaNaam) {
  const paginas = document.querySelectorAll('.page');

  paginas.forEach(function(pagina) {
    pagina.classList.remove('active');
  });

  document.getElementById(paginaNaam).classList.add('active');

  if (paginaNaam === 'producten') {
    toonProducten();
  }

  if (paginaNaam === 'winkelwagen') {
    toonWinkelwagen();
  }

  if (paginaNaam === 'afrekenen') {
    toonAfrekenen();
  }

  window.scrollTo(0, 0);
}

// Productkaarten tonen op de productenpagina
function toonProducten() {
  const lijst = document.getElementById('productenLijst');
  const zoekwaarde = document.getElementById('zoekInput').value.toLowerCase();
  const filter = document.getElementById('filterSelect').value;

  let resultaat = producten.filter(function(product) {
    return product.naam.toLowerCase().includes(zoekwaarde);
  });

  if (filter === 'Gerecht' || filter === 'Toetje') {
    resultaat = resultaat.filter(function(product) {
      return product.soort === filter;
    });
  }

  if (filter === 'laag') {
    resultaat.sort(function(a, b) {
      return a.prijs - b.prijs;
    });
  }

  if (filter === 'hoog') {
    resultaat.sort(function(a, b) {
      return b.prijs - a.prijs;
    });
  }

  lijst.innerHTML = '';

  resultaat.forEach(function(product) {
    lijst.innerHTML += `
      <div class="product-card" onclick="openDetail(${product.id})">
        <div class="product-icoon">${product.icoon}</div>
        <h2>${product.naam}</h2>
        <p>Gram: ${product.gram}</p>
        <p>Grootte: ${product.grootte}</p>
        <p class="prijs">€${Number(product.prijs).toFixed(2).replace('.', ',')}</p>
        <span class="soort">${product.soort}</span>
      </div>
    `;
  });
}

// Detailpagina openen voor het geselecteerde product
function openDetail(id) {
  gekozenProduct = producten.find(function(product) {
    return product.id === id;
  });

  const detailBox = document.getElementById('detailBox');

  detailBox.innerHTML = `
    <div class="detail-emoji">${gekozenProduct.icoon}</div>
    <div class="detail-info">
      <h1>${gekozenProduct.naam}</h1>
      <p class="prijs">Prijs: €${Number(gekozenProduct.prijs).toFixed(2).replace('.', ',')}</p>
      <p>${gekozenProduct.gram} gram</p>
      <label>Grootte:
        <select id="grootteInput">
          <option>Klein</option>
          <option selected>Middel</option>
          <option>Groot</option>
        </select>
      </label>
      <label>Aantal:
        <input id="aantalInput" type="number" min="1" value="1">
      </label>
      <button onclick="toevoegenAanWinkelwagen()">Voeg toe aan winkelwagen</button>
    </div>
    <div class="ingredienten">
      <h2>Ingrediënten</h2>
      <p>${gekozenProduct.ingredienten}</p>
    </div>
  `;

  showPage('detail');
}

// Product met gekozen aantal en grootte toevoegen aan winkelwagen
function toevoegenAanWinkelwagen() {
  const aantal = Number(document.getElementById('aantalInput').value);
  const grootte = document.getElementById('grootteInput').value;

  winkelwagen.push({
    id: gekozenProduct.id,
    naam: gekozenProduct.naam,
    prijs: Number(gekozenProduct.prijs),
    grootte: grootte,
    aantal: aantal
  });

  updateAantal();
  alert('Product is toegevoegd aan de winkelwagen.');
  showPage('winkelwagen');
}

function updateAantal() {
  let totaalAantal = 0;

  winkelwagen.forEach(function(item) {
    totaalAantal += item.aantal;
  });

  document.getElementById('cartAantal').innerText = totaalAantal;
}

function toonWinkelwagen() {
  const vak = document.getElementById('winkelwagenInhoud');

  if (winkelwagen.length === 0) {
    vak.innerHTML = '<p>Uw winkelwagen is nog leeg.</p>';
    return;
  }

  vak.innerHTML = maakTabel(true);
}

function toonAfrekenen() {
  const vak = document.getElementById('afrekenInhoud');

  if (winkelwagen.length === 0) {
    vak.innerHTML = '<p>Er staan geen producten in uw winkelwagen.</p>';
    return;
  }

  vak.innerHTML = maakTabel(false);
}

// Bouw de HTML-tabel voor winkelwagen of afrekenen
function maakTabel(metKnoppen) {
  let html = `
    <table>
      <tr>
        <th>Naam</th>
        <th>Grootte</th>
        <th>Aantal</th>
        <th>Prijs</th>
        <th></th>
      </tr>
  `;

  let totaal = 0;

  winkelwagen.forEach(function(item, index) {
    const subtotaal = item.prijs * item.aantal;
    totaal += subtotaal;

    html += `
      <tr>
        <td>${item.naam}</td>
        <td>${item.grootte}</td>
        <td>${item.aantal}</td>
        <td>€${subtotaal.toFixed(2).replace('.', ',')}</td>
        <td>
          ${metKnoppen ? `<button class="kleine-knop" onclick="wijzigAantal(${index})">Wijzigen</button>
          <button class="kleine-knop verwijder" onclick="verwijderItem(${index})">Verwijderen</button>` : ''}
        </td>
      </tr>
    `;
  });

  html += `</table><p class="totaal">Totaal: €${totaal.toFixed(2).replace('.', ',')}</p>`;
  return html;
}

function verwijderItem(index) {
  winkelwagen.splice(index, 1);
  updateAantal();
  toonWinkelwagen();
}

function wijzigAantal(index) {
  const nieuwAantal = prompt('Vul het nieuwe aantal in:', winkelwagen[index].aantal);

  if (nieuwAantal !== null && Number(nieuwAantal) > 0) {
    winkelwagen[index].aantal = Number(nieuwAantal);
    updateAantal();
    toonWinkelwagen();
  }
}

function bestellingAfronden() {
  if (winkelwagen.length === 0) {
    alert('Uw winkelwagen is leeg.');
    return;
  }

  const bestelling = {
    klant: {
      naam: document.getElementById('klantNaam').value,
      email: document.getElementById('klantEmail').value,
      telefoon: document.getElementById('klantTelefoon').value,
      straat: document.getElementById('klantStraat').value,
      huisnummer: document.getElementById('klantHuisnummer').value,
      postcode: document.getElementById('klantPostcode').value,
      woonplaats: document.getElementById('klantWoonplaats').value
    },
    betaalmethode: document.getElementById('betaalmethode').value,
    producten: winkelwagen
  };

  fetch('bestelling_opslaan.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(bestelling)
  })
  .then(function(response) {
    return response.json();
  })
  .then(function(data) {
    if (data.success) {
      alert('Bestelling is opgeslagen in de database. Bestelnummer: ' + data.bestelling_id);
      winkelwagen = [];
      updateAantal();
      showPage('home');
    } else {
      alert(data.message);
    }
  })
  .catch(function() {
    alert('Er ging iets mis met opslaan. Gebruik XAMPP en open de site via localhost.');
  });
}

function wachtwoordTonen() {
  const input = document.getElementById('wachtwoord');

  if (input.type === 'password') {
    input.type = 'text';
  } else {
    input.type = 'password';
  }
}

toonProducten();
updateAantal();

function registreren() {
  const gegevens = {
    naam: document.getElementById('regNaam').value,
    adres: document.getElementById('regAdres').value,
    woonplaats: document.getElementById('regWoonplaats').value,
    telefoon: document.getElementById('regTelefoon').value,
    postcode: document.getElementById('regPostcode').value,
    iban: document.getElementById('regIban').value,
    email: document.getElementById('regEmail').value,
    wachtwoord: document.getElementById('regWachtwoord').value,
    wachtwoord2: document.getElementById('regWachtwoord2').value
  };

  fetch('registratie_opslaan.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(gegevens)
  })
  .then(function(response) {
    return response.json();
  })
  .then(function(data) {
    document.getElementById('registratieMelding').innerText = data.message;

    if (data.success) {
      alert('Account is aangemaakt. U kunt nu inloggen.');
      showPage('login');
    }
  })
  .catch(function() {
    alert('Registreren lukt niet. Open de website via localhost met XAMPP.');
  });
}

function inloggen() {
  const gegevens = {
    email: document.getElementById('loginEmail').value,
    wachtwoord: document.getElementById('wachtwoord').value
  };

  fetch('inloggen.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(gegevens)
  })
  .then(function(response) {
    return response.json();
  })
  .then(function(data) {
    document.getElementById('loginMelding').innerText = data.message;

    if (data.success) {
      alert('U bent ingelogd.');
      window.location.reload();
    }
  })
  .catch(function() {
    alert('Inloggen lukt niet. Open de website via localhost met XAMPP.');
  });
}
