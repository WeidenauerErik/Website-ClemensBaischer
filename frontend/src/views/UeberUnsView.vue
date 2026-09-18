<template>
  <main class="ueber">
    <section class="intro">
      <p class="eyebrow">Über uns</p>
      <h1 class="intro__title">Handwerk mit Anspruch</h1>
    </section>

    <section class="content">
      <div class="content__grid">
        <div class="content__text-col">
          <p class="content__text">
            Baischer Fleischerei &amp; Catering ist ein Betrieb in Neukirchen an der Enknach.
            Fleisch und Wurstwaren aus eigener Produktion, Lohnschlachtung für Landwirte aus der
            Umgebung und Catering für jedes Event, von der kleinen Feier bis zur
            Festküchen-Betreuung.
          </p>
          <p class="content__text">
            Clemens Baischer vertrat Österreich bei den EuroSkills im Fleischerhandwerk.
            Präzision, Schnitt und Präsentation zählen dort genauso wie bei uns im Alltag.
          </p>
        </div>
        <img src="/images/clemens-baischer.jpeg" alt="Clemens Baischer bei der Arbeit" class="content__image">
      </div>
      <div class="skills-grid">
        <img
          src="/images/clemens-skillsaustria.jpeg"
          alt="Clemens Baischer als Teilnehmer von Skills Austria im Fleischerhandwerk"
          class="content__image"
        >
        <img
          src="/images/euroskills.jpeg"
          alt="Clemens Baischer bei den EuroSkills"
          class="content__image"
        >
      </div>
    </section>
  </main>
</template>

<style scoped>
.ueber {
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
  grid-template-columns: repeat(auto-fit, minmax(min(320px, 100%), 1fr));
  gap: 40px;
  align-items: start;
}

@media (min-width: 640px) {
  .content__grid {
    gap: 64px;
  }
}

.content__text-col {
  display: flex;
  flex-direction: column;
  gap: 22px;
  padding-top: 8px;
}

.content__text {
  font-size: 19px;
  line-height: 1.75;
  color: #4f4f4f;
  margin: 0;
}

.content__image {
  width: 100%;
  display: block;
}

.content__image--full {
  margin-top: 24px;
}

.skills-grid {
  margin-top: 24px;
  display: grid;
  grid-template-columns: 1fr;
  align-items: start;
  gap: 16px;
}

@media (min-width: 640px) {
  .skills-grid {
    grid-template-columns: 1fr 2.6fr;
    gap: 24px;
  }
}
</style>
