import { defineStore } from 'pinia'
import { fetchWrapper } from 'src/helpers/fetchWrapper'

const baseUrl = `${process.env.BACKEND_API}/users`;

console.log(baseUrl);

export const useAuthStore = defineStore('auth', {
  state: () => ({
    // initialize state from local storage to enable user to stay logged in
    //user: JSON.parse(localStorage.getItem('user')),
    user: [],
    returnUrl: null
  }),

  actions: {
    async login(username, password) {
        const user = await fetchWrapper.post(`${baseUrl}/authenticate`, { username, password });

        if(user.id) {
          // update pinia state
          this.user = user;

          // store user details and jwt in local storage to keep user logged in between page refreshes
          //localStorage.setItem('user', JSON.stringify(user));

          // redirect to previous url or default to start page
          console.log("Auth", this.returnUrl)
          this.router.push(this.returnUrl || '/start');
        } else {

          // redirect to login if no user has been selected
          this.router.push('/account/login');
        }

    },
    async refresh() {
        const user = await fetchWrapper.post(`${baseUrl}/refresh_token`);

        if(user.id) {
          // update pinia state
          this.user = user;

          // store user details and jwt in local storage to keep user logged in between page refreshes
          //localStorage.setItem('user', JSON.stringify(user));

          // redirect to previous url or default to home page
          //this.router.push(this.returnUrl || '/');
        } else {

          // redirect to login if no user has been selected
          this.router.push('/account/login');
        }
    },
    async change_pwd(userid, password) {
        const rc = await fetchWrapper.post(`${baseUrl}/change_password`, { userid, password });

        if(rc) {
          console.log("Password changed")
        } else {
          console.log("Failed")
        }
    },
    isAdmin() {
      return (this.user.rolle == "admin"? true : false)
    },
    isTokenExpired() {
        return ((this.user.expire * 1000) < Date.now()? true: false)
    },
    async logout() {
      
        await fetchWrapper.post(`${baseUrl}/logout`)

        this.user = null
        localStorage.removeItem('user')
        this.router.push('/account/login')
    }
  }
})
