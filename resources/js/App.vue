<template>
  <router-view />
</template>

<script>
import axios from "axios";
import http from "./helpers/http";

export default {
  data() {
    return {
      notifications: [],
    };
  },
  methods: {
    //  checkRolesAuthenticity(){
    // axios.get('/api/user/permissions', {
    //     headers: {
    //     Authorization: `Bearer ${localStorage.getItem('token')}`
    //     }
    // })
    //     .then((response) => {
    //         this.myPermissions = response.data.permissions
    //         this.myRole = response.data.role
    //         // this.root.myPermissions  = response.data.permissions
    //         // inject('myPermissions') = response.data.permissions
    //     })
    //     .catch((error) => {
    //     if(!error.response && error.message === 'Network Error'){
    //         ElNotification({
    //         title: 'خطأ',
    //         message: 'تأكد من الاتصال بالانترنت',
    //         type: 'error',
    //         showClose: false,
    //         })
    //     }else{
    //         // console.log(error)
    //         // ElNotification({
    //         // title: 'خطأ',
    //         // message: error,
    //         // type: 'error',
    //         // showClose: false,
    //         // })
    //     }
    // })
    // }

    getNotifications() {
      http()
        .get("/api/notifications", {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        })
        .then((response) => {
          this.notifications = response.data.notifications;
        })
        .catch((error) => {
          if (!error.response && error.message === "Network Error") {
            ElNotification({
              title: "خطأ",
              message: "تأكد من الاتصال بالانترنت",
              type: "error",
              showClose: false,
            });
          } else {
            // console.log(error)
            // ElNotification({
            // title: 'خطأ',
            // message: error,
            // type: 'error',
            // showClose: false,
            // })
          }
        });
    },
  },
  mounted() {
    this.getNotifications();
  },
};
</script>

<style lang="scss" scoped></style>
