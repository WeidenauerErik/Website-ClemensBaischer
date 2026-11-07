<template>
  <main class="impressum">
    <section class="intro">
      <p class="eyebrow">Impressum</p>
      <h1 class="intro__title">Impressum</h1>
    </section>

    <section class="content">
      <h2 class="content__heading">Angaben gemäß § 5 E-Commerce-Gesetz (ECG), § 14 Unternehmensgesetzbuch (UGB) und § 25 Mediengesetz (MedienG)</h2>

      <h3 class="content__subheading">Unternehmensangaben</h3>
      <p class="content__text">Unternehmensname: Baischer Fleischerei &amp; Catering</p>
      <p class="content__text">Inhaber / Firmenwortlaut: Baischer Fleischerei &amp; Catering</p>
      <p class="content__text">Unternehmenssitz: Flurstraße 9</p>
      <p class="content__text">PLZ / Ort: 5145 Neukirchen an der Enknach, Österreich</p>
      <p class="content__text">Telefon: <a class="content__link" href="tel:+436504977387">+43 650 4977387</a></p>
      <p class="content__text">E-Mail: <a class="content__link" href="mailto:baischerc@outlook.com">baischerc@outlook.com</a></p>
      <p class="content__text">Website: <a class="content__link" href="https://baischer-fleischerei-catering.com/" target="_blank" rel="noopener">https://baischer-fleischerei-catering.com/</a></p>

      <h3 class="content__subheading">Unternehmensgegenstand</h3>
      <p class="content__text">
        Betrieb einer Fleischerei sowie Erbringung von Cateringdienstleistungen, Herstellung und
        Verkauf von Fleisch- und Wurstwaren und Zubereitung von Speisen.
      </p>

      <h3 class="content__subheading">Gewerberechtliche Angaben</h3>
      <p class="content__text">Gewerbebehörde: Bezirkshauptmannschaft Braunau</p>
      <p class="content__text">Kammerzugehörigkeit: Wirtschaftskammer Österreich, zuständige Landeskammer</p>
      <p class="content__text">
        Anwendbare Rechtsvorschriften: Gewerbeordnung 1994 (GewO), abrufbar unter
        <a class="content__link" href="https://www.ris.bka.gv.at" target="_blank" rel="noopener">www.ris.bka.gv.at</a>
      </p>

      <h3 class="content__subheading">Medieninhaber und Offenlegung gemäß § 25 MedienG</h3>
      <p class="content__text">
        Informationen über das Unternehmen Baischer Fleischerei &amp; Catering, dessen Produkte,
        Dienstleistungen und Angebote im Bereich Fleischerei und Catering sowie Möglichkeiten zur
        Kontaktaufnahme und Anfrage.
      </p>

      <h3 class="content__subheading">Haftung für Inhalte</h3>
      <p class="content__text">
        Die Inhalte dieser Website werden mit größtmöglicher Sorgfalt erstellt und regelmäßig
        überprüft. Dennoch kann keine Gewähr für die Richtigkeit, Vollständigkeit und Aktualität
        sämtlicher Informationen übernommen werden.
      </p>
      <p class="content__text">Gesetzliche Haftungsansprüche bleiben unberührt.</p>

      <h3 class="content__subheading">Haftung für externe Links</h3>
      <p class="content__text">
        Diese Website kann Links zu externen Websites Dritter enthalten. Auf deren Inhalte haben
        wir keinen Einfluss. Für die Inhalte verlinkter Seiten sind die jeweiligen Betreiber
        verantwortlich. Bei Bekanntwerden konkreter Rechtsverletzungen werden entsprechende Links
        unverzüglich entfernt.
      </p>

      <h3 class="content__subheading">Urheberrecht</h3>
      <p class="content__text">
        Die auf dieser Website veröffentlichten Inhalte, Texte, Fotografien und Grafiken
        unterliegen, soweit anwendbar, dem Urheberrecht.
      </p>
      <p class="content__text">
        Jede Nutzung außerhalb der gesetzlichen Schranken des Urheberrechts bedarf der Zustimmung
        der jeweiligen Rechteinhaber.
      </p>

      <h3 class="content__subheading">Datenschutz</h3>
      <p class="content__text">
        Informationen zur Verarbeitung personenbezogener Daten finden Sie in unserer gesonderten
        Datenschutzerklärung.
      </p>
    </section>
  </main>
</template>

<style scoped>
.impressum {
  animation: rise 420ms cubic-bezier(.23, 1, .32, 1) both;
}

.eyebrow {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 11px;
  letter-spacing: .22em;
  text-transform: uppercase;
  color: #5f5f5f;
  margin: 0 0 24px;
}

.intro {
  max-width: 1240px;
  margin: 0 auto;
  padding: 64px 20px 40px;
}

@media (min-width: 640px) {
  .intro {
    padding: 96px 32px 64px;
  }
}

.intro__title {
  font-family: 'Spectral', Georgia, serif;
  font-weight: 500;
  font-size: clamp(38px, 6vw, 76px);
  line-height: 1.03;
  letter-spacing: -.02em;
  margin: 0;
  color: #3a3a3a;
  max-width: 12ch;
}

.content {
  max-width: 820px;
  margin: 0 auto;
  padding: 0 20px 96px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

@media (min-width: 640px) {
  .content {
    padding: 0 32px 120px;
  }
}

.content__heading {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 15px;
  letter-spacing: .02em;
  color: #3a3a3a;
  margin: 0 0 24px;
}

.content__subheading {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 13px;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: #3a3a3a;
  margin: 32px 0 4px;
}

.content__text {
  font-size: 16px;
  line-height: 1.7;
  color: #4f4f4f;
  margin: 0;
}

.content__link {
  color: #4f4f4f;
  text-decoration: underline;
}
</style>
