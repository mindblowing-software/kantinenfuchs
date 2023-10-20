import { defineStore } from "pinia";
import { fetchWrapper } from "src/helpers/fetchBackendWrapper";

// this store uses rails devise auth
const baseUrl = `${process.env.BACKEND_RAILS_API}`;

console.log(baseUrl);

export const useAuthStore = defineStore("auth", {
  state: () => ({
    // initialize state from local storage to enable user to stay logged in
    //user: JSON.parse(localStorage.getItem('user')),
    user: [],
    token: null,
    returnUrl: null,
  }),

  actions: {
    async login(username, password) {
      var userData = {};
      userData.user = {};
      userData.user.email = username;
      userData.user.password = password;

      const response = await fetchWrapper.post(`${baseUrl}/login`, userData);
      const data = await response.json();

      console.log(response);

      const user = data.status.data.user;
      const hasura_token = data.status.data.token;

      console.log(hasura_token);

      if (user.id) {
        // update pinia state
        this.user = user;
        this.token = response.headers?.get("Authorization");

        console.log(this.token);

        // store user details and jwt in local storage to keep user logged in between page refreshes
        //localStorage.setItem('user', JSON.stringify(user));

        // redirect to previous url or default to home page
        this.router.push(this.returnUrl || "/");
      } else {
        // redirect to login if no user has been selected
        this.router.push("/account/login");
      }
    },
    async refresh() {
      const response = await fetchWrapper.post(`${baseUrl}/token/refresh`);
      const data = await response.json();

      console.log("After refresh");

      const user = data.status.data.user;

      if (user.id) {
        // update pinia state
        this.user = user;

        // store user details and jwt in local storage to keep user logged in between page refreshes
        //localStorage.setItem('user', JSON.stringify(user));

        // redirect to previous url or default to home page
        //this.router.push(this.returnUrl || '/');
      } else {
        // redirect to login if no user has been selected
        this.router.push("/account/login");
      }
    },
    async change_pwd(userid, password) {
      const rc = await fetchWrapper.post(`${baseUrl}/change_password`, {
        userid,
        password,
      });

      if (rc) {
        console.log("Password changed");
      } else {
        console.log("Failed");
      }
    },
    isAdmin() {
      return this.user.rolle == "admin" ? true : false;
    },
    isTokenExpired() {
      return this.user.expire * 1000 < Date.now() ? true : false;
    },
    async logout() {
      await fetchWrapper.delete(`${baseUrl}/logout`);

      this.user = null;
      this.token = null;

      this.router.push("/account/login");
    },
    forceLogin() {
      this.router.push("/account/login");
    },
  },
});
