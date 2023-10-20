<template>
  <q-page class="flex flex-center">
    <div class="column justify-center items-center">
      <div class="col">
        <img
          alt="Quasar logo"
          src="~assets/thumb-up-fox.jpg"
          style="width: 200px; height: 200px"
        >
      </div>
      <form @submit.prevent="submitLogin" class="q-pa-md">
          <q-card flat bordered class="login-card">
          <q-card-section>
              <div class="text-h6">Anmelden</div>
          </q-card-section>

          <q-card-section class="q-gutter-y-md column">

              <q-input v-model="username" dense filled label="Email" />
              <q-input v-model="password" dense filled :type="isPwd ? 'password' : 'text'" label="Password">
                  <template v-slot:append>
                  <q-icon
                      :name="isPwd ? 'visibility_off' : 'visibility'"
                      class="cursor-pointer"
                      @click="isPwd = !isPwd"
                  />
                  </template>
              </q-input>

              <div class="row justify-end">
                  <q-btn
                      type="submit"
                      :loading="submitting"
                      label="Login"
                      class="q-mt-md"
                      color="teal"
                  >
                      <template v-slot:loading>
                      <q-spinner-facebook />
                      </template>
                  </q-btn>
              </div>

          </q-card-section>
          </q-card>
      </form>
    </div>
  </q-page>    
</template>

<style lang="sass" scoped>
.login-card
  width: 150%
  max-width: 250px
</style>

<script>
import { defineComponent, ref } from 'vue';
import { useAuthStore } from 'stores/RailsAuth';

export default defineComponent({
    name: 'LoginPage',

    setup () {

        const submitting = ref(false)
        const auth = useAuthStore()

        return {
            password: ref(''),
            isPwd: ref(true),

            username: ref(''),
            submitting,
            auth,
        }
    },
    methods: {
        submitLogin () {
            this.submitting = true

            this.auth.login(this.username, this.password)
            // Simulating a delay here.
            // When we are done, we reset "submitting"
            // Boolean to false to restore the
            // initial state.
            setTimeout(() => {
                // delay simulated, we are done,
                // now restoring submit to its initial state
                this.submitting = false
            }, 3000)
        }
    }
})
</script>