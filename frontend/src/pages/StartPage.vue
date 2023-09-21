<template>
  <q-page>
    <div>
      <q-card flat class="bg-grey-2 q-mt-lg q-ma-md">
        <q-card-section>
          <div class="text-h6 text-weight-bold text-center">Wieviele Kinder bekommen heute Essen?</div>
        </q-card-section>

        <q-card-section>
          <q-card flat class="bg-blue q-ma-md">
            <div class="row q-pt-lg">
              <div class="col">
                <div class="text-h1 text-weight-bolder text-center text-white">24</div>
              </div>
            </div> 
            <div class="row q-pb-lg">
              <div class="col">
                <div class="text-h6 text-weight-bold text-center text-white">Kinder</div>
              </div>
            </div>
          </q-card>
        </q-card-section>

        <q-card-section>
          <div class="column q-pa-sm items-center">
            <q-btn to="/start" class="btn-fixed-width" color="negative" text-color="white" label="Ändern" />
          </div>
          <div class="column q-pa-sm items-center">
            <q-btn to="/start" class="btn-fixed-width" color="positive" text-color="white" label="Bestätigen" />
          </div>
        </q-card-section>
      </q-card>

      <q-card flat class="q-mt-xl q-ma-md">
        <q-card-section>
          <div class="column q-pa-sm items-center">
            <q-btn to="/" class="btn-fixed-width" color="primary" text-color="white" label="Logout" />
          </div>
        </q-card-section>
      </q-card>      
    </div>
  </q-page>
</template>

<style lang="sass" scoped>
.btn-fixed-width
  width: 200px
</style>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
  name: "StartPage",
  setup() {
    
  }
})
</script>
