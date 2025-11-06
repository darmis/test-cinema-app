<style scoped>
.text-xxs {
  font-size: 0.6rem;
}
</style>

<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div v-if="!isLoading">
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
            <div :class="[
              'border rounded px-3 py-2 flex justify-between',
              isSeatTaken(seat.id)
                ? 'border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed'
                : 'cursor-pointer',
              !isSeatTaken(seat.id) && ticketStore.isSelected(seat.id) ? 'border-blue-300 border-2' : ''
            ]" @click="!isSeatTaken(seat.id) && ticketStore.toggleSeat(seat.id)">
              <div :class="isSeatTaken(seat.id) ? 'text-gray-400' : ''">
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
          <div class="mb-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                class="text-red-500">*</span></label>
            <input v-model="ticketStore.email" type="email" required class="border rounded px-3 py-2">
          </div>
        </div>
        <div class="m-2">
          <button
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
            :disabled="isLoading || !isEmailValid || !selectedSchedule || ticketStore.selectedSeats.length === 0"
            @click="orderTickets">Order</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useSchedule } from '@/stores/useSchedule'
import { useTicket } from '@/stores/useTicket'

const router = useRouter()
const scheduleStore = useSchedule()
const ticketStore = useTicket()
const selectedMovie = ref('')
const selectedSchedule = ref('')
const isLoading = ref(true)
let takenSeatsCheck = null

const movies = computed(() => scheduleStore.movies)
const schedules = computed(() => scheduleStore.schedules)
const seats = computed(() => scheduleStore.seats)

const filteredSchedules = computed(() => {
  if (!selectedMovie.value) {
    return schedules.value
  }
  return schedules.value.filter(schedule => schedule.movie_id == selectedMovie.value)
})

const isEmailValid = computed(() => {
  if (!ticketStore.email) {
    return false
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(ticketStore.email)
})

const isSeatTaken = (seatId) => {
  const seatIdNum = Number(seatId)
  const isUserSeat = scheduleStore.userSeatIds.some(id => Number(id) === seatIdNum) ||
    ticketStore.selectedSeats.some(id => Number(id) === seatIdNum)

  if (isUserSeat) {
    return false
  }

  return scheduleStore.othersTakenSeatIds.some(id => Number(id) === seatIdNum)
}

onMounted(async () => {
  isLoading.value = true
  ticketStore.initUserToken()
  await scheduleStore.fetchAllSchedules()
  await scheduleStore.fetchAllMovies()

  // existing selections check if user_token exists
  if (localStorage.getItem('user_token')) {
    const cartData = await ticketStore.fetchCart()
    if (cartData) {
      if (cartData.schedule_id) {
        const schedule = scheduleStore.schedules.find(s => s.id == cartData.schedule_id)
        if (schedule) {
          selectedMovie.value = schedule.movie_id
          selectedSchedule.value = cartData.schedule_id
          await scheduleStore.fetchSeatsForSchedule(cartData.schedule_id)

          if (cartData.seat_ids && cartData.seat_ids.length > 0) {
            ticketStore.selectedSeats = cartData.seat_ids
          }

          // every 3 seconds checks for other users carts and orders seats
          scheduleStore.fetchTakenSeats(cartData.schedule_id, ticketStore.userToken)
          takenSeatsCheck = setInterval(() => {
            if (selectedSchedule.value) {
              scheduleStore.fetchTakenSeats(selectedSchedule.value, ticketStore.userToken)
            }
          }, 3000)
        }
      }
    }
  }
  isLoading.value = false
})

onUnmounted(() => {
  if (takenSeatsCheck) {
    clearInterval(takenSeatsCheck)
  }
  scheduleStore.userSeatIds = []
  scheduleStore.othersTakenSeatIds = []
  isLoading.value = false
})

watch(selectedMovie, () => {
  if (!isLoading.value) {
    selectedSchedule.value = ''
    ticketStore.clearSelectedSeats()
    scheduleStore.seats = []
  }
})

watch(selectedSchedule, (id) => {
  if (takenSeatsCheck) {
    clearInterval(takenSeatsCheck)
    takenSeatsCheck = null
  }

  if (id) {
    ticketStore.clearSelectedSeats()
    ticketStore.updateCartSchedule(id)
    scheduleStore.fetchSeatsForSchedule(id)

    scheduleStore.fetchTakenSeats(id, ticketStore.userToken)
    takenSeatsCheck = setInterval(() => {
      if (selectedSchedule.value) {
        scheduleStore.fetchTakenSeats(selectedSchedule.value, ticketStore.userToken)
      }
    }, 3000)
  } else {
    scheduleStore.userSeatIds = []
    scheduleStore.othersTakenSeatIds = []
  }
})

async function orderTickets() {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  // order form validation
  if (
    !ticketStore.email ||
    !selectedSchedule.value ||
    ticketStore.selectedSeats.length === 0 ||
    !emailRegex.test(ticketStore.email)
  ) {
    return;
  }

  isLoading.value = true;

  try {
    await ticketStore.createOrder(selectedSchedule.value)

    // clearing all data after successful order create
    ticketStore.clearSelectedSeats()
    ticketStore.email = ''
    selectedMovie.value = ''
    selectedSchedule.value = ''
    scheduleStore.seats = []
    localStorage.removeItem('user_token')
    ticketStore.userToken = null

    isLoading.value = false;

    router.push('/order-confirmation')
  } catch (error) {
    isLoading.value = false;
  }
}
</script>
