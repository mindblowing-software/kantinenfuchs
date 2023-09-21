<template>
  <q-page class="">
    <div class="column justify-center items-center">
      <div class="col">
        <img
          alt="Quasar logo"
          src="~assets/thumb-up-fox.jpg"
          style="width: 200px; height: 200px"
        >
      </div>
    </div>
    <div class="column justify-center items-center">
      <div class="col">
        <div class="q-pa-md q-gutter-sm">
          <q-btn to="/start" color="white" text-color="black" label="Login" />
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'IndexPage'
})
</script>
