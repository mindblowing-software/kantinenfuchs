<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          Kantinenfuchs
        </q-toolbar-title>

        <div>v{{ version }}</div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
    >
    <q-list>

      <q-item header>
        <q-item-section avatar>
      <!--
          <q-avatar size="56px" class="q-mb-sm">
            <img src="https://xsgames.co/randomusers/avatar.php?g=female">
      -->
          <q-avatar color="primary" text-color="white">
            {{ auth.user.firstname.substring(0,1) + auth.user.lastname.substring(0,1) }}
          </q-avatar>
        </q-item-section>
        <q-item-section >
          <div class="text-weight-bold">{{auth.user.firstname}} {{auth.user.lastname}}</div>
          <div>{{auth.user.login}}</div>
        </q-item-section>
      </q-item>

      <q-item 
        @click="logout()"
        exact
        clickable 
        v-ripple>
        <q-item-section avatar>
          <q-icon name="logout" />
        </q-item-section>

        <q-item-section>
          Logout
        </q-item-section>
      </q-item>

      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { version } from '../../package.json'
import { defineComponent, ref } from 'vue'
import { useAuthStore } from 'stores/Auth'

export default defineComponent({
  name: 'MainLayout',

  setup () {
    const leftDrawerOpen = ref(false)

    const auth = useAuthStore()
    
    function logout() {
      auth.logout()
    }

    return {
      leftDrawerOpen,
      toggleLeftDrawer () {
        leftDrawerOpen.value = !leftDrawerOpen.value
      },
      version: version,
      auth,
      logout,
    }
  }
})
</script>
