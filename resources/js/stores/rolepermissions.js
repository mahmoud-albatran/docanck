import { defineStore } from 'pinia'
import { ElNotification } from 'element-plus'
import http from '../helpers/http'

export const RolePermissionStore = defineStore("RolePermissionData",{
    state : () => ({
      loadingRolePermission: false,
      permissions: [],
      roleId: null,

    }),

    getters: {

    },

    actions: {

        async showRolePermission(role){
           this.permissions = []
           this.loadingRolePermission = true
           http().get('api/role-permissions/', {
                params: {
                    role: role,
                    search: this.search
                },
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
           })
           .then((response) => {
                this.permissions = response.data.permissions
                this.roleId = response.data.roleId
                console.log(this.permissions)
                console.log(this.roleId)
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadingRolePermission = false
            })
        },
        storeRolePermission(roleId, permissionId){

            let form_data = new FormData();

            form_data.append('permission_id', permissionId);


           http().put('api/role-permissions/'+roleId, {
            permission_id: permissionId
           }, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
           })
           .then((response) => {
                // this.permissions = response.data.permissions
                // this.roleId = response.data.roleId
                // console.log(this.permissions)
                // console.log(this.roleId)
                 ElNotification({
                    title: 'success',
                    message: response.data.message,
                    type: 'success',
                    showClose: false,
                })
                // this.showRolePermission(roleId)
            })
            .catch((error) => {
                console.log(error)
            }).finally(() => {
                this.loadingRolePermission = false
            })
        },



    }

})
