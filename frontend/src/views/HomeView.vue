<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Movie</label>
        <select v-model="selectedMovie" class="w-full border rounded px-3 py-2">
          <option :value="''">Select movie</option>
          <option v-for="movie in movies" :key="movie.id" :value="movie.id">
            {{ movie.title }}
          </option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label>
        <select v-model="selectedSchedule" class="w-full border rounded px-3 py-2">
          <option :value="''">Select schedule</option>
          <option v-for="schedule in filteredSchedules" :key="schedule.id" :value="schedule.id">
            {{ schedule.date }} {{ schedule.start_time }} ({{ schedule.hall_name }}, {{ schedule.movie_title }})
          </option>
        </select>
      </div>
    </div>
    <div v-if="seats.length" class="mt-4">
      <h2 class="text-xl font-semibold mb-2">Seats</h2>
      <div class="flex flex-wrap gap-2">
        <template v-for="(seat, index) in seats" :key="seat.id">
          <div v-if="index === 0 || seats[index - 1].row !== seat.row" class="w-full"></div>
          <div 
            :class="[
              'border rounded px-3 py-2 flex justify-between cursor-pointer',
              ticketStore.isSelected(seat.id) ? 'border-blue-300 border-2' : ''
            ]"
            @click="ticketStore.toggleSeat(seat.id)"
          >
            <div>
              <div class="text-xxs">Row: {{ seat.row }}</div>
              <div class="text-xxs">Seat: {{ seat.column }}</div>
              <div class="text-sm py-2 text-center">{{ seat.price }} Eur</div>
            </div>
          </div>
        </template>
      </div>
    </div>
    <div v-if="ticketStore.totalPrice > 0" class="text-right">
      <div class="m-2">
        <div class="p-4">Total price: {{ ticketStore.totalPrice }} Eur</div>
        <div>Email: <input v-model="ticketStore.email" type="email" class="border rounded px-3 py-2"></div>
      </div>
      <div class="m-2">
        <button class="bg-blue-500 text-white px-4 py-2 rounded" @click="orderTickets">Order</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useSchedule } from '@/stores/useSchedule'
import { useTicket } from '@/stores/useTicket'

const scheduleStore = useSchedule()
const ticketStore = useTicket()
const selectedMovie = ref('')
const selectedSchedule = ref('')

const movies = computed(() => scheduleStore.movies)
const schedules = computed(() => scheduleStore.schedules)
const seats = computed(() => scheduleStore.seats)

const filteredSchedules = computed(() => {
  if (!selectedMovie.value) {
    return schedules.value
  }
  return schedules.value.filter(schedule => schedule.movie_id == selectedMovie.value)
})

onMounted(() => {
  scheduleStore.fetchAllSchedules()
  scheduleStore.fetchAllMovies()
})

watch(selectedMovie, () => {
  selectedSchedule.value = ''
  ticketStore.clearSelectedSeats()
  scheduleStore.seats = []
})

watch(selectedSchedule, (id) => {
  ticketStore.clearSelectedSeats()
  scheduleStore.fetchSeatsForSchedule(id)
})

const orderTickets = () => {
  // order tickets functionality
}
</script>


<style scoped>
.text-xxs {
  font-size: 0.6rem;
}
</style>
