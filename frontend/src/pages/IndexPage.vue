<template>
  <q-page>
    <div>
      <q-card flat class="bg-grey-2 q-mt-lg q-ma-md">
        <q-card-section>
          <div v-if="meals_confirmed" class="text-h6 text-weight-bold text-center">Heute wurde bereits das Essen bestellt
            für </div>
          <div v-else class="text-h6 text-weight-bold text-center">Wieviele Kinder bekommen heute Essen?</div>
        </q-card-section>

        <q-card-section>
          <q-card flat class="bg-blue q-ma-md">
            <div class="row q-pt-lg">
              <div class="col">
                <div class="text-h1 text-weight-bolder text-center text-white">{{ count }}</div>
              </div>
            </div>
            <div class="row q-pb-lg">
              <div class="col">
                <div class="text-h6 text-weight-bold text-center text-white">Kinder</div>
              </div>
            </div>
          </q-card>
        </q-card-section>
        <q-card-section v-if="meals_confirmed">
          <div class="column q-pa-sm items-center">
            <div class="q-gutter-md">
              <q-btn class="btn-small-fixed-width text-h6" disabled color="negative" text-color="white" label="-" />
              <q-btn class="btn-small-fixed-width text-h6" disabled color="negative" text-color="white" label="+" />
            </div>
          </div>
          <div class="column q-pa-sm items-center">
            <q-btn class="btn-fixed-width" color="info" text-color="white" label="Korrigieren"
              @click="meals_confirmed = false" />
          </div>
        </q-card-section>
        <q-card-section v-else>
          <div class="column q-pa-sm items-center">
            <div class="q-gutter-md">
              <q-btn v-if="count > 0" class="btn-small-fixed-width text-h6" color="negative" text-color="white" label="-"
                @click="count--" />
              <q-btn v-if="count == 0" class="btn-small-fixed-width text-h6" disabled color="negative" text-color="white"
                label="-" />
              <q-btn class="btn-small-fixed-width text-h6" color="negative" text-color="white" label="+"
                @click="count++" />
            </div>
          </div>
          <div class="column q-pa-sm items-center">
            <q-btn class="btn-fixed-width" color="positive" text-color="white" label="Bestätigen"
              @click="confirm = true" />
          </div>
        </q-card-section>
      </q-card>

      <q-card flat class="q-mt-xl q-ma-md">
        <q-card-section>
          <div class="column q-pa-sm items-center">
            <q-btn class="btn-fixed-width" color="primary" text-color="white" label="Logout" @click="logout()" />
          </div>
        </q-card-section>
      </q-card>

      <q-dialog v-model="confirm" persistent>
        <q-card>
          <q-card-section class="row items-center">
            <q-avatar icon="lunch_dining" color="primary" text-color="white" />
            <span class="q-ml-sm">Hiermit wird eine Bestellung für {{ count }} Essen aufgegeben.</span>
          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat label="Abbrechen" color="primary" v-close-popup />
            <q-btn flat label="Bestätigen" color="primary" @click="meals_confirmed = true" v-close-popup />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>
  </q-page>
</template>

<style lang="sass" scoped>
.btn-fixed-width
  width: 200px
.btn-small-fixed-width
  width: 90px
</style>

<script setup>
import { defineComponent, ref } from 'vue'
import { useAuthStore } from 'stores/RailsAuth'
import { fetchWrapper } from "src/helpers/fetchBackendWrapper";
// import HasuraApi from 'src/helpers/hasuraApi'
// import KUNDEBYID from 'src/queries/kunde_by_id.gql'

const auth = useAuthStore()
const confirm = ref(false)
const meals_confirmed = ref(false)


const count = ref(0)
const kunde = ref({})

function logout() {
  auth.logout()
}


async function fetchData() {

  // const api = new HasuraApi()
  const baseUrl = `${process.env.BACKEND_RAILS_API}`;

  // var data = await api.graphql(KUNDEBYID, { id: this.auth.user.kunde_id })
  const response = await fetchWrapper.get(`${baseUrl}/customers/${auth.user.customer_id}`);
  const data = await response.json();

  console.log(data);

  // this.kunde = data.data.kunden_by_pk
  // this.count = this.kunde.default_anzahl
  count.value = data.number_orders
}

fetchData()
</script>
