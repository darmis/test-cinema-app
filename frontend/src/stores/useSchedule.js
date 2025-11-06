import { defineStore } from 'pinia'
import axios from 'axios'

export const useSchedule = defineStore('schedule', {
  state: () => ({
    schedules: [],
    movies: [],
    seats: [],
    userSeatIds: [],
    othersTakenSeatIds: [],
  }),
  actions: {
    async fetchAllSchedules() {
      this.loading = true
      this.error = ''
      try {
        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost'
        const res = await axios.get(`${apiUrl}/api/schedules`)
        this.schedules = res.data.data
      } catch {
        this.schedules = []
      }
    },
    async fetchSeatsForSchedule(scheduleId) {
      if (!scheduleId) {
        this.seats = []
        return
      }

      try {
        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost'
        const res = await axios.get(`${apiUrl}/api/schedules/${scheduleId}/seats`)
        this.seats = res.data.data
      } catch {
        this.seats = []
      }
    },
    async fetchAllMovies() {
      try {
        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost'
        const res = await axios.get(`${apiUrl}/api/movies`)
        this.movies = res.data.data
      } catch {
        this.movies = []
      }
    },
    async fetchTakenSeats(scheduleId, userToken = null) {
      if (!scheduleId) {
        this.userSeatIds = []
        this.othersTakenSeatIds = []
        return
      }

      try {
        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost'
        const params = userToken ? { user_token: userToken } : {}
        const res = await axios.get(`${apiUrl}/api/schedules/${scheduleId}/taken-seats`, { params })
        
        this.userSeatIds = res.data.user_seat_ids ?? []
        this.othersTakenSeatIds = res.data.other_taken_seat_ids ?? []
      } catch {
        this.userSeatIds = []
        this.othersTakenSeatIds = []
      }
    },
  },
})


