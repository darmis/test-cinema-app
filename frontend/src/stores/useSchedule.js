import { defineStore } from 'pinia'
import axios from 'axios'

export const useSchedule = defineStore('schedule', {
  state: () => ({
    schedules: [],
    movies: [],
    seats: [],
  }),
  actions: {
    async fetchAllSchedules() {
      this.loading = true
      this.error = ''
      try {
        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost'
        const res = await axios.get(`${apiUrl}/api/schedules`)
        const data = Array.isArray(res.data) ? res.data : (res.data?.data || [])
        this.schedules = data
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
        const data = Array.isArray(res.data) ? res.data : (res.data?.data || [])
        this.seats = data
      } catch {
        this.seats = []
      }
    },
    async fetchAllMovies() {
      try {
        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost'
        const res = await axios.get(`${apiUrl}/api/movies`)
        const data = Array.isArray(res.data) ? res.data : (res.data?.data || [])
        this.movies = data
      } catch {
        this.movies = []
      }
    },
  },
})


