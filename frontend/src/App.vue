<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'

const navItems = [
  { label: 'Start', to: { name: 'home' } },
  { label: 'Catering', to: { name: 'catering' } },
  { label: 'Vermietung', to: { name: 'vermietung' } },
  { label: 'Über uns', to: { name: 'ueber-uns' } },
  { label: 'Kontakt', to: { name: 'kontakt' } },
]

const route = useRoute()
const menuOpen = ref(false)

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value
}

const closeMenu = () => {
  menuOpen.value = false
}

watch(() => route.fullPath, closeMenu)

let scrollY = 0

watch(menuOpen, (open) => {
  if (open) {
    scrollY = window.scrollY
    document.body.style.position = 'fixed'
    document.body.style.top = `-${scrollY}px`
    document.body.style.left = '0'
    document.body.style.right = '0'
  } else {
    document.body.style.position = ''
    document.body.style.top = ''
    document.body.style.left = ''
    document.body.style.right = ''
    window.scrollTo(0, scrollY)
  }
})

const onKeydown = (e) => {
  if (e.key === 'Escape') closeMenu()
}

const onResize = () => {
  if (window.innerWidth >= 768) closeMenu()
}

onMounted(() => {
  window.addEventListener('keydown', onKeydown)
  window.addEventListener('resize', onResize)
})

onUnmounted(() => {
  window.removeEventListener('keydown', onKeydown)
  window.removeEventListener('resize', onResize)
  document.body.style.overflow = ''
})
</script>

<template>
  <div class="page">
    <header class="site-header">
      <div class="site-header__inner">
        <RouterLink :to="{ name: 'home' }" class="brand">
          <img src="/images/logo.png" alt="Baischer Fleischerei und Catering" class="brand__logo">
          <span class="brand__name">Baischer<br>Fleischerei &amp; Catering</span>
        </RouterLink>

        <nav class="nav nav--desktop">
          <RouterLink
            v-for="item in navItems"
            :key="item.label"
            :to="item.to"
            class="nav__item"
            active-class="nav__item--active"
            exact-active-class="nav__item--active"
          >
            {{ item.label }}
          </RouterLink>
        </nav>

        <button
          type="button"
          class="burger"
          :class="{ 'burger--open': menuOpen }"
          :aria-expanded="menuOpen"
          aria-controls="mobile-nav"
          aria-label="Menü öffnen"
          @click="toggleMenu"
        >
          <span class="burger__line"></span>
          <span class="burger__line"></span>
          <span class="burger__line"></span>
        </button>
      </div>

      <transition name="mobile-nav">
        <nav v-if="menuOpen" id="mobile-nav" class="nav nav--mobile">
          <RouterLink
            v-for="item in navItems"
            :key="item.label"
            :to="item.to"
            class="nav__item nav__item--mobile"
            active-class="nav__item--mobile-active"
            exact-active-class="nav__item--mobile-active"
          >
            {{ item.label }}
          </RouterLink>
        </nav>
      </transition>
    </header>

    <RouterView />

    <footer class="site-footer">
      <div class="site-footer__inner">
        <p class="site-footer__text">Baischer Clemens · Flurstraße 9, Neukirchen an der Enknach</p>
        <p class="site-footer__text">+43 650 497 73 87 · baischerc@outlook.com</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.page {
  min-height: 100vh;
  background: #efece7;
}

.site-header {
  position: sticky;
  top: 0;
  z-index: 50;
  background: rgba(239, 236, 231, .94);
  backdrop-filter: saturate(140%) blur(6px);
  border-bottom: 1px solid rgba(84, 84, 84, .16);
}

.site-header__inner {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  align-items: stretch;
  justify-content: space-between;
  gap: 24px;
}

@media (min-width: 640px) {
  .site-header__inner {
    padding: 0 32px;
  }
}

.brand {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  padding: 14px 0;
  min-width: 0;
}

.brand__logo {
  flex: none;
  height: 46px;
  width: auto;
  object-fit: contain;
}

@media (min-width: 640px) {
  .brand__logo {
    height: 54px;
    width: auto;
  }
}

.brand__name {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 10px;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: #545454;
  line-height: 1.5;
}

@media (min-width: 640px) {
  .brand__name {
    font-size: 11px;
    letter-spacing: .22em;
  }
}

/* Desktop horizontal nav: shown from tablet width up */
.nav--desktop {
  display: none;
  align-items: stretch;
  gap: 0;
}

@media (min-width: 768px) {
  .nav--desktop {
    display: flex;
  }
}

.nav__item {
  display: flex;
  align-items: center;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 12px;
  letter-spacing: .12em;
  text-transform: uppercase;
  padding: 0 18px;
  border: none;
  border-bottom: 2px solid transparent;
  background: transparent;
  color: #5f5f5f;
  cursor: pointer;
  transition: color 160ms ease, border-color 160ms ease;
}

.nav__item--active {
  border-bottom-color: #545454;
  color: #2f2f2f;
}

/* Burger button: shown below tablet width only */
.burger {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 5px;
  width: 44px;
  height: 44px;
  margin: auto 0;
  padding: 0;
  border: none;
  background: transparent;
  cursor: pointer;
  flex: none;
}

@media (min-width: 768px) {
  .burger {
    display: none;
  }
}

.burger__line {
  width: 22px;
  height: 2px;
  background: #3a3a3a;
  transition: transform 220ms cubic-bezier(.23, 1, .32, 1), opacity 160ms ease;
}

.burger--open .burger__line:nth-child(1) {
  transform: translateY(7px) rotate(45deg);
}

.burger--open .burger__line:nth-child(2) {
  opacity: 0;
}

.burger--open .burger__line:nth-child(3) {
  transform: translateY(-7px) rotate(-45deg);
}

.nav--mobile {
  display: flex;
  flex-direction: column;
  padding: 4px 24px 20px;
  border-top: 1px solid rgba(84, 84, 84, .16);
}

@media (min-width: 640px) {
  .nav--mobile {
    padding: 4px 32px 24px;
  }
}

.nav__item--mobile {
  padding: 16px 0;
  border-bottom: 1px solid rgba(84, 84, 84, .12);
  font-size: 13px;
}

.nav__item--mobile:last-child {
  border-bottom: none;
}

.nav__item--mobile-active {
  color: #2f2f2f;
}

.mobile-nav-enter-active,
.mobile-nav-leave-active {
  transition: opacity 200ms ease, transform 200ms cubic-bezier(.23, 1, .32, 1);
  transform-origin: top;
}

.mobile-nav-enter-from,
.mobile-nav-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

.site-footer {
  border-top: 1px solid rgba(84, 84, 84, .16);
}

.site-footer__inner {
  max-width: 1240px;
  margin: 0 auto;
  padding: 40px 24px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px 24px;
  justify-content: space-between;
  align-items: baseline;
}

@media (min-width: 640px) {
  .site-footer__inner {
    padding: 40px 32px;
  }
}

.site-footer__text {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 11px;
  letter-spacing: .16em;
  text-transform: uppercase;
  color: #5f5f5f;
  margin: 0;
}
</style>
