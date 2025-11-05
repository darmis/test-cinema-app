import { defineStore } from 'pinia'
import { useSchedule } from './useSchedule'

export const useTicket = defineStore('ticket', {
  state: () => ({
    selectedSeats: [],
    email: '',
  }),
  getters: {
    totalPrice() {
      const scheduleStore = useSchedule()
      return this.selectedSeats.reduce((total, seatId) => {
        const seat = scheduleStore.seats.find(s => s.id === seatId)
        if (seat && seat.price) {
          return total + parseFloat(seat.price)
        }
        return total
      }, 0).toFixed(2)
    },
  },
  actions: {
    toggleSeat(seatId) {
      const index = this.selectedSeats.indexOf(seatId)
      if (index > -1) {
        this.selectedSeats.splice(index, 1)
      } else {
        this.selectedSeats.push(seatId)
      }
    },
    isSelected(seatId) {
      return this.selectedSeats.includes(seatId)
    },
    clearSelectedSeats() {
      this.selectedSeats = []
    },
    clearEmail() {
      this.email = ''
    },
  },
})

