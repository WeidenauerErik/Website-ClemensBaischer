import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior() {
    return { top: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/HomeView.vue'),
      meta: {
        title: 'Baischer Fleischerei & Catering',
        description:
          'Baischer Fleischerei & Catering in Neukirchen an der Enknach — Catering für jedes Event sowie Fleisch und Wurstwaren aus eigener Produktion.',
      },
    },
    {
      path: '/catering',
      name: 'catering',
      component: () => import('../views/CateringView.vue'),
      meta: {
        title: 'Catering für jeden Anlass',
        description:
          'Catering von Baischer Fleischerei in Neukirchen an der Enknach: Buffets, kalte Platten und Festküchen-Betreuung für Firmenfeste, Hochzeiten und private Feiern.',
      },
    },
    {
      path: '/ueber-uns',
      name: 'ueber-uns',
      component: () => import('../views/UeberUnsView.vue'),
      meta: {
        title: 'Über uns',
        description:
          'Baischer Fleischerei & Catering aus Neukirchen an der Enknach: Fleisch und Wurstwaren aus eigener Produktion, Lohnschlachtung für Landwirte und Clemens Baischer bei den EuroSkills im Fleischerhandwerk.',
      },
    },
    {
      path: '/kontakt',
      name: 'kontakt',
      component: () => import('../views/KontaktView.vue'),
      meta: {
        title: 'Kontakt',
        description:
          'Kontakt zu Baischer Fleischerei & Catering in Neukirchen an der Enknach: Telefon, WhatsApp, E-Mail und Anfahrt in der Flurstraße 9.',
      },
    },
  ],
})

router.afterEach((to) => {
  document.title =
    to.name === 'home' || !to.meta?.title
      ? 'Baischer Fleischerei & Catering'
      : `${to.meta.title} · Baischer Fleischerei & Catering`

  if (to.meta?.description) {
    let metaDescription = document.querySelector('meta[name="description"]')
    if (!metaDescription) {
      metaDescription = document.createElement('meta')
      metaDescription.setAttribute('name', 'description')
      document.head.appendChild(metaDescription)
    }
    metaDescription.setAttribute('content', to.meta.description)
  }
})

export default router
