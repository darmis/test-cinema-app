import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import OrderConfirmationView from '../views/OrderConfirmationView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/order-confirmation',
      name: 'order-confirmation',
      component: OrderConfirmationView
    }
  ]
})

export default router


