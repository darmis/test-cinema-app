import { defineStore } from 'pinia'
import axios from 'axios'
import { useSchedule } from './useSchedule'

export const useTicket = defineStore('ticket', {
  state: () => ({
    selectedSeats: [],
    email: '',
    userToken: null,
    scheduleId: null,
  }),
  getters: {
    totalPrice() {
      const scheduleStore = useSchedule()
      let total = 0
      for (const seatId of this.selectedSeats) {
        const seat = scheduleStore.seats.find(s => s.id === seatId)
        if (seat && seat.price) {
          total += parseFloat(seat.price)
        }
      }
      return total.toFixed(2)
    },
  },
  actions: {
    initUserToken() {
      if (!this.userToken) {
        const stored = localStorage.getItem('user_token')
        if (stored) {
          this.userToken = stored
        } else {
          this.userToken = this.generateUserToken()
          localStorage.setItem('user_token', this.userToken)
        }
      }
      return this.userToken
    },
    generateUserToken() {
        return Date.now().toString() + Math.floor(1000000000 + Math.random() * 9000000000).toString();
    },
    async fetchCart() {
      if (!this.userToken) return null

      try {
        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost'
        const res = await axios.get(`${apiUrl}/api/cart`, {
          params: { user_token: this.userToken }
        })
        return res.data
      } catch {
        return null
      }
    },
    async updateCartSchedule(scheduleId) {
      if (!scheduleId || !this.userToken) return

      this.scheduleId = scheduleId

      try {
        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost'
        await axios.post(`${apiUrl}/api/cart`, {
          user_token: this.userToken,
          schedule_id: scheduleId,
        })
      } catch {
        // 
      }
    },
    async updateCartItems(scheduleId) {
      if (!this.userToken || !scheduleId) return

      try {
        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost'
        await axios.post(`${apiUrl}/api/cart/items`, {
          user_token: this.userToken,
          schedule_id: scheduleId,
          seat_ids: this.selectedSeats,
        })
      } catch {
        // 
      }
    },
    toggleSeat(seatId) {
      const index = this.selectedSeats.indexOf(seatId)
      if (index > -1) {
        this.selectedSeats.splice(index, 1)
      } else {
        this.selectedSeats.push(seatId)
      }
      if (this.scheduleId) {
        this.updateCartItems(this.scheduleId)
      }
    },
    isSelected(seatId) {
      return this.selectedSeats.includes(seatId)
    },
    clearSelectedSeats() {
      this.selectedSeats = []
      if (this.scheduleId) {
        this.updateCartItems(this.scheduleId)
      }
    },
    async createOrder(scheduleId) {
      if (!scheduleId || !this.email || this.selectedSeats.length === 0) {
        throw new Error('Email, schedule, and at least one seat are required')
      }

      try {
        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost'
        const res = await axios.post(`${apiUrl}/api/orders`, {
          email: this.email,
          schedule_id: scheduleId,
          seat_ids: this.selectedSeats,
        }, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
        })
        return res.data
      } catch (error) {
        throw error
      }
    },
  },
})

