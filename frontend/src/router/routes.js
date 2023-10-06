
const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue') },
      { path: 'start', component: () => import('pages/StartPage.vue')}
    ]
  },
  {
    path: '/account',
    component: () => import('layouts/AccountLayout.vue'),
    children: [
    { path: '', redirect: '/account/login' },
    { path: 'login', component: () => import('pages/LoginPage.vue') },
    { path: 'register', component: () => import('pages/RegisterPage.vue') },
    ]
  },
  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
