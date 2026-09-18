<template>
  <main class="kontakt">
    <section class="intro">
      <p class="eyebrow">Kontakt</p>
      <h1 class="intro__title">Reden wir über Ihr Fest</h1>
      <p class="intro__text">
        Am besten mit Anlass, Termin und ungefährer Gästezahl. Wir melden uns mit einem
        passenden Vorschlag.
      </p>
    </section>

    <section class="content">
      <div class="content__grid">
        <div class="details">
          <div>
            <p class="details__label">Telefon &amp; WhatsApp</p>
            <a href="tel:+436504977387" class="details__phone">+43 650 497 73 87</a>
          </div>
          <div>
            <p class="details__label">E-Mail</p>
            <a href="mailto:baischerc@outlook.com" class="details__email">baischerc@outlook.com</a>
          </div>
          <div>
            <p class="details__label">Adresse</p>
            <p class="details__address">Flurstraße 9<br>Neukirchen an der Enknach</p>
          </div>
          <a
            href="https://www.openstreetmap.org/?mlat=48.182339&mlon=13.036287#map=17/48.182339/13.036287"
            target="_blank"
            rel="noopener"
            class="route-btn"
          >Route öffnen</a>
        </div>
        <iframe
          title="Karte Neukirchen an der Enknach"
          src="https://www.openstreetmap.org/export/embed.html?bbox=12.9963%2C48.1673%2C13.0763%2C48.1973&layer=mapnik&marker=48.182339%2C13.036287"
          class="map"
        ></iframe>
      </div>
    </section>
  </main>
</template>

<style scoped>
.kontakt {
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
  padding: 64px 20px 32px;
}

@media (min-width: 640px) {
  .intro {
    padding: 96px 32px 56px;
  }
}

.intro__title {
  font-family: 'Spectral', Georgia, serif;
  font-weight: 500;
  font-size: clamp(38px, 6vw, 76px);
  line-height: 1.03;
  letter-spacing: -.02em;
  margin: 0 0 20px;
  color: #3a3a3a;
  max-width: 12ch;
}

.intro__text {
  font-size: 19px;
  line-height: 1.65;
  color: #5f5f5f;
  margin: 0;
  max-width: 48ch;
}

.content {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 20px 64px;
}

@media (min-width: 640px) {
  .content {
    padding: 0 32px 96px;
  }
}

.content__grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(300px, 100%), 1fr));
  gap: 40px;
  align-items: start;
  border-top: 1px solid rgba(84, 84, 84, .16);
  padding-top: 40px;
}

@media (min-width: 640px) {
  .content__grid {
    gap: 56px;
    padding-top: 48px;
  }
}

.details {
  display: flex;
  flex-direction: column;
  gap: 34px;
}

.details__label {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 10px;
  letter-spacing: .22em;
  text-transform: uppercase;
  color: #5f5f5f;
  margin: 0 0 10px;
}

.details__phone {
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: clamp(24px, 3vw, 38px);
  letter-spacing: -.01em;
  font-feature-settings: 'tnum';
  color: #3a3a3a;
}

.details__phone:hover {
  color: #545454;
}

.details__email {
  font-size: 21px;
  color: #3a3a3a;
  border-bottom: 1px solid rgba(84, 84, 84, .35);
}

.details__email:hover {
  color: #545454;
}

.details__address {
  font-size: 19px;
  line-height: 1.55;
  color: #3a3a3a;
  margin: 0;
}

.route-btn {
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 14px;
  letter-spacing: .02em;
  padding: 16px 32px;
  background: #545454;
  color: #fff;
  align-self: flex-start;
  transition: transform 160ms cubic-bezier(.23, 1, .32, 1), background 160ms ease;
}

.route-btn:hover {
  background: #3a3a3a;
  color: #fff;
}

.route-btn:active {
  transform: scale(.97);
}

.map {
  width: 100%;
  height: 300px;
  border: 1px solid rgba(84, 84, 84, .16);
  display: block;
  filter: grayscale(.55);
}

@media (min-width: 640px) {
  .map {
    height: 440px;
  }
}
</style>
